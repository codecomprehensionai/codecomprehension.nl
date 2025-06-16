<?php

use Illuminate\Support\Facades\Schedule;

// use Spatie\Health\Commands\DispatchQueueCheckJobsCommand;
// use Spatie\Health\Commands\ScheduleCheckHeartbeatCommand;

Schedule::onOneServer()->group(function () {
    /* Horizon */
    Schedule::command('horizon:snapshot')->everyFiveMinutes();

    /* Healthcheck */
    // Schedule::command(ScheduleCheckHeartbeatCommand::class)->everyMinute();
    // Schedule::command(DispatchQueueCheckJobsCommand::class)->everyMinute();

    /* Cleanup */
    Schedule::command('activitylog:clean')->daily();
    Schedule::command('auth:clear-resets')->daily();
});
