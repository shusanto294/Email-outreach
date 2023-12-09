<?php

namespace App\Http\Controllers;

use App\Models\Lead;
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
        
        $client = Client::account('default');  
        $client->connect();
        $inboxFolder = $client->getFolder('INBOX');


        
        $messages = $inboxFolder->messages()->unseen()->limit(50)->get();
        // $messages = $inboxFolder->messages()->all()->limit(10)->get();

        if(count($messages) < 1){
            echo 'No new emails found !';
            return;
        }

        $keywords = Setting::where('key', 'ignore_replies_keywords')->first();
        $cleanedString = str_replace(', ', ',', $keywords->value);
        $ignores = explode(",", $cleanedString);
        

        foreach ($messages as $message) {
            $sender = $message->getFrom()[0];
            $fromEmail =  $sender->mailbox . '@' . $sender->host;
    
            $shouldStore = true;
            $emailString = $sender->personal. $sender->mailbox. $sender->host. $message->getSubject(). $message->getHTMLBody();
    
            echo '<div style="background: #ddd; padding: 20px; margin-bottom: 20px;">';
            echo '<p>Name : ' . $sender->personal . '</p>';
            echo '<p>Email: ' . $fromEmail. '</p>';
            //echo '<p>From: ' . $message->getFrom()[0]->mailbox . '@' . $message->getFrom()[0]->host . '</p>';
            echo '<div style="margin-bottom: 20px;"><h3>' . $message->getSubject() . '</h3></div>';
    
            foreach ($ignores as $ignore) {
                if (strpos($emailString, $ignore)) {
                    echo '<p style="color: red;">Substring found in the text.</p>';
                    $shouldStore = false;
                    break;
                }
            }
    
            echo '</div>';
    
     
             if ($shouldStore) {
                $lead = Lead::where('email', $fromEmail)->first();
                $campaignID = 0;
    
                if($lead){
                    $campaignID = $lead->campaign_id;
                }
    
                 Reply::create([
                     'from_name' => $sender->personal,
                     'from_address' => $sender->mailbox . '@' . $sender->host,
                     'to' => $mailbox->mail_username,
                     'subject' => $message->getSubject(),
                     'body' => $message->getHTMLBody(),
                     'campaign_id' => $campaignID
                 ]);

                 $lead->replied = 1;
                 $lead->save();
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


       $client = Client::account('default');  
       $client->connect();
       $inboxFolder = $client->getFolder('INBOX');

       $messages = $inboxFolder->messages()->unseen()->limit(50)->get();
       //$messages = $inboxFolder->messages()->all()->limit(10)->get();

       if(count($messages) < 1){
           echo 'No new emails found !';
           return;
       }

       $keywords = Setting::where('key', 'ignore_replies_keywords')->first();
       $cleanedString = str_replace(', ', ',', $keywords->value);
       $ignores = explode(",", $cleanedString);

       foreach ($messages as $message) {
        $sender = $message->getFrom()[0];
        $fromEmail =  $sender->mailbox . '@' . $sender->host;

        $shouldStore = true;
        $emailString = $sender->personal. $sender->mailbox. $sender->host. $message->getSubject(). $message->getHTMLBody();

        echo '<div style="background: #ddd; padding: 20px; margin-bottom: 20px;">';
        echo '<p>Name : ' . $sender->personal . '</p>';
        echo '<p>Email: ' . $fromEmail. '</p>';
        //echo '<p>From: ' . $message->getFrom()[0]->mailbox . '@' . $message->getFrom()[0]->host . '</p>';
        echo '<div style="margin-bottom: 20px;"><h3>' . $message->getSubject() . '</h3></div>';

        foreach ($ignores as $ignore) {
            if (strpos($emailString, $ignore)) {
                echo '<p style="color: red;">Substring found in the text.</p>';
                $shouldStore = false;
                break;
            }
        }

        echo '</div>';

 
         if ($shouldStore) {
            $lead = Lead::where('email', $fromEmail)->first();
            $campaignID = 0;

            if($lead){
                $campaignID = $lead->campaign_id;
            }

             Reply::create([
                 'from_name' => $sender->personal,
                 'from_address' => $sender->mailbox . '@' . $sender->host,
                 'to' => $mailbox->mail_username,
                 'subject' => $message->getSubject(),
                 'body' => $message->getHTMLBody(),
                 'campaign_id' => $campaignID
             ]);

            $lead->replied = 1;
            $lead->save();
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
