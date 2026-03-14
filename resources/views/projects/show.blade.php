@extends('layout')

@section('title', $project->name . ' - TaskFlow')

@section('content')
    <div class="container container-large">
        <a href="/workspaces/{{ $workspace->id }}" style="margin-bottom: 2rem; display: inline-block; color: #667eea; text-decoration: none;">← Back to Workspace</a>

        <div>
            <p style="color: #666; margin-bottom: 0.5rem;">{{ $workspace->name }}</p>
            <div style="display: flex; justify-content: flex-start; align-items: center; gap: 1rem; margin-bottom: 2rem;">
                <h1 style="margin: 0; padding: 0;">{{ $project->name }}</h1>
                
                <a href="/workspaces/{{ $workspace->id }}/projects/{{ $project->id }}/edit" class="btn btn-secondary" style="text-decoration: none; display: inline-block; background: #667eea; color: white;">Edit</a>
                
                <form method="POST" action="/workspaces/{{ $workspace->id }}/projects/{{ $project->id }}" style="display: inline; margin: 0;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-secondary" style="background: #dc3545; color: white; cursor: pointer; border: none; padding: 0.75rem 1.5rem;" onclick="return confirm('Are you sure you want to delete this project?');">Delete</button>
                </form>
            </div>
        </div>

        @if(session('success'))
            <div style="background: #d4edda; color: #155724; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border: 1px solid #c3e6cb;">
                {{ session('success') }}
            </div>
        @endif

        <div style="background: #f8f9fa; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border: 1px solid #e9ecef;">
            <form method="GET" action="/workspaces/{{ $workspace->id }}/projects/{{ $project->id }}" style="display: flex; gap: 1rem; align-items: flex-end; flex-wrap: wrap;">
                
                <div style="flex: 2; min-width: 200px;">
                    <label for="search" style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.25rem;">Search Task</label>
                    <input 
                        type="search" 
                        name="search" 
                        id="search"
                        placeholder="Search tasks..." 
                        value="{{ request('search') }}"
                        class="form-control"
                        style="width: 100%; padding: 0.5rem; border: 1px solid #ced4da; border-radius: 4px;"
                    >
                </div>
                <div style="flex: 1; min-width: 150px;">
                    <label for="status" style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.25rem;">Status Filter</label>
                    <select name="status" id="status" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #ced4da; border-radius: 4px;">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>
                
                <div style="flex: 1; min-width: 150px;">
                    <label for="priority" style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.25rem;">Priority Filter</label>
                    <select name="priority" id="priority" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #ced4da; border-radius: 4px;">
                        <option value="">All Priorities</option>
                        <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low</option>
                        <option value="normal" {{ request('priority') === 'normal' ? 'selected' : '' }}>Normal</option>
                        <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High</option>
                    </select>
                </div>
                
                <div style="display: flex; gap: 0.5rem;">
                    <button type="submit" class="btn btn-primary" style="padding: 0.5rem 1rem;">Apply Filters</button>
                    @if(request()->has('status') || request()->has('priority') || request()->has('search'))
                        <a href="/workspaces/{{ $workspace->id }}/projects/{{ $project->id }}" class="btn btn-secondary" style="padding: 0.5rem 1rem; text-decoration: none;">Clear</a>
                    @endif
                </div>
            </form>
        </div>

        <div class="section">
            <div style="display: flex; justify-content: flex-start; align-items: center; gap: 1rem; margin-bottom: 1.5rem;">
                <h2 class="section-title" style="border: none; padding: 0; margin: 0;">Tasks</h2>
                <a href="/workspaces/{{ $workspace->id }}/projects/{{ $project->id }}/tasks/create" class="btn btn-primary" style="text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; border-radius: 8px; font-weight: 600; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3); transition: all 0.3s ease; margin-top: 0; width: auto; font-size: 0.9rem;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    New Task
                </a>
            </div>
            
            <div id="loading-tasks" style="text-align: center; padding: 2rem;">
                <div class="loading-spinner" style="border: 4px solid #f3f3f3; border-top: 4px solid #667eea; border-radius: 50%; width: 40px; height: 40px; animation: spin 1s linear infinite; margin: 0 auto 1rem;"></div>
                <p style="color: #6c757d;">Loading tasks...</p>
            </div>
            
            <div id="tasks-container" class="items-grid" style="display: none;"></div>
            
            <div id="tasks-empty" class="empty-state" style="display: none;">
                <div class="empty-state-icon">✓</div>
                <p>No tasks in this project. <a href="/workspaces/{{ $workspace->id }}/projects/{{ $project->id }}/tasks/create" style="color: #667eea; text-decoration: none;">Create one</a></p>
            </div>
        </div>
    </div>
    
    <style>
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const statusParams = urlParams.has('status') ? 'status=' + encodeURIComponent(urlParams.get('status')) : '';
            const priorityParams = urlParams.has('priority') ? 'priority=' + encodeURIComponent(urlParams.get('priority')) : '';
            const searchParams = urlParams.has('search') ? 'search=' + encodeURIComponent(urlParams.get('search')) : '';
            
            let queryParams = [];
            if (statusParams) queryParams.push(statusParams);
            if (priorityParams) queryParams.push(priorityParams);
            if (searchParams) queryParams.push(searchParams);
            let queryString = queryParams.length > 0 ? '?' + queryParams.join('&') : '';
            
            fetch('/workspaces/{{ $workspace->id }}/projects/{{ $project->id }}' + queryString, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                const formatDate = (dateString) => {
                    const options = { year: 'numeric', month: 'short', day: 'numeric' };
                    return new Date(dateString).toLocaleDateString('en-US', options);
                };

                const tasksContainer = document.getElementById('tasks-container');
                const loadingTasks = document.getElementById('loading-tasks');
                const tasksEmpty = document.getElementById('tasks-empty');
                
                loadingTasks.style.display = 'none';
                
                if (data.tasks && data.tasks.length > 0) {
                    tasksContainer.style.display = 'grid';
                    tasksContainer.innerHTML = data.tasks.map(task => {
                        const isCompleted = task.status === 'completed';
                        const csrfToken = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '';
                        
                        let actionsHtml = '';
                        if (!isCompleted) {
                            actionsHtml = `
                                <form method="POST" action="/workspaces/{{ $workspace->id }}/projects/{{ $project->id }}/tasks/${task.id}/complete" style="display: inline;">
                                    <input type="hidden" name="_token" value="${csrfToken}">
                                    <button type="submit"
                                            style="padding: 0.4rem 0.9rem; background: #28a745; color: white; border: none; border-radius: 5px; font-size: 0.85rem; font-weight: 600; cursor: pointer;">
                                        ✓ Mark Complete
                                    </button>
                                </form>
                            `;
                        } else {
                            actionsHtml = `
                                <span style="padding: 0.4rem 0.9rem; background: #e9ecef; color: #6c757d; border-radius: 5px; font-size: 0.85rem; font-weight: 600;">
                                    ✓ Completed
                                </span>
                            `;
                        }

                        let priorityBadge = '';
                        if (task.priority) {
                            priorityBadge = `<span class="status-badge priority-${task.priority}">
                                ${task.priority.charAt(0).toUpperCase() + task.priority.slice(1)}
                            </span>`;
                        }
                        
                        let descriptionHtml = '';
                        if (task.description) {
                            descriptionHtml = `<p>${task.description.length > 80 ? task.description.substring(0, 80) + '...' : task.description}</p>`;
                        }
                        
                        let userHtml = '';
                        if (task.user && task.user.name) {
                            userHtml = `<div style="margin-top: 0.5rem; font-size: 0.9rem; color: #667eea; font-weight: 500;">
                                👤 ${task.user.name}
                            </div>`;
                        }
                        
                        return `
                        <div class="item-card" style="${isCompleted ? 'opacity: 0.8;' : ''}">
                            <div style="margin-bottom: 0.5rem;">
                                <h3 style="${isCompleted ? 'text-decoration: line-through; color: #888;' : ''}">${task.name}</h3>
                            </div>

                            ${descriptionHtml}
                            
                            ${userHtml}

                            <div style="margin-top: 1rem;">
                                <span class="status-badge status-${task.status || 'pending'}">
                                    ${(task.status || 'pending').charAt(0).toUpperCase() + (task.status || 'pending').slice(1)}
                                </span>
                                ${priorityBadge}
                            </div>

                            <div class="meta" style="margin-top: 0.75rem;">
                                Created: ${formatDate(task.created_at)}
                            </div>

                            <div style="display: flex; gap: 0.5rem; margin-top: 1rem; flex-wrap: wrap; align-items: center;">
                                <a href="/workspaces/{{ $workspace->id }}/projects/{{ $project->id }}/tasks/${task.id}/edit"
                                   style="padding: 0.4rem 0.9rem; background: #667eea; color: white; border-radius: 5px; text-decoration: none; font-size: 0.85rem; font-weight: 600;">
                                    ✏️ Edit
                                </a>
                                ${actionsHtml}
                            </div>
                        </div>
                        `;
                    }).join('');
                } else {
                    tasksEmpty.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error fetching data:', error);
                document.getElementById('loading-tasks').innerHTML = '<p style="color: red;">Failed to load data.</p>';
            });
        });
    </script>
@endsection
