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

    public function test_user_can_create_task()
    {
        $user = User::factory()->create();
        $user_token['token'] = $user->createToken('appToken')->accessToken;
        $taskData = [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'due_date' => now()->addDays(5)->format('Y-m-d'),
            // ... other fields
        ];
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $user_token["token"], 'Accept' => 'application/json'])
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

    public function test_user_can_fetch_specific_task()
    {
        $user = User::factory()->create();
        $user_token['token'] = $user->createToken('appToken')->accessToken;

        $task = Task::factory()->create();
        $id = (string) $task->id;
        $path = (string) 'api/v1/get-task/' . $id;

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $user_token["token"], 'Accept' => 'application/json'])
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

    public function test_user_can_update_task()
    {
        $user = User::factory()->create();
        $user_token['token'] = $user->createToken('appToken')->accessToken;

        $task = Task::factory()->create();
        $id = (string) $task->id;
        $path = (string) 'api/v1/update-task/' . $id;
        // dd($path);
        $updateData = ['title' => 'Updated Title'];
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $user_token["token"], 'Accept' => 'application/json'])
            ->putJson($path, $updateData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'title' => 'Updated Title']);
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

    public function test_user_can_delete_task()
    {
        $user = User::factory()->create();
        $user_token['token'] = $user->createToken('appToken')->accessToken;

        $task = Task::factory()->create();
        $id = (string) $task->id;

        $path = (string) 'api/v1/delete-task/' . $id;

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $user_token["token"], 'Accept' => 'application/json'])
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
