<?php

namespace App\Http\Controllers;

use App\Models\Attempts;
use App\Models\Quizzes;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function redirect(): mixed
    {
        $user = auth()->user();

        return match ($user->role) {
            'teacher' => redirect()->route('teacher.dashboard'),
            'student' => redirect()->route('student.dashboard'),
            default => abort(403),
        };
    }

    public function teacher(): View
    {
        $user = auth()->user();

        $stats = [
            'quizzes_count' => Quizzes::query()->where('created_by', $user->id)->count(),
            'published_quizzes_count' => Quizzes::query()->where('created_by', $user->id)->where('is_published', true)->count(),
            'student_attempts_count' => Attempts::query()
                ->whereHas('quiz', fn ($query) => $query->where('created_by', $user->id))
                ->count(),
            'students_count' => User::query()->where('role', 'student')->count(),
            'pending_students_count' => User::query()->where('role', 'student')->whereNull('approved_at')->count(),
        ];

        $recentQuizzes = Quizzes::query()
            ->where('created_by', $user->id)
            ->latest()
            ->take(5)
            ->get();

        $recentAttempts = Attempts::query()
            ->with(['user', 'quiz'])
            ->whereHas('quiz', fn ($query) => $query->where('created_by', $user->id))
            ->latest()
            ->take(6)
            ->get();

        return view('dashboards.teacher', compact('user', 'stats', 'recentQuizzes', 'recentAttempts'));
    }

    public function student(): View
    {
        $user = auth()->user();

        $stats = [
            'available_quizzes_count' => Quizzes::query()->where('is_published', true)->count(),
            'attempts_count' => Attempts::query()->where('user_id', $user->id)->count(),
            'completed_attempts_count' => Attempts::query()->where('user_id', $user->id)->where('status', 'finished')->count(),
            'average_score' => round((float) Attempts::query()->where('user_id', $user->id)->avg('score'), 1),
        ];

        $availableQuizzes = Quizzes::query()
            ->with('creator')
            ->where('is_published', true)
            ->whereNull('cancelled_at')
            ->whereNotNull('starts_at')
            ->whereNotNull('ends_at')
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>', now())
            ->latest()
            ->take(6)
            ->get();

        $recentAttempts = Attempts::query()
            ->with('quiz')
            ->where('user_id', $user->id)
            ->latest()
            ->take(6)
            ->get();

        return view('dashboards.student', compact('user', 'stats', 'availableQuizzes', 'recentAttempts'));
    }
}
