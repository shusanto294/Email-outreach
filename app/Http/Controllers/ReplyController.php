<?php

namespace App\Http\Controllers;

use App\Models\Reply;
use App\Models\Mailbox;
use App\Models\Setting;
use Webklex\IMAP\Facades\Client;
use App\Http\Requests\StoreReplyRequest;
use App\Http\Requests\UpdateReplyRequest;

class ReplyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
        return view('replies', [
            'replies' => Reply::orderBy('id', 'desc')->paginate(20)
        ]);
    }

    public function checkReplies($mailBoxId){
        $mailbox = Mailbox::find($mailBoxId);

        config(['imap.accounts.default.host' => $mailbox->mail_imap_host ]);
        config(['imap.accounts.default.port' => $mailbox->mail_imap_port ]);
        config(['imap.accounts.default.username' => $mailbox->mail_username ]);
        config(['imap.accounts.default.password' => $mailbox->mail_password ]);
        
        /** @var \Webklex\PHPIMAP\Client $client */
        $client = Client::account('default');  

        // Connect to the IMAP Server
        $client->connect();

        // Get all Mailboxes
        /** @var \Webklex\PHPIMAP\Support\FolderCollection $folders */
        $inboxFolder = $client->getFolder('INBOX');


        // $twoDaysAgo = now()->subDays(2);
        $messages = $inboxFolder->messages()->unseen()->limit(10)->get();
        if(count($messages) < 1){
            echo 'No new emails found !';
            return;
        }

        /** @var \Webklex\PHPIMAP\Message $message */
        foreach ($messages as $message) {
            echo '<div style="background: #ddd; padding: 20px; margin-bottom: 20px;">';
            $sender = $message->getFrom()[0];
            echo '<p>Name : ' . $sender->personal . '</p>';
            echo '<p>Email: ' . $sender->mailbox . '@' . $sender->host . '</p>';
            //echo '<p>From: ' . $message->getFrom()[0]->mailbox . '@' . $message->getFrom()[0]->host . '</p>';
            echo '<div style="margin-bottom: 20px;"><h3>' . $message->getSubject() . '</h3></div>';
            //echo $message->getHTMLBody();
            echo '</div>';

            $substrings = [
                "?utf", 
                "Client configuration settings for",
                "Mail delivery failed",
                "Warning: message",
                "Delivery Status Notification"
            ];
            $shouldStore = true;

            foreach ($substrings as $substring) {
                if (strpos($message->getSubject(), $substring)) {
                    $shouldStore = false;
                    break;
                }
            }
        
            if ($shouldStore) {
                Reply::create([
                    'from_name' => $sender->personal,
                    'from_address' => $sender->mailbox . '@' . $sender->host,
                    'to' => $mailbox->mail_username,
                    'subject' => $message->getSubject(),
                    'body' => $message->getHTMLBody()
                ]);
            }

            $message->setFlag(['Seen']);
        }


        // Disconnect from the IMAP server
        $client->disconnect();

    }

    public function checkRepliesFromAllInbox(){

        $lastReplyCheckedFrom = Setting::where('key', 'last_reply_checked_from')->first();
        $mailbox = Mailbox::where('id', '>', $lastReplyCheckedFrom->value)->orderBy('id', 'asc')->first();
        if(!$mailbox){
            $mailbox = Mailbox::orderBy('id', 'asc')->first();
        }

        //Change the checked inbox information
        $lastReplyCheckedFrom->value = $mailbox->id;
        $lastReplyCheckedFrom->save();

        echo '<p>Checking mailbox : ' . $mailbox->mail_username .'</p>';

        config(['imap.accounts.default.host' => $mailbox->mail_imap_host ]);
        config(['imap.accounts.default.port' => $mailbox->mail_imap_port ]);
        config(['imap.accounts.default.username' => $mailbox->mail_username ]);
        config(['imap.accounts.default.password' => $mailbox->mail_password ]);


       /** @var \Webklex\PHPIMAP\Client $client */
       $client = Client::account('default');  

       // Connect to the IMAP Server
       $client->connect();

       // Get all Mailboxes
       /** @var \Webklex\PHPIMAP\Support\FolderCollection $folders */
       $inboxFolder = $client->getFolder('INBOX');


    //    $twoDaysAgo = now()->subDays(2);
       $messages = $inboxFolder->messages()->unseen()->limit(10)->get();
       if(count($messages) < 1){
           echo 'No new emails found !';
           return;
       }

       /** @var \Webklex\PHPIMAP\Message $message */
       foreach ($messages as $message) {
           echo '<div style="background: #ddd; padding: 20px; margin-bottom: 20px;">';
           $sender = $message->getFrom()[0];
           echo '<p>Name : ' . $sender->personal . '</p>';
           echo '<p>Email: ' . $sender->mailbox . '@' . $sender->host . '</p>';
           //echo '<p>From: ' . $message->getFrom()[0]->mailbox . '@' . $message->getFrom()[0]->host . '</p>';
           echo '<div style="margin-bottom: 20px;"><h3>' . $message->getSubject() . '</h3></div>';
           //echo $message->getHTMLBody();
           echo '</div>';

            $substrings = [
                            "?utf", 
                            "Client configuration settings for",
                            "Mail delivery failed",
                            "Warning: message",
                            "Delivery Status Notification"
                        ];
            $shouldStore = true;

            foreach ($substrings as $substring) {
                if (strpos($message->getSubject(), $substring)) {
                    $shouldStore = false;
                    break;
                }
            }
        
            if ($shouldStore) {
                Reply::create([
                    'from_name' => $sender->personal,
                    'from_address' => $sender->mailbox . '@' . $sender->host,
                    'to' => $mailbox->mail_username,
                    'subject' => $message->getSubject(),
                    'body' => $message->getHTMLBody()
                ]);
            }

           $message->setFlag(['Seen']);
       }


       // Disconnect from the IMAP server
       $client->disconnect();

    }

    public function delete($id){
        $reply = Reply::find($id);
        $reply->delete();
        return redirect('/replies')->with('error', 'Reply deleted successfully');
    }

    public function show($id){
        $reply = Reply::find($id);
        $reply->seen += 1;
        $reply->save();
        return view('reply-single', [
            'reply' => $reply
        ]);
    }
}
