<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(RefreshDatabase::class);

it('creates user and returns token on register', function () {
    $payload = [
        'name' => 'Pepe test',
        'email' => 'pepe@test.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ];
    /** @var TestCase $this */
    $response = $this->post('/api/auth/register', $payload);

    $response->assertStatus(201)
        ->assertJsonStructure(['data' => ['access_token', 'token_type', 'expires_in', 'user']])
        ->assertJsonPath('data.user.email', 'pepe@test.com');
});

it('does not expose password on register', function () {
    $payload = [
        'name' => 'Pepe test',
        'email' => 'pepe@test.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ];
    /** @var TestCase $this */
    $response = $this->post('/api/auth/register', $payload);

    expect($response->json('data.user'))->not->toHavekey('password');
});


it('requires all fields on register', function () {
    /** @var TestCase $this */
    $response = $this->post('/api/auth/register', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'email', 'password']);
});

it('returns token for valid credentials on login', function () {
    User::factory()->create(['email' => 'pepe@pepe.com', 'password' => bcrypt('password1234')]);
    /** @var TestCase $this */
    $response = $this->post('/api/auth/login', [
        'email' => 'pepe@pepe.com',
        'password' => 'password1234'
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure(['data' => ['access_token', 'token_type', 'expires_in', 'user']]);
});

it('returns user with valid token on me', function () {
    $user = User::factory()->create();
    /** @var TestCase $this */
    $this->actingAs($user, 'api')
        ->getJson('/api/auth:api/me')
        ->assertStatus(200)
        ->assertJsonPath('data.email', $user->email);
});

it('succeeds with valid token on logout', function () {
    $user = User::factory()->create();
    /** @var TestCase $this */
    $this->actingAs($user, 'api')
        ->postJson('/api/auth:api/logout')
        ->assertStatus(200);
});