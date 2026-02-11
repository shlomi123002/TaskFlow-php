<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\WorkspaceController;
use App\Http\Controllers\Api\V1\ProjectController;
use App\Http\Controllers\Api\V1\TaskController;

Route::prefix('v1')->group(function () {
    // auth
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);

        // workspaces CRUD
        Route::get('/workspaces', [WorkspaceController::class, 'index']);
        Route::post('/workspaces', [WorkspaceController::class, 'store']);
        Route::put('/workspaces/{workspaceId}', [WorkspaceController::class, 'update']);
        Route::delete('/workspaces/{workspaceId}', [WorkspaceController::class, 'destroy']);
        // prohects CRUD
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

    });
});
