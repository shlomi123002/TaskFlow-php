<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Events\TaskCompleted;


class TaskController extends Controller
{
    public function __construct(private TaskService $taskService)
    {
    }

    public function index(Request $request, string $workspaceId, string $projectId): JsonResponse
    {
        $tasks = $this->taskService->getTasksForProject(
            $request->user(),
            $workspaceId,
            $projectId,
            $request->query('status'),
            $request->query('priority')
        );

        return response()->json([
            'data' => $tasks,
        ]);
    }


    public function store(Request $request, string $workspaceId, string $projectId): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:200'],
            'description' => ['nullable', 'string', 'max:5000'],
            'status' => ['nullable', 'in:pending,completed'],
            'priority' => ['nullable', 'in:low,normal,high'],
        ]);

        $task = $this->taskService->createTask($request->user(), $workspaceId, $projectId, $validated);

        return response()->json([
            'data' => [
                'id' => (string) $task->id,
                'project_id' => (string) $task->project_id,
                'name' => $task->name,
                'description' => $task->description,
                'status' => $task->status,
                'priority' => $task->priority,
                'created_at' => $task->created_at,
                'updated_at' => $task->updated_at,
            ],
        ], 201);
    }

    public function update(Request $request, string $workspaceId, string $projectId, string $taskId): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:200'],
            'description' => ['nullable', 'string', 'max:5000'],
            'status' => ['required', 'in:pending,completed'],
            'priority' => ['required', 'in:low,normal,high'],
        ]);

        $task = $this->taskService->updateTask($request->user(), $workspaceId, $projectId, $taskId, $validated);

        return response()->json([
            'data' => [
                'id' => (string) $task->id,
                'project_id' => (string) $task->project_id,
                'name' => $task->name,
                'description' => $task->description,
                'status' => $task->status,
                'priority' => $task->priority,
                'created_at' => $task->created_at,
                'updated_at' => $task->updated_at,
            ],
        ]);
    }

    public function destroy(Request $request, string $workspaceId, string $projectId, string $taskId): JsonResponse
    {
        $this->taskService->deleteTask($request->user(), $workspaceId, $projectId, $taskId);

        return response()->json([], 204);
    }


    public function all(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['nullable', 'in:pending,completed'],
            'priority' => ['nullable', 'in:low,normal,high'],
        ]);

        $user = $request->user();

        $data = $this->taskService->getAllTasksForUser(
            $user,
            $validated['status'] ?? null,
            $validated['priority'] ?? null
        );

        return response()->json(['data' => $data]);
    }

}
