<?php

namespace App\Services;

use App\Events\TaskCompleted;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Collection;

class TaskService
{
    public function __construct(private WorkspaceService $workspaceService)
    {
    }

    public function getTasksForProject(User $user, string $workspaceId, string $projectId, ?string $status = null, ?string $priority = null, ?string $search = null)
    {
        $workspace = $this->workspaceService->getWorkspaceForUser($user, $workspaceId);

        $project = $workspace->projects()
            ->whereKey($projectId)
            ->firstOrFail();

        $query = $project->tasks()->with('user');

        if ($status) {
            $query->where('status', $status);
        }

        if ($priority) {
            $query->where('priority', $priority);
        }

        $tasks = $query->orderBy('created_at', 'desc')
            ->get([
                'id',
                'project_id',
                'user_id',
                'name',
                'description',
                'status',
                'priority',
                'created_at',
                'updated_at',
            ]);

        if ($search) {
            $search = strtolower($search);
            $tasks = $tasks->filter(function ($task) use ($search) {
                return stripos($task->name, $search) !== false || 
                       stripos($task->description ?? '', $search) !== false;
            });
            $tasks = $tasks->values();
        }

        return $tasks;
    }

    public function createTask(User $user, string $workspaceId, string $projectId, array $data): Task
    {
        $workspace = $this->workspaceService->getWorkspaceForUser($user, $workspaceId);

        $project = $workspace->projects()
            ->whereKey($projectId)
            ->firstOrFail();

        $task = Task::create([
            'project_id' => $project->id,
            'user_id' => $data['user_id'],
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'status' => $data['status'] ?? 'pending',
            'priority' => $data['priority'] ?? 'normal',
        ]);

        // if caller created a task that is already completed dispatch the event
        if ($task->status === 'completed') {
            event(new TaskCompleted($task));
        }

        return $task;
    }

    public function updateTask(User $user, string $workspaceId, string $projectId, string $taskId, array $data): Task
    {
        $workspace = $this->workspaceService->getWorkspaceForUser($user, $workspaceId);

        $project = $workspace->projects()
            ->whereKey($projectId)
            ->firstOrFail();

        $task = $project->tasks()
            ->whereKey($taskId)
            ->firstOrFail();

        $previousStatus = $task->status;

        $task->update($data);

        if ($previousStatus !== 'completed' && $task->status === 'completed') {
            event(new TaskCompleted($task));
        }

        return $task;
    }

    public function completeTask(User $user, string $workspaceId, string $projectId, string $taskId): Task
    {
        $workspace = $this->workspaceService->getWorkspaceForUser($user, $workspaceId);

        $project = $workspace->projects()
            ->whereKey($projectId)
            ->firstOrFail();

        $task = $project->tasks()
            ->whereKey($taskId)
            ->firstOrFail();

        if ($task->status !== 'completed') {
            $task->update(['status' => 'completed']);
            event(new TaskCompleted($task));
        }

        return $task;
    }

    public function deleteTask(User $user, string $workspaceId, string $projectId, string $taskId): void
    {
        $workspace = $this->workspaceService->getWorkspaceForUser($user, $workspaceId);

        $project = $workspace->projects()
            ->whereKey($projectId)
            ->firstOrFail();

        $task = $project->tasks()
            ->whereKey($taskId)
            ->firstOrFail();

        $task->delete();
    }

    public function getTaskForUser(User $user, string $workspaceId, string $projectId, string $taskId): Task
    {
        $workspace = $this->workspaceService->getWorkspaceForUser($user, $workspaceId);

        $project = $workspace->projects()
            ->whereKey($projectId)
            ->firstOrFail();

        return $project->tasks()
            ->whereKey($taskId)
            ->firstOrFail();
    }

    public function getAllTasksForUser(User $user, ?string $status = null, ?string $priority = null): Collection
    {
        $workspaceIds = $user->workspaces()->pluck('workspaces.id')->toArray();

        $tasksQuery = Task::query()
            ->with(['project.workspace'])
            ->whereHas('project', function ($q) use ($workspaceIds) {
                $q->whereIn('workspace_id', $workspaceIds);
            });

        if ($status) {
            $tasksQuery->where('status', $status);
        }

        if ($priority) {
            $tasksQuery->where('priority', $priority);
        }

        $tasks = $tasksQuery->orderByDesc('created_at')->get();

        return $tasks->map(function (Task $task) {
            return [
                'id' => $task->id,
                'name' => $task->name,
                'status' => $task->status,
                'priority' => $task->priority,
                'project' => [
                    'id' => $task->project->id,
                    'name' => $task->project->name,
                    'workspace' => [
                        'id' => $task->project->workspace->id,
                        'name' => $task->project->workspace->name,
                    ],
                ],
                'created_at' => $task->created_at,
                'updated_at' => $task->updated_at,
            ];
        });
    }
}
