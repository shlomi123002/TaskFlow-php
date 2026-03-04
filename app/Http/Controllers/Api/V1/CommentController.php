<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Models\Comment;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * List comments for a task.
     */
    public function index(string $workspaceId, string $projectId, string $taskId): JsonResponse
    {
        $task = Task::findOrFail($taskId);

        // Load comments with the user who created them, ordered newest first
        $comments = $task->comments()->with('user')->latest()->get();

        return response()->json(['data' => $comments], 200);
    }

    /**
     * Store a new comment for the task.
     */
    public function store(StoreCommentRequest $request, string $workspaceId, string $projectId, string $taskId): JsonResponse
    {
        $task = Task::findOrFail($taskId);

        $user = $request->user();

        $comment = $task->comments()->create([
            'user_id' => $user->id,
            'comment' => $request->validated()['comment'],
        ]);

        $comment->load('user');

        return response()->json(['data' => $comment], 201);
    }

    /**
     * Update an existing comment.
     */
    public function update(StoreCommentRequest $request, string $workspaceId, string $projectId, string $taskId, string $commentId): JsonResponse
    {
        $task = Task::findOrFail($taskId);

        $comment = Comment::findOrFail($commentId);

        // Verify that the comment belongs to the task
        if ($comment->task_id != $task->id) {
            return response()->json(['message' => 'Comment does not belong to task'], 400);
        }

        // Authorization: only the creator or admin can update
        $this->authorize('update', $comment);

        $comment->comment = $request->validated()['comment'];
        $comment->save();

        $comment->load('user');

        return response()->json(['data' => $comment], 200);
    }

    /**
     * Delete comment.
     */
    public function destroy(Request $request, string $workspaceId, string $projectId, string $taskId, string $commentId): JsonResponse
    {
        $task = Task::findOrFail($taskId);

        $comment = Comment::findOrFail($commentId);

        // Verify that the comment belongs to the task
        if ($comment->task_id != $task->id) {
            return response()->json(['message' => 'Comment does not belong to task'], 400);
        }

        // Authorization: only the creator or admin can delete
        $this->authorize('delete', $comment);

        $comment->delete();

        return response()->json(null, 204);
    }
}
