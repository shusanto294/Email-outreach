<?php

namespace App\Jobs;

use App\Models\Lead;
use App\Models\Reply;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Webklex\IMAP\Facades\Client;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CheckReplies implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $mailbox;

    /**
     * Create a new job instance.
     */
    public function __construct($mailbox)
    {
        $this->mailbox = $mailbox;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $mailbox = $this->mailbox;
        // var_dump($mailbox);

        config(['imap.accounts.default.host' => $mailbox->mail_imap_host ]);
        config(['imap.accounts.default.port' => $mailbox->mail_imap_port ]);
        config(['imap.accounts.default.username' => $mailbox->mail_username ]);
        config(['imap.accounts.default.password' => $mailbox->mail_password ]);
        
        $client = Client::account('default');  
        $client->connect();
        $inboxFolder = $client->getFolder('INBOX');


        
        //$messages = $inboxFolder->messages()->unseen()->limit(10)->get();
        $messages = $inboxFolder->messages()->all()->limit(10)->get();


        if(count($messages) < 1){
            echo 'No new emails found on ' . $mailbox->mail_username;
            return;
        }

        foreach ($messages as $message) {

            $sender = $message->getFrom()[0];

            if ($sender) {
                echo $message->getSubject();
                echo PHP_EOL;
                
                $fromEmail = $sender->mail;
                $emailString = $fromEmail . $sender->personal . $message->getSubject() . $message->getHTMLBody();

                // Create new reply without campaign_id initially
                $newReply = Reply::create([
                    'from_name' => $sender->personal,
                    'from_address' => $sender->mailbox . '@' . $sender->host,
                    'to' => $mailbox->mail_username,
                    'subject' => $message->getSubject(),
                    'body' => $message->getHTMLBody(),
                    'campaign_id' => null // Set to null initially
                ]);

                // Look for matching lead
                $lead = Lead::where('email', $fromEmail)->first();

                if ($lead) {
                    $campaignID = $lead->campaign_id;
                    $lead->replied = 1;
                    $lead->save();

                    // Update reply with campaign_id
                    $newReply->campaign_id = $campaignID;
                    $newReply->save();
                }
            }

            // $message->setFlag(['Seen']);

            //Mode message to the Archive folder
            $message->move('INBOX.Archive');

    
        }


        // Disconnect from the IMAP server
        $client->disconnect();

        
    }



    


}
