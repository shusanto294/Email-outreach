<?php

namespace App\Jobs;

use App\Models\Lead;
use App\Models\Apikey;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class PersonalizeLead implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $lead;

    /**
     * Create a new job instance.
     *
     * @param Lead $lead
     * @return void
     */
    public function __construct(Lead $lead)
    {
        $this->lead = $lead;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    // public function handle()
    // {
    //     $lead = $this->lead;
    //     // $fullName = $lead->name;
    //     // $nameParts = explode(" ", $fullName);
    //     // $firstName = $nameParts[0] ? $nameParts[0] : '';
    //     // $websiteContent = $lead->website_content;


    //     $lastApiKeyUsed = Setting::where('key', 'last_api_key_used')->first();

    //     if(!$lastApiKeyUsed){
    //         Setting::create(array(
    //             'key' => 'last_api_key_used',
    //             'value' => 0
    //         ));

    //         return 'Setting added';
    //     }

    //     $apiKey = Apikey::where('id', '>', $lastApiKeyUsed->value)->first();

    //     if(!$apiKey){
    //         $apiKey = Apikey::orderBy('id', 'asc')->first();
    //     }

    //     $lastApiKeyUsed->value = $apiKey->id;
    //     $lastApiKeyUsed->save();

    //     if($apiKey){
    //         config(['openai.api_key' => $apiKey->key ]);
    //     }else{
    //         //Add a error log that the api key is not found
    //         return 'Api key not found';
    //     }

        
    //     $result = OpenAI::chat()->create([
    //         'model' => 'gpt-3.5-turbo',   
    //         'messages' => [
    //             ["role" => "system", "content" => "You are Shusanto a B2B lead generation expert you will be provided lead details and you will write a short email for the person asking them if they are interested in your service."],
    //             ["role" => "user", "content" => $lead]
    //         ]

    //     ]);


    //     $input_tocken_before = intval($apiKey->input_tocken);
    //     $apiKey->input_tocken = $input_tocken_before + $result->usage->promptTokens;

    //     $output_tocken_before = intval($apiKey->output_tocken);
    //     $apiKey->output_tocken = $output_tocken_before + $result->usage->completionTokens;

    //     $apiKey->save();


    //     $personalization =  nl2br($result->choices[0]->message->content);
    //     // $lead->website_content = $websiteContent;
    //     $lead->personalization = $personalization;
    //     $lead->save();

    //     $lead->personalization = "PersonalizationL ". $websiteContent;
    //     $lead->save();

        
    // }


    public function handle()
    {
        // Extract the lead details from the object
        $lead = $this->lead;
 
        // $lead = Lead::where('personalization', null)->first();

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
            'model' => 'gpt-4o-mini',
            'messages' => [
                ["role" => "system", "content" => "You are Shusanto, a B2B lead generation expert. You will be provided lead details and you will write a short email for the person asking them if they are interested in your service. The email should not be more then 500 charecters. End with Shusanto <br> B2B  lead generation expert. Don't write any subject line just write the email body."],
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

        // echo $personalizedLine;

    }
    
    



}
