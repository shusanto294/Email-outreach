<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Email;
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
        config(['mail.mailers.smtp.host' => Setting::where('key', 'MAIL_HOST')->first()->value ]);
        config(['mail.mailers.smtp.port' => Setting::where('key', 'MAIL_PORT')->first()->value ]);
        config(['mail.mailers.smtp.username' => Setting::where('key', 'MAIL_USERNAME')->first()->value ]);
        config(['mail.mailers.smtp.password' => Setting::where('key', 'MAIL_PASSWORD')->first()->value ]);

        config(['mail.from.address' => Setting::where('key', 'MAIL_USERNAME')->first()->value ]);
        config(['mail.from.name' => Setting::where('key', 'MAIL_FROM_NAME')->first()->value ]);

        $sendEmailsSetting = Setting::where('key', 'send_emails')->first();

        if($sendEmailsSetting->value == 'on'){
            $email = Email::where('sent', 0)->orderBy('id', 'asc')->first();
            if($email){
                $campaign = Campaign::find($email->campaign_id);
                $lead = Lead::find($email->lead_id);

                $email->sent += 1;
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

                return $email;
            }else{
                echo 'No Emails to send'; 
            }

        }else{
            echo 'Emails sendings are off right now';
        }

    }

    public function testEmail(){
        config(['mail.mailers.smtp.host' => Setting::where('key', 'MAIL_HOST')->first()->value ]);
        config(['mail.mailers.smtp.port' => Setting::where('key', 'MAIL_PORT')->first()->value ]);
        config(['mail.mailers.smtp.username' => Setting::where('key', 'MAIL_USERNAME')->first()->value ]);
        config(['mail.mailers.smtp.password' => Setting::where('key', 'MAIL_PASSWORD')->first()->value ]);

        config(['mail.from.address' => Setting::where('key', 'MAIL_USERNAME')->first()->value ]);
        config(['mail.from.name' => Setting::where('key', 'MAIL_FROM_NAME')->first()->value ]);
        
        $uniqueId = time() . mt_rand(1000, 9999);

        $subject = 'Test email - '. $uniqueId;
        $body = 'This is a test email generated from the outreach softwere to check the delivaribility - '. $uniqueId;

        $testEmailAddress = Setting::where('key', 'TEST_EMAILS_TO')->first();
        if($testEmailAddress){
            $sendEmailTo = $testEmailAddress->value;
            Mail::html($body, function (Message $message) use ($sendEmailTo, $subject) {
                $message->to($sendEmailTo)->subject($subject);
            });
            return redirect()->back()->with('message', 'Test email sent successfully');
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
