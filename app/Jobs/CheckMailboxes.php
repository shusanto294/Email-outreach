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

class CheckMailboxes implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
    public function handle(): void
    {
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
                    $newReply = Reply::create([
                        'from_name'    => $sender->personal,
                        'from_address' => $sender->mailbox . '@' . $sender->host,
                        'to'           => $mailbox->mail_username,
                        'subject'      => $message->getSubject(),
                        'body'         => $message->getHTMLBody(),
                        'campaign_id'  => null
                    ]);
    
                    $lead = Lead::where('email', $fromEmail)->first();
    
                    if ($lead) {
                        $newReply->campaign_id = $lead->campaign_id;
                        $lead->replied = true;
                        $lead->save();
                        $newReply->save();
                    }
                }
    
                // Move to Archive
                // $message->move('INBOX.Archive');

                // Mark as seen
                $message->setFlag('Seen');
            }
    
            $client->disconnect();
        }
    }


}
