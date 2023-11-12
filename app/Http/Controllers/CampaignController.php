<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Email;
use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            'campaigns' => DB::table('campaigns')->orderBy('id', 'desc')->paginate(10)
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

    //Show leads on this campaign
    public function showEmails($id){
        $emails = Email::where('campaign_id', $id)->orderBy('id', 'desc')->paginate(20);
        return view('emails', [
          'emails' => $emails
        ]);
    }

    //Show sent leads on this campaign
    public function showSent($id){
        $emails = Email::where('campaign_id', $id)->where('sent', '>', 0)->orderBy('id', 'desc')->paginate(20);
        return view('emails', [
          'emails' => $emails
        ]);
    }

    //Show email opened leads on this campaign
    public function showOpened($id){
        $emails = Email::where('campaign_id', $id)->where('opened', '>', 0)->orderBy('id', 'desc')->paginate(20);
        return view('emails', [
          'emails' => $emails
        ]);
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

    public function regerate_emails($id){

        $campaign = Campaign::find($id);
        //$leads = Lead::where('leadlist_id', $request->list_id)->where('subscribe', 1)->get();
        $emails = Email::where('campaign_id', $id)->get();
        $count = 0;

        foreach($emails as $email){
            if($email->sent == null){
                $lead = Lead::find($email->lead_id);

                $subject = $campaign->subject;
                $body = $campaign->body;
    
                $fullName = $lead->name;
                $nameParts = explode(" ", $fullName);
    
                $firstName = $nameParts[0] ? $nameParts[0] : '';
                $company = $lead->company ? $lead->company : '';
                $personalizedLine = $lead->personalized_line ? $lead->personalized_line : '';
                $website = $lead->company_website;
    
                $dynamicSubject = str_replace(["[firstname]", "[company]", "[personalizedLine]", "[website]"], [$firstName, $company, $personalizedLine, $website], $subject);
                $dynamicBody = str_replace(["[firstname]", "[company]", "[personalizedLine]", "[website]"], [$firstName, $company, $personalizedLine, $website], $body);
                
                $uniqueId = uniqid(rand(), true);
                $prefix = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'), 0, 4);
                $finalUniqueId = $prefix . $uniqueId;
    
                $email->subject = $dynamicSubject;
                $email->body = $dynamicBody;
                $email->save();

                $count += 1;
            }
        }
        return redirect()->back()->with('success', $count . ' emails regenerated !');
        
    }

    public function delete($id){
        $campaign = Campaign::find($id);
        $campaign->delete();

        return redirect()->back()->with('error', 'Campaign deleted successfully');
    }


}
