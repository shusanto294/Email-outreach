<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Email;
use App\Jobs\VerifyList;
use App\Models\Campaign;
use App\Models\Leadlist;
use App\Jobs\VerifyEmail;
use Illuminate\Http\Request;
use App\Jobs\PersonalizeLead;
use App\Jobs\FetchWebsiteContent;

use App\Jobs\PersonalizeLeadList;
use Illuminate\Support\Facades\DB;
use OpenAI\Laravel\Facades\OpenAI;
use App\Jobs\FetchWebsiteContentList;
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
        //Total Leads
        $totalLeads = Lead::where('leadlist_id', $listId)->count();

        //Verified
        $verified = Lead::where('leadlist_id', $listId)->where('verified', 'true')->count();

        //Fetched website content
        $fetchedWebsiteContent = Lead::where('leadlist_id', $listId)->where('website_content', '!=', '')->where('website_content', '!=', 'n/a')->count();

        //Personalized
        $personalized = Lead::where('leadlist_id', $listId)->where('personalization', '!=', '')->where('personalization', '!=', 'n/a')->count();

        //Added to campaign
        $addedToCampaign = Lead::where('leadlist_id', $listId)->where('campaign_id', '!=', '')->count();

        return view('list-single', [
            'id' => $listId,
            'totalLeads' => $totalLeads,
            'verified' => $verified,
            'fetchedWebsiteContent' => $fetchedWebsiteContent,
            'personalized' => $personalized,
            'addedToCampaign' => $addedToCampaign
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
                        ->where('personalization', "")
                        ->orderBy('id', 'desc')
                        ->paginate(20);
        return view('leads', [
          'leads' => $leads
        ]);
    }

    public function show_has_ps($listId)
    {
        $leads = Lead::where('leadlist_id', $listId)
                        ->where('personalization', '!=', '')
                        ->where('personalization', '!=', 'n/a')
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

        $leads = Lead::where('leadlist_id', $listID)->where('campaign_id', )->get();
        foreach($leads as $lead){
            $lead->campaign_id = $campaignID;
            $lead->save();
        }
        
        return redirect('/lists')->with('success', 'Leads added to cammpaign successfully');
    }

    public function api_create_list(Request $request){
      $leadlist  = Leadlist::create([
          'name' => $request->name,
      ]);
      echo ("********************************* ");
      echo ($request->name . " - list created");
      echo (" *********************************");
    }

  public function download($id) {
    // Find the LeadList instance by ID
    $list = LeadList::find($id);

    // Fetch leads from the database
    $leads = Lead::where('leadlist_id', $id)->orderBy('id', 'desc')->get();
    
    // Check if any leads were found
    if ($leads->isEmpty()) {
        return response('No leads found', 404);
    }

    // Specify the column names you want to include
    //$selectedColumns = ['name', 'linkedin_profile', 'title', 'company', 'company_website', 'location',  'email', 'personalization', 'subscribe', 'sent', 'opened', 'replied'];
    $selectedColumns = ['name', 'linkedin_profile', 'title', 'company', 'company_website', 'location',  'email', 'subscribe', 'sent', 'opened', 'replied'];

    // Open a temporary file in memory
    $csvFile = fopen('php://temp', 'r+');

    // Add CSV header
    fputcsv($csvFile, $selectedColumns);

    // Add CSV rows with only the selected columns
    foreach ($leads as $lead) {
        // Extract only the selected columns' values
        $rowData = [];
        foreach ($selectedColumns as $column) {
            $rowData[] = $lead->$column;
        }
        fputcsv($csvFile, $rowData);
    }

    // Rewind the file pointer to the beginning of the file
    rewind($csvFile);

    // Read the content of the file
    $csvContent = stream_get_contents($csvFile);

    // Close the file
    fclose($csvFile);

    // Create the response with the CSV content
    return response($csvContent, 200)
        ->header('Content-Type', 'text/csv')
        ->header('Content-Disposition', 'attachment; filename="' . $list->name . '.csv"');
}

public function upload($id){
    return view('upload', [
        'list_id' => $id
    ]);

}

public function verify_list($id){
    VerifyList::dispatch($id);
    return redirect()->back()->with('success', 'List verification started');
}


public function fetch_website_content($id){
    FetchWebsiteContentList::dispatch($id);
    return redirect()->back()->with('success', 'List website content fetching started');
}


public function personalize_list($id){
    PersonalizeLeadList::dispatch($id);
    return redirect()->back()->with('success', 'List personalization started');
}





}
