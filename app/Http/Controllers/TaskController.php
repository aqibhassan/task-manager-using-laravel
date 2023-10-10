<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TaskController extends Controller
{
    // Fetch all tasks
    public function index()
    {
        return response()->json(Task::paginate(15), 200);
    }

    // Store a new task
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

    // Fetch a specific task
    public function show($id)
    {
        // Fetch the task by its ID
        $task = Task::find($id);

        if (!$task) {
            return response()->json(['error' => 'Task not found'], 404);
        }

        return response()->json($task);
    }

    // Update a task
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

    // Delete a task
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
