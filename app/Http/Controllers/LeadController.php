<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Email;
use GuzzleHttp\Client;
use App\Models\Leadlist;
use App\Imports\LeadsImport;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Log;
// use Goutte\Client;
// use Symfony\Component\HttpClient\HttpClient;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\StoreLeadRequest;
use App\Http\Requests\UpdateLeadRequest;
use Symfony\Component\DomCrawler\Crawler;
use GuzzleHttp\Exception\RequestException;

use HTMLPurifier;
use HTMLPurifier_Config;

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

    public function update(Request $request, $id){
        $lead = Lead::find($id);

        $fullName = $lead->name;
        $nameParts = explode(" ", $fullName);
        $firstName = $nameParts[0] ? $nameParts[0] : '';

        $lead->subscribe = $request->subscribe;
        $lead->leadlist_id = $request->leadListID;
        #$lead->personalized_line = $request->personalizedLine;
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
            ->orWhere('email', 'like', '%' . $request->searchText . '%');
        })->orderBy('created_at', 'desc')->paginate(10);
    
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
        $currentDateTime = Carbon::now();
        $formattedDateTime = $currentDateTime->format('d F Y - h:i A');
    
        $leadList = Leadlist::create(['name' => $formattedDateTime]);
    
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

        echo $newEntriesCount . ' - new leads entries added';
        // echo $skippedEntriesCount . ' - leads already exists in the database';
        
    }
    

    

    public function personalize()
    {
        $lead = Lead::where('personalized_line', null)->first();
    
        if (!$lead) {
            return "No lead found.";
        }
    
        $fullName = $lead->name;
        $nameParts = explode(" ", $fullName);
        $firstName = $nameParts[0] ? $nameParts[0] : '';
    
        if (empty($lead->company_website)) {
            // If the company_website is empty, mark personalized_line as 'n/a' and return an appropriate message
            $lead->personalized_line = 'n/a';
            $lead->save();
            return "Company website is empty.";
        }
    
        echo "<a target='_blank' href='$lead->company_website'>$lead->company_website</a>";
        echo '<hr>';
    
        // Specify the headers
        $headers = [
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/107.0.0.0 Safari/537.36',
        ];
    
        // Create a Guzzle client with headers
        $client = new Client(['headers' => $headers]);
    
        try {
            // Make a GET request to the URL with headers
            //$response = $client->get($lead->company_website);
            $response = $client->get('https://sababafest.com/');
    
            // Check if the response status code is 200 (OK)
            if ($response->getStatusCode() == 200) {
                $htmlContent = $response->getBody()->getContents();
                $crawler = new Crawler($htmlContent);
                $paragraphs = $crawler->filter('p')->extract(array('_text'));
                $websiteContent = implode(' ', $paragraphs);
    
                echo $websiteContent;
                echo '<hr>';
    
                if ($websiteContent) {
                    $result = OpenAI::chat()->create([
                        'model' => 'gpt-3.5-turbo',
                        'messages' => [
                            ["role" => "system", "content" => "You are a freelance web developer. You will be provided informations from peoples website and you will write a short sentence of compliment about their work based on their website content. Don't compliment on their webistes design, content, color, user friendlyness ect. Start with I love..."],
                            ["role" => "user", "content" => $websiteContent]
                        ],
                    ]);
    
                    $personalizedLine =  $result->choices[0]->message->content;
    
                    $lead->personalized_line = $personalizedLine;
                    $lead->save();
    
                    echo $personalizedLine;
                } else {
                    $lead->personalized_line = 'n/a';
                    $lead->leadlist_id = 1;
                    $lead->campaign_id = 0;
                    $lead->save();
                }
            } else {
                $lead->personalized_line = 'n/a';
                $lead->leadlist_id = 1;
                $lead->campaign_id = 0;
                $lead->save();
    
                return "Failed to fetch the website content. Status code: " . $response->getStatusCode();
            }
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $lead->personalized_line = 'n/a';
            $lead->leadlist_id = 1;
            $lead->campaign_id = 0;
            $lead->save();
    
            // An exception occurred, indicating that the link is invalid
            return "Failed to fetch the website content. Error: " . $e->getMessage();
        } catch (\Exception $e) {
            $lead->personalized_line = 'n/a';
            $lead->leadlist_id = 1;
            $lead->campaign_id = 0;
            $lead->save();
    
            // Handle other exceptions here if needed
            return "An unexpected error occurred: " . $e->getMessage();
        }
    }
    
        
        


    }
