<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Email;
use App\Models\Reply;
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

    // public function show_sent($id){
    //     $leads = Lead::where('campaign_id', $id)->where('sent', 1)->orderBy('id', 'desc')->paginate(20);
    //     return view('leads', [
    //         'leads' => $leads
    //     ]);
    // }

    // public function show_opened($id){
    //     $leads = Lead::where('campaign_id', $id)->where('opened', 1)->orderBy('id', 'desc')->paginate(20);
    //     return view('leads', [
    //         'leads' => $leads
    //     ]);
    // }

    // public function show_replied($id){
    //     $leads = Lead::where('campaign_id', $id)->where('replied', 1)->orderBy('id', 'desc')->paginate(20);
    //     return view('leads', [
    //         'leads' => $leads
    //     ]);
    // }


}
