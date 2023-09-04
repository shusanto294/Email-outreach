<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Email;
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
            'emails' => DB::table('emails')->orderBy('id', 'desc')->paginate(100)
        ]);
    }

    public function show($id){
        $email = Email::find($id);
        return view('email-single', [
            'email' => $email
        ]);
    }

    public function send(){
        $email = Email::where('sent', 0)->orderBy('id', 'desc')->first();
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
            echo 'No Email to send'; 
        }

    }

    public function testEmail(){
        $email = Email::orderBy('id', 'desc')->first();
        if($email){
            $campaign = Campaign::find($email->campaign_id);
            $lead = Lead::find($email->lead_id);

            $email->sent += 1;
            $email->save(); 

            $subject = $email->subject;
            $body = $email->body;

            Mail::html($body, function (Message $message) use ($lead, $campaign, $subject) {
                $message->to('shusanto294@gmail.com')->subject($subject);
            });

            return $email;
        }else{
            echo 'No Email to send'; 
        }
    }

    public function showSent($id){
        $emails = Email::where('campaign_id', $id)->latest()->paginate(100);
        return view('emails', [
            'emails' => $emails
        ]);
    }

    public function showOpened($id){
        $emails = Email::where('campaign_id', $id)->where('opened','>', 0)->latest()->paginate(100);
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
