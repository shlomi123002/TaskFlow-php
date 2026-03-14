<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Workspace;

class WorkspacePolicy
{
    /**
     * Determine if the user can share the workspace (add users).
     * Only the workspace creator can share.
     */
    public function share(User $user, Workspace $workspace): bool
    {
        return $workspace->created_by === $user->id;
    }

    /**
     * Determine if the user can remove users from the workspace.
     * Only the workspace creator can remove users.
     */
    public function removeUser(User $user, Workspace $workspace): bool
    {
        return $workspace->created_by === $user->id;
    }

    /**
     * Determine if the user can view the workspace.
     */
    public function view(User $user, Workspace $workspace): bool
    {
        return $workspace->users()->where('user_id', $user->id)->exists();
    }

    /**
     * Determine if the user can update the workspace.
     * Only the workspace creator can update.
     */
    public function update(User $user, Workspace $workspace): bool
    {
        return $workspace->created_by === $user->id;
    }

    /**
     * Determine if the user can delete the workspace.
     * Only the workspace creator can delete.
     */
    public function delete(User $user, Workspace $workspace): bool
    {
        return $workspace->created_by === $user->id;
    }
}
