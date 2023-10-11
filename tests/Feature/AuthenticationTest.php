<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class AuthenticationTest extends TestCase
{
    use WithFaker, DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate', ['--database' => 'sqlite_testing']);
        $this->artisan('passport:client', ['--personal' => true, '--name' => 'Test Personal Access Client']);
    }

    /**
     * Tests if a user can successfully authenticate using the login API endpoint.
     * 
     * Procedure:
     * 1. A new user is created using the User factory with a known password.
     * 2. An attempt is made to authenticate this user using the 'api/v1/login' endpoint.
     * 3. Assertions are made to ensure:
     * - The response status is 200 (indicating successful authentication).
     * - The response has the expected JSON structure (success, token, user).
     * 
     * @return void
     */
    public function test_successful_login()
    {
        // Create a new user
        $user = User::factory()->create([
            'password' => bcrypt($password = 'password')
        ]);

        // Attempt to authenticate
        $response = $this->postJson('api/v1/login', [
            'email' => $user->email,
            'password' => $password,
        ]);
        // Assertions
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'token',
            'user'
        ]);
    }

    /**
     * Tests if the login API endpoint responds correctly when provided with incorrect credentials.
     * 
     * Procedure:
     * 1. An attempt is made to authenticate using the 'api/v1/login' endpoint with invalid email and password.
     * 2. Assertions are made to ensure:
     *    - The response status is 401 (indicating unauthorized access).
     *    - The response contains the expected JSON message indicating authentication failure.
     * 
     * @return void
     */
    public function test_unsuccessful_login()
    {
        // Attempt to authenticate with wrong credentials
        $response = $this->postJson('/api/v1/login', [
            'email' => 'fakeemail@example.com',
            'password' => 'wrongpassword',
        ]);

        // Assertions
        $response->assertStatus(401);
        $response->assertJson(['success' => false, 'message' => 'Failed to authenticate.']);
    }

    /**
     * Tests if the logout API endpoint successfully logs out an authenticated user.
     * 
     * Procedure:
     * 1. A new user is created using the User factory.
     * 2. The user is authenticated to generate an access token.
     * 3. The generated token is used to make a request to the 'api/v1/logout-user' endpoint, simulating a logout action.
     * 4. Assertions are made to ensure:
     *    - The response status is 200 (indicating successful request).
     *    - The response contains the expected JSON message indicating successful logout.
     * 
     * @return void
     */
    public function test_successful_logout()
    {
        // Create a new user
        $user = User::factory()->create();

        // Authenticate the user to get a token
        $token = $user->createToken('appToken')->accessToken;

        // Use the token to hit the logout endpoint
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->postJson('/api/v1/logout-user');

        // Assertions
        $response->assertStatus(200);
        $response->assertJson(['success' => true, 'message' => 'Logged out successfully']);
    }
    /**
     * Tests if the logout API endpoint responds correctly when an invalid token is provided.
     * 
     * Procedure:
     * 1. An invalid token is used to make a request to the 'api/v1/logout-user' endpoint.
     * 2. Assertions are made to ensure:
     *    - The response status is 401 (indicating unauthorized request).
     *    - The response contains the expected JSON message indicating authentication failure.
     * 
     * @return void
     */
    public function test_unsuccessful_logout_due_to_invalid_token()
    {
        // Use an invalid token to hit the logout endpoint
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . 'invalidToken',
            'Accept' => 'application/json'
        ])->postJson('/api/v1/logout-user');

        // Assertions
        $response->assertStatus(401);
        $response->assertJson(['error' => 'Unauthenticated or Invalid Token']);
    }
}
