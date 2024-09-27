<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\WebsiteContent; // Ensure this import is correct
use Illuminate\Support\Facades\Log; // Import the Log facade


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
                FetchWebsiteContent::dispatch($lead->company_website, $lead);

                // Update the lead to mark it as added to the queue
                $lead->update(['added_for_website_scraping' => true]);

                // Use the Log facade for logging
                Log::info("Lead {$lead->id} has been added for verification.");
            } catch (\Exception $e) {
                // Use the Log facade for error logging
                Log::error("Failed to process lead {$lead->id}: {$e->getMessage()}");
            }
        }
    }
}
