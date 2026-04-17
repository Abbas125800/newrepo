<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('allows a teacher to register and redirects to the teacher dashboard', function () {
    $response = $this->post('/register', [
        'name' => 'استاد تست',
        'email' => 'teacher@test.com',
        'role' => 'teacher',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertRedirect('/dashboard');

    $this->followRedirects($response)
        ->assertSee('داشبورد استاد');

    expect(User::query()->where('email', 'teacher@test.com')->exists())->toBeTrue();
});

it('prevents an unapproved student from logging in', function () {
    $user = User::factory()->create([
        'role' => 'student',
        'email' => 'student@test.com',
        'password' => 'password123',
    ]);

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'password123',
    ])->assertSessionHasErrors('email');
});

it('allows an approved student to login and redirects to the student dashboard', function () {
    $teacher = User::factory()->create([
        'role' => 'teacher',
        'approved_at' => now(),
    ]);

    $user = User::factory()->create([
        'role' => 'student',
        'email' => 'student@test.com',
        'password' => 'password123',
        'approved_at' => now(),
        'approved_by' => $teacher->id,
    ]);

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password123',
    ]);

    $response->assertRedirect('/dashboard');

    $this->followRedirects($response)
        ->assertSee('داشبورد شاگرد');
});

it('prevents a student from opening the teacher dashboard', function () {
    $teacher = User::factory()->create([
        'role' => 'teacher',
        'approved_at' => now(),
    ]);

    $student = User::factory()->create([
        'role' => 'student',
        'approved_at' => now(),
        'approved_by' => $teacher->id,
    ]);

    $this->actingAs($student)
        ->get('/teacher/dashboard')
        ->assertForbidden();
});
