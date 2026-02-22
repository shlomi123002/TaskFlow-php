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
            
            <div id="loading-projects" style="text-align: center; padding: 2rem;">
                <div class="loading-spinner" style="border: 4px solid #f3f3f3; border-top: 4px solid #667eea; border-radius: 50%; width: 40px; height: 40px; animation: spin 1s linear infinite; margin: 0 auto 1rem;"></div>
                <p style="color: #6c757d;">Loading projects...</p>
            </div>
            
            <div id="projects-container" class="items-grid" style="display: none;"></div>
            
            <div id="projects-empty" class="empty-state" style="display: none;">
                <div class="empty-state-icon">📊</div>
                <p>No projects in this workspace. <a href="/workspaces/{{ $workspace->id }}/projects/create" style="color: #667eea; text-decoration: none;">Create one</a></p>
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
            fetch('/workspaces/{{ $workspace->id }}', {
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

                const projectsContainer = document.getElementById('projects-container');
                const loadingProjects = document.getElementById('loading-projects');
                const projectsEmpty = document.getElementById('projects-empty');
                
                loadingProjects.style.display = 'none';
                
                if (data.projects && data.projects.length > 0) {
                    projectsContainer.style.display = 'grid';
                    projectsContainer.innerHTML = data.projects.map(project => `
                        <a href="/workspaces/{{ $workspace->id }}/projects/${project.id}" style="text-decoration: none; color: inherit;">
                            <div class="item-card" style="cursor: pointer;">
                                <h3>${project.name}</h3>
                                <p style="margin-bottom: 1rem;">
                                    <strong>${project.tasks ? project.tasks.length : 0}</strong> Task(s)
                                </p>
                                <div class="meta">
                                    Created: ${formatDate(project.created_at)}
                                </div>
                            </div>
                        </a>
                    `).join('');
                } else {
                    projectsEmpty.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error fetching data:', error);
                document.getElementById('loading-projects').innerHTML = '<p style="color: red;">Failed to load data.</p>';
            });
        });
    </script>
@endsection
