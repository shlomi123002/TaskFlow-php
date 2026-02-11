<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\WorkspaceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WorkspaceController extends Controller
{
    public function __construct(private WorkspaceService $workspaceService)
    {
    }
    /**
     * GET /api/v1/workspaces
     * מחזיר רק את ה-workspaces של המשתמש המחובר
     */
    public function index(Request $request): JsonResponse
    {
        $workspaces = $this->workspaceService->getWorkspacesForUser($request->user());

        return response()->json([
            'data' => $workspaces,
        ]);
    }

    /**
     * POST /api/v1/workspaces
     * יוצר workspace ומצרף אליו את המשתמש היוצר כחבר
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:120'],
        ]);

        $workspace = $this->workspaceService->createWorkspace($request->user(), $validated);

        return response()->json([
            'data' => [
                'id' => (string) $workspace->id,
                'name' => $workspace->name,
                'created_at' => $workspace->created_at,
                'updated_at' => $workspace->updated_at,
            ],
        ], 201);
    }

    /**
     * PUT /api/v1/workspaces/{workspaceId}
     * מאפשר עדכון רק אם המשתמש חבר ב-workspace הזה
     */
    public function update(Request $request, string $workspaceId): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:120'],
        ]);

        $workspace = $this->workspaceService->updateWorkspace($request->user(), $workspaceId, $validated);

        return response()->json([
            'data' => [
                'id' => (string) $workspace->id,
                'name' => $workspace->name,
                'created_at' => $workspace->created_at,
                'updated_at' => $workspace->updated_at,
            ],
        ]);
    }

    /**
     * DELETE /api/v1/workspaces/{workspaceId}
     * Soft delete, ורק אם המשתמש חבר ב-workspace
     */
    public function destroy(Request $request, string $workspaceId): JsonResponse
    {
        $this->workspaceService->deleteWorkspace($request->user(), $workspaceId);

        return response()->json([], 204);
    }
}
