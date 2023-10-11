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
        ])->postJson('api/v1/create-task', $taskData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('tasks', ['title' => $taskData['title']]);
        // Delete the last created task
        $lastTask = Task::orderBy('id', 'desc')->first();
        if ($lastTask) {
            $lastTask->delete();
        }

        // Delete the last created user
        $user->delete();
    }
    /**
     * Tests if a user receives an error when trying to create a task with invalid data through the API endpoint.
     * Procedure:
     * 1. Creates a new user using the User factory.
     * 2. Generates an authentication token for the created user.
     * 3. Prepares mock invalid task data (e.g., missing title).
     * 4. Sends a POST request to the 'api/v1/create-task' endpoint using the generated token.
     * 5. Asserts that the API response status is 422  (indicating data validation error).
     * 6. Asserts that the response contains validation error messages.
     * 7. Cleanup: Deletes the created user from the database.
     */
    public function test_user_receives_error_when_creating_task_with_invalid_data()
    {
        $user = User::factory()->create();
        $user_token['token'] = $user->createToken('appToken')->accessToken;

        // Intentionally missing the 'title' for the task to trigger a validation error
        $taskData = [
            'description' => $this->faker->paragraph,
            'due_date' => now()->addDays(5)->format('Y-m-d'),
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $user_token["token"],
            'Accept' => 'application/json'
        ])->postJson('api/v1/create-task', $taskData);

        $response->assertStatus(422);  // 422 Unprocessable Entity
        $response->assertJsonValidationErrors(['title']);

        // Cleanup: Delete the created user
        $user->delete();
    }
    /**
     * Tests if a user receives an error when trying to create a task without an authentication token or with an invalid token.
     * Procedure:
     * 1. Prepares mock task data using Faker.
     * 2. Sends a POST request to the 'api/v1/create-task' endpoint without a token or with an invalid token.
     * 3. Asserts that the API response status is 401 (indicating unauthorized access).
     * 4. Optionally, assert that the response contains an error message indicating unauthorized access.
     */
    public function test_user_receives_error_when_creating_task_without_token()
    {
        $taskData = [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'due_date' => now()->addDays(5)->format('Y-m-d'),
        ];

        $response = $this->withHeaders([
            'Accept' => 'application/json'
        ])->postJson('api/v1/create-task', $taskData);  // Notice we're not providing the 'Authorization' header

        $response->assertStatus(401);
        $response->assertJson(['error' => 'Unauthenticated or Invalid Token']);  // The exact error message might vary based on your setup
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
        $path = (string) 'api/v1/get-task/' . $task->id;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $user_token["token"],
            'Accept' => 'application/json'
        ])->getJson($path);

        $response->assertStatus(200);
        $response->assertJsonFragment(['title' => $task->title]);

        // Delete the last created task
        $task->delete();

        // Delete the last created user
        $user->delete();
    }
    /**
     * Tests if a user receives an error when trying to fetch a non-existent task through the API endpoint.
     * Procedure:
     * 1. Creates a new user using the User factory.
     * 2. Generates an authentication token for the created user.
     * 3. Constructs the API endpoint path using a non-existent task ID.
     * 4. Sends a GET request to the constructed endpoint using the generated token.
     * 5. Asserts that the API response status is 404 (indicating the task was not found).
     * 6. Asserts that the response contains an error message indicating the task was not found.
     * 7. Cleanup: Deletes the created user from the database.
     */
    public function test_user_receives_error_when_fetching_non_existent_task()
    {
        $user = User::factory()->create();
        $user_token['token'] = $user->createToken('appToken')->accessToken;

        $nonExistentTaskId = 123456;  // Assuming this ID doesn't exist in the database
        $path = (string) 'api/v1/get-task/' . $nonExistentTaskId;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $user_token["token"],
            'Accept' => 'application/json'
        ])->getJson($path);

        $response->assertStatus(404);
        $response->assertJson(['error' => 'Task not found']);

        // Cleanup: Delete the created user
        $user->delete();
    }

    /**
     * Tests if a user receives an error when trying to fetch a task without an authentication token or with an invalid token.
     * Procedure:
     * 1. Creates a new task using the Task factory.
     * 2. Constructs the API endpoint path using the created task's ID.
     * 3. Sends a GET request to the constructed endpoint without a token or with an invalid token.
     * 4. Asserts that the API response status is 401 (indicating unauthorized access).
     * 5. Optionally, assert that the response contains an error message indicating unauthorized access.
     * 6. Cleanup: Deletes the created task from the database.
     */
    public function test_user_receives_error_when_fetching_task_without_token()
    {
        $task = Task::factory()->create();
        $path = (string) 'api/v1/get-task/' . $task->id;

        $response = $this->withHeaders([
            'Accept' => 'application/json'
        ])->getJson($path);  // Notice we're not providing the 'Authorization' header

        $response->assertStatus(401);
        $response->assertJson(['error' => 'Unauthenticated or Invalid Token']);  // The exact error message might vary based on your setup

        // Cleanup: Delete the created task
        $task->delete();
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
        $path = (string) 'api/v1/update-task/' . $task->id;

        $updateData = ['title' => 'Updated Title'];
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $user_token["token"],
            'Accept' => 'application/json'
        ])->putJson($path, $updateData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated Title'
        ]);
        // Delete the last created task
        $task->delete();

        // Delete the last created user
        $user->delete();
    }

    /**
     * Tests if a user receives an error when trying to update a non-existent task through the API endpoint.
     * Procedure:
     * 1. Creates a new user using the User factory.
     * 2. Generates an authentication token for the created user.
     * 3. Constructs the API endpoint path using a non-existent task ID.
     * 4. Prepares mock updated task data.
     * 5. Sends a PUT request to the constructed endpoint using the generated token and mock data.
     * 6. Asserts that the API response status is 404 (indicating the task was not found).
     * 7. Asserts that the response contains an error message indicating the task was not found.
     * 8. Cleanup: Deletes the created user from the database.
     */
    public function test_user_receives_error_when_updating_non_existent_task()
    {
        $user = User::factory()->create();
        $user_token['token'] = $user->createToken('appToken')->accessToken;

        $nonExistentTaskId = 123456;  // Assuming this ID doesn't exist in the database
        $path = (string) 'api/v1/update-task/' . $nonExistentTaskId;

        $updateData = ['title' => 'Updated Title'];
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $user_token["token"],
            'Accept' => 'application/json'
        ])->putJson($path, $updateData);

        $response->assertStatus(404);
        $response->assertJson(['error' => 'Task not found']);

        // Cleanup: Delete the created user
        $user->delete();
    }
    /**
     * Tests if a user receives an error when trying to update a task without an authentication token or with an invalid token.
     * Procedure:
     * 1. Creates a new task using the Task factory.
     * 2. Constructs the API endpoint path using the created task's ID.
     * 3. Prepares mock updated task data.
     * 4. Sends a PUT request to the constructed endpoint without a token or with an invalid token.
     * 5. Asserts that the API response status is 401 (indicating unauthorized access).
     * 6. Optionally, assert that the response contains an error message indicating unauthorized access.
     * 7. Cleanup: Deletes the created task from the database.
     */
    public function test_user_receives_error_when_updating_task_without_token()
    {
        $task = Task::factory()->create();
        $path = (string) 'api/v1/update-task/' . $task->id;
        $updateData = ['title' => 'Updated Title'];
        $token = null;
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->putJson($path, $updateData);
        $response->assertStatus(401);
        $response->assertJson(['error' => 'Unauthenticated or Invalid Token']);  // The exact error message might vary based on your setup

        // Cleanup: Delete the created task
        $task->delete();
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
        $path = (string) 'api/v1/delete-task/' . $task->id;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $user_token["token"],
            'Accept' => 'application/json'
        ])->deleteJson($path);

        $response->assertStatus(204);
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);

        // Delete the last created user
        $user->delete();
    }

    /**
     * Tests if a user receives an error when trying to delete a task without an authentication token or with an invalid token.
     * Procedure:
     * 1. Creates a new task using the Task factory.
     * 2. Constructs the API endpoint path using the created task's ID.
     * 3. Sends a DELETE request to the constructed endpoint without a token.
     * 4. Asserts that the API response status is 401 (indicating unauthorized access).
     * 5. Assert that the response contains an error message indicating unauthorized access.
     * 6. Cleanup: Deletes the created task from the database.
     */
    public function test_user_receives_error_when_deleting_task_without_token()
    {
        $task = Task::factory()->create();
        $path = (string) 'api/v1/delete-task/' . $task->id;
        $token = null;
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->deleteJson($path);

        $response->assertStatus(401);
        $response->assertJson(['error' => 'Unauthenticated or Invalid Token']);

        // Delete the last created task
        $task->delete();
    }
    /**
     * Tests if a user receives an error when trying to delete a non-existent task through the API endpoint.
     * Procedure:
     * 1. Creates a new user using the User factory.
     * 2. Generates an authentication token for the created user.
     * 3. Constructs the API endpoint path using a non-existent task ID.
     * 4. Sends a DELETE request to the constructed endpoint using the generated token.
     * 5. Asserts that the API response status is 404 (indicating the task was not found).
     * 6. Asserts that the response contains an error message indicating the task was not found.
     * 7. Cleanup: Deletes the created user from the database.
     */
    public function test_user_receives_error_when_deleting_non_existent_task()
    {
        $user = User::factory()->create();
        $user_token['token'] = $user->createToken('appToken')->accessToken;

        $nonExistentTaskId = 123456;  // Assuming this ID doesn't exist in the database
        $path = (string) 'api/v1/delete-task/' . $nonExistentTaskId;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $user_token["token"],
            'Accept' => 'application/json'
        ])->deleteJson($path);

        $response->assertStatus(404);
        $response->assertJson(['error' => 'Task not found']);

        // Delete the last created user
        $user->delete();
    }
}
