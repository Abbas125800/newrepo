@extends('layouts.auth', ['title' => 'آزمون‌های منتشرشده'])

@section('content')
    <section class="panel" style="margin-bottom: 20px;">
        <div class="dashboard-header">
            <div>
                <div class="eyebrow">آزمون‌های شاگرد</div>
                <h2>آزمون‌های منتشرشده</h2>
                <p class="muted">آزمون مناسب را انتخاب کنید، شروع کنید و بعد از ثبت پاسخ‌ها همان لحظه نتیجه را ببینید.</p>
            </div>
            <div class="inline-actions">
                <a class="button secondary" href="{{ route('student.dashboard') }}">بازگشت به داشبورد</a>
            </div>
        </div>
    </section>

    <section class="grid two">
        @forelse ($quizzes as $quiz)
            <article class="card">
                <h3>{{ $quiz->title }}</h3>
                <p class="muted">{{ $quiz->description }}</p>
                <div class="inline-actions" style="justify-content:space-between; width:100%;">
                    <span class="badge">{{ $quiz->questions_count }} سوال</span>
                    <span class="badge ok">{{ $quiz->duration }} دقیقه</span>
                </div>
                <div class="muted" style="margin: 14px 0 18px;">استاد: {{ $quiz->creator?->name ?? 'نامشخص' }}</div>
                <a class="button primary" href="{{ route('student.quizzes.show', $quiz) }}">مشاهده و شروع</a>
            </article>
        @empty
            <div class="empty">فعلاً آزمون منتشرشده‌ای وجود ندارد.</div>
        @endforelse
    </section>
@endsection
