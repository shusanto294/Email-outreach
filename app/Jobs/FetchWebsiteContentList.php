<?php

namespace App\Jobs;

use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use App\Models\WebsiteContent;


class FetchWebsiteContentList implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $listId;

    /**
     * Create a new job instance.
     *
     * @param int $listId
     * @return void
     */
    public function __construct($listId)
    {
        $this->listId = $listId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Use cursor for better memory efficiency
        $leads = Lead::where('leadlist_id', $this->listId)
                      ->whereNull('added_for_website_scraping')
                      ->cursor();
    
        foreach ($leads as $lead) {
            try {
                $email = $lead->email;
                list($user, $domain) = explode("@", $email);
                $url = 'https://' . $domain;

                FetchWebsiteContent::dispatch($url, $lead);

                // Update the lead to mark it as added to the queue
                $lead->update(['added_for_website_scraping' => true]);

            } catch (\Exception $e) {
                // Add this eror to the log
                // Log::error($e->getMessage());
            }
        }
    }
}
