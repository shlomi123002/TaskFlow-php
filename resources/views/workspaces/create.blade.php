@extends('layout')

@section('title', 'Create Workspace - TaskFlow')

@section('content')
    <div class="container" style="max-width: 600px;">
        <a href="/home" style="margin-bottom: 2rem; display: inline-block; color: #667eea; text-decoration: none;">← Back to Dashboard</a>

        <h1>Create New Workspace</h1>

        @if($errors->any())
            <div class="alert alert-error">
                <ul class="error-list">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="/workspaces">
            @csrf

            <div class="form-group">
                <label for="name">Workspace Name</label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    value="{{ old('name') }}" 
                    placeholder="Enter workspace name"
                    required
                >
                @error('name')
                    <small style="color: #721c24;">{{ $message }}</small>
                @enderror
            </div>

            <div style="display: flex; gap: 1rem;">
                <button type="submit" class="btn btn-primary" style="flex: 1;">Create Workspace</button>
                <a href="/home" class="btn btn-secondary" style="flex: 1; text-align: center; text-decoration: none; background: #e0e0e0; color: #333;">Cancel</a>
            </div>
        </form>
    </div>
@endsection
