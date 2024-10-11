<?php

namespace App\Jobs;

use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class AddToCampaign implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $listId;
    public $campaignId;

    /**
     * Create a new job instance.
     */
    public function __construct($listId, $campaignId)
    {
        $this->listId = $listId;
        $this->campaignId = $campaignId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Use cursor for better memory efficiency
        $leads = Lead::where('leadlist_id', $this->listId)
                      ->where('verified', 1)
                      ->where('personalization', "!=" ,  NULL)
                      ->whereNull('campaign_id')
                      ->cursor();
    
        foreach ($leads as $lead) {
            $lead->campaign_id = $this->campaignId;
            $lead->save();
        }
    }
}
