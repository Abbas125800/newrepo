@extends('layouts.auth', ['title' => 'ویرایش آزمون'])

@section('content')
    <section class="panel" style="margin-bottom: 20px;">
        <div class="dashboard-header">
            <div>
                <div class="eyebrow">ویرایش کامل آزمون</div>
                <h2>{{ $quiz->title }}</h2>
                <p class="muted">در این بخش می‌توانید متن آزمون، زمان، سوالات، نوع سوال و پاسخ‌های صحیح را هر زمان تغییر دهید.</p>
            </div>
            <div class="inline-actions">
                <a class="button secondary" href="{{ route('teacher.quizzes.show', $quiz) }}">بازگشت به جزئیات</a>
            </div>
        </div>
    </section>

    @include('teacher.quizzes._form', [
        'quiz' => $quiz,
        'action' => route('teacher.quizzes.update', $quiz),
        'method' => 'PUT',
        'submitLabel' => 'ذخیره تغییرات',
    ])
@endsection
