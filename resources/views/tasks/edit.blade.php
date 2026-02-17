@extends('layout')

@section('title', 'Edit Task - TaskFlow')

@section('content')
    <div class="container" style="max-width: 600px;">
        <a href="/workspaces/{{ $workspace->id }}/projects/{{ $project->id }}" style="margin-bottom: 2rem; display: inline-block; color: #667eea; text-decoration: none;">← Back to Project</a>

        <h1>Edit Task</h1>
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

        <form method="POST" action="/workspaces/{{ $workspace->id }}/projects/{{ $project->id }}/tasks/{{ $task->id }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="name">Task Name</label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    value="{{ old('name', $task->name) }}" 
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
                    placeholder="Enter task description"
                    rows="4"
                    style="width: 100%; padding: 0.75rem; border: 2px solid #e0e0e0; border-radius: 5px; font-size: 1rem;"
                >{{ old('description', $task->description) }}</textarea>
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
                        <option value="pending" {{ old('status', $task->status) === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="completed" {{ old('status', $task->status) === 'completed' ? 'selected' : '' }}>Completed</option>
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
                        <option value="low" {{ old('priority', $task->priority) === 'low' ? 'selected' : '' }}>Low</option>
                        <option value="normal" {{ old('priority', $task->priority) === 'normal' ? 'selected' : '' }}>Normal</option>
                        <option value="high" {{ old('priority', $task->priority) === 'high' ? 'selected' : '' }}>High</option>
                    </select>
                    @error('priority')
                        <small style="color: #721c24;">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                <button type="submit" class="btn btn-primary" style="flex: 1;">Update Task</button>
                <form method="POST" action="/workspaces/{{ $workspace->id }}/projects/{{ $project->id }}/tasks/{{ $task->id }}" style="flex: 1;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="width: 100%; background: #dc3545; color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 5px; font-weight: 600; cursor: pointer;" onclick="return confirm('Are you sure you want to delete this task?');">Delete Task</button>
                </form>
            </div>
        </form>
    </div>
@endsection
