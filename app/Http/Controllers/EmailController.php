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
        //return $currentTime;

        $sendEmailsSetting = Setting::where('key', 'send_emails')->first();

        if($sendEmailsSetting->value == 'on'){

            $lastEmailSentFrom = Setting::where('key', 'last_email_sent_from')->first();
            $mailbox = Mailbox::where('id', '>', $lastEmailSentFrom->value)->where('status', 'on')->first();

            if(!$mailbox){
                $mailbox = Mailbox::orderBy('id', 'asc')->first();
            }

            $lastEmailSentFrom->value = $mailbox->id;
            $lastEmailSentFrom->save();

            config(['mail.mailers.smtp.host' => $mailbox->mail_smtp_host ]);
            config(['mail.mailers.smtp.port' => $mailbox->mail_smtp_port ]);
            config(['mail.mailers.smtp.username' => $mailbox->mail_username ]);
            config(['mail.mailers.smtp.password' => $mailbox->mail_password ]);
            config(['mail.from.address' => $mailbox->mail_username ]);
            config(['mail.from.name' => $mailbox->mail_from_name ]);
            
            $email = Email::where('sent', null)->orderBy('id', 'asc')->first();
            if($email){
                $campaign = Campaign::find($email->campaign_id);
                $lead = Lead::find($email->lead_id);

                $email->sent = $currentTime;
                $email->mailbox_id = $mailbox->id;
                $email->sent_from = $mailbox->mail_username;
                $email->save(); 

                $subject = $email->subject;
                $body = $email->body;

                $trackingUrl = route('track.email', ['uid' => $email->uid]);
                $trackingPixel = '<img src="' . $trackingUrl . '" alt="" style="display: none;">';

                $body .= $trackingPixel;

                Mail::html($body, function (Message $message) use ($lead, $campaign, $subject) {
                    $message->to($lead->email)->subject($subject);
                });

                return $email;
                //return $mailbox;
            }else{
                echo 'No Emails to send'; 
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
    


}
