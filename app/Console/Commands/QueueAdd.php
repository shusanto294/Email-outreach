<?php

namespace App\Console\Commands;

use App\Models\Lead;
use App\Jobs\VerifyEmail;
use App\Jobs\PersonalizeLead;
use Illuminate\Console\Command;
use App\Jobs\FetchWebsiteContent;

class QueueAdd extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'queue:add';

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
    public function handle()
    {
        while (true) {
            $leads = Lead::where('added_to_queue', null)->paginate(100);
            $leadsCount = $leads->count();

            foreach ($leads as $lead) {
                $email = $lead->email;
                $domain = substr(strrchr($email, "@"), 1);

                VerifyEmail::dispatch($lead);
                FetchWebsiteContent::dispatch($lead->company_website, $lead);
                PersonalizeLead::dispatch($lead);

                // Update the lead to mark it as added to the queue
                $lead->update(['added_to_queue' => true]);
            }

            if ($leadsCount == 0) {
                // Sleep for a certain period before checking again
                sleep(60); // Sleep for 60 seconds
            }

            $this->info(count($leads) . ' Job has been added to the queue successfully!');
        }

        return 0;
    }
}
