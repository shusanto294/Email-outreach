<?php

namespace App\Jobs;

use App\Models\Lead;
use App\Models\Reply;
use App\Models\Mailbox;
use Illuminate\Bus\Queueable;
use Ddeboer\Imap\Server;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class CheckMailboxes implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $tries = 3;
    protected $backoff = 10;

    public function handle()
    {
        try {
            foreach (Mailbox::cursor() as $mailbox) {
                $server = new Server(
                    $mailbox->mail_imap_host,
                    $mailbox->mail_imap_port,
                    $mailbox->mail_imap_encryption ?? 'ssl'
                );

                $connection = $server->authenticate($mailbox->mail_username, $mailbox->mail_password);

                $mailbox = $connection->getMailbox('INBOX');
                $messages = $mailbox->getMessages(new \Ddeboer\Imap\Search\Flag\Unseen());

                foreach ($messages as $message) {
                    $sender = $message->getFrom();
                    if ($sender && $lead = Lead::where('email', $sender->getAddress())->first()) {
                        $subject = $this->decodeMimeStr($message->getSubject());
                        $body = $message->getBodyHtml() ?: $message->getBodyText();

                        Reply::create([
                            'from_name'    => $sender->getName(),
                            'from_address' => $sender->getAddress(),
                            'to'           => $mailbox->mail_username,
                            'subject'      => $subject,
                            'body'         => $body,
                            'campaign_id'  => $lead->campaign_id ?? null,
                        ]);
                    }
                    $message->markAsSeen();
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to check mailboxes: ' . $e->getMessage());
            throw $e;
        }
    }

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
