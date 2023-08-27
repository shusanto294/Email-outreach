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
            //echo $finalUniqueId;

            //$dynamicBody .= '<img src="'.route('track.email',$email->id).'?uid='.$finalUniqueId.'">';
            $dynamicBody .= '<img src="'.route('track.email',$email->id).'">';

            Mail::html($dynamicBody, function (Message $message) use ($lead, $campaign, $dynamicSubject) {
                $message->to($lead->email)->subject($dynamicSubject);
            });

            //return $email;
            echo 'Email sent to : '. $lead->email;
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
        $emails = Email::where('campaign_id', $id)->where('opened', 1)->latest()->paginate(10);
        return view('emails', [
            'emails' => $emails
        ]);
    }

    public function trackEmail($id){
        $email = Email::find($id);
        $email->opened = 1;
        $email->save();

        $trackingPixel = file_get_contents(public_path('images/mypixel.png'));
        return response($trackingPixel)->header('Content-Type', 'image/png');
    }
}
