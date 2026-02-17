@extends('layout')

@section('title', 'Dashboard - TaskFlow')

@section('content')
    <div class="container container-large">
        <div class="dashboard">
            <div>
                <h1 style="text-align: left;">Dashboard</h1>
                
                <div class="search-bar">
                    <form method="GET" action="/home">
                        <input 
                            type="search" 
                            name="search" 
                            placeholder="Search workspaces, projects, or tasks..." 
                            value="{{ request('search') }}"
                        >
                    </form>
                </div>
            </div>
        </div>

        <!-- Workspaces Section -->
        <div class="section">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h2 class="section-title" style="border: none; padding: 0; margin: 0;">Workspaces</h2>
                <a href="/workspaces/create" class="btn btn-primary" style="text-decoration: none; display: inline-block; padding: 0.6rem 1.5rem;">+ New Workspace</a>
            </div>
            @if(count($workspaces) > 0)
                <div class="items-grid">
                    @foreach($workspaces as $workspace)
                        <a href="/workspaces/{{ $workspace->id }}" style="text-decoration: none; color: inherit;">
                            <div class="item-card" style="cursor: pointer;">
                                <h3>{{ $workspace->name }}</h3>
                                <p style="margin-bottom: 1rem;">
                                    <strong>{{ count($workspace->projects) }}</strong> Project{{ count($workspace->projects) !== 1 ? 's' : '' }}
                                </p>
                                <div class="meta">
                                    Created: {{ $workspace->created_at->format('M d, Y') }}
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-state-icon">📁</div>
                    <p>No workspaces found. <a href="/workspaces/create" style="color: #667eea; text-decoration: none;">Create one</a> to get started!</p>
                </div>
            @endif
        </div>

        <!-- Projects Section -->
        <div class="section">
            <h2 class="section-title">Projects</h2>
            @if(count($projects) > 0)
                <div class="items-grid">
                    @foreach($projects as $project)
                        <a href="/workspaces/{{ $project->workspace->id }}/projects/{{ $project->id }}" style="text-decoration: none; color: inherit;">
                            <div class="item-card" style="cursor: pointer;">
                                <h3>{{ $project->name }}</h3>
                                <p style="margin-bottom: 1rem;">
                                    <strong>{{ count($project->tasks) }}</strong> Task{{ count($project->tasks) !== 1 ? 's' : '' }}
                                </p>
                                <div class="meta">
                                    Workspace: <em>{{ $project->workspace->name }}</em><br>
                                    Created: {{ $project->created_at->format('M d, Y') }}
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-state-icon">📊</div>
                    <p>No projects found. Create one in a workspace to get started!</p>
                </div>
            @endif
        </div>

        <!-- Tasks Section -->
        <div class="section">
            <h2 class="section-title">Tasks</h2>
            @if(count($tasks) > 0)
                <div class="items-grid">
                    @foreach($tasks as $task)
                        <a href="/workspaces/{{ $task->project->workspace->id }}/projects/{{ $task->project->id }}/tasks/{{ $task->id }}/edit" style="text-decoration: none; color: inherit;">
                            <div class="item-card" style="cursor: pointer;">
                                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.5rem;">
                                    <h3 style="flex: 1;">{{ $task->name }}</h3>
                                </div>
                                
                                @if($task->description)
                                    <p>{{ Str::limit($task->description, 80) }}</p>
                                @endif

                                <div style="margin-top: 1rem;">
                                    <span class="status-badge status-{{ $task->status ?? 'pending' }}">
                                        {{ ucfirst($task->status ?? 'pending') }}
                                    </span>
                                    @if($task->priority)
                                        <span class="status-badge priority-{{ $task->priority }}">
                                            {{ ucfirst($task->priority) }} Priority
                                        </span>
                                    @endif
                                </div>

                                <div class="meta">
                                    Project: <em>{{ $task->project->name }}</em><br>
                                    Created: {{ $task->created_at->format('M d, Y') }}
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-state-icon">✓</div>
                    <p>No tasks found. Create one in a project to get started!</p>
                </div>
            @endif
        </div>
    </div>
@endsection
