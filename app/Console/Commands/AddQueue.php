<?php

namespace App\Console\Commands;

use App\Models\Lead;
use App\Jobs\VerifyEmail;
use App\Jobs\PersonalizeLead;
use Illuminate\Console\Command;
use App\Jobs\FetchWebsiteContent;

class AddQueue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add:queue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add a job to the queue';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    // public function handle()
    // {
    //     // Use cursor for better memory efficiency
    //     $leads = Lead::where('added_to_queue', null)->cursor();
    
    //     foreach ($leads as $lead) {
    //         try {
    //             VerifyEmail::dispatch($lead);
    //             FetchWebsiteContent::dispatch($lead->company_website, $lead);
    //             PersonalizeLead::dispatch($lead);
                
    //             // Update the lead to mark it as added to the queue
    //             $lead->update(['added_to_queue' => true]);
    
    //             $this->info("Job has been added to the queue for lead {$lead->id}.");
    //         } catch (\Exception $e) {
    //             \Log::error("Failed to process lead {$lead->id}: {$e->getMessage()}");
    //         }
    //     }
        
    //     // Optionally, you can check if you want to sleep
    //     if ($leads->isEmpty()) {
    //         sleep(60); // Sleep for 60 seconds if no leads are found
    //     }
    // }

    public function handle()
    {
        $this->info("Success");
    }
    
}
