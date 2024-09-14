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
    public function handle()
    {
        $lead = $this->lead;
        $fullName = $lead->name;
        $nameParts = explode(" ", $fullName);
        $firstName = $nameParts[0] ? $nameParts[0] : '';
        $websiteContent = $lead->website_content;

        if (strlen($websiteContent) > 5000) {
            // If yes, take the first 5000 characters
            $websiteContent = substr($websiteContent, 0, 5000);
        }

        $lastApiKeyUsed = Setting::where('key', 'last_api_key_used')->first();

        if(!$lastApiKeyUsed){
            Setting::create(array(
                'key' => 'last_api_key_used',
                'value' => 0
            ));

            return 'Setting added';
        }

        $apiKey = Apikey::where('id', '>', $lastApiKeyUsed->value)->first();

        if(!$apiKey){
            $apiKey = Apikey::orderBy('id', 'asc')->first();
        }

        $lastApiKeyUsed->value = $apiKey->id;
        $lastApiKeyUsed->save();

        #return $apiKey;

        #$openaiApiKey = Setting::where('key', 'openai_api_key')->first();
        if($apiKey){
            config(['openai.api_key' => $apiKey->key ]);
        }else{
            //Add a error log that the api key is not found
            return 'Api key not found';
        }

        
        $result = OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',   
            //Website redesign Services
            // 'messages' => [
            //     ["role" => "system", "content" => "You are Shusanto a freelance web developer. You will be provided information from $lead->company's website and you will write a short email to $firstName who is the owner of $lead->company to offer your website redesign service saying what you love about their company and why you wanted to reach out. Also add how your website redesign service can benifit $lead->company's company. Don't use they/their or gramatical 3rd person to refer to $lead->company or their company, use you/your or gramatical 2nd person instead. Don't write any email subject line, the email should not be more than 100 words. The email signature Should be Shusanto Modak \n Freelance Web Developer"],
            //     ["role" => "user", "content" => $websiteContent]
            // ],
            //Seeking collaboration with website design companies
            'messages' => [
                ["role" => "system", "content" => "You are Shusanto a freelance web developer. You will be provided information from $lead->company's website which is a website design company and you will write a short email for $firstName who is the owner of $lead->company, asking them if there are any opportunity in their company for you as a web developer. You will write what you love about their company, how you can contribute to their company and why you wanted to reach out. You don't use they/their or gramatical 3rd person to refer to $firstName or $lead->company, you use you/your or gramatical 2nd person instead. You will approch them to have a quick chat. You don't write any email subject line, and the email will not be more than 100 words. The email signature Should be Shusanto Modak \n Freelance Web Developer"],
                ["role" => "user", "content" => $websiteContent]
            ]

        ]);

        $input_tocken_before = intval($apiKey->input_tocken);
        $apiKey->input_tocken = $input_tocken_before + $result->usage->promptTokens;

        $output_tocken_before = intval($apiKey->output_tocken);
        $apiKey->output_tocken = $output_tocken_before + $result->usage->completionTokens;

        $apiKey->save();


        $personalizedLine =  nl2br($result->choices[0]->message->content);
        $lead->website_content = $websiteContent;
        $lead->personalized_line = $personalizedLine;
        $lead->save();

        
    }
}
