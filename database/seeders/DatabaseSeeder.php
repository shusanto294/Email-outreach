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

        DB::table('users')->insert([
            'name' => 'Shusanto kumar modak',
            'email' => 'shusanto294@gmail.com',
            'password' => Hash::make('apple727354'),
        ]);

        DB::table('settings')->insert([
            'key' => 'send_emails',
            'value' => 'off',
        ]);
        
    }
}
