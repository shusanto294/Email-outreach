<?php

namespace App\Console\Commands;

use App\Models\Lead;
use App\Models\Reply;
use App\Models\Mailbox;
use Illuminate\Console\Command;
use Webklex\IMAP\Facades\Client;

class CheckMailboxes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-mailboxes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
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
                    $lead = Lead::where('email', $fromEmail)->first();
    
                    if ($lead) {
                        $newReply = Reply::create([
                            'from_name'    => $sender->personal,
                            'from_address' => $sender->mailbox . '@' . $sender->host,
                            'to'           => $mailbox->mail_username,
                            'subject'      => $message->getSubject(),
                            'body'         => $message->getHTMLBody(),
                            'campaign_id'  => $lead->campaign_id ?? null,
                        ]);
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
