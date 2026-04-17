@extends('layouts.auth', ['title' => 'داشبورد شاگرد'])

@section('content')
    <section class="panel" style="margin-bottom: 20px;">
        <div class="dashboard-header">
            <div>
                <div class="eyebrow">داشبورد شاگرد</div>
                <h2>{{ $user->name }}، خوش آمدید</h2>
                <p class="muted">در این صفحه می‌توانید آزمون‌های منتشرشده را ببینید، سابقه تلاش‌های خود را مرور کنید و وضعیت نمره‌ها را دنبال کنید.</p>
            </div>

            <div class="inline-actions">
                <a class="button primary" href="{{ route('student.quizzes.index') }}">مشاهده آزمون‌ها</a>
                <span class="badge">Student</span>
                <span class="badge ok">{{ $user->email }}</span>
            </div>
        </div>

        <div class="metrics">
            <div class="metric">
                <strong>{{ $stats['available_quizzes_count'] }}</strong>
                <span class="muted">آزمون در دسترس</span>
            </div>
            <div class="metric">
                <strong>{{ $stats['attempts_count'] }}</strong>
                <span class="muted">کل تلاش‌های شما</span>
            </div>
            <div class="metric">
                <strong>{{ $stats['average_score'] }}</strong>
                <span class="muted">میانگین امتیاز</span>
            </div>
        </div>
    </section>

    <section class="grid two">
        <div class="card">
            <h3>آزمون‌های آماده شرکت</h3>
            @forelse ($availableQuizzes as $quiz)
                <div class="list-item">
                    <div>
                        <strong>{{ $quiz->title }}</strong>
                        <div class="muted">استاد: {{ $quiz->creator?->name ?? 'نامشخص' }}</div>
                    </div>
                    <a class="badge" href="{{ route('student.quizzes.show', $quiz) }}">{{ $quiz->duration }} دقیقه</a>
                </div>
            @empty
                <div class="empty">فعلاً آزمون منتشرشده‌ای برای شما وجود ندارد.</div>
            @endforelse
        </div>

        <div class="card">
            <h3>سابقه تلاش‌های اخیر</h3>
            @forelse ($recentAttempts as $attempt)
                <div class="list-item">
                    <div>
                        <strong>{{ $attempt->quiz?->title ?? 'آزمون' }}</strong>
                        <div class="muted">امتیاز: {{ $attempt->score }}</div>
                    </div>
                    <span class="badge {{ $attempt->status === 'finished' ? 'ok' : '' }}">
                        {{ $attempt->status }}
                    </span>
                </div>
            @empty
                <div class="empty">هنوز هیچ تلاشی توسط این حساب ثبت نشده است.</div>
            @endforelse
        </div>
    </section>
@endsection
