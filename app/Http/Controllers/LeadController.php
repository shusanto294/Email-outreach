<?php

namespace App\Http\Controllers;

use HTMLPurifier;
use App\Models\Lead;
use App\Models\Email;
use App\Models\Reply;
use App\Models\Apikey;
use GuzzleHttp\Client;
use App\Models\Setting;
use App\Models\Leadlist;
use HTMLPurifier_Config;
use App\Imports\LeadsImport;
// use Goutte\Client;
// use Symfony\Component\HttpClient\HttpClient;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\StoreLeadRequest;
use App\Http\Requests\UpdateLeadRequest;
use Symfony\Component\DomCrawler\Crawler;
use GuzzleHttp\Exception\RequestException;

class LeadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function import(Request $request){
        
        $listID = $request->list_id;
        $file = $request->file('file');
        
        Excel::import(new LeadsImport($listID), $file);
        return redirect()->back()->with('success', 'Leads imported successfully !');

    }

    public function index(){
        return view('leads', [
            'leads' => DB::table('leads')->orderBy('id', 'desc')->paginate(20)
        ]);
    }

    public function show($id){
        // Find the lead by its ID
        $lead = Lead::find($id);
    
        if (!$lead) {
            abort(404, 'Lead not found');
        }
    
        // Retrieve emails related to the lead's campaign and lead ID
        $emails = Email::where('lead_id', $lead->id)
                       ->latest()
                       ->get();

        $replies = Reply::where('from_address', $lead->email)
                        ->latest()
                        ->get();
    
        return view('lead-single', [
            'lead' => $lead,
            'emails' => $emails,
            'replies' => $replies
        ]);
    }

    // public function delete(Request $request, $id){
    //     $lead = Lead::find($id);
    //     $lead->delete();
    //     return redirect('/leads')->with('warning', "Lead deleted succesfully !");
    // }

    public function update(Request $request, $id){
        $lead = Lead::find($id);

        $fullName = $lead->name;
        $nameParts = explode(" ", $fullName);
        $firstName = $nameParts[0] ? $nameParts[0] : '';

        $lead->subscribe = $request->subscribe;
        $lead->leadlist_id = $request->leadListID;
        $lead->website_content = $request->websiteContent;
        $lead->personalization = str_replace(["[firstname]", "[company]", "[website]"], [$firstName, $lead->company, $lead->company_website], $request->personalizedLine);


        $lead->save();
        return redirect()->back();
    }

    public function search(Request $request) {
        $leads = Lead::where(function($query) use ($request) {
            $query->where('name', 'like', '%' . $request->searchText . '%')
            ->orWhere('linkedin_profile', 'like', '%' . $request->searchText . '%')
            ->orWhere('title', 'like', '%' . $request->searchText . '%')
            ->orWhere('company', 'like', '%' . $request->searchText . '%')
            ->orWhere('company', 'like', '%' . $request->searchText . '%')
            ->orWhere('company_website', 'like', '%' . $request->searchText . '%')
            ->orWhere('location', 'like', '%' . $request->searchText . '%')
            ->orWhere('email', 'like', '%' . $request->searchText . '%')
            ->orWhere('personalization', 'like', '%' . $request->searchText . '%');
        })->orderBy('created_at', 'desc')->paginate(25);
    
        return view('leads', [
            'leads' => $leads
        ]);
    }

    public function verify_lead(){
        $lead = Lead::where('verified', null)->first();

        if(!$lead){
            echo 'No lead found';
            return;
        }

        $email = $lead->email;
        $domain = substr($email, strpos($email, '@') + 1);
        
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $lead->verified = 'false';
            $lead->save();
            echo $email. 'Email format is not valid <br>';
            return;
        }

        if(!checkdnsrr($domain)) {
            $lead->verified = 'false';
            $lead->save();
            echo $email . ' - Could not be verified *****';
            return;
       }

       echo $email . ' - Verified';
       $lead->verified = 'true';
       $lead->save();
       
    }


    public function personalize()
    {
        
        $lead = Lead::where('personalization', null)->first();

        if (!$lead) {
            return "No lead found.";
        }
    
        $fullName = $lead->name;
        $nameParts = explode(" ", $fullName);
        $firstName = $nameParts[0] ? $nameParts[0] : '';

        // Get the setting for the last used API key
        $lastApiKeyUsed = Setting::where('key', 'last_api_key_used')->first();
    
        $apiKey = Apikey::where('id', '>', $lastApiKeyUsed->value)->first();
            
        if(!$apiKey){
            $apiKey = Apikey::orderBy('id', 'asc')->first();
        }


        $lastApiKeyUsed->value = $apiKey->id;
        $lastApiKeyUsed->save();

        #$openaiApiKey = Setting::where('key', 'openai_api_key')->first();


        if($apiKey){
            config(['openai.api_key' => $apiKey->key ]);
        }else{
            dd('Open AI api key not found');
        }

        $websiteContent = $lead->website_content ? $lead->website_content : '';

        // Shorten the website content to 2000 characters
        $websiteContentShorten = substr($websiteContent, 0, 2000);
        
        // Create a prompt for OpenAI using the lead details
        $leadDetails = "Name: $firstName\nCompany: $lead->company n\Job Title: $lead->title n\Location: $lead->location n\Content: $websiteContentShorten";
        $prompt = [
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ["role" => "system", "content" => "You are Shusanto, a B2B lead generation expert. You will be provided lead details and you will write a short email for the person asking them if they are interested in your service. Start with what you love about them and how you can help them. How you can provide contact information of their targeted customers so they can reachout to them adn grow their business in a cost effecive way. Don't write any email subject line. Don't use any placeholders in the emails so the email can be sent as is."],
                ["role" => "user", "content" => $leadDetails]
            ]
        ];
    
        // Call the OpenAI API
        $result = OpenAI::chat()->create($prompt);

        $input_tocken_before = intval($apiKey->input_tocken);
        $apiKey->input_tocken = $input_tocken_before + $result->usage->promptTokens;

        $output_tocken_before = intval($apiKey->output_tocken);
        $apiKey->output_tocken = $output_tocken_before + $result->usage->completionTokens;

        $apiKey->save();


        $personalizedLine =  nl2br($result->choices[0]->message->content);
        $lead->website_content = $websiteContent;
        $lead->personalization = $personalizedLine;
        $lead->save();

        echo $personalizedLine;

    }

    public function skip_lead_personalization(){
        $lead = Lead::where('website_content', "")->first();
        if($lead){
            $lead->website_content = 'n/a';
            $lead->leadlist_id = 2;
            $lead->campaign_id = 0;
            $lead->save();
        }
        echo 'Skipped - '. $lead->email . ' from personalization';
    }

    public function get_lead_with_no_ps(){
        $lead = Lead::where('personalization', null)->first();
        return response()->json($lead);
    }


    public function uplaod_leads(Request $request)
    {
        $listID = $request->input('list_id');
        $leads = $request->input('data');
    
        $existingLeads = Lead::whereIn('email', array_column($leads, 'email'))->get();
        $existingEmails = $existingLeads->pluck('email')->toArray();
        $newLeads = array_filter($leads, function($lead) use ($existingEmails) {
            return !in_array($lead['email'], $existingEmails);
        });
    
        $newLeads = array_map(function($lead) use ($listID) {
            return [
                'leadlist_id' => $listID,
                'name' => $lead['name'] ?? '',
                'linkedin_profile' => $lead['linkedin_profile'] ?? '',
                'title' => $lead['title'] ?? '',
                'company' => $lead['company'] ?? 'Unknown Company',
                'company_website' => $lead['company_website'] ?? '',
                'location' => $lead['location'] ?? '',
                'email' => $lead['email'],
                // 'personalization' => $lead['personalization'] ?? '',
                'subscribe' => $lead['subscribe'] ?? 1,
                'sent' => $lead['sent'] ?? 0,
                'opened' => $lead['opened'] ?? 0,
                'replied' => $lead['replied'] ?? 0,
                'created_at' => now()
            ];
        }, $newLeads);
    
        Lead::insert($newLeads);
        
        // Return the number of leads that were imported AND the number of leads that were skipped
        return response()->json([
            'imported' => count($newLeads),
            'skipped' => count($leads) - count($newLeads)
        ]);
    }


    public function upload_instant_data_scraper(Request $request)
    {
        $listID = $request->input('list_id');
        $leads = $request->input('data');
    
        // Filter out leads where 'zp_xvo3G 3' (email) is blank or invalid
        $leads = array_filter($leads, function($lead) {
            return !empty($lead['zp_xvo3G 3']) && filter_var($lead['zp_xvo3G 3'], FILTER_VALIDATE_EMAIL);
        });
    
        // Get existing leads to check for duplicates
        $existingLeads = Lead::whereIn('email', array_column($leads, 'zp_xvo3G 3'))->get();
        $existingEmails = $existingLeads->pluck('email')->toArray();
    
        // Filter new leads by excluding any duplicates based on email
        $newLeads = array_filter($leads, function($lead) use ($existingEmails) {
            return !in_array($lead['zp_xvo3G 3'], $existingEmails);
        });
    
        // Map the new leads to the required format
        $newLeads = array_map(function($lead) use ($listID) {
            return [
                'leadlist_id' => $listID,
                'name' => $lead['zp_p2Xqs'] ?? '',
                'linkedin_profile' => $lead['zp_p2Xqs href 5'] ?? '',
                'title' => $lead['zp_xvo3G'] ?? '',
                'company' => $lead['zp_xvo3G 2'] ?? 'Unknown Company',
                'location' => $lead['zp_xvo3G 4'] ?? '',
                'email' => $lead['zp_xvo3G 3'],
                'subscribe' => $lead['subscribe'] ?? 1,
                'sent' => $lead['sent'] ?? 0,
                'opened' => $lead['opened'] ?? 0,
                'replied' => $lead['replied'] ?? 0,
                'created_at' => now()
            ];
        }, $newLeads);
    
        // Insert the new leads into the database
        Lead::insert($newLeads);
    
        // Return the number of leads that were imported AND the number of leads that were skipped
        return response()->json([
            'imported' => count($newLeads),
            'skipped' => count($leads) - count($newLeads)
        ]);
    }
    
    

    }




