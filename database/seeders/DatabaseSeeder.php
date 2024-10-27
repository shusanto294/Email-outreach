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
            'key' => 'subject_line_prompt',
            'value' => "You are Shusanto, a B2B lead generation expert. You will be provided lead details and you will write a short and attractive email subject line that will make the person curious about what's inside the email and open the email. The subject line should not be more than 100 characters.",
        ]);

        DB::table('settings')->insert([
            'key' => 'personalization_prompt',
            'value' => "You are Shusanto, a B2B lead generation expert. You will be provided lead details and you will write a short email for the person asking them if they are interested in your service. The email should not be more then 500 characters. Don't write any subject line just write the email body. Don't put any placeholder texts like [Your Contact Information] etc. Start with why you love about them and why you wanted to reach out and then shortly explain how your B2B lead generation service can benefit them. End with Shusanto <br> B2B  lead generation expert.",
        ]);

        DB::table('settings')->insert([
            'key' => 'daily_sending_limit',
            'value' => 390
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
            'name' => '[firstname], looking for B2B leads?',
            'subject' => '[firstname], looking for B2B leads?',
            'body' => '[personalizedLine]'
        ]);
        
        
    }
}
