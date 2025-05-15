<?php

use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\{postJson, getJson, actingAs, assertDatabaseHas};

beforeEach(function () {
    $this->artisan('migrate:fresh');
});


it('can register a user', function () {
    $response = $this->postJson('/api/v1/register', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertCreated()
        ->assertJsonStructure(['success', 'message'])
        ->assertJson(['success' => true, 'message' => 'Register Successfully.']);
});

it('fails to register a user with invalid data', function () {
    $response = $this->postJson('/api/v1/register', [
        'name' => '',
        'email' => 'john.com',
        'password' => 'password123',
        'password_confirmation' => 'password',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'email', 'password']);
});

it('can login a user', function () {
    $user = \App\Models\User::factory()->create([
        'password' => bcrypt('password123'),
    ]);

    $response = $this->postJson('/api/v1/login', [
        'email' => $user->email,
        'password' => 'password123',
    ]);

    $response->assertOk()
        ->assertJsonStructure(['token']);
});

it('fails to login with invalid credentials', function () {
    $response = $this->postJson('/api/v1/login', [
        'email' => 'john@example.com',
        'password' => 'wrongpassword',
    ]);

    $response->assertUnauthorized()
        ->assertJson(['message' => 'Invalid credentials']);
});


it('can get authenticated user details', function () {
    $user = \App\Models\User::factory()->create();
    sanctum::actingAs($user);

    $response = getJson('/api/v1/me');

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
});

it('can logout a user', function () {
    $user = \App\Models\User::factory()->create();
    Sanctum::actingAs($user);

    $response = postJson('/api/v1/logout');

    $response->assertOk()
        ->assertJson(['message' => 'Logged out successfully']);
});
