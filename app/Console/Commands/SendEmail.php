<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Lead;
use App\Models\Mailbox;
use App\Models\Setting;
use App\Models\Campaign;
use App\Models\Email;
use Illuminate\Support\Facades\Mail;
use Illuminate\Console\Command;
use Illuminate\Mail\Message;

class SendEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $sendPerMinuteSetting = Setting::where('key', 'send_per_minute')->first();
        $sendPerMinute = $sendPerMinuteSetting ? (int) $sendPerMinuteSetting->value : 1;
    
        for ($i = 0; $i < $sendPerMinute; $i++) {
            SendEmail::dispatch()->onQueue('high');
        }
    
        echo "Added {$sendPerMinute} emails to queue";
    }
    
}
