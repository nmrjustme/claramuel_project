<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\CleanupRoomHolds;
use Illuminate\Console\Scheduling\Schedule;
use App\Models\RoomHold;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(CleanupRoomHolds::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->withSchedule(function (Schedule $schedule) {
        // Clean up expired room holds every minute
        $schedule->call(function () {
            RoomHold::where('expires_at', '<', now())
                ->orWhere('status', 'expired')
                ->delete();
        })->everyMinute();
    })
    ->create();