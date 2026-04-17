<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\StudentManagementController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::middleware(['auth', 'approved'])->group(function (): void {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'redirect'])->name('dashboard');

    Route::middleware('role:teacher')->group(function (): void {
        Route::get('/teacher/dashboard', [DashboardController::class, 'teacher'])->name('teacher.dashboard');
        Route::get('/teacher/students', [StudentManagementController::class, 'index'])->name('teacher.students.index');
        Route::patch('/teacher/students/{student}/approve', [StudentManagementController::class, 'approve'])->name('teacher.students.approve');
        Route::patch('/teacher/students/{student}/revoke', [StudentManagementController::class, 'revoke'])->name('teacher.students.revoke');
        Route::delete('/teacher/students/{student}', [StudentManagementController::class, 'destroy'])->name('teacher.students.destroy');
        Route::get('/teacher/quizzes', [QuizController::class, 'teacherIndex'])->name('teacher.quizzes.index');
        Route::get('/teacher/quizzes/create', [QuizController::class, 'teacherCreate'])->name('teacher.quizzes.create');
        Route::post('/teacher/quizzes', [QuizController::class, 'teacherStore'])->name('teacher.quizzes.store');
        Route::get('/teacher/quizzes/{quiz}/edit', [QuizController::class, 'teacherEdit'])->name('teacher.quizzes.edit');
        Route::get('/teacher/quizzes/{quiz}', [QuizController::class, 'teacherShow'])->name('teacher.quizzes.show');
        Route::put('/teacher/quizzes/{quiz}', [QuizController::class, 'teacherUpdate'])->name('teacher.quizzes.update');
        Route::delete('/teacher/quizzes/{quiz}', [QuizController::class, 'teacherDestroy'])->name('teacher.quizzes.destroy');
        Route::patch('/teacher/quizzes/{quiz}/cancel', [QuizController::class, 'teacherCancel'])->name('teacher.quizzes.cancel');
        Route::patch('/teacher/quizzes/{quiz}/restart', [QuizController::class, 'teacherRestart'])->name('teacher.quizzes.restart');
    });

    Route::middleware('role:student')->group(function (): void {
        Route::get('/student/dashboard', [DashboardController::class, 'student'])->name('student.dashboard');
        Route::get('/student/quizzes', [QuizController::class, 'studentIndex'])->name('student.quizzes.index');
        Route::get('/student/quizzes/{quiz}', [QuizController::class, 'studentShow'])->name('student.quizzes.show');
        Route::post('/student/quizzes/{quiz}/start', [QuizController::class, 'start'])->name('student.quizzes.start');
        Route::get('/student/attempts/{attempt}', [QuizController::class, 'attempt'])->name('student.attempts.show');
        Route::post('/student/attempts/{attempt}/submit', [QuizController::class, 'submit'])->name('student.attempts.submit');
        Route::get('/student/attempts/{attempt}/result', [QuizController::class, 'result'])->name('student.attempts.result');
    });
});
