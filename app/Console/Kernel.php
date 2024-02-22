<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        \App\Console\Commands\SendDeliveryNotifications::class,
        \App\Console\Commands\SendSecurityQuantityNotifications::class,
        \App\Console\Commands\SendUnsettledPaymentNotifications::class,
    ];
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();

    // $this->app->booted(function () use ($schedule) {
    //     $schedule->command('SendDeliveryNotifications')->dailyAt('00:00');
    // });
    // $schedule->command('app:send-delivery-notifications')->everyMinute();

    $this->app->booted(function () use ($schedule) {


        $schedule->command('app:send-delivery-notifications')->dailyAt('23:40');

        $schedule->command('app:send-security-quantity-notifications')->dailyAt('5:00')->days([Schedule::MONDAY,Schedule::SATURDAY]);

        $schedule->command('app:send-unsettled-payment-notifications')->dailyAt('5:00')->days([Schedule::MONDAY,Schedule::SATURDAY]);

    });


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
