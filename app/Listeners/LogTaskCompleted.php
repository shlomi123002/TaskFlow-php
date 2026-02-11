<?php

namespace App\Listeners;

use App\Events\TaskCompleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;


class LogTaskCompleted
{
    public function handle(TaskCompleted $event): void
    {
        DB::table('activity_logs')->insert([
            'task_id' => $event->task->id,
            'action' => 'completed',
            'occurred_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
