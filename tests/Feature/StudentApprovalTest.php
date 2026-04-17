<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('registers a student without logging them in', function () {
    $response = $this->post('/register', [
        'name' => 'شاگرد جدید',
        'email' => 'new-student@test.com',
        'role' => 'student',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertRedirect('/login');

    $student = User::query()->where('email', 'new-student@test.com')->first();

    expect($student)->not->toBeNull()
        ->and($student->approved_at)->toBeNull();
});

it('allows a teacher to approve a student', function () {
    $teacher = User::factory()->create([
        'role' => 'teacher',
        'approved_at' => now(),
    ]);

    $student = User::factory()->create([
        'role' => 'student',
        'approved_at' => null,
    ]);

    $this->actingAs($teacher)
        ->patch("/teacher/students/{$student->id}/approve")
        ->assertRedirect();

    $student->refresh();

    expect($student->approved_at)->not->toBeNull()
        ->and($student->approved_by)->toBe($teacher->id);
});
