// Create: bootstrap/app.php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withSchedule(function (Schedule $schedule) {
        // ✅ Vérification des abonnements expirés
        $schedule->command('subscriptions:check-expiration')
            ->dailyAt('08:00')
            ->withoutOverlapping()
            ->onOneServer();

        // ✅ Nettoyage des transactions en attente
        $schedule->command('transactions:cleanup-pending')
            ->everyThirtyMinutes()
            ->withoutOverlapping()
            ->onOneServer();

        // ✅ Vérification des naissances
        $schedule->command('births:check-verification')
            ->dailyAt('09:00')
            ->withoutOverlapping()
            ->onOneServer();
    })
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'check.subscription' => \App\Http\Middleware\CheckSubscription::class,
            'check.admin' => \App\Http\Middleware\CheckAdminRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
