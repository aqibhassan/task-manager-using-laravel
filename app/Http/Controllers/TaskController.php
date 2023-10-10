<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TaskController extends Controller
{
    /**
     * Fetches a paginated list of all tasks.
     * 
     * @return \Illuminate\Http\JsonResponse
     * The paginated list of tasks with a 200 HTTP status.
     */
    public function index()
    {
        return response()->json(Task::paginate(15), 200);
    }

    /**
     * Stores a new task in the database based on the provided request data.
     * 
     * The method validates the request data before creating the task.
     * If validation fails, it returns a 400 HTTP status with the validation errors. 
     * If the task is created successfully, it returns the created task with a 201 HTTP status.
     * 
     * @param \Illuminate\Http\Request $request
     * The HTTP request containing the data for the task to be created.
     * 
     * @return \Illuminate\Http\JsonResponse
     * The created task with a 201 HTTP status, or validation errors with a 400 HTTP status.
     * 
     * @throws \Illuminate\Validation\ValidationException
     * If validation of the request data fails.
     */
    public function store(Request $request)
    {
        try {
            // Validate the request data
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'due_date' => 'required|date|after:today',
                'priority' => 'sometimes|required|in:low,medium,high',
                'status' => 'sometimes|required|in:new,in_progress,on_hold,completed,review',
                'completed' => 'sometimes|required|boolean',
                'notes' => 'nullable|string'
            ]);

            // Create the task using the validated data
            $task = Task::create($validatedData);

            return response()->json($task, 201);
        } catch (ValidationException $e) {
            // Return validation errors
            return response()->json(['errors' => $e->validator->errors()], 400);
        }
    }

    /**
     * Fetches a specific task by its ID.
     * 
     * If the task with the provided ID is not found, 
     * it returns a 404 HTTP status with an error message. If the task is found,
     * it returns the task details with a 200 HTTP status.
     * 
     * @param int $id
     * The ID of the task to be fetched.
     * 
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
     * 
     * The method first checks if the task with the provided ID exists. If not, 
     * it returns a 404 HTTP status with an error message.
     * 
     * The method then validates the request data before updating the task. If validation fails, 
     * it returns a 400 HTTP status with the validation errors. If the task is updated successfully,
     * it returns the updated task details with a 200 HTTP status.
     * 
     * @param \Illuminate\Http\Request $request
     * The HTTP request containing the data for the task to be updated.
     * @param int $id
     * The ID of the task to be updated.
     * 
     * @return \Illuminate\Http\JsonResponse
     * The updated task details with a 200 HTTP status, or an error message with a 404 HTTP status if task not found, 
     * or validation errors with a 400 HTTP status if validation fails.
     * 
     * @throws \Illuminate\Validation\ValidationException
     * If validation of the request data fails.
     */
    public function update(Request $request, $id)
    {
        // Fetch the task by its ID
        $task = Task::find($id);

        if (!$task) {
            return response()->json(['error' => 'Task not found'], 404);
        }

        try {
            // Validate the request data
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'due_date' => 'sometimes|nullable|date|after:today',
                'priority' => 'sometimes|in:low,medium,high',
                'status' => 'sometimes|in:new,in_progress,on_hold,completed,review',
                'completed' => 'sometimes|boolean',
                'notes' => 'nullable|string'
            ]);

            // Update the task using the validated data
            $task->update($validatedData);

            return response()->json($task, 200);
        } catch (ValidationException $e) {
            // Return validation errors
            return response()->json(['errors' => $e->validator->errors()], 400);
        }
    }

    /**
     * Deletes a specific task from the database based on the provided task ID.
     * 
     * The method first checks if the task with the provided ID exists. If not, 
     * it returns a 404 HTTP status with an error message.
     * 
     * If the task is found, it is deleted from the database. Upon successful deletion, 
     * it returns a 204 No Content HTTP status with a success message.
     * 
     * @param int $id
     * The ID of the task to be deleted.
     * 
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
