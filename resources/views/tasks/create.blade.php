@extends('layout')

@section('title', 'Create Task - TaskFlow')

@section('content')
    <div class="container" style="max-width: 600px;">
        <a href="/workspaces/{{ $workspace->id }}/projects/{{ $project->id }}" style="margin-bottom: 2rem; display: inline-block; color: #667eea; text-decoration: none;">← Back to Project</a>

        <h1>Create New Task</h1>
        <p style="color: #666; margin-bottom: 2rem;">
            In: <strong>{{ $workspace->name }}</strong> → <strong>{{ $project->name }}</strong>
        </p>

        @if($errors->any())
            <div class="alert alert-error">
                <ul class="error-list">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="/workspaces/{{ $workspace->id }}/projects/{{ $project->id }}/tasks">
            @csrf

            <div class="form-group">
                <label for="name">Task Name</label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    value="{{ old('name') }}" 
                    placeholder="Enter task name"
                    required
                >
                @error('name')
                    <small style="color: #721c24;">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea 
                    id="description" 
                    name="description" 
                    placeholder="Enter task description (optional)"
                    rows="4"
                    style="width: 100%; padding: 0.75rem; border: 2px solid #e0e0e0; border-radius: 5px; font-size: 1rem;"
                >{{ old('description') }}</textarea>
                @error('description')
                    <small style="color: #721c24;">{{ $message }}</small>
                @enderror
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label for="status">Status</label>
                    <select 
                        id="status" 
                        name="status"
                        style="width: 100%; padding: 0.75rem; border: 2px solid #e0e0e0; border-radius: 5px; font-size: 1rem;"
                    >
                        <option value="pending" {{ old('status', 'pending') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="completed" {{ old('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                    @error('status')
                        <small style="color: #721c24;">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="priority">Priority</label>
                    <select 
                        id="priority" 
                        name="priority"
                        style="width: 100%; padding: 0.75rem; border: 2px solid #e0e0e0; border-radius: 5px; font-size: 1rem;"
                    >
                        <option value="low" {{ old('priority', 'normal') === 'low' ? 'selected' : '' }}>Low</option>
                        <option value="normal" {{ old('priority', 'normal') === 'normal' ? 'selected' : '' }}>Normal</option>
                        <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>High</option>
                    </select>
                    @error('priority')
                        <small style="color: #721c24;">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                <button type="submit" class="btn btn-primary" style="flex: 1;">Create Task</button>
                <a href="/workspaces/{{ $workspace->id }}/projects/{{ $project->id }}" class="btn btn-secondary" style="flex: 1; text-align: center; text-decoration: none; background: #e0e0e0; color: #333;">Cancel</a>
            </div>
        </form>
    </div>
@endsection
