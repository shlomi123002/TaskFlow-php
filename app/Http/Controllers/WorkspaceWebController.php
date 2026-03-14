<?php
namespace App\Http\Controllers;

use App\Models\Workspace;
use App\Services\WorkspaceService;
use Illuminate\Http\Request;

class WorkspaceWebController extends Controller
{
    public function __construct(private WorkspaceService $workspaceService)
    {
    }

    public function index(Request $request)
    {
        $workspaces = $this->workspaceService->getWorkspacesForUser($request->user());
        return view('workspaces.index', compact('workspaces'));
    }

    public function create()
    {
        return view('workspaces.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:120'],
        ]);

        $this->workspaceService->createWorkspace($request->user(), $validated);

        return redirect('/home')->with('success', 'Workspace created successfully!');
    }

    public function edit(Request $request, string $workspaceId)
    {
        $workspace = $this->workspaceService->getWorkspaceForUser($request->user(), $workspaceId);
        return view('workspaces.edit', compact('workspace'));
    }

    public function update(Request $request, string $workspaceId)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:120'],
        ]);

        $this->workspaceService->updateWorkspace($request->user(), $workspaceId, $validated);

        return redirect('/home')->with('success', 'Workspace updated successfully!');
    }

    public function destroy(Request $request, string $workspaceId)
    {
        $this->workspaceService->deleteWorkspace($request->user(), $workspaceId);

        return redirect('/home')->with('success', 'Workspace deleted successfully!');
    }

    public function show(Request $request, string $workspaceId)
    {

        $workspace = $this->workspaceService->getWorkspaceForUser($request->user(), $workspaceId);
        
        if ($request->wantsJson()) {
            $projects = $workspace->projects()->with('tasks')->get();
            return response()->json([
                'workspace' => $workspace,
                'projects' => $projects,
            ]);
        }

        return view('workspaces.show', [
            'workspace' => $workspace,
            'projects' => []
        ]);
    }

    public function share(Request $request, string $workspaceId)
    {
        $workspace = $this->workspaceService->getWorkspaceForUser($request->user(), $workspaceId);
        $availableUsers = $this->workspaceService->getAvailableUsersForSharing($request->user());
        $sharedUserIds = $workspace->users()->pluck('user_id')->toArray();

        return view('workspaces.share', compact('workspace', 'availableUsers', 'sharedUserIds'));
    }

    public function storeShare(Request $request, string $workspaceId)
    {
        $validated = $request->validate([
            'user_ids' => ['required', 'array'],
            'user_ids.*' => ['required', 'string', 'exists:users,id'],
        ]);

        $this->workspaceService->shareWorkspace($request->user(), $workspaceId, $validated['user_ids']);

        return redirect("/workspaces/{$workspaceId}")->with('success', 'Workspace shared successfully!');
    }

    public function removeUser(Request $request, string $workspaceId, string $userId)
    {
        $this->workspaceService->removeUserFromWorkspace($request->user(), $workspaceId, $userId);

        return redirect("/workspaces/{$workspaceId}/share")->with('success', 'User removed from workspace successfully!');
    }
}
