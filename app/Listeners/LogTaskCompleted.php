<?php

namespace App\Listeners;

use App\Events\TaskCompleted;
use App\Models\ActivityLog;

class LogTaskCompletion
{
    public function handle(TaskCompleted $event): void
    {
        ActivityLog::create([
            'task_id' => $event->task->id,
            'user_id' => $event->completedBy?->id,
            'action' => 'task_completed',
            'meta' => [
                'task_title' => $event->task->title ?? null,
            ],
            'occurred_at' => $event->completedAt,
        ]);
    }
}