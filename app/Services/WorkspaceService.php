<?php

namespace App\Services;

use App\Models\User;
use App\Models\Workspace;

class WorkspaceService
{
    /**
     * Get all workspaces for a user
     */
    public function getWorkspacesForUser(User $user): array
    {
        return $user->workspaces()
            ->with('projects')
            ->with('tasks')
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }

    /**
     * Create a new workspace and attach the user as a member
     */
    public function createWorkspace(User $user, array $data): Workspace
    {
        $workspace = Workspace::create([
            'name' => $data['name'],
        ]);

        // Attach the creator as a member
        $user->workspaces()->attach($workspace->id);

        return $workspace;
    }

    /**
     * Update a workspace (with multi-tenant check)
     */
    public function updateWorkspace(User $user, string $workspaceId, array $data): Workspace
    {
        $workspace = $user->workspaces()
            ->whereKey($workspaceId)
            ->firstOrFail();

        $workspace->update([
            'name' => $data['name'],
        ]);

        return $workspace;
    }

    /**
     * Delete a workspace (with multi-tenant check)
     */
    public function deleteWorkspace(User $user, string $workspaceId): void
    {
        $workspace = $user->workspaces()
            ->whereKey($workspaceId)
            ->firstOrFail();

        $workspace->delete();
    }

    /**
     * Get a workspace by ID with multi-tenant check
     */
    public function getWorkspaceForUser(User $user, string $workspaceId): Workspace
    {
        return $user->workspaces()
            ->whereKey($workspaceId)
            ->firstOrFail();
    }

    /**
     * Share a workspace with other users
     */
    public function shareWorkspace(User $user, string $workspaceId, array $userIds): Workspace
    {
        $workspace = $user->workspaces()
            ->whereKey($workspaceId)
            ->firstOrFail();

        // Attach users to the workspace (sync will add new and remove old ones if needed, but we use attach for add-only)
        foreach ($userIds as $sharedUserId) {
            $workspace->users()->syncWithoutDetaching([$sharedUserId]);
        }

        return $workspace;
    }

    /**
     * Get all users available for sharing (excluding the current user)
     */
    public function getAvailableUsersForSharing(User $user): array
    {
        return User::where('id', '!=', $user->id)
            ->orderBy('name')
            ->get()
            ->toArray();
    }
}
