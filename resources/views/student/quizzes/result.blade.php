@extends('layouts.auth', ['title' => 'نتیجه آزمون'])

@section('content')
    <section class="hero">
        <div class="panel">
            <div class="eyebrow">نتیجه فوری آزمون</div>
            <h2>{{ $attempt->quiz->title }}</h2>
            <p class="lead">امتحان شما ثبت شد و نتیجه بلافاصله محاسبه گردید.</p>

            <div class="metrics">
                <div class="metric">
                    <strong>{{ $attempt->score }}</strong>
                    <span class="muted">پاسخ صحیح</span>
                </div>
                <div class="metric">
                    <strong>{{ $attempt->result?->wrong_answers ?? 0 }}</strong>
                    <span class="muted">پاسخ غلط</span>
                </div>
                <div class="metric">
                    <strong>{{ $attempt->result?->percentage ?? 0 }}%</strong>
                    <span class="muted">درصد نهایی</span>
                </div>
            </div>

            <div class="inline-actions" style="margin-top: 22px;">
                <a class="button primary" href="{{ route('student.quizzes.index') }}">امتحان‌های دیگر</a>
                <a class="button secondary" href="{{ route('student.dashboard') }}">بازگشت به داشبورد</a>
            </div>
        </div>

        <div class="panel">
            <div class="eyebrow">خلاصه ثبت</div>
            <div class="list">
                <div class="list-item">
                    <span>استاد برگزارکننده</span>
                    <strong>{{ $attempt->quiz->creator?->name }}</strong>
                </div>
                <div class="list-item">
                    <span>تعداد کل سوالات</span>
                    <strong>{{ $attempt->result?->total_questions ?? 0 }}</strong>
                </div>
                <div class="list-item">
                    <span>زمان پایان</span>
                    <strong>{{ $attempt->finished_at?->format('Y/m/d H:i') }}</strong>
                </div>
            </div>
        </div>
    </section>

    <section class="card">
        <h3>مرور پاسخ‌ها</h3>
        <div class="list" style="margin-top: 16px;">
            @foreach ($attempt->quiz->questions as $question)
                @php($answer = $answersByQuestion->get($question->id))
                @php($correctOption = $question->options->firstWhere('is_correct', true))
                <div class="list-item" style="align-items:start;">
                    <div>
                        <strong>{{ $loop->iteration }}. {{ $question->question_text }}</strong>
                        <div class="muted" style="margin-top: 8px;">
                            @if ($question->type === 'short_answer')
                                <div>پاسخ صحیح: {{ $question->correct_text }}</div>
                                <div>پاسخ شما: {{ $answer?->answer_text ?? 'بدون پاسخ' }}</div>
                            @else
                                <div>پاسخ شما: {{ $answer?->option?->option_text ?? 'بدون پاسخ' }}</div>
                                <div>پاسخ صحیح: {{ $correctOption?->option_text }}</div>
                            @endif
                        </div>
                    </div>
                    <span class="badge {{ ($question->type === 'short_answer'
                        ? (mb_strtolower(trim((string) ($answer?->answer_text))) === mb_strtolower(trim((string) $question->correct_text)))
                        : ($answer?->option?->is_correct)) ? 'ok' : '' }}">
                        {{ ($question->type === 'short_answer'
                            ? (mb_strtolower(trim((string) ($answer?->answer_text))) === mb_strtolower(trim((string) $question->correct_text)))
                            : ($answer?->option?->is_correct)) ? 'درست' : 'غلط' }}
                    </span>
                </div>
            @endforeach
        </div>
    </section>
@endsection
