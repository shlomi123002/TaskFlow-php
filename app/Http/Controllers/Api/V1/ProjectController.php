<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Services\ProjectService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function __construct(private ProjectService $projectService)
    {
    }
    public function index(Request $request, string $workspaceId): JsonResponse
    {
        $projects = $this->projectService->getProjectsForWorkspace($request->user(), $workspaceId);

        return response()->json(['data' => $projects]);
    }

    public function store(Request $request, string $workspaceId): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:120'],
        ]);

        $project = $this->projectService->createProject($request->user(), $workspaceId, $validated);

        return response()->json([
            'data' => [
                'id' => (string) $project->id,
                'workspace_id' => (string) $project->workspace_id,
                'name' => $project->name,
                'created_at' => $project->created_at,
                'updated_at' => $project->updated_at,
            ],
        ], 201);
    }

    public function update(Request $request, string $workspaceId, string $projectId): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:120'],
        ]);

        $project = $this->projectService->updateProject($request->user(), $workspaceId, $projectId, $validated);

        return response()->json([
            'data' => [
                'id' => (string) $project->id,
                'workspace_id' => (string) $project->workspace_id,
                'name' => $project->name,
                'created_at' => $project->created_at,
                'updated_at' => $project->updated_at,
            ],
        ]);
    }

    public function destroy(Request $request, string $workspaceId, string $projectId): JsonResponse
    {
        $this->projectService->deleteProject($request->user(), $workspaceId, $projectId);

        return response()->json([], 204);
    }

    public function all(Request $request): JsonResponse
    {
        $projects = $this->projectService->getAllProjectsForUser($request->user());

        return response()->json(['data' => $projects]);
    }

}
