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
use Carbon\Carbon;

class EmailController extends Controller
{   
    public function index(){
        return view('emails', [
            'emails' => DB::table('emails')->orderBy('id', 'desc')->paginate(20)
        ]);
    }

    public function responded(){
        return view('emails', [
            'emails' => DB::table('emails')->where('campaign_id', 0)->orderBy('id', 'desc')->paginate(20)
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
        $currentTime = Carbon::now();
        $sendEmailsSetting = Setting::where('key', 'send_emails')->first();

        if($sendEmailsSetting->value == 'on'){

            $lastEmailSentFrom = Setting::where('key', 'last_email_sent_from')->first();
            $mailbox = Mailbox::where('id', '>', $lastEmailSentFrom->value)->where('status', 'on')->first();

            if(!$mailbox){
                $mailbox = Mailbox::where('status', 'on')->orderBy('id', 'asc')->first();
            }

            $lastEmailSentFrom->value = $mailbox->id;
            $lastEmailSentFrom->save();

            //return $mailbox;

            config(['mail.mailers.smtp.host' => $mailbox->mail_smtp_host ]);
            config(['mail.mailers.smtp.port' => $mailbox->mail_smtp_port ]);
            config(['mail.mailers.smtp.username' => $mailbox->mail_username ]);
            config(['mail.mailers.smtp.password' => $mailbox->mail_password ]);
            config(['mail.from.address' => $mailbox->mail_username ]);
            config(['mail.from.name' => $mailbox->mail_from_name ]);

            $lead = Lead::where('campaign_id', '!=' ,  0)->where('sent', 0)->orderBy('id', 'asc')->first();
            // $email = Email::where('sent', null)->orderBy('id', 'asc')->first();

            if($lead){
                $lead->sent = 1;
                $lead->save();
                
                $campaign = Campaign::find($lead->campaign_id);

                $subject = $campaign->subject;
                $body = $campaign->body;

                $fullName = $lead->name;
                $nameParts = explode(" ", $fullName);
                $firstName = $nameParts[0] ? $nameParts[0] : '';

                $company = $lead->company ? $lead->company : '';
                $personalizedLine = $lead->personalization ? $lead->personalization : '';
                $website = $lead->company_website;

                $dynamicSubject = str_replace(["[firstname]", "[company]", "[personalizedLine]", "[website]"], [$firstName, $company, $personalizedLine, $website], $subject);
                $dynamicBody = str_replace(["[firstname]", "[company]", "[personalizedLine]", "[website]"], [$firstName, $company, $personalizedLine, $website], $body);
                
                $uniqueId = uniqid(rand(), true);
                $prefix = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'), 0, 4);
                $finalUniqueId = $prefix . $uniqueId;

                $email = Email::create([
                    'subject' => $dynamicSubject,
                    'body' => $dynamicBody,
                    'campaign_id' => $campaign->id,
                    'lead_id' => $lead->id,
                    'uid' => $finalUniqueId
                ]);

                $email->sent = $currentTime;
                $email->mailbox_id = $mailbox->id;
                $email->sent_from = $mailbox->mail_username;
                $email->save(); 


                // $trackingUrl = route('track.email', ['uid' => $email->uid]);
                // $trackingPixel = '<img src="' . $trackingUrl . '" alt="" style="display: none;">';
                // $dynamicBody .= $trackingPixel;

                Mail::html($dynamicBody, function (Message $message) use ($lead, $campaign, $dynamicSubject) {
                    $message->to($lead->email)->subject($dynamicSubject);
                });

                echo 'Email sent to ' . $lead->email . ' successfully !';

            }else{
                echo 'No leads found !'; 
            }


        }else{
            echo 'Emails sendings are off right now';
        }

    }

    public function showSent($id){
        $emails = Email::where('campaign_id', $id)->latest()->paginate(20);
        return view('emails', [
            'emails' => $emails
        ]);
    }

    public function showOpened($id){
        $emails = Email::where('campaign_id', $id)->where('opened','>', 0)->orderBy('opened_count', 'desc')->paginate(20);
        return view('emails', [
            'emails' => $emails
        ]);
    }

    public function trackEmail($uid){
        $email = Email::where('uid', $uid)->first();
        $currentTime = Carbon::now();
        if ($email) {
            $email->opened = $currentTime;
            $email->opened_count += 1;
            $email->save();

            $lead = Lead::find($email->lead_id);
            $lead->opened = 1;
            $lead->save();
    
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

    public function delete($id){
        $email = Email::find($id);
        $email->delete();
        return redirect('/emails')->with('success', 'Email deleted successfully');
    }

        public function send_reply(Request $request, $replyID){
        //return $request->all();
        $reply = Reply::find($replyID);
        $mailbox = Mailbox::where('mail_username', $reply->to)->first();
        $lead = Lead::where('email', $reply->from_address)->first();

        config(['mail.mailers.smtp.host' => $mailbox->mail_smtp_host ]);
        config(['mail.mailers.smtp.port' => $mailbox->mail_smtp_port ]);
        config(['mail.mailers.smtp.username' => $mailbox->mail_username ]);
        config(['mail.mailers.smtp.password' => $mailbox->mail_password ]);
        config(['mail.from.address' => $mailbox->mail_username ]);
        config(['mail.from.name' => $mailbox->mail_from_name ]);

        $uniqueId = uniqid(rand(), true);
        $prefix = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'), 0, 4);
        $finalUniqueId = $prefix . $uniqueId;

        $emailSubject = $request->subject;
        $emailBody = $request->body;

        $email = Email::create([
            'subject' => $emailSubject,
            'body' => $emailBody,
            'campaign_id' => 0,
            'lead_id' => 0,
            'uid' => $finalUniqueId
        ]);

        $currentTime = Carbon::now();

        $email->sent = $currentTime;
        $email->mailbox_id = $mailbox->id;
        $email->sent_from = $mailbox->mail_username;
        $email->save(); 

        // $trackingUrl = route('track.email', ['uid' => $email->uid]);
        // $trackingPixel = '<img src="' . $trackingUrl . '" alt="" style="display: none;">';

        // $emailBody .= $trackingPixel;

        Mail::html($emailBody, function (Message $message) use ($reply, $emailSubject) {
            $message->to($reply->from_address)->subject($emailSubject);
            // $message->to("shusanto294@gmail.com")->subject($emailSubject);
        });

        return redirect('/inbox')->with('success', 'Reply sent successfully!');


    }
    


}
