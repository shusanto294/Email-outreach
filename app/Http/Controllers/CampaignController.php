<?php

namespace App\Http\Controllers;

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
        $emails = Email::where('campaign_id', $id)->orderBy('id', 'desc')->paginate(10);
        return view('emails', [
          'emails' => $emails
        ]);
    }

    //Show sent leads on this campaign
    public function showSent($id){
        $emails = Email::where('campaign_id', $id)->where('sent', '>', 0)->orderBy('id', 'desc')->paginate(10);
        return view('emails', [
          'emails' => $emails
        ]);
    }

    //Show email opened leads on this campaign
    public function showOpened($id){
        $emails = Email::where('campaign_id', $id)->where('opened', '>', 0)->orderBy('id', 'desc')->paginate(10);
        return view('emails', [
          'emails' => $emails
        ]);
    }



}
