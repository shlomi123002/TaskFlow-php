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

        <div class="section">
            <div style="display: flex; justify-content: flex-start; align-items: center; gap: 1rem; margin-bottom: 1.5rem;">
                <h2 class="section-title" style="border: none; padding: 0; margin: 0;">Workspaces</h2>
                <a href="/workspaces/create" class="btn btn-primary" style="text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3); transition: all 0.3s ease; margin-top: 0; width: auto;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    New Workspace
                </a>
            </div>
            
            <div id="loading-workspaces" style="text-align: center; padding: 2rem;">
                <div class="loading-spinner" style="border: 4px solid #f3f3f3; border-top: 4px solid #667eea; border-radius: 50%; width: 40px; height: 40px; animation: spin 1s linear infinite; margin: 0 auto 1rem;"></div>
                <p style="color: #6c757d;">Loading workspaces...</p>
            </div>
            <div id="workspaces-container" class="items-grid" style="display: none;"></div>
            <div id="workspaces-empty" class="empty-state" style="display: none;">
                <div class="empty-state-icon">📁</div>
                <p>No workspaces found. <a href="/workspaces/create" style="color: #667eea; text-decoration: none;">Create one</a> to get started!</p>
            </div>
        </div>

        <!-- Projects Section -->
        <div class="section">
            <h2 class="section-title">Projects</h2>
            
            <div id="loading-projects" style="text-align: center; padding: 2rem;">
                <div class="loading-spinner" style="border: 4px solid #f3f3f3; border-top: 4px solid #667eea; border-radius: 50%; width: 40px; height: 40px; animation: spin 1s linear infinite; margin: 0 auto 1rem;"></div>
                <p style="color: #6c757d;">Loading projects...</p>
            </div>
            <div id="projects-container" class="items-grid" style="display: none;"></div>
            <div id="projects-empty" class="empty-state" style="display: none;">
                <div class="empty-state-icon">📊</div>
                <p>No projects found. Create one in a workspace to get started!</p>
            </div>
        </div>

        <!-- Tasks Section -->
        <div class="section">
            <h2 class="section-title">Tasks</h2>
            
            <div id="loading-tasks" style="text-align: center; padding: 2rem;">
                <div class="loading-spinner" style="border: 4px solid #f3f3f3; border-top: 4px solid #667eea; border-radius: 50%; width: 40px; height: 40px; animation: spin 1s linear infinite; margin: 0 auto 1rem;"></div>
                <p style="color: #6c757d;">Loading tasks...</p>
            </div>
            <div id="tasks-container" class="items-grid" style="display: none;"></div>
            <div id="tasks-empty" class="empty-state" style="display: none;">
                <div class="empty-state-icon">✓</div>
                <p>No tasks found. Create one in a project to get started!</p>
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
            // Get search parameter from URL if any
            const urlParams = new URLSearchParams(window.location.search);
            const searchStr = urlParams.has('search') ? '?search=' + encodeURIComponent(urlParams.get('search')) : '';
            
            fetch('/home' + searchStr, {
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
                // Formatting Date
                const formatDate = (dateString) => {
                    const options = { year: 'numeric', month: 'short', day: 'numeric' };
                    return new Date(dateString).toLocaleDateString('en-US', options);
                };

                // Render Workspaces
                const workspacesContainer = document.getElementById('workspaces-container');
                const loadingWorkspaces = document.getElementById('loading-workspaces');
                const workspacesEmpty = document.getElementById('workspaces-empty');
                
                loadingWorkspaces.style.display = 'none';
                
                if (data.workspaces && data.workspaces.length > 0) {
                    workspacesContainer.style.display = 'grid';
                    workspacesContainer.innerHTML = data.workspaces.map(workspace => `
                        <a href="/workspaces/${workspace.id}" style="text-decoration: none; color: inherit;">
                            <div class="item-card" style="cursor: pointer;">
                                <h3>${workspace.name}</h3>
                                <p style="margin-bottom: 1rem;">
                                    <strong>${workspace.projects ? workspace.projects.length : 0}</strong> Project(s)
                                </p>
                                <div class="meta">
                                    Created: ${formatDate(workspace.created_at)}
                                </div>
                            </div>
                        </a>
                    `).join('');
                } else {
                    workspacesEmpty.style.display = 'block';
                }

                // Render Projects
                const projectsContainer = document.getElementById('projects-container');
                const loadingProjects = document.getElementById('loading-projects');
                const projectsEmpty = document.getElementById('projects-empty');
                
                loadingProjects.style.display = 'none';
                
                if (data.projects && data.projects.length > 0) {
                    projectsContainer.style.display = 'grid';
                    projectsContainer.innerHTML = data.projects.map(project => `
                        <a href="/workspaces/${project.workspace_id}/projects/${project.id}" style="text-decoration: none; color: inherit;">
                            <div class="item-card" style="cursor: pointer;">
                                <h3>${project.name}</h3>
                                <p style="margin-bottom: 1rem;">
                                    <strong>${project.tasks ? project.tasks.length : 0}</strong> Task(s)
                                </p>
                                <div class="meta">
                                    Workspace: <em>${project.workspace ? project.workspace.name : ''}</em><br>
                                    Created: ${formatDate(project.created_at)}
                                </div>
                            </div>
                        </a>
                    `).join('');
                } else {
                    projectsEmpty.style.display = 'block';
                }

                // Render Tasks
                const tasksContainer = document.getElementById('tasks-container');
                const loadingTasks = document.getElementById('loading-tasks');
                const tasksEmpty = document.getElementById('tasks-empty');
                
                loadingTasks.style.display = 'none';
                
                if (data.tasks && data.tasks.length > 0) {
                    tasksContainer.style.display = 'grid';
                    tasksContainer.innerHTML = data.tasks.map(task => {
                        const isCompleted = task.status === 'completed';
                        const wId = task.project && task.project.workspace ? task.project.workspace.id : '';
                        const pId = task.project ? task.project.id : '';
                        const csrfToken = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '';
                        
                        let actionsHtml = '';
                        if (!isCompleted) {
                            actionsHtml = `
                                <form method="POST" action="/workspaces/${wId}/projects/${pId}/tasks/${task.id}/complete" style="display: inline;">
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
                                ${task.priority.charAt(0).toUpperCase() + task.priority.slice(1)} Priority
                            </span>`;
                        }
                        
                        return `
                        <div class="item-card" style="${isCompleted ? 'opacity: 0.75;' : ''}">
                            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.5rem;">
                                <h3 style="flex: 1; ${isCompleted ? 'text-decoration: line-through; color: #888;' : ''}">${task.name}</h3>
                            </div>

                            <div style="margin-top: 1rem;">
                                <span class="status-badge status-${task.status || 'pending'}">
                                    ${(task.status || 'pending').charAt(0).toUpperCase() + (task.status || 'pending').slice(1)}
                                </span>
                                ${priorityBadge}
                            </div>

                            <div class="meta" style="margin-top: 0.75rem;">
                                Project: <em>${task.project ? task.project.name : ''}</em><br>
                                Created: ${formatDate(task.created_at)}
                            </div>

                            <div style="display: flex; gap: 0.5rem; margin-top: 1rem; flex-wrap: wrap;">
                                <a href="/workspaces/${wId}/projects/${pId}/tasks/${task.id}/edit"
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
                document.getElementById('loading-workspaces').innerHTML = '<p style="color: red;">Failed to load data.</p>';
                document.getElementById('loading-projects').innerHTML = '<p style="color: red;">Failed to load data.</p>';
                document.getElementById('loading-tasks').innerHTML = '<p style="color: red;">Failed to load data.</p>';
            });
        });
    </script>
@endsection
