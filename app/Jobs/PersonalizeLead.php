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
    //     $openAiPrompt = Setting::where('key', 'personalization_prompt')->first();
    //     $openAiPromptText = $openAiPrompt->value;

    //     // Extract the lead details from the object
    //     $lead = $this->lead;
 
    //     // $lead = Lead::where('personalization', null)->first();

    //     if (!$lead) {
    //         return "No lead found.";
    //     }
    
    //     $fullName = $lead->name;
    //     $nameParts = explode(" ", $fullName);
    //     $firstName = $nameParts[0] ? $nameParts[0] : '';

    //     // Get the setting for the last used API key
    //     $lastApiKeyUsed = Setting::where('key', 'last_api_key_used')->first();
    
    //     $apiKey = Apikey::where('id', '>', $lastApiKeyUsed->value)->first();
            
    //     if(!$apiKey){
    //         $apiKey = Apikey::orderBy('id', 'asc')->first();
    //     }


    //     $lastApiKeyUsed->value = $apiKey->id;
    //     $lastApiKeyUsed->save();

    //     #$openaiApiKey = Setting::where('key', 'openai_api_key')->first();


    //     if($apiKey){
    //         config(['openai.api_key' => $apiKey->key ]);
    //     }else{
    //         dd('Open AI api key not found');
    //     }

    //     $websiteContent = $lead->website_content ? $lead->website_content : '';

    //     // Shorten the website content to 2000 characters
    //     $websiteContentShorten = substr($websiteContent, 0, 2000);
        
    //     // Create a prompt for OpenAI using the lead details
    //     $leadDetails = "Name: $firstName\nCompany: $lead->company n\Job Title: $lead->title n\Location: $lead->location n\Content: $websiteContentShorten";
    //     $prompt = [
    //         'model' => 'gpt-4o-mini',
    //         'messages' => [
    //             ["role" => "system", "content" => $openAiPromptText],
    //             ["role" => "user", "content" => $leadDetails]
    //         ]
    //     ];
    
    //     // Call the OpenAI API
    //     $result = OpenAI::chat()->create($prompt);

    //     $input_tocken_before = intval($apiKey->input_tocken);
    //     $apiKey->input_tocken = $input_tocken_before + $result->usage->promptTokens;

    //     $output_tocken_before = intval($apiKey->output_tocken);
    //     $apiKey->output_tocken = $output_tocken_before + $result->usage->completionTokens;

    //     $apiKey->save();


    //     $personalizedLine =  nl2br($result->choices[0]->message->content);
    //     $lead->website_content = $websiteContent;
    //     $lead->personalization = $personalizedLine;
    //     $lead->save();

    // }


    public function handle()
    {
        $subject_line_prompt = Setting::where('key', 'subject_line_prompt')->first();
        $subject_line_prompt_text = $subject_line_prompt->value;

        $personalization_prompt = Setting::where('key', 'personalization_prompt')->first();
        $personalization_prompt_text = $personalization_prompt->value;

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
        

        //Personalize subject line

        $prompt = [
            'model' => 'gpt-4o-mini',
            'messages' => [
                ["role" => "system", "content" => $subject_line_prompt_text],
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


        $personalization =  nl2br($result->choices[0]->message->content);
        // $lead->website_content = $websiteContent;
        $lead->personalizedSubjectLine = $personalization;
        $lead->save();


        //Personalization for the email body

        $prompt = [
            'model' => 'gpt-4o-mini',
            'messages' => [
                ["role" => "system", "content" => $personalization_prompt_text],
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


        $personalization =  nl2br($result->choices[0]->message->content);
        // $lead->website_content = $websiteContent;
        $lead->personalization = $personalization;
        $lead->save();

    }



}