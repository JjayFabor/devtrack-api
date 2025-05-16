<?php

use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\{postJson, getJson, actingAs, assertDatabaseHas};

beforeEach(function () {
    $this->artisan('migrate:fresh');
});

it('can generate a new token', function () {
    \App\Models\User::factory()->create([
        'email' => 'test@gmail.com',
        'password' => bcrypt('secret123'),
    ]);

    $response = postJson('/api/v1/token', [
        'email' => 'test@gmail.com',
        'password' => 'secret123',
    ]);

    $response->assertCreated()
        ->assertJson([
            'success' => true,
            'message' => 'Token generated successfully',
            'token' => $response->json('token'),
        ]);
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
