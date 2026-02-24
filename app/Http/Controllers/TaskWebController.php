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

    public function index(Request $request)
    {
        $user = $request->user();
        $search = $request->query('search');
        $status = $request->query('status');
        $priority = $request->query('priority');

        $tasksQuery = Task::whereIn('project_id', \App\Models\Project::whereIn('workspace_id', $user->workspaces()->pluck('id'))->pluck('id'))
            ->with('project', 'project.workspace');

        if ($status) {
            $tasksQuery->where('status', $status);
        }

        if ($priority) {
            $tasksQuery->where('priority', $priority);
        }

        $tasks = $tasksQuery->orderBy('created_at', 'desc')->get();

        if ($search) {
            $search = strtolower($search);
            $tasks = $tasks->filter(function ($task) use ($search) {
                return stripos($task->name, $search) !== false || 
                       stripos($task->description ?? '', $search) !== false;
            });
            $tasks = $tasks->values();
        }

        if ($request->wantsJson()) {
            return response()->json([
                'tasks' => $tasks,
            ]);
        }

        return view('tasks.index');
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

    public function complete(Request $request, string $workspaceId, string $projectId, string $taskId)
    {
        $this->taskService->completeTask($request->user(), $workspaceId, $projectId, $taskId);

        return redirect()->back()->with('success', 'Task marked as completed! 🎉');
    }

    public function destroy(Request $request, string $workspaceId, string $projectId, string $taskId)
    {
        $this->taskService->deleteTask($request->user(), $workspaceId, $projectId, $taskId);

        return redirect("/workspaces/{$workspaceId}/projects/{$projectId}")->with('success', 'Task deleted successfully!');
    }
}
