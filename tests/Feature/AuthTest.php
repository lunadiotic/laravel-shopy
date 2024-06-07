<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test if a user can register successfully.
     *
     * This function sends a POST request to the '/api/register' endpoint with the user registration data.
     * The function asserts that the response status is 201 and that the response JSON structure contains the 'id', 'name', 'email', and 'role' keys.
     *
     * @return void
     */
    public function test_user_can_register()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'buyer',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['id', 'name', 'email', 'role']);
    }

    /**
     * Test if a user can log in successfully.
     *
     * This function creates a user with a password using the User factory.
     * It then sends a POST request to the '/api/login' endpoint with the user's email and password.
     * The function asserts that the response status is 200 and that the response JSON structure contains a 'token' key.
     *
     * @return void
     */
    public function test_user_can_login()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['token']);
    }
}
