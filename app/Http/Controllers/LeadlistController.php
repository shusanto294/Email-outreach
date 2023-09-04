<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Email;
use App\Models\Campaign;
use App\Models\Leadlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreLeadlistRequest;
use App\Http\Requests\UpdateLeadlistRequest;

class LeadlistController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('lists', [
            'lists' => DB::table('leadlists')->orderBy('id', 'desc')->paginate(100)
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $campaign = Leadlist::create([
            'name' => $request['listName'],
        ]);
        return redirect()->back();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreLeadlistRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreLeadlistRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Leadlist  $leadlist
     * @return \Illuminate\Http\Response
     */
    public function show($listId)
    {
        $leads = Lead::where('leadlist_id', $listId)->orderBy('id', 'desc')->paginate(100);
        return view('leads', [
          'leads' => $leads
        ]);
    }

    public function show_no_ps($listId)
    {
        $leads = Lead::where('leadlist_id', $listId)->where('personalized_line', null)->orderBy('id', 'desc')->paginate(100);
        return view('leads', [
          'leads' => $leads
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Leadlist  $leadlist
     * @return \Illuminate\Http\Response
     */
    public function edit(Leadlist $leadlist)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateLeadlistRequest  $request
     * @param  \App\Models\Leadlist  $leadlist
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateLeadlistRequest $request, Leadlist $leadlist)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Leadlist  $leadlist
     * @return \Illuminate\Http\Response
     */
    public function destroy(Leadlist $leadlist)
    {
        //
    }

    public function add_to_campaign($id){
        $list = Leadlist::find($id);
        return view('add-to-campaign', [
          'list' => $list
        ]);
    }
    public function create_emails(Request $request){
        //return $request->all();
        $campaign = Campaign::find($request->campaign_id);
        $leads = Lead::where('leadlist_id', $request->list_id)->where('subscribe', 1)->get();

        foreach($leads as $lead){
            $existingEmail = Email::where('campaign_id', $campaign->id)->where('lead_id', $lead->id)->first();
            if ($existingEmail === null) {
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
                
            }
            
        }
        return redirect()->back()->with('success', 'Complete');
        
    }
}
