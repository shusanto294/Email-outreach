<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Email;
use App\Models\Mailbox;
use App\Models\Setting;
use App\Models\Campaign;
use App\Mail\MyTestEmail;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{   
    public function index(){
        return view('emails', [
            'emails' => DB::table('emails')->orderBy('id', 'desc')->paginate(20)
        ]);
    }

    public function show($id){
        $email = Email::find($id);
        return view('email-single', [
            'email' => $email
        ]);
    }
    public function edit($id){
        $email = Email::find($id);
        return view('edit-email', [
            'email' => $email
        ]);
    }

    public function update(Request $request, $id){
        $email = Email::find($id);
        $email->subject = $request->subject;
        $email->body = $request->body;
        $email->save();
        return redirect()->back();
    }

    public function send(){
        
        $sendEmailsSetting = Setting::where('key', 'send_emails')->first();

        if($sendEmailsSetting->value == 'on'){

            $lastEmailSentFrom = Setting::where('key', 'last_email_sent_from')->first();
            $mailbox = Mailbox::where('id', '>', $lastEmailSentFrom->value)->orderBy('id', 'asc')->first();
            if(!$mailbox){
                $mailbox = Mailbox::orderBy('id', 'asc')->first();
            }

            config(['mail.mailers.smtp.host' => $mailbox->mail_smtp_host ]);
            config(['mail.mailers.smtp.port' => $mailbox->mail_smtp_port ]);
            config(['mail.mailers.smtp.username' => $mailbox->mail_username ]);
            config(['mail.mailers.smtp.password' => $mailbox->mail_password ]);
            config(['mail.from.address' => $mailbox->mail_username ]);
            config(['mail.from.name' => $mailbox->mail_from_name ]);
            
            $email = Email::where('sent', 0)->orderBy('id', 'asc')->first();
            if($email){
                $campaign = Campaign::find($email->campaign_id);
                $lead = Lead::find($email->lead_id);

                $email->sent += 1;
                $email->mailbox_id = $mailbox->id;
                $email->sent_from = $mailbox->mail_username;
                $email->save(); 

                $subject = $email->subject;
                $body = $email->body;
                
                $uniqueId = uniqid(rand(), true);
                $prefix = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'), 0, 4);
                $finalUniqueId = $prefix . $uniqueId;
                $trackingUrl = route('track.email', ['id' => $email->id, 'uid' => $finalUniqueId]);
                $trackingPixel = '<img src="' . $trackingUrl . '" alt="" style="display: none;">';

                $body .= $trackingPixel;

                Mail::html($body, function (Message $message) use ($lead, $campaign, $subject) {
                    $message->to($lead->email)->subject($subject);
                });

                $lastEmailSentFrom->value = $mailbox->id;
                $lastEmailSentFrom->save();

                return $email;
                //return $mailbox;
            }else{
                echo 'No Emails to send'; 
            }

        }else{
            echo 'Emails sendings are off right now';
        }
        

    }

    public function testEmail($mailboxID){
        $mailbox = Mailbox::find($mailboxID);

        config(['mail.mailers.smtp.host' => $mailbox->mail_smtp_host ]);
        config(['mail.mailers.smtp.port' => $mailbox->mail_smtp_port ]);
        config(['mail.mailers.smtp.username' => $mailbox->mail_username ]);
        config(['mail.mailers.smtp.password' => $mailbox->mail_password ]);
        config(['mail.from.address' => $mailbox->mail_username ]);
        config(['mail.from.name' => $mailbox->mail_from_name ]);
        
        $uniqueId = time() . mt_rand(1000, 9999);

        $subject = 'Test email - '. $uniqueId;
        $body = 'This is a test email generated from the outreach softwere to check the delivaribility - '. $uniqueId;

        $testEmailAddress = Setting::where('key', 'send_test_emails_to')->first();
        if($testEmailAddress){
            $sendEmailTo = $testEmailAddress->value;
            Mail::html($body, function (Message $message) use ($sendEmailTo, $subject) {
                $message->to($sendEmailTo)->subject($subject);
            });
            return redirect()->back()->with('success', 'Test email sent successfully');
        }else{
            return redirect()->back()->with('error', 'No email address set to send test emails');
        }
    }

    public function showSent($id){
        $emails = Email::where('campaign_id', $id)->latest()->paginate(20);
        return view('emails', [
            'emails' => $emails
        ]);
    }

    public function showOpened($id){
        $emails = Email::where('campaign_id', $id)->where('opened','>', 0)->latest()->paginate(20);
        return view('emails', [
            'emails' => $emails
        ]);
    }

    public function trackEmail($id){
        $email = Email::find($id);
        if ($email) {
            $email->opened += 1;
            $email->save();
    
            $trackingPixelPath = public_path('images/mypixel.png');
            $trackingPixelContents = file_get_contents($trackingPixelPath);
    
            return response($trackingPixelContents)
                ->header('Content-Type', 'image/png')
                ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');
        }
        // Return something in case the email ID is not found
        abort(404);
    }
    


}
