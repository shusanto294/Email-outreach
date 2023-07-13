<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
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
            'campaigns' => DB::table('campaigns')->paginate(10)
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
    public function showLeads($id){
        $campaign = Campaign::find($id);
        $leads = $campaign->leads()->paginate(10);;
        return view('leads', [
            'leads' => $leads
        ]);
    }

    //Show sent leads on this campaign
    public function showSent($id){
        $campaign = Campaign::find($id);
        $leads = $campaign->leads()->where('sent', 1)->paginate(10);;
        return view('leads', [
            'leads' => $leads
        ]);
    }

    //Show email opened leads on this campaign
    public function showOpened($id){
        $campaign = Campaign::find($id);
        $leads = $campaign->leads()->where('opened', 1)->paginate(10);;
        return view('leads', [
            'leads' => $leads
        ]);
    }



}
