<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrontendAuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\WorkspaceWebController;
use App\Http\Controllers\ProjectWebController;
use App\Http\Controllers\TaskWebController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/register', [FrontendAuthController::class, 'showRegister']);
Route::post('/register', [FrontendAuthController::class, 'register']);

Route::get('/login', [FrontendAuthController::class, 'showLogin']);
Route::post('/login', [FrontendAuthController::class, 'login']);

Route::post('/logout', [FrontendAuthController::class, 'logout']);

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/tasks', [TaskWebController::class, 'index'])->name('tasks.index');
    
    // Workspace CRUD
    Route::get('/workspaces/create', [WorkspaceWebController::class, 'create'])->name('workspaces.create');
    Route::post('/workspaces', [WorkspaceWebController::class, 'store'])->name('workspaces.store');
    Route::get('/workspaces/{workspaceId}', [WorkspaceWebController::class, 'show'])->name('workspaces.show');
    Route::get('/workspaces/{workspaceId}/edit', [WorkspaceWebController::class, 'edit'])->name('workspaces.edit');
    Route::put('/workspaces/{workspaceId}', [WorkspaceWebController::class, 'update'])->name('workspaces.update');
    Route::delete('/workspaces/{workspaceId}', [WorkspaceWebController::class, 'destroy'])->name('workspaces.destroy');
    Route::get('/workspaces/{workspaceId}/share', [WorkspaceWebController::class, 'share'])->name('workspaces.share');
    Route::post('/workspaces/{workspaceId}/share', [WorkspaceWebController::class, 'storeShare'])->name('workspaces.storeShare');
    Route::delete('/workspaces/{workspaceId}/users/{userId}', [WorkspaceWebController::class, 'removeUser'])->name('workspaces.removeUser');
    
    // Project CRUD
    Route::get('/workspaces/{workspaceId}/projects/create', [ProjectWebController::class, 'create'])->name('projects.create');
    Route::post('/workspaces/{workspaceId}/projects', [ProjectWebController::class, 'store'])->name('projects.store');
    Route::get('/workspaces/{workspaceId}/projects/{projectId}', [ProjectWebController::class, 'show'])->name('projects.show');
    Route::get('/workspaces/{workspaceId}/projects/{projectId}/edit', [ProjectWebController::class, 'edit'])->name('projects.edit');
    Route::put('/workspaces/{workspaceId}/projects/{projectId}', [ProjectWebController::class, 'update'])->name('projects.update');
    Route::delete('/workspaces/{workspaceId}/projects/{projectId}', [ProjectWebController::class, 'destroy'])->name('projects.destroy');
    
    // Task CRUD
    Route::get('/workspaces/{workspaceId}/projects/{projectId}/tasks/create', [TaskWebController::class, 'create'])->name('tasks.create');
    Route::post('/workspaces/{workspaceId}/projects/{projectId}/tasks', [TaskWebController::class, 'store'])->name('tasks.store');
    Route::get('/workspaces/{workspaceId}/projects/{projectId}/tasks/{taskId}/edit', [TaskWebController::class, 'edit'])->name('tasks.edit');
    Route::put('/workspaces/{workspaceId}/projects/{projectId}/tasks/{taskId}', [TaskWebController::class, 'update'])->name('tasks.update');
    Route::delete('/workspaces/{workspaceId}/projects/{projectId}/tasks/{taskId}', [TaskWebController::class, 'destroy'])->name('tasks.destroy');
    Route::post('/workspaces/{workspaceId}/projects/{projectId}/tasks/{taskId}/complete', [TaskWebController::class, 'complete'])->name('tasks.complete');
});
