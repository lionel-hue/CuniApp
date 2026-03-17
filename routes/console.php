// routes/console.php
<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('subscriptions:check-expiration')
    ->dailyAt('08:00')
    ->withoutOverlapping()
    ->onOneServer();

Schedule::command('transactions:cleanup-pending')
    ->everyThirtyMinutes()
    ->withoutOverlapping()
    ->onOneServer();

Schedule::command('births:check-verification')
    ->dailyAt('09:00')
    ->withoutOverlapping()
    ->onOneServer();
