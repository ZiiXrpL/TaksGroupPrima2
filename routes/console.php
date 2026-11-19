<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\Task;
use App\Services\NotificationService;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('tasks:publish-scheduled', function (NotificationService $notifications) {
    Task::query()
        ->where('status', Task::STATUS_DRAFT)
        ->whereNotNull('scheduled_publish_at')
        ->where('scheduled_publish_at', '<=', now())
        ->with(['groups.members', 'individualAssignments.student'])
        ->get()
        ->each(function (Task $task) use ($notifications) {
            $task->update([
                'status' => Task::STATUS_PUBLISHED,
                'scheduled_publish_at' => null,
                'published_at' => now(),
            ]);

            $notifications->publishTask($task);
        });
    $this->info('Scheduled tasks checked.');
})->purpose('Publish tasks whose scheduled publish time has arrived');

Schedule::command('tasks:publish-scheduled')->everyMinute();
