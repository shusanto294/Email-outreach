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
use OpenAI\Laravel\Facades\OpenAI;

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
            'lists' => Leadlist::orderBy('id', 'desc')->paginate(10)
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
        $leads = Lead::where('leadlist_id', $listId)->orderBy('id', 'desc')->paginate(20);
        return view('leads', [
          'leads' => $leads
        ]);
    }

    public function show_no_ws($listId)
    {
        $leads = Lead::where('leadlist_id', $listId)->where('website_content', "")->where('website_content', "n/a")->orderBy('id', 'desc')->paginate(20);
        return view('leads', [
          'leads' => $leads
        ]);
    }

    public function show_has_ws($listId)
    {
        $websiteContentFilters = ['', 'n/a'];
        $leads = Lead::where('leadlist_id', $listId)
                        ->whereNotIn('website_content', $websiteContentFilters)
                        ->orderBy('id', 'desc')
                        ->paginate(20);
        return view('leads', [
          'leads' => $leads
        ]);
    }

    public function show_no_ps($listId)
    {
        $websiteContentFilters = ['', 'n/a'];
        $leads = Lead::where('leadlist_id', $listId)
                        ->where('personalized_line', "")
                        ->orderBy('id', 'desc')
                        ->paginate(20);
        return view('leads', [
          'leads' => $leads
        ]);
    }

    public function show_has_ps($listId)
    {
        $leads = Lead::where('leadlist_id', $listId)
                        ->where('personalized_line', '!=', '')
                        ->where('personalized_line', '!=', 'n/a')
                        ->orderBy('id', 'desc')
                        ->paginate(20);
        return view('leads', [
          'leads' => $leads
        ]);
    }

    public function show_verified($listId)
    {
        $leads = Lead::where('leadlist_id', $listId)->where('verified', 'true')->orderBy('id', 'desc')->paginate(20);
        return view('leads', [
          'leads' => $leads
        ]);
    }

    public function show_not_verified($listId)
    {
        $leads = Lead::where('leadlist_id', $listId)->where('verified', '!=' , 'true')->orderBy('id', 'desc')->paginate(20);
        return view('leads', [
          'leads' => $leads
        ]);
    }

    public function add_to_campaign($id){
        $list = Leadlist::find($id);
        return view('add-to-campaign', [
          'list' => $list
        ]);
    }
    public function leadlist_leads_change_campaign_id(Request $request){
        $data = $request->all();
        $listID = $data['list_id'];
        $campaignID = $data['campaign_id'];

        $leads = Lead::where('leadlist_id', $listID)->get();
        foreach($leads as $lead){
            $lead->campaign_id = $campaignID;
            $lead->save();
        }
        
        return redirect('/lists')->with('success', 'Leads added to cammpaign successfully');
    }


}
