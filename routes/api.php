<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthenticationController;
use App\Http\Controllers\TaskController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group(['namespace' => 'Api', 'prefix' => 'v1'], function () {
    Route::post('login', [AuthenticationController::class, 'login_user']);

    Route::middleware('auth:api')->group(function () {
        Route::post('/logout-user', [AuthenticationController::class, 'destroy']);
        // Create a new task
        Route::post('/create-task', [TaskController::class, 'store']);
        // Get all tasks
        Route::get('/get-all-tasks', [TaskController::class, 'index']);
        // Get a single task by ID
        Route::get('/get-task/{id}', [TaskController::class, 'show']);

        // Update a task by ID
        Route::put('/update-task/{id}', [TaskController::class, 'update']);

        // Delete a task by ID
        Route::delete('/delete-task/{id}', [TaskController::class, 'destroy']);
    });
});
