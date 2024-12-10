<?php

namespace App\Console;

use App\Models\Setting;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */

    protected $commands = [
        // Commands\AddQueue::class,
    ];


    
    protected function schedule(Schedule $schedule)
    {   
        
        // Run app:send-email every minute between 9 AM and 5 PM, excluding Sunday (0) and Saturday (6)
        /*
        $schedule->command('app:send-email')
        ->everyMinute()
        ->between('05:00', '14:00') // 5AM to 2 PM
        ->skip(function () {
            return in_array(now()->dayOfWeek, [0, 6]); // Skip on Sunday (0) and Saturday (6)
        });
        */


        $schedule->command('app:send-email')->everyMinute();
        $schedule->command('app:check-replies')->everyMinute();

    }
    

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
