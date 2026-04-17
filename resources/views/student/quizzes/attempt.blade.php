@extends('layouts.auth', ['title' => 'شرکت در آزمون'])

@section('content')
    <section class="panel" style="margin-bottom: 20px;">
        <div class="dashboard-header">
            <div>
                <div class="eyebrow">در حال امتحان</div>
                <h2>{{ $attempt->quiz->title }}</h2>
                <p class="muted">همه سوالات را پاسخ دهید و سپس آزمون را نهایی کنید تا نمره شما فوراً اعلام شود.</p>
            </div>
            <div class="inline-actions">
                <span class="badge">{{ $attempt->quiz->duration }} دقیقه</span>
                <span class="badge ok">{{ $attempt->quiz->questions->count() }} سوال</span>
                <span class="badge" id="attempt-countdown" data-deadline="{{ $attempt->quiz->ends_at?->toIso8601String() }}">در حال محاسبه...</span>
            </div>
        </div>
    </section>

    @if ($errors->any())
        <div class="alert error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('student.attempts.submit', $attempt) }}" class="grid">
        @csrf
        @foreach ($attempt->quiz->questions as $question)
            <section class="card">
                <h3>{{ $loop->iteration }}. {{ $question->question_text }}</h3>
                <div class="muted" style="margin-top: 6px;">نوع سوال: {{ $question->type }}</div>

                @if ($question->type === 'short_answer')
                    <div class="input-row" style="margin-top: 16px;">
                        <label class="field-label">پاسخ شما</label>
                        <input class="input" type="text" name="answers[{{ $question->id }}]" value="{{ old("answers.$question->id") }}">
                    </div>
                @else
                    <div class="list" style="margin-top: 16px;">
                        @foreach ($question->options as $option)
                            <label class="list-item" style="cursor:pointer;">
                                <div>{{ $option->option_text }}</div>
                                <input
                                    type="radio"
                                    name="answers[{{ $question->id }}]"
                                    value="{{ $option->id }}"
                                    @checked(old("answers.$question->id", $selectedAnswers[$question->id] ?? null) == $option->id)
                                >
                            </label>
                        @endforeach
                    </div>
                @endif
            </section>
        @endforeach

        <button class="button primary" type="submit">ثبت نهایی و مشاهده نتیجه</button>
    </form>

    <script>
        const attemptCountdown = document.getElementById('attempt-countdown');
        const attemptForm = document.querySelector('form[action*="/submit"]');

        if (attemptCountdown && attemptForm) {
            const deadline = new Date(attemptCountdown.dataset.deadline).getTime();
            let submitted = false;

            const updateCountdown = () => {
                const diff = deadline - Date.now();

                if (diff <= 0) {
                    attemptCountdown.textContent = 'زمان تمام شد';

                    if (!submitted) {
                        submitted = true;
                        attemptForm.submit();
                    }

                    return;
                }

                const totalSeconds = Math.floor(diff / 1000);
                const hours = Math.floor(totalSeconds / 3600);
                const minutes = Math.floor((totalSeconds % 3600) / 60);
                const seconds = totalSeconds % 60;

                attemptCountdown.textContent = `زمان باقی‌مانده ${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            };

            updateCountdown();
            setInterval(updateCountdown, 1000);
        }
    </script>
@endsection
