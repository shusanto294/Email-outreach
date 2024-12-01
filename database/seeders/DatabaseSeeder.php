<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Users

        DB::table('users')->insert([
            'name' => 'Shusanto kumar modak',
            'email' => 'contact@shusanto.com',
            'password' => Hash::make('password'),
        ]);

        // Settings

        DB::table('settings')->insert([
            'key' => 'send_emails',
            'value' => 'off',
        ]);

        DB::table('settings')->insert([
            'key' => 'last_email_sent_from',
            'value' => 0,
        ]);

        DB::table('settings')->insert([
            'key' => 'last_api_key_used',
            'value' => 0,
        ]);

        DB::table('settings')->insert([
            'key' => 'last_reply_checked_from',
            'value' => 0,
        ]);

        DB::table('settings')->insert([
            'key' => 'send_test_emails_to',
            'value' => 'shusanto294@gmail.com',
        ]);

        DB::table('settings')->insert([
            'key' => 'personalization_prompt',
            'value' => "You are Shusanto, a B2B lead generation expert. You will be provided lead details and you will write a short email to the person asking them if they are interested in your services. You provide 2 types of service one is collecting B2B leads and change for the leads and another is Email outreach, resulting in 1-2 daily meetings and charge per booked meetings. The email should not be more than 500 characters. Don't write any subject line, write the body of the email. Don't put any placeholder texts like [Your Contact Information] etc. Start with what you love about them and why you wanted to reach out and then shortly explain how your B2B lead generation service can benefit them. End with Shusanto <br> B2B  lead generation expert.",
        ]);

        DB::table('settings')->insert([
            'key' => 'subject_line_prompt',
            'value' => "You are Shusanto, a B2B lead generation expert. You will be provided the lead details with the email copy and you will write a very personalized email subject line that looks like a genuine email from some friend or customer so the receiver opens the email anyway. The subject line should be between 41 to 50 characters. The subject line should reflect or summarize the actual email and should be also attractive enough for the receiver to open the email. Please don't use any quotation marks or placeholders. The subject line should be something like seeking collaboration with the company and must have a personal touch to it looks very personal.",
        ]);

        DB::table('settings')->insert([
            'key' => 'send_per_minute',
            'value' => 25
        ]);

        DB::table('settings')->insert([
            'key' => 'daily_sending_limit',
            'value' => 1500
        ]);

        DB::table('settings')->insert([
            'key' => 'email_sent_today',
            'value' => 0
        ]);


        // Lists

        DB::table('leadlists')->insert([
            'name' => 'First list'
        ]);


        // Lists

        DB::table('campaigns')->insert([
            'name' => 'Personalized Email Campaign',
            'subject' => '[personalizedSubjectLine]',
            'body' => '[personalization]'
        ]);
        
        
    }
}
