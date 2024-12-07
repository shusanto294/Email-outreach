<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Lead;
use App\Models\Email;
use App\Models\Mailbox;
use App\Models\Setting;
use App\Models\Campaign;
use App\Jobs\SendEmailJob;
use Illuminate\Mail\Message;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

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
        $currentTime = Carbon::now();
        $sendEmailsSetting = Setting::where('key', 'send_emails')->first();
        $dailySendingLimit = Setting::where('key', 'daily_sending_limit')->first();
        $emailSentTodaySetting = Setting::firstOrCreate(
            ['key' => 'email_sent_today'],
            ['value' => 0, 'updated_at' => $currentTime]
        );
    
        $sendPerMinuteSetting = Setting::where('key', 'send_per_minute')->first();
        $sendPerMinute = $sendPerMinuteSetting ? (int) $sendPerMinuteSetting->value : 1;
    
        $sendEmailSetting = Setting::where('key', 'send_emails')->first();
        $sendEmail = $sendEmailSetting ? $sendEmailSetting->value : 'off';
    
        // Reset the counter at midnight
        if ($emailSentTodaySetting->updated_at->isToday() == false) {
            $emailSentTodaySetting->value = 0;
            $emailSentTodaySetting->updated_at = $currentTime;
            $emailSentTodaySetting->save();
        }
    
        if ($sendEmail == "on" && $emailSentTodaySetting->value < $dailySendingLimit->value) {
            $scheduledEmail = 0;
            for ($i = 0; $i < $sendPerMinute; $i++) {
                SendEmailJob::dispatch()->onQueue('high');
                $scheduledEmail += 1;
            }
            $emailSentTodaySetting->value += $scheduledEmail;
            $emailSentTodaySetting->save();
    
            echo "Added {$sendPerMinute} emails to queue";
        } else {
            echo "Email sending is off or reached today's limit";
        }
    }
    
    
}
