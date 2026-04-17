@extends('layouts.auth', ['title' => 'ایجاد آزمون'])

@section('content')
    <section class="panel" style="margin-bottom: 20px;">
        <div class="dashboard-header">
            <div>
                <div class="eyebrow">آزمون‌ساز استاد</div>
                <h2>ایجاد آزمون جدید</h2>
                <p class="muted">شما می‌توانید سوال چهارگزینه‌ای، صحیح/غلط و پاسخ کوتاه بسازید و بعداً همه را ویرایش یا حذف کنید.</p>
            </div>
            <div class="inline-actions">
                <a class="button secondary" href="{{ route('teacher.quizzes.index') }}">بازگشت</a>
            </div>
        </div>
    </section>

    @include('teacher.quizzes._form', [
        'action' => route('teacher.quizzes.store'),
        'submitLabel' => 'ذخیره آزمون',
    ])
@endsection
