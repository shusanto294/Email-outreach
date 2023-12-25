<?php

namespace App\Http\Controllers;

use HTMLPurifier;
use App\Models\Lead;
use App\Models\Email;
use GuzzleHttp\Client;
use App\Models\Setting;
use App\Models\Leadlist;
use HTMLPurifier_Config;
use App\Imports\LeadsImport;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
// use Goutte\Client;
// use Symfony\Component\HttpClient\HttpClient;
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
    
        return view('lead-single', [
            'lead' => $lead,
            'emails' => $emails
        ]);
    }

    public function delete(Request $request, $id){
        $lead = Lead::find($id);
        $lead->delete();
        return redirect('/leads')->with('warning', "Lead deleted succesfully !");
    }

    public function update(Request $request, $id){
        $lead = Lead::find($id);

        $fullName = $lead->name;
        $nameParts = explode(" ", $fullName);
        $firstName = $nameParts[0] ? $nameParts[0] : '';

        $lead->subscribe = $request->subscribe;
        $lead->leadlist_id = $request->leadListID;
        $lead->website_content = $request->websiteContent;
        $lead->personalized_line = str_replace(["[firstname]", "[company]", "[website]"], [$firstName, $lead->company, $lead->company_website], $request->personalizedLine);


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
            ->orWhere('personalized_line', 'like', '%' . $request->searchText . '%');
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

    public function upload_leads(Request $request){
        // $currentDateTime = Carbon::now();
        // $formattedDateTime = $currentDateTime->format('d F Y - h:i A');
    
        $leadList = Leadlist::orderBy('id', 'desc')->first();
    
        $data = $request->all();
        
        $newEntriesCount = 0;
        $skippedEntriesCount = 0;
    
        foreach($data as $lead){
            $email = $lead[6];
    
            // Check if the email already exists in the database
            $existingLead = Lead::where('email', $email)->first();
    
            if (!$existingLead) {
                // Email doesn't exist, create a new entry
                Lead::create([
                    'name' => $lead[0],
                    'linkedin_profile' => $lead[1],
                    'title' => $lead[2],
                    'company' => $lead[3],
                    'company_website' => $lead[4],
                    'location' => $lead[5],
                    'email' => $email,
                    'leadlist_id' => $leadList->id
                ]);
                
                $newEntriesCount++;
            } else {
                // Email already exists, skip the entry
                $skippedEntriesCount++;
            }
        }

        echo $newEntriesCount . ' - new leads added';
        // echo $skippedEntriesCount . ' - leads already exists in the database';
        
    }
    

    public function personalize()
    {
        
        $lead = Lead::where('website_content', "")->first();
    
        if (!$lead) {
            return "No lead found.";
        }

        //echo "<a href='$lead->company_website' target='blank'>$lead->company_website</a><hr>";
    
        $fullName = $lead->name;
        $nameParts = explode(" ", $fullName);
        $firstName = $nameParts[0] ? $nameParts[0] : '';
    
        if (empty($lead->company_website)) {
            $lead->website_content = 'n/a';
            $lead->save();
            return "Company website is empty.";
        }
    
        // Specify the headers
        $headers = [
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/107.0.0.0 Safari/537.36',
        ];
    
        // Create a Guzzle client with headers
        $client = new Client(['headers' => $headers]);
    
        try {
            // Make a GET request to the URL with headers
            $response = $client->get($lead->company_website, ['timeout' => 30]);
    
            // Check if the response status code is 200 (OK)
            if ($response->getStatusCode() == 200) {

                $htmlContent = $response->getBody()->getContents();
                $crawler = new Crawler($htmlContent);
                
                // Remove script and style tags along with their content
                $crawler->filter('script, style')->each(function (Crawler $node) {
                    $node->getNode(0)->parentNode->removeChild($node->getNode(0));
                });

                $websiteContent = '';
                

                try {
                    $visibleText = $crawler->filter('body')->text();
                    $websiteContent = $visibleText;
        
                    // Optionally, you can use the trim() function to remove leading and trailing whitespaces
                    $visibleText = trim($visibleText);
                } catch (\Exception $ex) {
                    // Handle the case when no visible text is found
                    $lead->website_content = 'n/a';
                    $lead->leadlist_id = 1;
                    $lead->campaign_id = 0;
                    $lead->save();
        
                    echo 'No visible text found';
                    return;
                }
                
  
                if ($websiteContent != '') {
                    if (strlen($websiteContent) > 10000) {
                        // If yes, take the first 1000 characters
                        $websiteContent = substr($websiteContent, 0, 10000);
                    }

                    //echo "<p>$websiteContent</p><hr>";

                    $openaiApiKey = Setting::where('key', 'openai_api_key')->first();
                    if($openaiApiKey){
                        config(['openai.api_key' => $openaiApiKey->value ]);
                    }else{
                        dd('Open AI api key not found');
                    }
                    
                    $result = OpenAI::chat()->create([
                        //'model' => 'gpt-3.5-turbo',   
                        'model' => 'gpt-3.5-turbo-1106',
                        'messages' => [
                            ["role" => "system", "content" => "You are Shusanto a freelance web developer. You will be provided information from $lead->company's website and you will write a short email to $firstName who is the owner of $lead->company to offer your website redesign service saying what you love about their company and why you wanted to reach out. Also add how your website redesign service can benifit $lead->company's company. Don't use they/their or gramatical 3rd person to refer to $lead->company or their company, use you/your or gramatical 2nd person instead. Don't write any email subject line, the email should not be more than 100 words. The email signature Should be Shusano Modak \n Freelance Web Developer"],
                            ["role" => "user", "content" => $websiteContent]
                        ],

                        //'max_tokens' => 100,

                    ]);

                    $personalizedLine =  nl2br($result->choices[0]->message->content);
                    $lead->website_content = $websiteContent;
                    $lead->personalized_line = $personalizedLine;
                    $lead->save();
    
                    echo $personalizedLine;

                    echo '<hr>Usage<hr>';
                    echo 'Prompt tokens :'. $result->usage->promptTokens .'<br>';
                    echo 'Completion tokens :'. $result->usage->completionTokens .'<br>';
                    echo 'Total tokens :'. $result->usage->totalTokens;
                }else{
                    echo 'No visible text found';
                    $lead->website_content = 'n/a';
                    $lead->leadlist_id = 1;
                    $lead->campaign_id = 0;
                    $lead->save();
                }

            } else {
                $lead->website_content = 'n/a';
                $lead->leadlist_id = 1;
                $lead->campaign_id = 0;
                $lead->save();
                echo "Failed to fetch the website content. Status code: " . $response->getStatusCode();
            }
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $lead->website_content = 'n/a';
            $lead->leadlist_id = 1;
            $lead->campaign_id = 0;
            $lead->save();
        
            if ($e->hasResponse()) {
                echo "Failed to fetch the website content. Status code: " . $e->getResponse()->getStatusCode();
            } else {
                echo "Failed to fetch the website content. Error: " . $e->getMessage();
            }
        } catch (\GuzzleHttp\Exception\ConnectException $e) {
            $lead->website_content = 'n/a';
            $lead->leadlist_id = 1;
            $lead->campaign_id = 0;
            $lead->save();
            echo "Connection failed. Error: " . $e->getMessage();
        } catch (\GuzzleHttp\Exception\TransferException $e) {
            $lead->website_content = 'n/a';
            $lead->leadlist_id = 1;
            $lead->campaign_id = 0;
            $lead->save();
            echo "Transfer error. Error: " . $e->getMessage();
        }
    }

    public function get_lead_with_no_ps(){
        $lead = Lead::where('personalized_line', null)->first();
        return response()->json($lead);
    }
    
    

    }
