<?php

namespace App\Console;

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
        Commands\AddQueue::class,
    ];


    protected function schedule(Schedule $schedule)
    {
        // Run app:send-email every minute between 10 PM and 10 AM, excluding Friday and Saturday
        $schedule->command('app:send-email')
            ->everyMinute()
            ->between('22:00', '10:00')
            ->skip(function () {
                return in_array(now()->dayOfWeek, [5, 6]); // Skip Friday (5) and Saturday (6)
            });
    
        // Run app:check-mailboxes every minute
        $schedule->command('app:check-mailboxes')->everyMinute();
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
