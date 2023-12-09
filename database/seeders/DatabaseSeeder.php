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
            'value' => 'on',
        ]);

        DB::table('settings')->insert([
            'key' => 'last_email_sent_from',
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
            'key' => 'ignore_replies_keywords',
            'value' => 'Automatic reply, Undeliverable, cpanel, Mailer-Daemon, mailer-daemon, warmy.io, CK14RRD, 34N2J7J, HJQJV3V, B2FF62E, TH175DY',
        ]);

        /* Email accounts */

        DB::table('mailboxes')->insert([
            'mail_smtp_host' => 'akdiginova.tech',
            'mail_imap_host' => 'akdiginova.tech',
            'mail_smtp_port' => '465',
            'mail_imap_port' => '993',
            'mail_username' => 'shusanto@akdiginova.tech',
            'mail_password' => 'akdiginova.tech',
            'mail_from_name' => 'Shusanto Modak',
            'status' => 'on'
        ]);

        DB::table('mailboxes')->insert([
            'mail_smtp_host' => 'duidiginova.tech',
            'mail_imap_host' => 'duidiginova.tech',
            'mail_smtp_port' => '465',
            'mail_imap_port' => '993',
            'mail_username' => 'shusanto@duidiginova.tech',
            'mail_password' => 'duidiginova.tech',
            'mail_from_name' => 'Shusanto Modak',
            'status' => 'on'
        ]);

        DB::table('mailboxes')->insert([
            'mail_smtp_host' => 'tindiginova.tech',
            'mail_imap_host' => 'tindiginova.tech',
            'mail_smtp_port' => '465',
            'mail_imap_port' => '993',
            'mail_username' => 'shusanto@tindiginova.tech',
            'mail_password' => 'tindiginova.tech',
            'mail_from_name' => 'Shusanto Modak',
            'status' => 'on'
        ]);

        DB::table('mailboxes')->insert([
            'mail_smtp_host' => 'chardiginova.tech',
            'mail_imap_host' => 'chardiginova.tech',
            'mail_smtp_port' => '465',
            'mail_imap_port' => '993',
            'mail_username' => 'shusanto@chardiginova.tech',
            'mail_password' => 'chardiginova.tech',
            'mail_from_name' => 'Shusanto Modak',
            'status' => 'on'
        ]);

        DB::table('mailboxes')->insert([
            'mail_smtp_host' => 'pachdiginova.tech',
            'mail_imap_host' => 'pachdiginova.tech',
            'mail_smtp_port' => '465',
            'mail_imap_port' => '993',
            'mail_username' => 'shusanto@pachdiginova.tech',
            'mail_password' => 'pachdiginova.tech',
            'mail_from_name' => 'Shusanto Modak',
            'status' => 'on'
        ]);

        DB::table('mailboxes')->insert([
            'mail_smtp_host' => 'choydiginova.tech',
            'mail_imap_host' => 'choydiginova.tech',
            'mail_smtp_port' => '465',
            'mail_imap_port' => '993',
            'mail_username' => 'shusanto@choydiginova.tech',
            'mail_password' => 'choydiginova.tech',
            'mail_from_name' => 'Shusanto Modak',
            'status' => 'on'
        ]);

        DB::table('mailboxes')->insert([
            'mail_smtp_host' => 'satdiginova.tech',
            'mail_imap_host' => 'satdiginova.tech',
            'mail_smtp_port' => '465',
            'mail_imap_port' => '993',
            'mail_username' => 'shusanto@satdiginova.tech',
            'mail_password' => 'satdiginova.tech',
            'mail_from_name' => 'Shusanto Modak',
            'status' => 'on'
        ]);

        DB::table('mailboxes')->insert([
            'mail_smtp_host' => 'aatdiginova.tech',
            'mail_imap_host' => 'aatdiginova.tech',
            'mail_smtp_port' => '465',
            'mail_imap_port' => '993',
            'mail_username' => 'shusanto@aatdiginova.tech',
            'mail_password' => 'aatdiginova.tech',
            'mail_from_name' => 'Shusanto Modak',
            'status' => 'on'
        ]);

        DB::table('mailboxes')->insert([
            'mail_smtp_host' => 'noydiginova.tech',
            'mail_imap_host' => 'noydiginova.tech',
            'mail_smtp_port' => '465',
            'mail_imap_port' => '993',
            'mail_username' => 'shusanto@noydiginova.tech',
            'mail_password' => 'noydiginova.tech',
            'mail_from_name' => 'Shusanto Modak',
            'status' => 'on'
        ]);

        DB::table('mailboxes')->insert([
            'mail_smtp_host' => 'doshdiginova.tech',
            'mail_imap_host' => 'doshdiginova.tech',
            'mail_smtp_port' => '465',
            'mail_imap_port' => '993',
            'mail_username' => 'shusanto@doshdiginova.tech',
            'mail_password' => 'doshdiginova.tech',
            'mail_from_name' => 'Shusanto Modak',
            'status' => 'on'
        ]);

        // Lists

        DB::table('leadlists')->insert([
            'name' => 'Bad Leads'
        ]);

        // Lists

        DB::table('campaigns')->insert([
            'name' => 'First campaign',
            'subject' => 'Hey [firstname], how are you doing now ?',
            'body' => '<p>[firstname], just checking how your company: [company] is doing now.</p>'
        ]);
        
    }
}
