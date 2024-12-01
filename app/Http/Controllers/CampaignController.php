<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Email;
use App\Models\Reply;
use App\Models\Mailbox;
use App\Models\Setting;
use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mime\Part\TextPart;
use App\Http\Requests\StoreCampaignRequest;
use App\Http\Requests\UpdateCampaignRequest;

class CampaignController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('campaigns', [
            'campaigns' => DB::table('campaigns')->orderBy('id', 'desc')->paginate(5)
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $campaign = Campaign::create([
            'name' => $request['campaignName'],
            'subject' => $request['subject'],
            'body' => $request['body']
        ]);
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Campaign  $campaign
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $campaign = Campaign::find($id);
        return view('campaign-single', [
            'campaign' => $campaign
        ]);
    }

    /**
     * Update the specified resource.
     *
     * @param  \App\Models\Campaign  $campaign
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, $id)
    {
        $campaign = Campaign::find($id);
        $campaign->name = $request->campaignName;
        $campaign->subject = $request->emailSubject;
        $campaign->body =  $request->emailBody;
        $campaign->save();
        return redirect()->back();
    }

    public function showLeads($id){
        $leads = Lead::where('campaign_id', $id)->orderBy('id', 'desc')->paginate(20);
        return view('leads', [
          'leads' => $leads
        ]);
    }

    public function showSent($id){
        $emails = Email::where('campaign_id', $id)->orderBy('id', 'desc')->paginate(20);
        return view('emails', [
          'emails' => $emails
        ]);
    }

    public function showOpened($id){
        $emails = Email::where('campaign_id', $id)->where('opened_count', '!=' , 0)->orderBy('opened_count', 'desc')->paginate(20);
        return view('emails', [
          'emails' => $emails
        ]);
    }

    public function showReplied($id){
        $replies = Reply::where('campaign_id', $id)->orderBy('id', 'desc')->paginate(20);
        return view('replies', [
            'replies' => $replies
        ]);
    }

    public function showNotOpened($id){
        $emails = Email::where('campaign_id', $id)->where('sent', '!=', null)->where('opened', '=', null)->orderBy('id', 'desc')->paginate(20);
        return view('emails', [
            'emails' => $emails
        ]);
    }

    public function moveNotOpened($id, Request $request){
        $emails = Email::where('campaign_id', $id)->where('sent', '!=', null)->where('opened', '=', null)->orderBy('id', 'desc')->get();
        foreach($emails as $email){
            $leadID = $email->lead_id;
            $lead = Lead::find($leadID);
            $lead->leadlist_id = $request->list_id;
            $lead->save();
        }
        return redirect()->back()->with('success', 'Leads moved successfully');
    }

    public function duplicate($id){
        $campaign = Campaign::find($id);
        Campaign::create([
            'name' => 'Copy of - ' . $campaign->name,
            'subject' => $campaign->subject,
            'body' => $campaign->body,
        ]);

        return redirect()->back()->with('success', 'Campaign duplicated successfully');
    }


    public function delete($id){
        $campaign = Campaign::find($id);
        $campaign->delete();

        return redirect()->back()->with('error', 'Campaign deleted successfully');
    }

    public function send_test_email($campaignID)
    {
        $lastEmailSentFrom = Setting::where('key', 'last_email_sent_from')->first();
        $mailbox = Mailbox::where('id', '>', $lastEmailSentFrom->value)
                          ->where('status', 'on')->first();

        if (!$mailbox) {
            $mailbox = Mailbox::where('status', 'on')->orderBy('id', 'asc')->first();
        }

        $lastEmailSentFrom->value = $mailbox->id;
        $lastEmailSentFrom->save();

        // Configure mail settings
        config(['mail.mailers.smtp.host' => $mailbox->mail_smtp_host]);
        config(['mail.mailers.smtp.port' => $mailbox->mail_smtp_port]);
        config(['mail.mailers.smtp.username' => $mailbox->mail_username]);
        config(['mail.mailers.smtp.password' => $mailbox->mail_password]);
        config(['mail.from.address' => $mailbox->mail_username]);
        config(['mail.from.name' => $mailbox->mail_from_name]);

        // Find the next lead to send
        $lead = Lead::where('campaign_id', '=', $campaignID)
        ->inRandomOrder()
        ->first();


        if ($lead) {
            $lead->sent = 1;
            $lead->save();

            $campaign = Campaign::find($campaignID);
            $subject = $campaign->subject;
            $body = $campaign->body;

            $fullName = $lead->name;
            $nameParts = explode(" ", $fullName);
            $firstName = $nameParts[0] ?? '';
            $company = $lead->company ?? '';
            $personalizedLine = $lead->personalization ?? '';
            $personalizedSubjectLine = $lead->personalizedSubjectLine;

            $calendarLink = "<a href='" . url("calender/{$campaign->id}/{$lead->id}") . "'>Book a Meeting</a>";

            $dynamicSubject = str_replace(
                ["[firstname]", "[company]", "[personalization]", "[personalizedSubjectLine]", "[calenderLink]"], 
                [$firstName, $company, $personalizedLine, $personalizedSubjectLine, $calendarLink], 
                $subject
            );
            $dynamicBody = str_replace(
                ["[firstname]", "[company]", "[personalization]", "[personalizedSubjectLine]", "[calenderLink]"], 
                [$firstName, $company, $personalizedLine, $personalizedSubjectLine, $calendarLink], 
                $body
            );


            $emailAddresses = Setting::where('key', 'send_test_emails_to')->first();
            if($emailAddresses){
                $cleanedString = str_replace(', ', ',', $emailAddresses->value);
                $recipients = explode(",", $cleanedString);
    
                $uniqueId = time() . mt_rand(1000, 9999);
                $subject = 'Test email - '. $uniqueId;
                $body = 'This is a test email generated from the outreach softwere to check the delivaribility - '. $uniqueId;
            
                // Mail::html([], function (Message $message) use ($recipients, $dynamicSubject, $dynamicBody) {
                //     $textPart = new TextPart($dynamicBody, 'utf-8');
                //     $message->to($recipients)
                //             ->subject($dynamicSubject)
                //             ->setBody($textPart);
                // });

                Mail::html($dynamicBody, function (Message $message) use ($recipients, $dynamicSubject) {
                    $message->to($recipients)
                            ->subject($dynamicSubject);
                });
                
        
                return redirect()->back()->with('success', 'Test emails sent successfully');
            }else{
                echo 'No test emails set';
            }


        } else {
            return redirect()->back()->with('error', 'No leads found for test emails');
        }
    }



}
