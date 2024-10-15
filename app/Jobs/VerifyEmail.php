<?php

namespace App\Jobs;

use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\RFCValidation;
use Illuminate\Support\Facades\Log;

class VerifyEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $lead;

    /**
     * Create a new job instance.
     *
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
        $email = $this->lead->email;
        list($user, $domain) = explode("@", $email);
        
        if (checkdnsrr($domain, "MX")) {
            $this->lead->verified = 1;
        } else {
            $this->lead->verified = 0;
        }

        $this->lead->save();
    }

}
