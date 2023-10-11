<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Http\Requests\TaskRequest;

class TaskController extends Controller
{
    /**
     * Fetches a paginated list of all tasks.
     * @return \Illuminate\Http\JsonResponse
     * The paginated list of tasks with a 200 HTTP status.
     */
    public function index()
    {
        return response()->json(Task::paginate(15), 200);
    }

    /**
     * Stores a new task in the database based on the provided request data.
     * The method validates the request data before creating the task.
     * If validation fails, it returns a 400 HTTP status with the validation errors. 
     * If the task is created successfully, it returns the created task with a 201 HTTP status.
     * @param \Illuminate\Http\Request $request
     * The HTTP request containing the data for the task to be created.
     * @return \Illuminate\Http\JsonResponse
     * The created task with a 201 HTTP status, or validation errors with a 400 HTTP status.
     * @throws \Illuminate\Validation\ValidationException
     * If validation of the request data fails.
     */
    public function store(TaskRequest $request)
    {

        // Create the task using the validated data
        $task = Task::create($request->validated());

        return response()->json($task, 201);
    }

    /**
     * Fetches a specific task by its ID.
     * If the task with the provided ID is not found, 
     * it returns a 404 HTTP status with an error message. If the task is found,
     * it returns the task details with a 200 HTTP status.
     * @param int $id
     * The ID of the task to be fetched.
     * @return \Illuminate\Http\JsonResponse
     * The task details with a 200 HTTP status, or an error message with
     * a 404 HTTP status if not found.
     */
    public function show($id)
    {
        // Fetch the task by its ID
        $task = Task::find($id);

        if (!$task) {
            return response()->json(['error' => 'Task not found'], 404);
        }

        return response()->json($task);
    }

    /**
     * Updates a specific task in the database based on the provided request data and task ID.
     * The method first checks if the task with the provided ID exists. If not,
     * it returns a 404 HTTP status with an error message.
     * The method then validates the request data before updating the task. If validation fails, 
     * it returns a 400 HTTP status with the validation errors. If the task is updated successfully,
     * it returns the updated task details with a 200 HTTP status.
     * @param \Illuminate\Http\Request $request
     * The HTTP request containing the data for the task to be updated.
     * @param int $id
     * The ID of the task to be updated.
     * @return \Illuminate\Http\JsonResponse
     * The updated task details with a 200 HTTP status, or an error message with a 404 HTTP status if task not found,
     * or validation errors with a 400 HTTP status if validation fails.
     * @throws \Illuminate\Validation\ValidationException
     * If validation of the request data fails.
     */
    public function update(TaskRequest $request, $id)
    {
        // Fetch the task by its ID
        $task = Task::find($id);
        if (!$task) {
            return response()->json(['error' => 'Task not found'], 404);
        }

        // Update the task using the validated data
        $task->update($request->validated());
        return response()->json($task, 200);
    }

    /**
     * Deletes a specific task from the database based on the provided task ID.
     * The method first checks if the task with the provided ID exists. If not,
     * it returns a 404 HTTP status with an error message.
     * If the task is found, it is deleted from the database. Upon successful deletion,
     * it returns a 204 No Content HTTP status with a success message.
     * @param int $id
     * The ID of the task to be deleted.
     * @return \Illuminate\Http\JsonResponse
     * A success message with a 204 No Content HTTP status if the task is deleted successfully,
     * or an error message with a 404 HTTP status if the task is not found.
     */
    public function destroy($id)
    {
        // Fetch the task by its ID
        $task = Task::find($id);

        if (!$task) {
            return response()->json(['error' => 'Task not found'], 404);
        }

        // Delete the task
        $task->delete();

        return response()->json(
            ['success' => 'Task deleted Success'],
            204
        );  // 204 No Content response after successful deletion
    }
}
