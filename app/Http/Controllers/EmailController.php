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
            'emails' => DB::table('emails')->orderBy('id', 'desc')->paginate(10)
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

    public function testEmail($campaignID){
        $lead = Lead::inRandomOrder()->first();
        $campaign = Campaign::find($campaignID);
        if($lead){

            $subject = $campaign->subject;
            $body = $campaign->body;

            $fullName = $lead->name;
            $nameParts = explode(" ", $fullName);

            $firstName = $nameParts[0] ? $nameParts[0] : '';
            $company = $lead->company ? $lead->company : '';
            $personalizedLine = $lead->personalized_line ? $lead->personalized_line : '';

            $dynamicSubject = str_replace(["[firstname]", "[company]", "[personalizedLine]"], [$firstName, $company, $personalizedLine], $subject);
            $dynamicBody = str_replace(["[firstname]", "[company]", "[personalizedLine]"], [$firstName, $company, $personalizedLine], $body);

            
            Mail::html($dynamicBody, function (Message $message) use ($lead, $campaign, $dynamicSubject) {
                $message->to('shusanto294@gmail.com')->subject($dynamicSubject);
            });

            return redirect()->back()->with('success', 'Test email sent successfully');
        }else{
            echo 'No lead found'; 
        }
    }

    public function showSent($id){
        $emails = Email::where('campaign_id', $id)->latest()->paginate(10);
        return view('emails', [
            'emails' => $emails
        ]);
    }

    public function showOpened($id){
        $emails = Email::where('campaign_id', $id)->where('opened','>', 0)->latest()->paginate(10);
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
