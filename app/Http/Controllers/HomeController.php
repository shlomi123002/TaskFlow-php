<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Workspace;
use App\Models\Project;
use App\Models\Task;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $search = $request->query('search');

        // Get user's workspaces with their projects
        $workspaces = $user->workspaces()
            ->with('projects')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get user's projects
        $projects = Project::whereIn('workspace_id', $user->workspaces()->pluck('id'))
            ->with('workspace', 'tasks')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get user's tasks
        $tasks = Task::whereIn('project_id', Project::whereIn('workspace_id', $user->workspaces()->pluck('id'))->pluck('id'))
            ->with('project', 'project.workspace')
            ->orderBy('created_at', 'desc')
            ->get();

        // Apply search filter if provided
        if ($search) {
            $search = strtolower($search);

            $workspaces = $workspaces->filter(function ($workspace) use ($search) {
                return stripos($workspace->name, $search) !== false;
            });

            $projects = $projects->filter(function ($project) use ($search) {
                return stripos($project->name, $search) !== false;
            });

            $tasks = $tasks->filter(function ($task) use ($search) {
                return stripos($task->name, $search) !== false || 
                       stripos($task->description ?? '', $search) !== false;
            });
        }

        return view('home', [
            'workspaces' => $workspaces,
            'projects' => $projects,
            'tasks' => $tasks,
        ]);
    }
}
