<?php

namespace Tests\Feature;

use App\Events\TaskCompleted;
use App\Models\Workspace;
use App\Models\User;
use App\Services\TaskService;
use App\Services\WorkspaceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class TaskCompletionLoggingTest extends TestCase
{
    use RefreshDatabase;

    private function makeContext(): array
    {
        $user = User::factory()->create();
        $workspace = Workspace::create(['name' => 'test workspace']);
        $user->workspaces()->attach($workspace->id);
        $project = $workspace->projects()->create(['name' => 'test project']);
        $task = $project->tasks()->create([
            'name' => 'test task',
            'status' => 'pending',
            'priority' => 'normal',
        ]);

        return compact('user', 'workspace', 'project', 'task');
    }

    public function test_completing_task_dispatches_event()
    {
        $ctx = $this->makeContext();

        Event::fake();

        $service = new TaskService(new WorkspaceService());
        $service->updateTask(
            $ctx['user'],
            (string) $ctx['workspace']->id,
            (string) $ctx['project']->id,
            (string) $ctx['task']->id,
            [
                'name' => $ctx['task']->name,
                'description' => null,
                'status' => 'completed',
                'priority' => $ctx['task']->priority,
            ]
        );

        Event::assertDispatched(TaskCompleted::class, function (TaskCompleted $event) use ($ctx) {
            return $event->task->id === $ctx['task']->id;
        });
    }

    public function test_completing_task_logs_activity()
    {
        $ctx = $this->makeContext();

        $service = new TaskService(new WorkspaceService());
        $service->updateTask(
            $ctx['user'],
            (string) $ctx['workspace']->id,
            (string) $ctx['project']->id,
            (string) $ctx['task']->id,
            [
                'name' => $ctx['task']->name,
                'description' => null,
                'status' => 'completed',
                'priority' => $ctx['task']->priority,
            ]
        );

        $this->assertDatabaseHas('activity_logs', [
            'task_id' => $ctx['task']->id,
            'action' => 'completed',
        ]);
    }

    public function test_creating_already_completed_task_also_logs()
    {
        $user = User::factory()->create();
        $workspace = Workspace::create(['name' => 'new workspace']);
        $user->workspaces()->attach($workspace->id);
        $project = $workspace->projects()->create(['name' => 'another project']);

        $service = new TaskService(new WorkspaceService());
        $task = $service->createTask($user, (string) $workspace->id, (string) $project->id, [
            'name' => 'done right away',
            'status' => 'completed',
            'priority' => 'high',
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'task_id' => $task->id,
            'action' => 'completed',
        ]);
    }
}
