<?php

use App\Models\Attempts;
use App\Models\Options;
use App\Models\Questions;
use App\Models\Quizzes;
use App\Models\Quiz_Results;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('allows a teacher to create a published quiz with questions', function () {
    $teacher = User::factory()->create([
        'role' => 'teacher',
    ]);

    $startsAt = Carbon::now(config('app.timezone'))->addHour()->format('Y-m-d\TH:i');

    $response = $this->actingAs($teacher)->post('/teacher/quizzes', [
        'title' => 'آزمون لاراول',
        'description' => 'آزمون مقدماتی',
        'duration' => 25,
        'starts_at' => $startsAt,
        'questions' => [
            [
                'question_text' => 'Laravel چیست؟',
                'type' => 'mcq',
                'correct_option' => 1,
                'options' => [
                    ['option_text' => 'مرورگر'],
                    ['option_text' => 'فریم‌ورک PHP'],
                    ['option_text' => 'سیستم عامل'],
                    ['option_text' => 'بانک اطلاعاتی'],
                ],
            ],
        ],
    ]);

    $quiz = Quizzes::query()->first();

    $response->assertRedirect("/teacher/quizzes/{$quiz->id}");

    expect($quiz)->not->toBeNull()
        ->and($quiz->questions()->count())->toBe(1)
        ->and($quiz->questions()->first()->options()->count())->toBe(4)
        ->and($quiz->is_published)->toBeTrue()
        ->and($quiz->starts_at)->not->toBeNull()
        ->and($quiz->ends_at)->not->toBeNull();
});

it('allows a student to take a quiz and receive an immediate result', function () {
    $teacher = User::factory()->create([
        'role' => 'teacher',
    ]);

    $student = User::factory()->create([
        'role' => 'student',
        'approved_at' => now(),
        'approved_by' => $teacher->id,
    ]);

    $quiz = Quizzes::create([
        'title' => 'آزمون CSS',
        'description' => 'نمونه آزمون',
        'duration' => 20,
        'starts_at' => now()->subMinutes(5),
        'ends_at' => now()->addMinutes(15),
        'created_by' => $teacher->id,
        'is_published' => true,
    ]);

    $question = Questions::create([
        'quiz_id' => $quiz->id,
        'question_text' => 'کدام گزینه برای رنگ متن است؟',
        'type' => 'mcq',
    ]);

    $wrong = Options::create([
        'question_id' => $question->id,
        'option_text' => 'padding',
        'is_correct' => false,
    ]);

    $correct = Options::create([
        'question_id' => $question->id,
        'option_text' => 'color',
        'is_correct' => true,
    ]);

    Options::create([
        'question_id' => $question->id,
        'option_text' => 'margin',
        'is_correct' => false,
    ]);

    Options::create([
        'question_id' => $question->id,
        'option_text' => 'gap',
        'is_correct' => false,
    ]);

    $startResponse = $this->actingAs($student)->post("/student/quizzes/{$quiz->id}/start");

    $attempt = Attempts::query()->first();

    $startResponse->assertRedirect("/student/attempts/{$attempt->id}");

    $submitResponse = $this->actingAs($student)->post("/student/attempts/{$attempt->id}/submit", [
        'answers' => [
            $question->id => $correct->id,
        ],
    ]);

    $submitResponse->assertRedirect("/student/attempts/{$attempt->id}/result");

    $attempt->refresh();
    $result = Quiz_Results::query()->where('attempt_id', $attempt->id)->first();

    expect($attempt->status)->toBe('finished')
        ->and($attempt->score)->toBe(1)
        ->and($result)->not->toBeNull()
        ->and((float) $result->percentage)->toBe(100.0);
});

it('allows a teacher to edit, cancel, restart, and delete a quiz', function () {
    $teacher = User::factory()->create([
        'role' => 'teacher',
    ]);

    $quiz = Quizzes::create([
        'title' => 'آزمون اولیه',
        'description' => 'نسخه اول',
        'duration' => 15,
        'starts_at' => now()->addHour(),
        'ends_at' => now()->addHour()->addMinutes(15),
        'created_by' => $teacher->id,
        'is_published' => true,
    ]);

    $updatedStartsAt = Carbon::now(config('app.timezone'))->addHours(2)->format('Y-m-d\TH:i');

    $this->actingAs($teacher)->put("/teacher/quizzes/{$quiz->id}", [
        'title' => 'آزمون ویرایش‌شده',
        'description' => 'نسخه جدید',
        'duration' => 20,
        'starts_at' => $updatedStartsAt,
        'questions' => [
            [
                'question_text' => 'پایتخت فرانسه چیست؟',
                'type' => 'short_answer',
                'correct_text' => 'Paris',
            ],
        ],
    ])->assertRedirect("/teacher/quizzes/{$quiz->id}");

    $quiz->refresh();

    expect($quiz->title)->toBe('آزمون ویرایش‌شده')
        ->and($quiz->questions()->count())->toBe(1)
        ->and($quiz->questions()->first()->type)->toBe('short_answer');

    $this->actingAs($teacher)->patch("/teacher/quizzes/{$quiz->id}/cancel")
        ->assertRedirect();

    $quiz->refresh();
    expect($quiz->cancelled_at)->not->toBeNull()
        ->and($quiz->is_published)->toBeFalse();

    $this->actingAs($teacher)->patch("/teacher/quizzes/{$quiz->id}/restart")
        ->assertRedirect();

    $quiz->refresh();
    expect($quiz->cancelled_at)->toBeNull()
        ->and($quiz->is_published)->toBeTrue();

    $this->actingAs($teacher)->delete("/teacher/quizzes/{$quiz->id}")
        ->assertRedirect('/teacher/quizzes');

    expect(Quizzes::query()->whereKey($quiz->id)->exists())->toBeFalse();
});

it('grades short answer questions immediately for students', function () {
    $teacher = User::factory()->create(['role' => 'teacher']);
    $student = User::factory()->create(['role' => 'student']);
    $student->update([
        'approved_at' => now(),
        'approved_by' => $teacher->id,
    ]);

    $quiz = Quizzes::create([
        'title' => 'آزمون پاسخ کوتاه',
        'description' => 'پاسخ کوتاه',
        'duration' => 10,
        'starts_at' => now()->subMinutes(2),
        'ends_at' => now()->addMinutes(8),
        'created_by' => $teacher->id,
        'is_published' => true,
    ]);

    $question = Questions::create([
        'quiz_id' => $quiz->id,
        'question_text' => 'پایتخت افغانستان چیست؟',
        'type' => 'short_answer',
        'correct_text' => 'کابل',
    ]);

    $this->actingAs($student)->post("/student/quizzes/{$quiz->id}/start");
    $attempt = Attempts::query()->firstOrFail();

    $this->actingAs($student)->post("/student/attempts/{$attempt->id}/submit", [
        'answers' => [
            $question->id => 'کابل',
        ],
    ])->assertRedirect("/student/attempts/{$attempt->id}/result");

    $attempt->refresh();
    $result = Quiz_Results::query()->where('attempt_id', $attempt->id)->first();
    $answer = $attempt->answers()->first();

    expect($attempt->score)->toBe(1)
        ->and((float) $result->percentage)->toBe(100.0)
        ->and($answer->answer_text)->toBe('کابل');
});

it('does not show or allow an exam after its time is finished', function () {
    $teacher = User::factory()->create(['role' => 'teacher']);
    $student = User::factory()->create(['role' => 'student']);
    $student->update([
        'approved_at' => now(),
        'approved_by' => $teacher->id,
    ]);

    $quiz = Quizzes::create([
        'title' => 'آزمون بسته‌شده',
        'description' => 'زمان آن تمام شده',
        'duration' => 5,
        'starts_at' => now()->subMinutes(10),
        'ends_at' => now()->subMinute(),
        'created_by' => $teacher->id,
        'is_published' => true,
    ]);

    $this->actingAs($student)->get('/student/quizzes')
        ->assertDontSee('آزمون بسته‌شده');

    $this->actingAs($student)->get("/student/quizzes/{$quiz->id}")
        ->assertNotFound();

    $this->actingAs($student)->post("/student/quizzes/{$quiz->id}/start")
        ->assertNotFound();
});
