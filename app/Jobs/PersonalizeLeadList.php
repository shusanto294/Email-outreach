<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Lead;
use Illuminate\Support\Facades\Log;

class PersonalizeLeadList implements ShouldQueue
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
                      ->where('verified', 1)
                      ->whereNull('personalization')
                      ->whereNull('added_for_personalization')
                      ->cursor();
    
        foreach ($leads as $lead) {
            try {
                PersonalizeLead::dispatch($lead);

                // Update the lead to mark it as added to the queue
                $lead->update(['added_for_personalization' => true]);
            } catch (\Exception $e) {
                // Add this eror to the log
                // Log::error($e->getMessage());
            }
        }
    }
}
