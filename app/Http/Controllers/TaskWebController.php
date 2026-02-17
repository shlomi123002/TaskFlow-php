<?php
namespace App\Http\Controllers;

use App\Models\Task;
use App\Services\TaskService;
use App\Services\ProjectService;
use App\Services\WorkspaceService;
use Illuminate\Http\Request;

class TaskWebController extends Controller
{
    public function __construct(
        private TaskService $taskService,
        private ProjectService $projectService,
        private WorkspaceService $workspaceService
    )
    {
    }

    public function create(Request $request, string $workspaceId, string $projectId)
    {
        $project = $this->projectService->getProjectForUser($request->user(), $workspaceId, $projectId);
        $workspace = $this->workspaceService->getWorkspaceForUser($request->user(), $workspaceId);
        
        return view('tasks.create', compact('project', 'workspace'));
    }

    public function store(Request $request, string $workspaceId, string $projectId)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:200'],
            'description' => ['nullable', 'string', 'max:5000'],
            'status' => ['nullable', 'in:pending,completed'],
            'priority' => ['nullable', 'in:low,normal,high'],
        ]);

        $this->taskService->createTask($request->user(), $workspaceId, $projectId, $validated);

        return redirect("/workspaces/{$workspaceId}/projects/{$projectId}")->with('success', 'Task created successfully!');
    }

    public function edit(Request $request, string $workspaceId, string $projectId, string $taskId)
    {
        $task = $this->taskService->getTaskForUser($request->user(), $workspaceId, $projectId, $taskId);
        $project = $this->projectService->getProjectForUser($request->user(), $workspaceId, $projectId);
        $workspace = $this->workspaceService->getWorkspaceForUser($request->user(), $workspaceId);
        
        return view('tasks.edit', compact('task', 'project', 'workspace'));
    }

    public function update(Request $request, string $workspaceId, string $projectId, string $taskId)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:200'],
            'description' => ['nullable', 'string', 'max:5000'],
            'status' => ['required', 'in:pending,completed'],
            'priority' => ['required', 'in:low,normal,high'],
        ]);

        $this->taskService->updateTask($request->user(), $workspaceId, $projectId, $taskId, $validated);

        return redirect("/workspaces/{$workspaceId}/projects/{$projectId}")->with('success', 'Task updated successfully!');
    }

    public function destroy(Request $request, string $workspaceId, string $projectId, string $taskId)
    {
        $this->taskService->deleteTask($request->user(), $workspaceId, $projectId, $taskId);

        return redirect("/workspaces/{$workspaceId}/projects/{$projectId}")->with('success', 'Task deleted successfully!');
    }
}
