@extends('layout')

@section('title', $project->name . ' - TaskFlow')

@section('content')
    <div class="container container-large">
        <a href="/workspaces/{{ $workspace->id }}" style="margin-bottom: 2rem; display: inline-block; color: #667eea; text-decoration: none;">← Back to Workspace</a>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <div>
                <p style="color: #666; margin-bottom: 0.5rem;">{{ $workspace->name }}</p>
                <h1 style="margin-bottom: 0;">{{ $project->name }}</h1>
            </div>
            <div style="display: flex; gap: 1rem;">
                <a href="/workspaces/{{ $workspace->id }}/projects/{{ $project->id }}/tasks/create" class="btn btn-primary" style="text-decoration: none; display: inline-block;">+ New Task</a>
                <a href="/workspaces/{{ $workspace->id }}/projects/{{ $project->id }}/edit" class="btn btn-secondary" style="text-decoration: none; display: inline-block; background: #667eea; color: white;">Edit</a>
                <form method="POST" action="/workspaces/{{ $workspace->id }}/projects/{{ $project->id }}" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-secondary" style="background: #dc3545; color: white; cursor: pointer; border: none;" onclick="return confirm('Are you sure you want to delete this project?');">Delete</button>
                </form>
            </div>
        </div>

        <div class="section">
            <h2 class="section-title">Tasks</h2>
            @if(count($tasks) > 0)
                <div class="items-grid">
                    @foreach($tasks as $task)
                        <a href="/workspaces/{{ $workspace->id }}/projects/{{ $project->id }}/tasks/{{ $task->id }}/edit" style="text-decoration: none; color: inherit;">
                            <div class="item-card" style="cursor: pointer;">
                                <h3>{{ $task->name }}</h3>
                                
                                @if($task->description)
                                    <p>{{ Str::limit($task->description, 80) }}</p>
                                @endif

                                <div style="margin-top: 1rem;">
                                    <span class="status-badge status-{{ $task->status ?? 'pending' }}">
                                        {{ ucfirst($task->status ?? 'pending') }}
                                    </span>
                                    @if($task->priority)
                                        <span class="status-badge priority-{{ $task->priority }}">
                                            {{ ucfirst($task->priority) }}
                                        </span>
                                    @endif
                                </div>

                                <div class="meta">
                                    Created: {{ $task->created_at->format('M d, Y') }}
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-state-icon">✓</div>
                    <p>No tasks in this project. <a href="/workspaces/{{ $workspace->id }}/projects/{{ $project->id }}/tasks/create" style="color: #667eea; text-decoration: none;">Create one</a></p>
                </div>
            @endif
        </div>
    </div>
@endsection
