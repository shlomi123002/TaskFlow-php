@extends('layout')

@section('title', $workspace->name . ' - TaskFlow')

@section('content')
    <div class="container container-large">
        <a href="/home" style="margin-bottom: 2rem; display: inline-block; color: #667eea; text-decoration: none;">← Back to Dashboard</a>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h1>{{ $workspace->name }}</h1>
            <div style="display: flex; gap: 1rem;">
                <a href="/workspaces/{{ $workspace->id }}/projects/create" class="btn btn-primary" style="text-decoration: none; display: inline-block;">+ New Project</a>
                <a href="/workspaces/{{ $workspace->id }}/edit" class="btn btn-secondary" style="text-decoration: none; display: inline-block; background: #667eea; color: white;">Edit</a>
                <form method="POST" action="/workspaces/{{ $workspace->id }}" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-secondary" style="background: #dc3545; color: white; cursor: pointer; border: none;" onclick="return confirm('Are you sure you want to delete this workspace?');">Delete</button>
                </form>
            </div>
        </div>

        <div class="section">
            <h2 class="section-title">Projects</h2>
            @if(count($projects) > 0)
                <div class="items-grid">
                    @foreach($projects as $project)
                        <a href="/workspaces/{{ $workspace->id }}/projects/{{ $project->id }}" style="text-decoration: none; color: inherit;">
                            <div class="item-card" style="cursor: pointer;">
                                <h3>{{ $project->name }}</h3>
                                <p style="margin-bottom: 1rem;">
                                    <strong>{{ count($project->tasks) }}</strong> Task{{ count($project->tasks) !== 1 ? 's' : '' }}
                                </p>
                                <div class="meta">
                                    Created: {{ $project->created_at->format('M d, Y') }}
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-state-icon">📊</div>
                    <p>No projects in this workspace. <a href="/workspaces/{{ $workspace->id }}/projects/create" style="color: #667eea; text-decoration: none;">Create one</a></p>
                </div>
            @endif
        </div>
    </div>
@endsection
