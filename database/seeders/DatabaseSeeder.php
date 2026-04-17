<?php

namespace Database\Seeders;

use App\Models\Attempts;
use App\Models\Options;
use App\Models\Questions;
use App\Models\Quizzes;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $teacher = User::query()->updateOrCreate([
            'email' => 'teacher@example.com',
        ], [
            'name' => 'استاد نمونه',
            'role' => 'teacher',
            'approved_at' => now(),
            'password' => Hash::make('password123'),
        ]);

        $student = User::query()->updateOrCreate([
            'email' => 'student@example.com',
        ], [
            'name' => 'شاگرد نمونه',
            'role' => 'student',
            'approved_at' => now(),
            'approved_by' => $teacher->id,
            'password' => Hash::make('password123'),
        ]);

        $quiz = Quizzes::query()->firstOrCreate([
            'title' => 'آزمون نمونه HTML',
        ], [
            'description' => 'آزمون نمایشی برای داشبورد مدرن سیستم.',
            'duration' => 30,
            'starts_at' => Carbon::now()->subMinutes(10),
            'ends_at' => Carbon::now()->addMinutes(20),
            'created_by' => $teacher->id,
            'is_published' => true,
        ]);

        if ($quiz->questions()->count() === 0) {
            $questionOne = Questions::create([
                'quiz_id' => $quiz->id,
                'question_text' => 'کدام تگ برای عنوان اصلی صفحه استفاده می‌شود؟',
                'type' => 'mcq',
            ]);

            foreach ([
                ['text' => '<h1>', 'correct' => true],
                ['text' => '<p>', 'correct' => false],
                ['text' => '<span>', 'correct' => false],
                ['text' => '<img>', 'correct' => false],
            ] as $option) {
                Options::create([
                    'question_id' => $questionOne->id,
                    'option_text' => $option['text'],
                    'is_correct' => $option['correct'],
                ]);
            }

            $questionTwo = Questions::create([
                'quiz_id' => $quiz->id,
                'question_text' => 'کدام ویژگی CSS برای رنگ متن استفاده می‌شود؟',
                'type' => 'mcq',
            ]);

            foreach ([
                ['text' => 'color', 'correct' => true],
                ['text' => 'background', 'correct' => false],
                ['text' => 'padding', 'correct' => false],
                ['text' => 'display', 'correct' => false],
            ] as $option) {
                Options::create([
                    'question_id' => $questionTwo->id,
                    'option_text' => $option['text'],
                    'is_correct' => $option['correct'],
                ]);
            }
        }

        Attempts::query()->firstOrCreate([
            'user_id' => $student->id,
            'quiz_id' => $quiz->id,
        ], [
            'score' => 85,
            'started_at' => now()->subHour(),
            'finished_at' => now()->subMinutes(25),
            'status' => 'finished',
        ]);
    }
}
