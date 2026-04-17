@extends('layouts.auth', ['title' => 'داشبورد استاد'])

@section('content')
    <section class="panel" style="margin-bottom: 20px;">
        <div class="dashboard-header">
            <div>
                <div class="eyebrow">داشبورد استاد</div>
                <h2>{{ $user->name }}، مدیریت سیستم از اینجا انجام می‌شود</h2>
                <p class="muted">از این بخش می‌توانید آزمون‌ها را مدیریت کنید، شاگردان را تایید کنید و وضعیت تلاش‌ها را زیر نظر داشته باشید.</p>
            </div>

            <div class="inline-actions">
                <a class="button primary" href="{{ route('teacher.quizzes.create') }}">ایجاد آزمون جدید</a>
                <a class="button secondary" href="{{ route('teacher.quizzes.index') }}">مدیریت آزمون‌ها</a>
                <a class="button secondary" href="{{ route('teacher.students.index') }}">مدیریت شاگردان</a>
                <span class="badge">Teacher</span>
                <span class="badge ok">{{ $user->email }}</span>
            </div>
        </div>

        <div class="metrics">
            <div class="metric">
                <strong>{{ $stats['quizzes_count'] }}</strong>
                <span class="muted">کل آزمون‌های شما</span>
            </div>
            <div class="metric">
                <strong>{{ $stats['published_quizzes_count'] }}</strong>
                <span class="muted">آزمون منتشرشده</span>
            </div>
            <div class="metric">
                <strong>{{ $stats['student_attempts_count'] }}</strong>
                <span class="muted">تلاش ثبت‌شده شاگردان</span>
            </div>
            <div class="metric">
                <strong>{{ $stats['pending_students_count'] }}</strong>
                <span class="muted">شاگرد در انتظار تایید</span>
            </div>
        </div>
    </section>

    <section class="grid two">
        <div class="card">
            <h3>آخرین آزمون‌های شما</h3>
            @forelse ($recentQuizzes as $quiz)
                <div class="list-item">
                    <div>
                        <strong>{{ $quiz->title }}</strong>
                        <div class="muted">{{ $quiz->duration }} دقیقه</div>
                    </div>
                    <a class="badge {{ $quiz->is_published ? 'ok' : '' }}" href="{{ route('teacher.quizzes.show', $quiz) }}">
                        {{ $quiz->is_published ? 'منتشرشده' : 'پیش‌نویس' }}
                    </a>
                </div>
            @empty
                <div class="empty">هنوز آزمونی توسط این حساب ثبت نشده است.</div>
            @endforelse
        </div>

        <div class="card">
            <h3>آخرین فعالیت شاگردان</h3>
            @forelse ($recentAttempts as $attempt)
                <div class="list-item">
                    <div>
                        <strong>{{ $attempt->user?->name ?? 'شاگرد' }}</strong>
                        <div class="muted">{{ $attempt->quiz?->title ?? 'آزمون' }}</div>
                    </div>
                    <span class="badge {{ $attempt->status === 'finished' ? 'ok' : '' }}">
                        {{ $attempt->status }}
                    </span>
                </div>
            @empty
                <div class="empty">هنوز هیچ تلاشی برای آزمون‌های شما ثبت نشده است.</div>
            @endforelse
        </div>
    </section>
@endsection
