@extends('layouts.auth', ['title' => $quiz->title])

@section('content')
    <section class="hero">
        <div class="panel">
            <div class="eyebrow">آماده شرکت در آزمون</div>
            <h2>{{ $quiz->title }}</h2>
            <p class="lead">{{ $quiz->description }}</p>

            <div class="metrics">
                <div class="metric">
                    <strong>{{ $quiz->questions->count() }}</strong>
                    <span class="muted">سوال</span>
                </div>
                <div class="metric">
                    <strong>{{ $quiz->duration }}</strong>
                    <span class="muted">دقیقه</span>
                </div>
                <div class="metric">
                    <strong>{{ $quiz->creator?->name }}</strong>
                    <span class="muted">استاد برگزارکننده</span>
                </div>
            </div>

            <div class="list" style="margin-top: 20px;">
                <div class="list-item">
                    <span>شروع آزمون</span>
                    <strong>{{ $quiz->starts_at?->timezone(config('app.timezone'))->format('Y/m/d H:i') }}</strong>
                </div>
                <div class="list-item">
                    <span>پایان آزمون</span>
                    <strong>{{ $quiz->ends_at?->timezone(config('app.timezone'))->format('Y/m/d H:i') }}</strong>
                </div>
                <div class="list-item">
                    <span>زمان باقی‌مانده</span>
                    <strong id="quiz-countdown" data-deadline="{{ $quiz->ends_at?->toIso8601String() }}">در حال محاسبه...</strong>
                </div>
            </div>

            <form method="POST" action="{{ route('student.quizzes.start', $quiz) }}" style="margin-top: 22px;">
                @csrf
                <button class="button primary" type="submit">
                    {{ $existingAttempt ? 'ادامه امتحان' : 'شروع امتحان' }}
                </button>
            </form>
        </div>

        <div class="panel">
            <div class="eyebrow">پیش‌نمایش</div>
            <h3>سوالات این آزمون</h3>
            <div class="list">
                @foreach ($quiz->questions as $question)
                    <div class="list-item" style="align-items:start;">
                        <div>
                            <strong>{{ $loop->iteration }}. {{ $question->question_text }}</strong>
                            <div class="muted" style="margin-top:8px;">
                                @foreach ($question->options as $option)
                                    <div>• {{ $option->option_text }}</div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <script>
        const countdown = document.getElementById('quiz-countdown');

        if (countdown) {
            const deadline = new Date(countdown.dataset.deadline).getTime();

            const updateCountdown = () => {
                const diff = deadline - Date.now();

                if (diff <= 0) {
                    countdown.textContent = 'زمان آزمون تمام شده است';
                    return;
                }

                const totalSeconds = Math.floor(diff / 1000);
                const hours = Math.floor(totalSeconds / 3600);
                const minutes = Math.floor((totalSeconds % 3600) / 60);
                const seconds = totalSeconds % 60;

                countdown.textContent = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            };

            updateCountdown();
            setInterval(updateCountdown, 1000);
        }
    </script>
@endsection
