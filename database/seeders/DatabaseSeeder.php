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
            'key' => 'ignore_replies_keywords',
            'value' => 'Automatic reply, Undeliverable, cpanel, Mailer-Daemon, mailer-daemon, warmy.io, CK14RRD, 34N2J7J, HJQJV3V, B2FF62E, TH175DY',
        ]);

        /* Email accounts */

        DB::table('mailboxes')->insert([
            'mail_smtp_host' => 'akdiginova.tech',
            'mail_imap_host' => 'akdiginova.tech',
            'mail_smtp_port' => '465',
            'mail_imap_port' => '993',
            'mail_username' => 'sm@akdiginova.tech',
            'mail_password' => 'akdiginova.tech',
            'mail_from_name' => 'Shusanto Modak',
            'status' => 'on'
        ]);

        DB::table('mailboxes')->insert([
            'mail_smtp_host' => 'duidiginova.tech',
            'mail_imap_host' => 'duidiginova.tech',
            'mail_smtp_port' => '465',
            'mail_imap_port' => '993',
            'mail_username' => 'sm@duidiginova.tech',
            'mail_password' => 'duidiginova.tech',
            'mail_from_name' => 'Shusanto Modak',
            'status' => 'on'
        ]);

        DB::table('mailboxes')->insert([
            'mail_smtp_host' => 'tindiginova.tech',
            'mail_imap_host' => 'tindiginova.tech',
            'mail_smtp_port' => '465',
            'mail_imap_port' => '993',
            'mail_username' => 'sm@tindiginova.tech',
            'mail_password' => 'tindiginova.tech',
            'mail_from_name' => 'Shusanto Modak',
            'status' => 'on'
        ]);

        DB::table('mailboxes')->insert([
            'mail_smtp_host' => 'chardiginova.tech',
            'mail_imap_host' => 'chardiginova.tech',
            'mail_smtp_port' => '465',
            'mail_imap_port' => '993',
            'mail_username' => 'sm@chardiginova.tech',
            'mail_password' => 'chardiginova.tech',
            'mail_from_name' => 'Shusanto Modak',
            'status' => 'on'
        ]);

        DB::table('mailboxes')->insert([
            'mail_smtp_host' => 'pachdiginova.tech',
            'mail_imap_host' => 'pachdiginova.tech',
            'mail_smtp_port' => '465',
            'mail_imap_port' => '993',
            'mail_username' => 'sm@pachdiginova.tech',
            'mail_password' => 'pachdiginova.tech',
            'mail_from_name' => 'Shusanto Modak',
            'status' => 'on'
        ]);

        DB::table('mailboxes')->insert([
            'mail_smtp_host' => 'choydiginova.tech',
            'mail_imap_host' => 'choydiginova.tech',
            'mail_smtp_port' => '465',
            'mail_imap_port' => '993',
            'mail_username' => 'sm@choydiginova.tech',
            'mail_password' => 'choydiginova.tech',
            'mail_from_name' => 'Shusanto Modak',
            'status' => 'on'
        ]);

        DB::table('mailboxes')->insert([
            'mail_smtp_host' => 'satdiginova.tech',
            'mail_imap_host' => 'satdiginova.tech',
            'mail_smtp_port' => '465',
            'mail_imap_port' => '993',
            'mail_username' => 'sm@satdiginova.tech',
            'mail_password' => 'satdiginova.tech',
            'mail_from_name' => 'Shusanto Modak',
            'status' => 'on'
        ]);

        DB::table('mailboxes')->insert([
            'mail_smtp_host' => 'aatdiginova.tech',
            'mail_imap_host' => 'aatdiginova.tech',
            'mail_smtp_port' => '465',
            'mail_imap_port' => '993',
            'mail_username' => 'sm@aatdiginova.tech',
            'mail_password' => 'aatdiginova.tech',
            'mail_from_name' => 'Shusanto Modak',
            'status' => 'on'
        ]);

        DB::table('mailboxes')->insert([
            'mail_smtp_host' => 'noydiginova.tech',
            'mail_imap_host' => 'noydiginova.tech',
            'mail_smtp_port' => '465',
            'mail_imap_port' => '993',
            'mail_username' => 'sm@noydiginova.tech',
            'mail_password' => 'noydiginova.tech',
            'mail_from_name' => 'Shusanto Modak',
            'status' => 'on'
        ]);

        DB::table('mailboxes')->insert([
            'mail_smtp_host' => 'doshdiginova.tech',
            'mail_imap_host' => 'doshdiginova.tech',
            'mail_smtp_port' => '465',
            'mail_imap_port' => '993',
            'mail_username' => 'sm@doshdiginova.tech',
            'mail_password' => 'doshdiginova.tech',
            'mail_from_name' => 'Shusanto Modak',
            'status' => 'on'
        ]);

        DB::table('mailboxes')->insert([
            'mail_smtp_host' => 'premium143.web-hosting.com',
            'mail_imap_host' => 'premium143.web-hosting.com',
            'mail_smtp_port' => '465',
            'mail_imap_port' => '993',
            'mail_username' => 'sm@agarodiginova.tech',
            'mail_password' => 'agarodiginova.tech',
            'mail_from_name' => 'Shusanto Modak',
            'status' => 'on'
        ]);

        DB::table('mailboxes')->insert([
            'mail_smtp_host' => 'premium143.web-hosting.com',
            'mail_imap_host' => 'premium143.web-hosting.com',
            'mail_smtp_port' => '465',
            'mail_imap_port' => '993',
            'mail_username' => 'sm@barodiginova.tech',
            'mail_password' => 'barodiginova.tech',
            'mail_from_name' => 'Shusanto Modak',
            'status' => 'on'
        ]);

        DB::table('mailboxes')->insert([
            'mail_smtp_host' => 'premium143.web-hosting.com',
            'mail_imap_host' => 'premium143.web-hosting.com',
            'mail_smtp_port' => '465',
            'mail_imap_port' => '993',
            'mail_username' => 'sm@terodiginova.tech',
            'mail_password' => 'terodiginova.tech',
            'mail_from_name' => 'Shusanto Modak',
            'status' => 'on'
        ]);

        DB::table('mailboxes')->insert([
            'mail_smtp_host' => 'premium143.web-hosting.com',
            'mail_imap_host' => 'premium143.web-hosting.com',
            'mail_smtp_port' => '465',
            'mail_imap_port' => '993',
            'mail_username' => 'sm@choddodiginova.tech',
            'mail_password' => 'choddodiginova.tech',
            'mail_from_name' => 'Shusanto Modak',
            'status' => 'on'
        ]);

        DB::table('mailboxes')->insert([
            'mail_smtp_host' => 'premium143.web-hosting.com',
            'mail_imap_host' => 'premium143.web-hosting.com',
            'mail_smtp_port' => '465',
            'mail_imap_port' => '993',
            'mail_username' => 'sm@ponerodiginova.tech',
            'mail_password' => 'ponerodiginova.tech',
            'mail_from_name' => 'Shusanto Modak',
            'status' => 'on'
        ]);

        DB::table('mailboxes')->insert([
            'mail_smtp_host' => 'premium143.web-hosting.com',
            'mail_imap_host' => 'premium143.web-hosting.com',
            'mail_smtp_port' => '465',
            'mail_imap_port' => '993',
            'mail_username' => 'sm@sholodiginova.tech',
            'mail_password' => 'sholodiginova.tech',
            'mail_from_name' => 'Shusanto Modak',
            'status' => 'on'
        ]);

        DB::table('mailboxes')->insert([
            'mail_smtp_host' => 'premium143.web-hosting.com',
            'mail_imap_host' => 'premium143.web-hosting.com',
            'mail_smtp_port' => '465',
            'mail_imap_port' => '993',
            'mail_username' => 'sm@soterodiginova.tech',
            'mail_password' => 'soterodiginova.tech',
            'mail_from_name' => 'Shusanto Modak',
            'status' => 'on'
        ]);

        DB::table('mailboxes')->insert([
            'mail_smtp_host' => 'premium143.web-hosting.com',
            'mail_imap_host' => 'premium143.web-hosting.com',
            'mail_smtp_port' => '465',
            'mail_imap_port' => '993',
            'mail_username' => 'sm@atharodiginova.tech',
            'mail_password' => 'atharodiginova.tech',
            'mail_from_name' => 'Shusanto Modak',
            'status' => 'on'
        ]);

        DB::table('mailboxes')->insert([
            'mail_smtp_host' => 'premium143.web-hosting.com',
            'mail_imap_host' => 'premium143.web-hosting.com',
            'mail_smtp_port' => '465',
            'mail_imap_port' => '993',
            'mail_username' => 'sm@unishdiginova.tech',
            'mail_password' => 'unishdiginova.tech',
            'mail_from_name' => 'Shusanto Modak',
            'status' => 'on'
        ]);

        DB::table('mailboxes')->insert([
            'mail_smtp_host' => 'premium143.web-hosting.com',
            'mail_imap_host' => 'premium143.web-hosting.com',
            'mail_smtp_port' => '465',
            'mail_imap_port' => '993',
            'mail_username' => 'sm@bishdiginova.tech',
            'mail_password' => 'bishdiginova.tech',
            'mail_from_name' => 'Shusanto Modak',
            'status' => 'on'
        ]);

        DB::table('mailboxes')->insert([
            'mail_smtp_host' => 'premium143.web-hosting.com',
            'mail_imap_host' => 'premium143.web-hosting.com',
            'mail_smtp_port' => '465',
            'mail_imap_port' => '993',
            'mail_username' => 'sm@ekushdiginova.tech',
            'mail_password' => 'ekushdiginova.tech',
            'mail_from_name' => 'Shusanto Modak',
            'status' => 'on'
        ]);

        DB::table('mailboxes')->insert([
            'mail_smtp_host' => 'premium143.web-hosting.com',
            'mail_imap_host' => 'premium143.web-hosting.com',
            'mail_smtp_port' => '465',
            'mail_imap_port' => '993',
            'mail_username' => 'sm@baishdiginova.tech',
            'mail_password' => 'baishdiginova.tech',
            'mail_from_name' => 'Shusanto Modak',
            'status' => 'on'
        ]);

        DB::table('mailboxes')->insert([
            'mail_smtp_host' => 'premium143.web-hosting.com',
            'mail_imap_host' => 'premium143.web-hosting.com',
            'mail_smtp_port' => '465',
            'mail_imap_port' => '993',
            'mail_username' => 'sm@teishdiginova.tech',
            'mail_password' => 'teishdiginova.tech',
            'mail_from_name' => 'Shusanto Modak',
            'status' => 'on'
        ]);

        DB::table('mailboxes')->insert([
            'mail_smtp_host' => 'premium143.web-hosting.com',
            'mail_imap_host' => 'premium143.web-hosting.com',
            'mail_smtp_port' => '465',
            'mail_imap_port' => '993',
            'mail_username' => 'sm@chobbishdiginova.tech',
            'mail_password' => 'chobbishdiginova.tech',
            'mail_from_name' => 'Shusanto Modak',
            'status' => 'on'
        ]);

        DB::table('mailboxes')->insert([
            'mail_smtp_host' => 'premium143.web-hosting.com',
            'mail_imap_host' => 'premium143.web-hosting.com',
            'mail_smtp_port' => '465',
            'mail_imap_port' => '993',
            'mail_username' => 'shusanto.m@pochishdiginova.tech',
            'mail_password' => 'pochishdiginova.tech',
            'mail_from_name' => 'Shusanto Modak',
            'status' => 'on'
        ]);

        DB::table('mailboxes')->insert([
            'mail_smtp_host' => 'premium143.web-hosting.com',
            'mail_imap_host' => 'premium143.web-hosting.com',
            'mail_smtp_port' => '465',
            'mail_imap_port' => '993',
            'mail_username' => 'shusanto.m@chabbishdiginova.tech',
            'mail_password' => 'chabbishdiginova.tech',
            'mail_from_name' => 'Shusanto Modak',
            'status' => 'on'
        ]);

        DB::table('mailboxes')->insert([
            'mail_smtp_host' => 'premium143.web-hosting.com',
            'mail_imap_host' => 'premium143.web-hosting.com',
            'mail_smtp_port' => '465',
            'mail_imap_port' => '993',
            'mail_username' => 'sm@satashdiginova.tech',
            'mail_password' => 'satashdiginova.tech',
            'mail_from_name' => 'Shusanto Modak',
            'status' => 'on'
        ]);

        DB::table('mailboxes')->insert([
            'mail_smtp_host' => 'premium143.web-hosting.com',
            'mail_imap_host' => 'premium143.web-hosting.com',
            'mail_smtp_port' => '465',
            'mail_imap_port' => '993',
            'mail_username' => 'sm@atashdiginova.tech',
            'mail_password' => 'atashdiginova.tech',
            'mail_from_name' => 'Shusanto Modak',
            'status' => 'on'
        ]);

        DB::table('mailboxes')->insert([
            'mail_smtp_host' => 'premium143.web-hosting.com',
            'mail_imap_host' => 'premium143.web-hosting.com',
            'mail_smtp_port' => '465',
            'mail_imap_port' => '993',
            'mail_username' => 'sm@untrishdiginova.tech',
            'mail_password' => 'untrishdiginova.tech',
            'mail_from_name' => 'Shusanto Modak',
            'status' => 'on'
        ]);

        DB::table('mailboxes')->insert([
            'mail_smtp_host' => 'premium143.web-hosting.com',
            'mail_imap_host' => 'premium143.web-hosting.com',
            'mail_smtp_port' => '465',
            'mail_imap_port' => '993',
            'mail_username' => 'sm@trishdiginova.techh',
            'mail_password' => 'trishdiginova.tech',
            'mail_from_name' => 'Shusanto Modak',
            'status' => 'on'
        ]);

        DB::table('mailboxes')->insert([
            'mail_smtp_host' => 'premium143.web-hosting.com',
            'mail_imap_host' => 'premium143.web-hosting.com',
            'mail_smtp_port' => '465',
            'mail_imap_port' => '993',
            'mail_username' => 'sm@aktrishdiginova.tech',
            'mail_password' => 'aktrishdiginova.tech',
            'mail_from_name' => 'Shusanto Modak',
            'status' => 'on'
        ]);

        DB::table('mailboxes')->insert([
            'mail_smtp_host' => 'premium143.web-hosting.com',
            'mail_imap_host' => 'premium143.web-hosting.com',
            'mail_smtp_port' => '465',
            'mail_imap_port' => '993',
            'mail_username' => 'sm@botrishdiginova.tech',
            'mail_password' => 'botrishdiginova.tech',
            'mail_from_name' => 'Shusanto Modak',
            'status' => 'on'
        ]);

        DB::table('mailboxes')->insert([
            'mail_smtp_host' => 'premium143.web-hosting.com',
            'mail_imap_host' => 'premium143.web-hosting.com',
            'mail_smtp_port' => '465',
            'mail_imap_port' => '993',
            'mail_username' => 'sm@tetrishdiginova.tech',
            'mail_password' => 'tetrishdiginova.tech',
            'mail_from_name' => 'Shusanto Modak',
            'status' => 'on'
        ]);

        DB::table('mailboxes')->insert([
            'mail_smtp_host' => 'premium143.web-hosting.com',
            'mail_imap_host' => 'premium143.web-hosting.com',
            'mail_smtp_port' => '465',
            'mail_imap_port' => '993',
            'mail_username' => 'sm@choutrishdiginova.online',
            'mail_password' => 'choutrishdiginova.online',
            'mail_from_name' => 'Shusanto Modak',
            'status' => 'on'
        ]);

        DB::table('mailboxes')->insert([
            'mail_smtp_host' => 'premium143.web-hosting.com',
            'mail_imap_host' => 'premium143.web-hosting.com',
            'mail_smtp_port' => '465',
            'mail_imap_port' => '993',
            'mail_username' => 'sm@poytrishdiginova.online',
            'mail_password' => 'poytrishdiginova.online',
            'mail_from_name' => 'Shusanto Modak',
            'status' => 'on'
        ]);

        DB::table('mailboxes')->insert([
            'mail_smtp_host' => 'premium143.web-hosting.com',
            'mail_imap_host' => 'premium143.web-hosting.com',
            'mail_smtp_port' => '465',
            'mail_imap_port' => '993',
            'mail_username' => 'sm@chotrishdiginova.online',
            'mail_password' => 'chotrishdiginova.online',
            'mail_from_name' => 'Shusanto Modak',
            'status' => 'on'
        ]);

        DB::table('mailboxes')->insert([
            'mail_smtp_host' => 'premium143.web-hosting.com',
            'mail_imap_host' => 'premium143.web-hosting.com',
            'mail_smtp_port' => '465',
            'mail_imap_port' => '993',
            'mail_username' => 'sm@saitrishdiginova.online',
            'mail_password' => 'saitrishdiginova.online',
            'mail_from_name' => 'Shusanto Modak',
            'status' => 'on'
        ]);

        DB::table('mailboxes')->insert([
            'mail_smtp_host' => 'premium143.web-hosting.com',
            'mail_imap_host' => 'premium143.web-hosting.com',
            'mail_smtp_port' => '465',
            'mail_imap_port' => '993',
            'mail_username' => 'sm@attrishdiginova.online',
            'mail_password' => 'attrishdiginova.online',
            'mail_from_name' => 'Shusanto Modak',
            'status' => 'on'
        ]);

        DB::table('mailboxes')->insert([
            'mail_smtp_host' => 'premium143.web-hosting.com',
            'mail_imap_host' => 'premium143.web-hosting.com',
            'mail_smtp_port' => '465',
            'mail_imap_port' => '993',
            'mail_username' => 'sm@unochollishdiginova.online',
            'mail_password' => 'unochollishdiginova.online',
            'mail_from_name' => 'Shusanto Modak',
            'status' => 'on'
        ]);

        DB::table('mailboxes')->insert([
            'mail_smtp_host' => 'premium143.web-hosting.com',
            'mail_imap_host' => 'premium143.web-hosting.com',
            'mail_smtp_port' => '465',
            'mail_imap_port' => '993',
            'mail_username' => 'sm@chollishdiginova.online',
            'mail_password' => 'chollishdiginova.online',
            'mail_from_name' => 'Shusanto Modak',
            'status' => 'on'
        ]);

        DB::table('mailboxes')->insert([
            'mail_smtp_host' => 'premium143.web-hosting.com',
            'mail_imap_host' => 'premium143.web-hosting.com',
            'mail_smtp_port' => '465',
            'mail_imap_port' => '993',
            'mail_username' => 'sm@akchollishdiginova.online',
            'mail_password' => 'akchollishdiginova.online',
            'mail_from_name' => 'Shusanto Modak',
            'status' => 'on'
        ]);

        DB::table('mailboxes')->insert([
            'mail_smtp_host' => 'premium143.web-hosting.com',
            'mail_imap_host' => 'premium143.web-hosting.com',
            'mail_smtp_port' => '465',
            'mail_imap_port' => '993',
            'mail_username' => 'sm@beyallishdiginova.online',
            'mail_password' => 'beyallishdiginova.online',
            'mail_from_name' => 'Shusanto Modak',
            'status' => 'on'
        ]);

        DB::table('mailboxes')->insert([
            'mail_smtp_host' => 'premium143.web-hosting.com',
            'mail_imap_host' => 'premium143.web-hosting.com',
            'mail_smtp_port' => '465',
            'mail_imap_port' => '993',
            'mail_username' => 'sm@tetallishdiginova.online',
            'mail_password' => 'tetallishdiginova.online',
            'mail_from_name' => 'Shusanto Modak',
            'status' => 'on'
        ]);

        DB::table('mailboxes')->insert([
            'mail_smtp_host' => 'premium143.web-hosting.com',
            'mail_imap_host' => 'premium143.web-hosting.com',
            'mail_smtp_port' => '465',
            'mail_imap_port' => '993',
            'mail_username' => 'sm@chuwallishdiginova.online',
            'mail_password' => 'chuwallishdiginova.online',
            'mail_from_name' => 'Shusanto Modak',
            'status' => 'on'
        ]);

        DB::table('mailboxes')->insert([
            'mail_smtp_host' => 'premium143.web-hosting.com',
            'mail_imap_host' => 'premium143.web-hosting.com',
            'mail_smtp_port' => '465',
            'mail_imap_port' => '993',
            'mail_username' => 'sm@poychollishdiginova.online',
            'mail_password' => 'poychollishdiginova.online',
            'mail_from_name' => 'Shusanto Modak',
            'status' => 'on'
        ]);

        DB::table('mailboxes')->insert([
            'mail_smtp_host' => 'premium143.web-hosting.com',
            'mail_imap_host' => 'premium143.web-hosting.com',
            'mail_smtp_port' => '465',
            'mail_imap_port' => '993',
            'mail_username' => 'sm@choychollishdiginova.online',
            'mail_password' => 'choychollishdiginova.online',
            'mail_from_name' => 'Shusanto Modak',
            'status' => 'on'
        ]);

        DB::table('mailboxes')->insert([
            'mail_smtp_host' => 'premium143.web-hosting.com',
            'mail_imap_host' => 'premium143.web-hosting.com',
            'mail_smtp_port' => '465',
            'mail_imap_port' => '993',
            'mail_username' => 'sm@satchollishdiginova.online',
            'mail_password' => 'satchollishdiginova.online',
            'mail_from_name' => 'Shusanto Modak',
            'status' => 'on'
        ]);

        DB::table('mailboxes')->insert([
            'mail_smtp_host' => 'premium143.web-hosting.com',
            'mail_imap_host' => 'premium143.web-hosting.com',
            'mail_smtp_port' => '465',
            'mail_imap_port' => '993',
            'mail_username' => 'sm@atchollishdiginova.online',
            'mail_password' => 'atchollishdiginova.online',
            'mail_from_name' => 'Shusanto Modak',
            'status' => 'on'
        ]);

        DB::table('mailboxes')->insert([
            'mail_smtp_host' => 'premium143.web-hosting.com',
            'mail_imap_host' => 'premium143.web-hosting.com',
            'mail_smtp_port' => '465',
            'mail_imap_port' => '993',
            'mail_username' => 'sm@unoponchashdiginova.online',
            'mail_password' => 'unoponchashdiginova.online',
            'mail_from_name' => 'Shusanto Modak',
            'status' => 'on'
        ]);

        DB::table('mailboxes')->insert([
            'mail_smtp_host' => 'premium143.web-hosting.com',
            'mail_imap_host' => 'premium143.web-hosting.com',
            'mail_smtp_port' => '465',
            'mail_imap_port' => '993',
            'mail_username' => 'sm@ponchashdiginova.online',
            'mail_password' => 'ponchashdiginova.online',
            'mail_from_name' => 'Shusanto Modak',
            'status' => 'on'
        ]);

        // Lists

        DB::table('leadlists')->insert([
            'name' => 'First list'
        ]);

        DB::table('leadlists')->insert([
            'name' => 'Skipped personalization'
        ]);

        // Lists

        DB::table('campaigns')->insert([
            'name' => '[firstname], looking for a website revamp?',
            'subject' => '[firstname], looking for a website revamp?',
            'body' => '<p>Hey [firstname],</p><p> Are you looking for a way to generate more business through your website? With a professionally designed online presence, you can take your online sales to a new level.</p><p>While your website has a lot going for it – such as the mobile friendly design – there are steps you can take to improve its appearance and conversion potential.</p><p>My one and only goal is simple: to redesign your website as a means for helping you generate more revenue.</p><p>If you understand the importance of a well designed website and if you want to have more online success in the future, take the time to respond to this email. My past work, along with hundreds of positive reviews, don’t lie.</p><p>Is now the time to redesign your website? The answer may be yes.</p><p>Cheers,<br>Shusanto Modak<br>Diginova Tech</p>'
        ]);

        DB::table('campaigns')->insert([
            'name' => 'Seeking collaboration with [company].',
            'subject' => 'Seeking collaboration with [company].',
            'body' => '<p>[personalizedLine]</p> <p>Would you be interested in a quick chat?</p><p>Regards,<br>Shusanto Modak<br>Diginova Tech</p>'
        ]);
        
    }
}
