<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Carbon\Carbon;
use App\Models\Lead;
use App\Models\Mailbox;
use App\Models\Setting;
use App\Models\Campaign;
use App\Models\Email;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $sendPerMinute = Setting::where('key', 'send_per_minute')->first();

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
                $personalizedSubjectLine = $lead->personalizedSubjectLine;
    
                $dynamicSubject = str_replace(
                    ["[firstname]", "[company]", "[personalization]", "[personalizedSubjectLine]"], 
                    [$firstName, $company, $personalizedLine, $personalizedSubjectLine], 
                    $subject
                );
                $dynamicBody = str_replace(
                    ["[firstname]", "[company]", "[personalization]", "[personalizedSubjectLine]"], 
                    [$firstName, $company, $personalizedLine, $personalizedSubjectLine], 
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
                    'sent_to'=> $lead->email,
                    'reciver_name' => $lead->name,
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
