<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use WithFaker;

    /**
     * Tests if a user can successfully create a task through the API endpoint.
     * Procedure:
     * 1. Creates a new user using the User factory.
     * 2. Generates an authentication token for the created user.
     * 3. Prepares mock task data using Faker.
     * 4. Sends a POST request to the 'api/v1/create-task' endpoint using the generated token.
     * 5. Asserts that the API response status is 201 (indicating successful creation).
     * 6. Asserts that the task has been successfully added to the database.
     * 7. Cleanup: Deletes the created task and user from the database.
     */
    public function test_user_can_create_task()
    {
        $user = User::factory()->create();
        $user_token['token'] = $user->createToken('appToken')->accessToken;
        $taskData = [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'due_date' => now()->addDays(5)->format('Y-m-d'),

        ];
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $user_token["token"],
            'Accept' => 'application/json'
        ])
            ->postJson('api/v1/create-task', $taskData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('tasks', ['title' => $taskData['title']]);
        // Delete the last created task
        $lastTask = Task::orderBy('id', 'desc')->first();
        if ($lastTask) {
            $lastTask->delete();
        }

        // Delete the last created user
        $lastUser = User::orderBy('id', 'desc')->first();
        if ($lastUser) {
            $lastUser->delete();
        }
    }

    /**
     * Tests if a user can successfully fetch a specific task through the API endpoint.
     * Procedure:
     * 1. Creates a new user using the User factory.
     * 2. Generates an authentication token for the created user.
     * 3. Creates a new task using the Task factory.
     * 4. Constructs the API endpoint path using the created task's ID.
     * 5. Sends a GET request to the constructed endpoint using the generated token.
     * 6. Asserts that the API response status is 200 (indicating successful retrieval).
     * 7. Asserts that the response contains the correct task details.
     * 8. Cleanup: Deletes the created task and user from the database.
     */

    public function test_user_can_fetch_specific_task()
    {
        $user = User::factory()->create();
        $user_token['token'] = $user->createToken('appToken')->accessToken;

        $task = Task::factory()->create();
        $id = (string) $task->id;
        $path = (string) 'api/v1/get-task/' . $id;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $user_token["token"],
            'Accept' => 'application/json'
        ])
            ->getJson($path);

        $response->assertStatus(200);
        $response->assertJsonFragment(['title' => $task->title]);

        // Delete the last created task
        $lastTask = Task::orderBy('id', 'desc')->first();
        if ($lastTask) {
            $lastTask->delete();
        }

        // Delete the last created user
        $lastUser = User::orderBy('id', 'desc')->first();
        if ($lastUser) {
            $lastUser->delete();
        }
    }
    /**
     * Tests if a user can successfully update a specific task through the API endpoint.
     * Procedure:
     * 1. Creates a new user using the User factory.
     * 2. Generates an authentication token for the created user.
     * 3. Creates a new task using the Task factory.
     * 4. Constructs the API endpoint path using the created task's ID.
     * 5. Prepares updated task data.
     * 6. Sends a PUT request to the constructed endpoint using the generated token and updated data.
     * 7. Asserts that the API response status is 200 (indicating successful update).
     * 8. Asserts that the task in the database has been updated with the new data.
     * 9. Cleanup: Deletes the updated task and user from the database.
     */
    public function test_user_can_update_task()
    {
        $user = User::factory()->create();
        $user_token['token'] = $user->createToken('appToken')->accessToken;

        $task = Task::factory()->create();
        $id = (string) $task->id;
        $path = (string) 'api/v1/update-task/' . $id;

        $updateData = ['title' => 'Updated Title'];
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $user_token["token"],
            'Accept' => 'application/json'
        ])
            ->putJson($path, $updateData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated Title'
        ]);
        // Delete the last created task
        $lastTask = Task::orderBy('id', 'desc')->first();
        if ($lastTask) {
            $lastTask->delete();
        }

        // Delete the last created user
        $lastUser = User::orderBy('id', 'desc')->first();
        if ($lastUser) {
            $lastUser->delete();
        }
    }

    /**
     * Tests if a user can successfully delete a specific task through the API endpoint.
     * Procedure:
     * 1. Creates a new user using the User factory.
     * 2. Generates an authentication token for the created user.
     * 3. Creates a new task using the Task factory.
     * 4. Constructs the API endpoint path using the created task's ID.
     * 5. Sends a DELETE request to the constructed endpoint using the generated token.
     * 6. Asserts that the API response status is 204 (indicating successful deletion).
     * 7. Asserts that the task has been successfully removed from the database.
     * 8. Cleanup: Deletes the created user from the database.
     */
    public function test_user_can_delete_task()
    {
        $user = User::factory()->create();
        $user_token['token'] = $user->createToken('appToken')->accessToken;

        $task = Task::factory()->create();
        $id = (string) $task->id;

        $path = (string) 'api/v1/delete-task/' . $id;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $user_token["token"],
            'Accept' => 'application/json'
        ])
            ->deleteJson($path);

        $response->assertStatus(204);
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
        // Delete the last created user
        $lastUser = User::orderBy('id', 'desc')->first();
        if ($lastUser) {
            $lastUser->delete();
        }
    }
}
