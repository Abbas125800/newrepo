@extends('layouts.auth', ['title' => 'مدیریت آزمون‌ها'])

@section('content')
    <section class="panel" style="margin-bottom: 20px;">
        <div class="dashboard-header">
            <div>
                <div class="eyebrow">پنل استاد</div>
                <h2>مدیریت آزمون‌ها</h2>
                <p class="muted">از این بخش می‌توانید آزمون‌ها را بسازید، ویرایش کنید، کنسل کنید، دوباره فعال کنید و در صورت نیاز حذف کامل انجام دهید.</p>
            </div>
            <div class="inline-actions">
                <a class="button primary" href="{{ route('teacher.quizzes.create') }}">ساخت آزمون</a>
                <a class="button secondary" href="{{ route('teacher.dashboard') }}">بازگشت به داشبورد</a>
            </div>
        </div>
    </section>

    <section class="card">
        <div class="list">
            @forelse ($quizzes as $quiz)
                <div class="list-item">
                    <div>
                        <strong>{{ $quiz->title }}</strong>
                        <div class="muted">
                            {{ $quiz->questions_count }} سوال، {{ $quiz->attempts_count }} تلاش، {{ $quiz->duration }} دقیقه
                        </div>
                    </div>
                    <div class="inline-actions">
                        <span class="badge {{ $quiz->cancelled_at ? '' : ($quiz->is_published ? 'ok' : '') }}">
                            {{ $quiz->cancelled_at ? 'کنسل‌شده' : ($quiz->is_published ? 'منتشرشده' : 'پیش‌نویس') }}
                        </span>
                        <a class="button secondary" href="{{ route('teacher.quizzes.edit', $quiz) }}">ویرایش</a>
                        <a class="button secondary" href="{{ route('teacher.quizzes.show', $quiz) }}">مشاهده</a>
                    </div>
                </div>
            @empty
                <div class="empty">هنوز آزمونی نساخته‌اید.</div>
            @endforelse
        </div>
    </section>
@endsection
