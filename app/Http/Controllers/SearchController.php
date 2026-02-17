<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Workspace;
use App\Models\Project;
use App\Models\Task;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->query('q');
        $user = Auth::user();

        $workspaces = collect();
        $projects = collect();
        $tasks = collect();

        if ($user) {
            $workspacesQuery = $user->workspaces();
            if ($q) {
                $workspacesQuery = $workspacesQuery->where('name', 'like', "%{$q}%");
            }
            $workspaces = $workspacesQuery->get();

            $projectsQuery = Project::whereIn('workspace_id', $user->workspaces()->pluck('id'));
            if ($q) {
                $projectsQuery = $projectsQuery->where('name', 'like', "%{$q}%");
            }
            $projects = $projectsQuery->get();

            $tasksQuery = Task::whereHas('project', function ($q2) use ($user) {
                $q2->whereIn('workspace_id', $user->workspaces()->pluck('id'));
            });
            if ($q) {
                $tasksQuery = $tasksQuery->where('name', 'like', "%{$q}%");
            }
            $tasks = $tasksQuery->get();
        }

        return view('search.index', compact('q', 'workspaces', 'projects', 'tasks'));
    }
}
