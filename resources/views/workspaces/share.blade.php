@extends('layout')

@section('title', 'Share Workspace - TaskFlow')

@section('content')
    <div class="container" style="max-width: 600px;">
        <a href="/workspaces/{{ $workspace->id }}" style="margin-bottom: 2rem; display: inline-block; color: #667eea; text-decoration: none;">← Back to Workspace</a>

        <h1>Share Workspace</h1>
        <p style="color: #666; margin-bottom: 2rem;">
            Share <strong>{{ $workspace->name }}</strong> with other users
        </p>

        @if($errors->any())
            <div class="alert alert-error" style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 1rem; border-radius: 5px; margin-bottom: 1.5rem;">
                <ul class="error-list" style="margin: 0; padding-left: 1.5rem;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="/workspaces/{{ $workspace->id }}/share" style="background: #f8f9fa; padding: 2rem; border-radius: 5px;">
            @csrf

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label for="user_ids" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #333;">Select Users to Share With</label>
                <select 
                    id="user_ids" 
                    name="user_ids[]" 
                    multiple 
                    style="width: 100%; padding: 0.75rem; border: 2px solid #e0e0e0; border-radius: 5px; font-size: 1rem; min-height: 150px;"
                    required
                >
                    @foreach($availableUsers as $user)
                        <option value="{{ $user['id'] }}" {{ in_array($user['id'], $sharedUserIds) ? 'selected' : '' }}>
                            {{ $user['name'] }} ({{ $user['email'] }})
                        </option>
                    @endforeach
                </select>
                <small style="display: block; margin-top: 0.5rem; color: #666;">Hold Ctrl (Cmd on Mac) to select multiple users. Users with access to this workspace can see all its projects and tasks.</small>
            </div>

            <div style="display: flex; gap: 1rem;">
                <button 
                    type="submit" 
                    style="padding: 0.75rem 1.5rem; background: #667eea; color: white; border: none; border-radius: 5px; font-weight: 600; cursor: pointer; font-size: 1rem;"
                >
                    Share Workspace
                </button>
                <a 
                    href="/workspaces/{{ $workspace->id }}" 
                    style="padding: 0.75rem 1.5rem; background: #6c757d; color: white; border: none; border-radius: 5px; font-weight: 600; text-decoration: none; display: inline-block; text-align: center; font-size: 1rem;"
                >
                    Cancel
                </a>
            </div>
        </form>

        <div style="margin-top: 2rem; padding-top: 2rem; border-top: 2px solid #e0e0e0;">
            <h3 style="color: #333; margin-bottom: 1rem;">Currently Shared With:</h3>
            @if($workspace->users()->count() > 0)
                <ul style="list-style: none; padding: 0; margin: 0;">
                    @foreach($workspace->users as $user)
                        <li style="padding: 0.75rem; background: white; margin-bottom: 0.5rem; border: 1px solid #e0e0e0; border-radius: 3px; display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <strong style="color: #333;">{{ $user->name }}</strong>
                                <small style="color: #666;">({{ $user->email }})</small>
                            </div>
                            <form method="POST" action="/workspaces/{{ $workspace->id }}/users/{{ $user->id }}" style="display: inline;" onsubmit="return confirm('Are you sure you want to remove this user from the workspace?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="padding: 0.5rem 1rem; background: #dc3545; color: white; border: none; border-radius: 3px; cursor: pointer; font-size: 0.875rem; font-weight: 600;">
                                    Delete
                                </button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            @else
                <p style="color: #999;">Not shared with anyone yet.</p>
            @endif
        </div>
    </div>
@endsection
