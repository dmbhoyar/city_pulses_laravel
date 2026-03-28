<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Run on 1st, 7th, 14th, 21st, and 28th of each month at 2:00 AM
        $schedule->command('coupons:fetch-amazon')->monthlyOn(1, '2:00');
        $schedule->command('coupons:fetch-amazon')->monthlyOn(7, '2:00');
        $schedule->command('coupons:fetch-amazon')->monthlyOn(14, '2:00');
        $schedule->command('coupons:fetch-amazon')->monthlyOn(21, '2:00');
        $schedule->command('coupons:fetch-amazon')->monthlyOn(28, '2:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
