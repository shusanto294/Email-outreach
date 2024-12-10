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

    protected $tries = 3; // Set the number of maximum attempts
    protected $backoff = 10; // Set the backoff time in seconds

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        try {
            $mailboxes = Mailbox::cursor();

            foreach ($mailboxes as $mailbox) {
                $client = Client::make([
                    'host'          => $mailbox->mail_imap_host,
                    'port'          => $mailbox->mail_imap_port,
                    'encryption'    => $mailbox->mail_imap_encryption ?? 'ssl',
                    'validate_cert' => true,
                    'username'      => $mailbox->mail_username,
                    'password'      => $mailbox->mail_password,
                    'protocol'      => 'imap'
                ]);

                $client->connect();
                $inboxFolder = $client->getFolder('INBOX');

                // Fetch unseen messages in batches
                $messages = $inboxFolder->messages()->unseen()->limit(50)->get();

                if ($messages->isEmpty()) {
                    continue;
                }

                foreach ($messages as $message) {
                    $sender = $message->getFrom()[0];

                    if ($sender) {
                        $fromEmail = $sender->mail;
                        $lead = Lead::where('email', $fromEmail)->first();

                        if ($lead) {
                            // Try to retrieve the HTML body, fallback to text body if HTML is missing
                            $body = $message->getHTMLBody();
                            if (empty($body)) {
                                $body = $message->getTextBody();
                            }

                            // Handle encoding and inline attachments
                            $body = $this->processEmailBody($body, $message);

                            // Decode subject if needed
                            $subject = $this->decodeMimeStr($message->getSubject());

                            Reply::create([
                                'from_name'    => $sender->personal,
                                'from_address' => $sender->mailbox . '@' . $sender->host,
                                'to'           => $mailbox->mail_username,
                                'subject'      => $subject,
                                'body'         => $body,
                                'campaign_id'  => $lead->campaign_id ?? null,
                            ]);
                        }
                    }

                    // Mark as seen
                    $message->setFlag('Seen');
                }

                $client->disconnect();
            }
        } catch (\Exception $e) {
            Log::error('Failed to check mailboxes: ' . $e->getMessage());
            throw $e; // Rethrow the exception to let Laravel handle retries
        }
    }

    /**
     * Decode MIME encoded string to properly display subject lines with special characters.
     */
    protected function decodeMimeStr($string)
    {
        $decoded = '';
        $elements = imap_mime_header_decode($string);

        foreach ($elements as $element) {
            $charset = !empty($element->charset) ? $element->charset : 'UTF-8';

            if (!in_array(strtoupper($charset), mb_list_encodings())) {
                $charset = 'ISO-8859-1'; // Fallback if charset is invalid
            }

            $decoded .= mb_convert_encoding($element->text, 'UTF-8', $charset);
        }

        return $decoded;
    }

    /**
     * Process the email body to handle encoding and inline attachments.
     */
    protected function processEmailBody($body, $message)
    {
        $encoding = $message->getHTMLCharset() ?: 'UTF-8';
    
        // Ensure encoding is valid and not empty
        if (empty($encoding) || !in_array(strtoupper($encoding), mb_list_encodings())) {
            $encoding = 'UTF-8'; // Fallback encoding
        }
    
        $body = mb_convert_encoding($body, 'UTF-8', $encoding);
    
        // Handle inline attachments
        $attachments = $message->getAttachments();
        foreach ($attachments as $attachment) {
            $disposition = $attachment->getDisposition();
            if ($disposition && strtolower($disposition) === 'inline') {
                $cidPrefix = 'cid:';
                $cidSource = $cidPrefix . $attachment->getId();
                $cidTarget = $cidPrefix . $attachment->getContentId();
                $body = str_replace($cidSource, $cidTarget, $body);
            }
        }
    
        return $body;
    }
    
    
}
