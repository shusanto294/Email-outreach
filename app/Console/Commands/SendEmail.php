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
        $currentTime = Carbon::now();
        $sendEmailsSetting = Setting::where('key', 'send_emails')->first();
        $dailySendingLimit = Setting::where('key', 'daily_sending_limit')->first();
        $emailSentTodaySetting = Setting::firstOrCreate(
            ['key' => 'email_sent_today'],
            ['value' => 0, 'updated_at' => $currentTime]
        );
    
        // Reset `email_sent_today` at midnight if `updated_at` is null or it's a new day
        if (!$emailSentTodaySetting->updated_at || $emailSentTodaySetting->updated_at->startOfDay() != $currentTime->startOfDay()) {
            $emailSentTodaySetting->value = 0;
            $emailSentTodaySetting->updated_at = $currentTime;
            $emailSentTodaySetting->save();
        }
    
        // Check if we can still send emails within the daily limit
        if ($sendEmailsSetting->value == 'on' && $emailSentTodaySetting->value < $dailySendingLimit->value) {
            
            $lastEmailSentFrom = Setting::where('key', 'last_email_sent_from')->first();
            $mailbox = Mailbox::where('id', '>', $lastEmailSentFrom->value)
                              ->where('status', 'on')->first();
    
            if (!$mailbox) {
                $mailbox = Mailbox::where('status', 'on')->orderBy('id', 'asc')->first();
            }
    
            $lastEmailSentFrom->value = $mailbox->id;
            $lastEmailSentFrom->save();
    
            // Configure mail settings
            config(['mail.mailers.smtp.host' => $mailbox->mail_smtp_host]);
            config(['mail.mailers.smtp.port' => $mailbox->mail_smtp_port]);
            config(['mail.mailers.smtp.username' => $mailbox->mail_username]);
            config(['mail.mailers.smtp.password' => $mailbox->mail_password]);
            config(['mail.from.address' => $mailbox->mail_username]);
            config(['mail.from.name' => $mailbox->mail_from_name]);
    
            // Find the next lead to send
            $lead = Lead::where('campaign_id', '!=', 0)
                        ->where('sent', 0)
                        ->orderBy('id', 'asc')
                        ->first();
    
            if ($lead) {
                $lead->sent = 1;
                $lead->save();
    
                $campaign = Campaign::find($lead->campaign_id);
                $subject = $campaign->subject;
                $body = $campaign->body;
    
                $fullName = $lead->name;
                $nameParts = explode(" ", $fullName);
                $firstName = $nameParts[0] ?? '';
                $company = $lead->company ?? '';
                $personalizedLine = $lead->personalization ?? '';
                $website = $lead->company_website;
    
                $dynamicSubject = str_replace(
                    ["[firstname]", "[company]", "[personalizedLine]", "[website]"], 
                    [$firstName, $company, $personalizedLine, $website], 
                    $subject
                );
                $dynamicBody = str_replace(
                    ["[firstname]", "[company]", "[personalizedLine]", "[website]"], 
                    [$firstName, $company, $personalizedLine, $website], 
                    $body
                );
    
                $uniqueId = uniqid(rand(), true);
                $prefix = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'), 0, 4);
                $finalUniqueId = $prefix . $uniqueId;
    
                $email = Email::create([
                    'subject' => $dynamicSubject,
                    'body' => $dynamicBody,
                    'campaign_id' => $campaign->id,
                    'lead_id' => $lead->id,
                    'uid' => $finalUniqueId
                ]);
    
                $email->sent = $currentTime;
                $email->mailbox_id = $mailbox->id;
                $email->sent_from = $mailbox->mail_username;
                $email->save();
    
                Mail::html($dynamicBody, function (Message $message) use ($lead, $dynamicSubject) {
                    $message->to($lead->email)->subject($dynamicSubject);
                });
    
                // Update the `email_sent_today` count
                $emailSentTodaySetting->value++;
                $emailSentTodaySetting->save();
    
                echo 'Email sent to ' . $lead->email . ' successfully!';
    
            } else {
                echo 'No leads found!';
            }
    
        } else {
            echo 'Daily sending limit reached or emails sending is off.';
        }
    }
}
