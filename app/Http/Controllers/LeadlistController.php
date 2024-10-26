<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Email;
use App\Jobs\VerifyList;
use App\Models\Campaign;
use App\Models\Leadlist;
use App\Jobs\VerifyEmail;
use App\Jobs\AddToCampaign;
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
        $verified = Lead::where('leadlist_id', $listId)->where('verified', 1)->count();

        //Fetched website content
        $fetchedWebsiteContent = Lead::where('leadlist_id', $listId)->where('website_content', '!=', '')->where('website_content', '!=', 'n/a')->count();

        //Personalized
        $personalized = Lead::where('leadlist_id', $listId)->where('personalization', '!=', '')->where('personalization', '!=', 'n/a')->count();

        //Added to campaign
        $addedToCampaign = Lead::where('leadlist_id', $listId)->where('campaign_id', '!=', null)->count();

        return view('list-single', [
            'id' => $listId,
            'totalLeads' => $totalLeads,
            'verified' => $verified,
            'fetchedWebsiteContent' => $fetchedWebsiteContent,
            'personalized' => $personalized,
            'addedToCampaign' => $addedToCampaign
        ]);
    }

    public function show_leads($listId)
    {
        return view('leads', [
            'leads' => Lead::where('leadlist_id', $listId)->orderBy('id', 'desc')->paginate(20)
        ]);
    }

    public function show_verified($listId)
    {
        $leads = Lead::where('leadlist_id', $listId)->where('verified', 'true')->orderBy('id', 'desc')->paginate(20);
        return view('leads', [
          'leads' => $leads
        ]);
    }

    public function show_fetched_content($listId)
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

    public function show_personalized($listId)
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

    public function added_to_campaign($listId)
    {
        $leads = Lead::where('leadlist_id', $listId)
                        ->where('campaign_id', '!=', null)
                        ->orderBy('id', 'desc')
                        ->paginate(20);
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


    public function leadlist_leads_change_campaign_id(Request $request)
    {
        $data = $request->all();
    
        $listID = $data['list_id'];
        $campaignID = $data['campaign_id'];
    
        // Update the campaign_id for all matching leads in a single query
        Lead::where('leadlist_id', $listID)
            ->where('verified', 1)
            ->where('personalization', '!=', null)
            ->update(['campaign_id' => $campaignID]);
    
        // Uncomment if you need to dispatch a job afterward
        // AddToCampaign::dispatch($listID, $campaignID);
    
        return redirect()->back()->with('success', 'Leads added to campaign');
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

public function upload_instant_data_scrapper($id){
    return view('upload-instant-data-scrapper', [
        'list_id' => $id
    ]);
}

public function verify_list($id){
    // VerifyList::dispatch($id);
    VerifyList::dispatch($id)->onQueue('medium');
    return redirect()->back()->with('success', 'List verification started');
}


public function fetch_website_content($id){
    FetchWebsiteContentList::dispatch($id)->onQueue('medium');;
    return redirect()->back()->with('success', 'List website content fetching started');
}


public function personalize_list($id){
    PersonalizeLeadList::dispatch($id)->onQueue('low');;
    return redirect()->back()->with('success', 'List personalization started');
}

public function delete($id){
    // Bulk delete leads associated with the given leadlist_id
    Lead::where('leadlist_id', $id)->delete();

    // Delete the lead list
    Leadlist::destroy($id);
    
    return redirect('/lists')->with('warning', 'List deleted successfully!');
}


}
