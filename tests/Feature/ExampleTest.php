<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guest is redirected to login page', function () {
    $response = $this->get('/');

    $response->assertRedirect('/login');
});

test('authenticated user can access the home page', function () {
    $user = User::create([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => bcrypt('password'),
    ]);

    $response = $this->actingAs($user)->get('/');

    $response->assertStatus(200);
});
