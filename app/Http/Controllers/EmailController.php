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
        $lead = Lead::where('sent', 0)->where('subscribe', 1)->orderBy('id', 'desc')->first();
        if($lead){
            $campaign = Campaign::find($lead->campaign_id);

            $lead->sent = 1;
            $lead->save(); 

            $subject = $campaign->subject;
            $body = $campaign->body;

            $fullName = $lead->name;
            $nameParts = explode(" ", $fullName);

            $firstName = $nameParts[0] ? $nameParts[0] : '';
            $company = $lead->company ? $lead->company : '';
            $personalizedLine = $lead->personalized_line ? $lead->personalized_line : '';

            $dynamicSubject = str_replace(["[firstname]", "[company]", "[personalizedLine]"], [$firstName, $company, $personalizedLine], $subject);
            $dynamicBody = str_replace(["[firstname]", "[company]", "[personalizedLine]"], [$firstName, $company, $personalizedLine], $body);

            //echo '<b>Subject : </b><br>'. $dynamicSubject. '<br><br>';
            //echo '<b>Email : </b>' . $dynamicBody;

            $email = Email::create([
                'subject' => $dynamicSubject,
                'body' => $dynamicBody,
                'campaign_id' => $campaign->id,
                'lead_id' => $lead->id
            ]);

            
            // Generate a unique ID based on the current time and a more random value
            $uniqueId = uniqid(rand(), true);
            // Generate a random prefix to add to the ID for further uniqueness
            $prefix = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'), 0, 4);
            // Combine the prefix and unique ID to create the final unique random ID
            $finalUniqueId = $prefix . $uniqueId;

            $trackingUrl = route('track.email', ['id' => $email->id, 'uid' => $finalUniqueId]);
            $trackingPixel = '<img src="' . $trackingUrl . '" alt="" style="display: none;">';

            $dynamicBody .= $trackingPixel;

            Mail::html($dynamicBody, function (Message $message) use ($lead, $campaign, $dynamicSubject) {
                $message->to($lead->email)->subject($dynamicSubject);
            });

            return $email;
        }else{
            echo 'No lead found'; 
        }

    }

    public function testEmail($campaignID){
        $lead = Lead::where('sent', 0)->where('subscribe', 1)->where('campaign_id', $campaignID)->inRandomOrder()->first();
        if($lead){
            $campaign = Campaign::find($campaignID);

            $subject = $campaign->subject;
            $body = $campaign->body;

            $fullName = $lead->name;
            $nameParts = explode(" ", $fullName);

            $firstName = $nameParts[0] ? $nameParts[0] : '';
            $company = $lead->company ? $lead->company : '';
            $personalizedLine = $lead->personalized_line ? $lead->personalized_line : '';

            $dynamicSubject = str_replace(["[firstname]", "[company]", "[personalizedLine]"], [$firstName, $company, $personalizedLine], $subject);
            $dynamicBody = str_replace(["[firstname]", "[company]", "[personalizedLine]"], [$firstName, $company, $personalizedLine], $body);

            $email = Email::create([
                'subject' => $dynamicSubject,
                'body' => $dynamicBody,
                'campaign_id' => $campaign->id,
                'lead_id' => $lead->id
            ]);

            
            // Generate a unique ID based on the current time and a more random value
            $uniqueId = uniqid(rand(), true);
            // Generate a random prefix to add to the ID for further uniqueness
            $prefix = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'), 0, 4);
            // Combine the prefix and unique ID to create the final unique random ID
            $finalUniqueId = $prefix . $uniqueId;

            $trackingUrl = route('track.email', ['id' => $email->id, 'uid' => $finalUniqueId]);
            $trackingPixel = '<img src="' . $trackingUrl . '" alt="" style="display: none;">';

            $dynamicBody .= $trackingPixel;


            // Mail::html($dynamicBody, function (Message $message) use ($lead, $campaign, $dynamicSubject) {
            //     $message->to('shusanto294@gmail.com')->subject($dynamicSubject);
            // });

            return $email;
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
