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

        <form id="update-form" method="POST" action="/workspaces/{{ $workspace->id }}/projects/{{ $project->id }}/tasks/{{ $task->id }}">
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

        </form>

        <div style="display: flex; gap: 1rem; margin-top: 1.5rem; flex-wrap: wrap;">
            <button type="submit" form="update-form" class="btn btn-primary" style="flex: 1;">Update Task</button>
            @if($task->status !== 'completed')
                <button type="button"
                        onclick="document.getElementById('complete-form').submit()"
                        style="flex: 1; padding: 0.75rem 1.5rem; background: #28a745; color: white; border: none; border-radius: 5px; font-weight: 600; cursor: pointer; font-size: 1rem;">
                    ✓ Mark as Complete
                </button>
            @else
                <span style="flex: 1; padding: 0.75rem 1.5rem; background: #d4edda; color: #155724; border-radius: 5px; font-weight: 600; font-size: 1rem; text-align: center;">
                    ✓ Already Completed
                </span>
            @endif
            <form method="POST" action="/workspaces/{{ $workspace->id }}/projects/{{ $project->id }}/tasks/{{ $task->id }}" style="flex: 1;">
                @csrf
                @method('DELETE')
                <button type="submit" style="width: 100%; background: #dc3545; color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 5px; font-weight: 600; cursor: pointer;" onclick="return confirm('Are you sure you want to delete this task?');">Delete Task</button>
            </form>
        </div>

        @if($task->status !== 'completed')
            <form id="complete-form" method="POST" action="/workspaces/{{ $workspace->id }}/projects/{{ $project->id }}/tasks/{{ $task->id }}/complete" style="display: none;">
                @csrf
            </form>
        @endif

        <!-- Comments Section -->
        <div style="margin-top: 3rem; padding-top: 2rem; border-top: 2px solid #e0e0e0;">
            <h2 style="margin-bottom: 1.5rem; color: #333;">Comments</h2>

            <!-- Comments List -->
            <div id="comments-container" style="margin-bottom: 2rem; max-height: 400px; overflow-y: auto;">
                <div id="loading-comments" style="color: #666; text-align: center; padding: 2rem;">Loading comments...</div>
            </div>

            <!-- Add Comment Form -->
            <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 5px;">
                <h3 style="margin-top: 0; margin-bottom: 1rem; color: #333; font-size: 1rem;">Add a Comment</h3>
                <form id="add-comment-form" style="display: flex; flex-direction: column; gap: 1rem;">
                    <textarea 
                        id="comment-text" 
                        placeholder="Write your comment here..." 
                        rows="3"
                        style="width: 100%; padding: 0.75rem; border: 2px solid #e0e0e0; border-radius: 5px; font-size: 1rem; font-family: inherit; resize: vertical;"
                    ></textarea>
                    <button 
                        type="submit" 
                        id="submit-comment-btn"
                        style="padding: 0.75rem 1.5rem; background: #667eea; color: white; border: none; border-radius: 5px; font-weight: 600; cursor: pointer; font-size: 1rem; align-self: flex-start;"
                    >
                        Post Comment
                    </button>
                </form>
                <div id="comment-error" style="color: #dc3545; margin-top: 0.5rem; display: none;"></div>
            </div>
        </div>
    </div>

    <script>
        const taskId = "{{ $task->id }}";
        const projectId = "{{ $project->id }}";
        const workspaceId = "{{ $workspace->id }}";

        // Load comments on page load
        document.addEventListener('DOMContentLoaded', loadComments);

        // Handle comment form submission
        document.getElementById('add-comment-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            await addComment();
        });

        async function loadComments() {
            try {
                const response = await fetch(`/api/v1/workspaces/${workspaceId}/projects/${projectId}/tasks/${taskId}/comments`, {
                    credentials: 'include',
                    headers: {
                        'Accept': 'application/json',
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                const comments = data.data || [];

                const container = document.getElementById('comments-container');
                
                if (comments.length === 0) {
                    container.innerHTML = '<p style="color: #999; text-align: center; padding: 2rem;">No comments yet. Be the first to comment!</p>';
                    return;
                }

                container.innerHTML = comments.map(comment => `
                    <div style="background: white; padding: 1rem; margin-bottom: 1rem; border: 1px solid #e0e0e0; border-radius: 5px;">
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.5rem;">
                            <strong style="color: #333;">${comment.user.name}</strong>
                            <div style="display: flex; gap: 0.5rem; align-items: center;">
                                <button class="edit-btn" data-id="${comment.id}" style="padding: 0.3rem 0.6rem; background: #667eea; color: white; border: none; border-radius: 3px; cursor: pointer; font-size: 0.85rem;">Edit</button>
                                <button class="delete-btn" data-id="${comment.id}" style="padding: 0.3rem 0.6rem; background: #dc3545; color: white; border: none; border-radius: 3px; cursor: pointer; font-size: 0.85rem;">Delete</button>
                                <small style="color: #999; white-space: nowrap;">${new Date(comment.created_at).toLocaleDateString()}</small>
                            </div>
                        </div>
                        <p id="comment-text-${comment.id}" style="margin: 0.5rem 0; color: #666; word-wrap: break-word;">${escapeHtml(comment.comment)}</p>
                        <div id="edit-form-${comment.id}" style="display: none; margin-top: 0.5rem;">
                            <textarea id="edit-textarea-${comment.id}" style="width: 100%; padding: 0.5rem; border: 2px solid #e0e0e0; border-radius: 3px; font-family: inherit; resize: vertical;" rows="2">${escapeHtml(comment.comment)}</textarea>
                            <div style="display: flex; gap: 0.5rem; margin-top: 0.5rem;">
                                <button class="save-btn" data-id="${comment.id}" style="padding: 0.5rem 1rem; background: #28a745; color: white; border: none; border-radius: 3px; cursor: pointer; font-size: 0.9rem;">Save</button>
                                <button class="cancel-btn" data-id="${comment.id}" style="padding: 0.5rem 1rem; background: #6c757d; color: white; border: none; border-radius: 3px; cursor: pointer; font-size: 0.9rem;">Cancel</button>
                            </div>
                        </div>
                        <small style="color: #999;">
                            ${comment.updated_at !== comment.created_at ? '(edited)' : ''}
                        </small>
                    </div>
                `).join('');

                // Attach event listeners
                document.querySelectorAll('.edit-btn').forEach(btn => {
                    btn.addEventListener('click', (e) => showEditForm(e.target.dataset.id));
                });
                document.querySelectorAll('.delete-btn').forEach(btn => {
                    btn.addEventListener('click', (e) => deleteComment(e.target.dataset.id));
                });
                document.querySelectorAll('.save-btn').forEach(btn => {
                    btn.addEventListener('click', (e) => saveComment(e.target.dataset.id));
                });
                document.querySelectorAll('.cancel-btn').forEach(btn => {
                    btn.addEventListener('click', (e) => cancelEdit(e.target.dataset.id));
                });
            } catch (error) {
                console.error('Error loading comments:', error);
                document.getElementById('comments-container').innerHTML = '<p style="color: #dc3545; text-align: center; padding: 2rem;">Failed to load comments</p>';
            }
        }

        async function addComment() {
            const commentText = document.getElementById('comment-text').value.trim();
            const errorDiv = document.getElementById('comment-error');
            const submitBtn = document.getElementById('submit-comment-btn');

            errorDiv.style.display = 'none';
            errorDiv.textContent = '';

            if (!commentText) {
                errorDiv.textContent = 'Comment cannot be empty';
                errorDiv.style.display = 'block';
                return;
            }

            submitBtn.disabled = true;
            submitBtn.textContent = 'Posting...';

            try {
                const response = await fetch(`/api/v1/workspaces/${workspaceId}/projects/${projectId}/tasks/${taskId}/comments`, {
                    method: 'POST',
                    credentials: 'include',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        comment: commentText
                    })
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.message || 'Failed to post comment');
                }

                document.getElementById('comment-text').value = '';
                await loadComments();
            } catch (error) {
                console.error('Error adding comment:', error);
                errorDiv.textContent = error.message || 'Failed to post comment';
                errorDiv.style.display = 'block';
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Post Comment';
            }
        }

        function showEditForm(commentId) {
            document.getElementById(`comment-text-${commentId}`).style.display = 'none';
            document.getElementById(`edit-form-${commentId}`).style.display = 'block';
        }

        function cancelEdit(commentId) {
            document.getElementById(`comment-text-${commentId}`).style.display = 'block';
            document.getElementById(`edit-form-${commentId}`).style.display = 'none';
        }

        async function saveComment(commentId) {
            const newText = document.getElementById(`edit-textarea-${commentId}`).value.trim();

            if (!newText) {
                alert('Comment cannot be empty');
                return;
            }

            try {
                const response = await fetch(`/api/v1/workspaces/${workspaceId}/projects/${projectId}/tasks/${taskId}/comments/${commentId}`, {
                    method: 'PUT',
                    credentials: 'include',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        comment: newText
                    })
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.message || 'Failed to update comment');
                }

                await loadComments();
            } catch (error) {
                console.error('Error updating comment:', error);
                alert(error.message || 'Failed to update comment');
            }
        }

        async function deleteComment(commentId) {
            if (!confirm('Are you sure you want to delete this comment?')) {
                return;
            }

            try {
                const response = await fetch(`/api/v1/workspaces/${workspaceId}/projects/${projectId}/tasks/${taskId}/comments/${commentId}`, {
                    method: 'DELETE',
                    credentials: 'include',
                    headers: {
                        'Accept': 'application/json',
                    }
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.message || 'Failed to delete comment');
                }

                await loadComments();
            } catch (error) {
                console.error('Error deleting comment:', error);
                alert(error.message || 'Failed to delete comment');
            }
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    </script>
@endsection
