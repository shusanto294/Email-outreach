<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Lead;
use App\Jobs\VerifyEmail;
use Illuminate\Support\Facades\Log;

class VerifyList implements ShouldQueue
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
                      ->whereNull('verified')
                      ->cursor();
    
        foreach ($leads as $lead) {
            try {
                VerifyEmail::dispatch($lead)->onQueue('medium');;

                // Update the lead to mark it as added to the queue
                $lead->update(['added_for_verification' => true]);
            } catch (\Exception $e) {
                // Use the Log facade for error logging
                //Do nothing for now
            }
        }
    }
}
