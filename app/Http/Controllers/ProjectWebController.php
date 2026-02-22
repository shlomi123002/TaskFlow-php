<?php
namespace App\Http\Controllers;

use App\Models\Project;
use App\Services\ProjectService;
use App\Services\WorkspaceService;
use Illuminate\Http\Request;

class ProjectWebController extends Controller
{
    public function __construct(
        private ProjectService $projectService,
        private WorkspaceService $workspaceService,
        private \App\Services\TaskService $taskService
    )
    {
    }

    public function create(Request $request, string $workspaceId)
    {
        $workspace = $this->workspaceService->getWorkspaceForUser($request->user(), $workspaceId);
        return view('projects.create', compact('workspace'));
    }

    public function store(Request $request, string $workspaceId)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:120'],
        ]);

        $this->projectService->createProject($request->user(), $workspaceId, $validated);

        return redirect("/workspaces/{$workspaceId}")->with('success', 'Project created successfully!');
    }

    public function edit(Request $request, string $workspaceId, string $projectId)
    {
        $project = $this->projectService->getProjectForUser($request->user(), $workspaceId, $projectId);
        $workspace = $this->workspaceService->getWorkspaceForUser($request->user(), $workspaceId);
        
        return view('projects.edit', compact('project', 'workspace'));
    }

    public function update(Request $request, string $workspaceId, string $projectId)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:120'],
        ]);

        $this->projectService->updateProject($request->user(), $workspaceId, $projectId, $validated);

        return redirect("/workspaces/{$workspaceId}")->with('success', 'Project updated successfully!');
    }

    public function destroy(Request $request, string $workspaceId, string $projectId)
    {
        $this->projectService->deleteProject($request->user(), $workspaceId, $projectId);

        return redirect("/workspaces/{$workspaceId}")->with('success', 'Project deleted successfully!');
    }

    public function show(Request $request, string $workspaceId, string $projectId)
    {
        $project = $this->projectService->getProjectForUser($request->user(), $workspaceId, $projectId);
        $workspace = $this->workspaceService->getWorkspaceForUser($request->user(), $workspaceId);
        
        $status = $request->query('status');
        $priority = $request->query('priority');

        if ($request->wantsJson()) {
            $tasks = $this->taskService->getTasksForProject($request->user(), $workspaceId, $projectId, $status, $priority);
            return response()->json([
                'project' => $project,
                'workspace' => $workspace,
                'tasks' => $tasks,
            ]);
        }
        
        return view('projects.show', [
            'project' => $project,
            'workspace' => $workspace,
            'status' => $status,
            'priority' => $priority,
            'tasks' => []
        ]);
    }
}
