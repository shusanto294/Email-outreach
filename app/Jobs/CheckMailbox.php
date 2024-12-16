<?php

namespace App\Jobs;

use App\Models\Lead;
use App\Models\Reply;
use Ddeboer\Imap\Server;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class CheckMailbox implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $mailbox;

    /**
     * Create a new job instance.
     *
     * @param $mailbox
     */
    public function __construct($mailbox)
    {
        $this->mailbox = $mailbox;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $server = new Server(
                $this->mailbox->mail_imap_host,
                $this->mailbox->mail_imap_port,
                $this->mailbox->mail_imap_encryption ?? 'ssl'
            );

            $connection = $server->authenticate($this->mailbox->mail_username, $this->mailbox->mail_password);

            $imapMailbox = $connection->getMailbox('INBOX');
            $messages = $imapMailbox->getMessages(new \Ddeboer\Imap\Search\Flag\Unseen());

            foreach ($messages as $message) {
                $sender = $message->getFrom();
                if ($sender) {
                    $lead = Lead::where('email', $sender->getAddress())->first();

                    $subject = $this->decodeMimeStr($message->getSubject()) ?? 'n/a';
                    $body = $message->getBodyHtml() ?: $message->getBodyText() ?: 'n/a';

                    Reply::create([
                        'from_name'    => 'na',
                        'from_address' => $sender->getAddress(),
                        'to'           => $this->mailbox->mail_username ?: 'n/a',
                        'subject'      => $subject,
                        'body'         => $body,
                        'campaign_id'  => $lead->campaign_id ?? 'n/a',
                    ]);
                }
                $message->markAsSeen();
            }
        } catch (\Exception $e) {
            Log::error('Failed to check mailbox: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Decode MIME string.
     *
     * @param string $string
     * @return string
     */
    protected function decodeMimeStr($string)
    {
        $decoded = '';
        $elements = imap_mime_header_decode($string);

        foreach ($elements as $element) {
            $charset = !empty($element->charset) && in_array(strtoupper($element->charset), mb_list_encodings()) ? $element->charset : 'ISO-8859-1';
            $decoded .= mb_convert_encoding($element->text, 'UTF-8', $charset);
        }

        return $decoded;
    }
}
