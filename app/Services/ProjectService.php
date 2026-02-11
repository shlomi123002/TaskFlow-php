<?php

namespace App\Services;

use App\Models\Project;
use App\Models\User;

class ProjectService
{
    public function __construct(private WorkspaceService $workspaceService)
    {
    }

    public function getProjectsForWorkspace(User $user, string $workspaceId)
    {
        $workspace = $this->workspaceService->getWorkspaceForUser($user, $workspaceId);

        return $workspace->projects()->with('tasks')
            ->orderBy('created_at', 'desc')
            ->get(['id', 'workspace_id', 'name', 'created_at', 'updated_at']);
    }

    public function createProject(User $user, string $workspaceId, array $data): Project
    {
        $workspace = $this->workspaceService->getWorkspaceForUser($user, $workspaceId);

        return Project::create([
            'workspace_id' => $workspace->id,
            'name' => $data['name'],
        ]);
    }

    public function updateProject(User $user, string $workspaceId, string $projectId, array $data): Project
    {
        $workspace = $this->workspaceService->getWorkspaceForUser($user, $workspaceId);

        $project = $workspace->projects()
            ->whereKey($projectId)
            ->firstOrFail();

        $project->update(['name' => $data['name']]);

        return $project;
    }

    public function deleteProject(User $user, string $workspaceId, string $projectId): void
    {
        $workspace = $this->workspaceService->getWorkspaceForUser($user, $workspaceId);

        $project = $workspace->projects()
            ->whereKey($projectId)
            ->firstOrFail();

        $project->delete();
    }

    public function getProjectForUser(User $user, string $workspaceId, string $projectId): Project
    {
        $workspace = $this->workspaceService->getWorkspaceForUser($user, $workspaceId);

        return $workspace->projects()
            ->whereKey($projectId)
            ->firstOrFail();
    }

    public function getAllProjectsForUser(User $user)
    {
        return Project::whereIn('workspace_id', $user->workspaces()->pluck('id'))
            ->orderBy('created_at', 'desc')
            ->get(['id', 'workspace_id', 'name', 'created_at', 'updated_at']);
    }
}
