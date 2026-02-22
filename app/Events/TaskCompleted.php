<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Task;
use App\Models\User;


class TaskCompleted
{
    use Dispatchable, SerializesModels;

    public ?User $completedBy;
    public string $completedAt;

    public function __construct(public Task $task, ?User $completedBy = null)
    {
        $this->completedBy = $completedBy;
        $this->completedAt = now()->toDateTimeString();
    }
}
