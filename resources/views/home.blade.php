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
                            placeholder="Search workspace" 
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


            })
            .catch(error => {
                console.error('Error fetching data:', error);
                document.getElementById('loading-workspaces').innerHTML = '<p style="color: red;">Failed to load data.</p>';
            });
        });
    </script>
@endsection
