<?php

namespace App\Jobs;

use App\Models\Lead;
use App\Models\Reply;
use App\Models\Mailbox;
use Illuminate\Bus\Queueable;
use Webklex\IMAP\Facades\Client;
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
                $client = Client::make([
                    'host'          => $mailbox->mail_imap_host,
                    'port'          => $mailbox->mail_imap_port,
                    'encryption'    => $mailbox->mail_imap_encryption ?? 'ssl',
                    'validate_cert' => true,
                    'username'      => $mailbox->mail_username,
                    'password'      => $mailbox->mail_password,
                    'protocol'      => 'imap'
                ])->connect();

                foreach ($client->getFolder('INBOX')->messages()->unseen()->limit(50)->get() as $message) {
                    $sender = $message->getFrom()[0] ?? null;
                    if ($sender && $lead = Lead::where('email', $sender->mail)->first()) {
                        $subject = $this->decodeMimeStr($message->getSubject());
                        $body = mb_convert_encoding($message->getHTMLBody() ?: $message->getTextBody(), 'UTF-8', $message->getHTMLCharset() ?: 'UTF-8');

                        Reply::create([
                            'from_name'    => $sender->personal,
                            'from_address' => "{$sender->mailbox}@{$sender->host}",
                            'to'           => $mailbox->mail_username,
                            'subject'      => $subject,
                            'body'         => $body,
                            'campaign_id'  => $lead->campaign_id ?? null,
                        ]);
                    }
                    $message->setFlag('Seen');
                }
                $client->disconnect();
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
