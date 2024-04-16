<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\AssignTaskController;
use App\Http\Controllers\AuthenticateUserController;
use App\Http\Middleware\CheckTokenMiddleware;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/login', [AuthenticateUserController::class, 'authenticate'])->name('login');

Route::middleware([CheckTokenMiddleware::class])->group(function () {
    Route::get('/tasks', [TaskController::class, 'index']);
    Route::post('/tasks', [TaskController::class, 'store']);
    Route::get('/tasks/list', [TaskController::class, 'taskList']);
    Route::put('/tasks/{id}', [TaskController::class, 'update']);
    Route::delete('/tasks/{id}', [TaskController::class, 'destroy']);
    Route::put('/tasks/{taskId}/change-status', [TaskController::class, 'changeTaskStatus']);
});

Route::middleware([CheckTokenMiddleware::class])->group(function () {
    Route::get('/user-list', [AssignTaskController::class, 'showUserList'])->name('user.list');
    Route::get('/fetch-user-list', [AssignTaskController::class, 'fetchUserList']);
    Route::post('/assign-task', [AssignTaskController::class, 'assign']);
    Route::delete('/unassign-task/{userId}/{taskId}', [AssignTaskController::class, 'unassignTask']);
    Route::get('/user/{userId}/tasks', [AssignTaskController::class, 'getUserWithTasks']);
});