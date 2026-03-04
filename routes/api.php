<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\WorkspaceController;
use App\Http\Controllers\Api\V1\ProjectController;
use App\Http\Controllers\Api\V1\TaskController;
use App\Http\Controllers\Api\V1\CommentController;

Route::prefix('v1')->group(function () {
    // auth
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware(['auth:sanctum,web'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);

        // workspaces CRUD
        Route::get('/workspaces', [WorkspaceController::class, 'index']);
        Route::post('/workspaces', [WorkspaceController::class, 'store']);
        Route::put('/workspaces/{workspaceId}', [WorkspaceController::class, 'update']);
        Route::delete('/workspaces/{workspaceId}', [WorkspaceController::class, 'destroy']);
        Route::post('/workspaces/{workspaceId}/share', [WorkspaceController::class, 'share']);
        Route::get('/workspaces/available-users', [WorkspaceController::class, 'availableUsers']);
        // projects CRUD
        Route::get('/workspaces/{workspaceId}/projects', [ProjectController::class, 'index']);
        Route::get('/projects', [ProjectController::class, 'all']);
        Route::post('/workspaces/{workspaceId}/projects', [ProjectController::class, 'store']);
        Route::put('/workspaces/{workspaceId}/projects/{projectId}', [ProjectController::class, 'update']);
        Route::delete('/workspaces/{workspaceId}/projects/{projectId}', [ProjectController::class, 'destroy']);
        // tasks CRUD
        Route::get('/workspaces/{workspaceId}/projects/{projectId}/tasks', [TaskController::class, 'index']);
        Route::get('/tasks', [TaskController::class, 'all']);
        Route::post('/workspaces/{workspaceId}/projects/{projectId}/tasks', [TaskController::class, 'store']);
        Route::put('/workspaces/{workspaceId}/projects/{projectId}/tasks/{taskId}', [TaskController::class, 'update']);
        Route::delete('/workspaces/{workspaceId}/projects/{projectId}/tasks/{taskId}', [TaskController::class, 'destroy']);
        // comments CRUD
        Route::get('/workspaces/{workspaceId}/projects/{projectId}/tasks/{taskId}/comments', [CommentController::class, 'index']);
        Route::post('/workspaces/{workspaceId}/projects/{projectId}/tasks/{taskId}/comments', [CommentController::class, 'store']);
        Route::put('/workspaces/{workspaceId}/projects/{projectId}/tasks/{taskId}/comments/{commentId}', [CommentController::class, 'update']);
        Route::delete('/workspaces/{workspaceId}/projects/{projectId}/tasks/{taskId}/comments/{commentId}', [CommentController::class, 'destroy']);

    });
});

