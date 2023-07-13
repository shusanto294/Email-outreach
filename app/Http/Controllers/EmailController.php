<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Campaign;
use App\Mail\MyTestEmail;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{
    public function send(){
        $lead = Lead::where('sent', 0)->where('subscribe', 1)->first();
        $campaign = Campaign::find($lead->campaign_id);

        $lead->sent = 1;
        $lead->save();

        $subject = $campaign->subject;
        $body = $campaign->body;

        $fullName = $lead->name;
        $nameParts = explode(" ", $fullName);
        $firstName = $nameParts[0];

        $dynamicSubject = str_replace(["[firstname]", "[company]"], [$firstName, $lead->company], $subject);
        $dynamicBody = str_replace(["[firstname]", "[company]"], [$firstName, $lead->company], $body);

        $dynamicBody .= '<img src="'.route('track.email',$lead->id).'?id='.$lead->id.'">';

        Mail::html($dynamicBody, function (Message $message) use ($lead, $campaign, $dynamicSubject) {
            $message->to($lead->email)->subject($dynamicSubject);
        });

        echo 'Email sent to - '. $lead->id.' - '.$lead->email; 

    }
    public function trackEmail($id){
        $lead = Lead::find($id);
        $lead->opened = 1;
        $lead->save();

        $trackingPixel = file_get_contents(public_path('images/mypixel.png'));
        return response($trackingPixel)->header('Content-Type', 'image/png');
    }
}
