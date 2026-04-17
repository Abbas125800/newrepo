@extends('layouts.auth', ['title' => $quiz->title])

@section('content')
    <section class="panel" style="margin-bottom: 20px;">
        <div class="dashboard-header">
            <div>
                <div class="eyebrow">جزئیات آزمون</div>
                <h2>{{ $quiz->title }}</h2>
                <p class="muted">{{ $quiz->description }}</p>
            </div>
            <div class="inline-actions">
                <span class="badge {{ $quiz->cancelled_at ? '' : ($quiz->is_published ? 'ok' : '') }}">
                    {{ $quiz->cancelled_at ? 'کنسل‌شده' : ($quiz->is_published ? 'منتشرشده' : 'پیش‌نویس') }}
                </span>
                <a class="button secondary" href="{{ route('teacher.quizzes.edit', $quiz) }}">ویرایش آزمون</a>
                <a class="button secondary" href="{{ route('teacher.quizzes.index') }}">بازگشت</a>
            </div>
        </div>

        <div class="metrics">
            <div class="metric">
                <strong>{{ $quiz->questions->count() }}</strong>
                <span class="muted">سوال</span>
            </div>
            <div class="metric">
                <strong>{{ $quiz->attempts->count() }}</strong>
                <span class="muted">تلاش ثبت‌شده</span>
            </div>
            <div class="metric">
                <strong>{{ $quiz->duration }}</strong>
                <span class="muted">دقیقه</span>
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
                <span>وضعیت زمانی</span>
                <strong>
                    @if ($quiz->cancelled_at)
                        کنسل‌شده
                    @elseif ($quiz->hasEnded())
                        بسته‌شده
                    @elseif ($quiz->hasStarted())
                        فعال
                    @else
                        زمان‌بندی‌شده
                    @endif
                </strong>
            </div>
        </div>

        <div class="inline-actions" style="margin-top: 20px;">
            @if ($quiz->cancelled_at)
                <form method="POST" action="{{ route('teacher.quizzes.restart', $quiz) }}">
                    @csrf
                    @method('PATCH')
                    <button class="button primary" type="submit">شروع دوباره آزمون</button>
                </form>
            @else
                <form method="POST" action="{{ route('teacher.quizzes.cancel', $quiz) }}">
                    @csrf
                    @method('PATCH')
                    <button class="button secondary" type="submit">کنسل کردن آزمون</button>
                </form>
            @endif

            <form method="POST" action="{{ route('teacher.quizzes.destroy', $quiz) }}" onsubmit="return confirm('آیا از حذف کامل این آزمون مطمئن هستید؟');">
                @csrf
                @method('DELETE')
                <button class="button secondary" type="submit">حذف کامل آزمون</button>
            </form>
        </div>
    </section>

    <section class="grid two">
        <div class="card">
            <h3>سوالات آزمون</h3>
            <div class="list">
                @foreach ($quiz->questions as $question)
                    <div class="list-item" style="align-items:start;">
                        <div>
                            <strong>{{ $loop->iteration }}. {{ $question->question_text }}</strong>
                            <div class="muted" style="margin-top:8px;">نوع سوال: {{ $question->type }}</div>
                            @if ($question->type === 'short_answer')
                                <div class="muted" style="margin-top:8px;">پاسخ صحیح: {{ $question->correct_text }}</div>
                            @else
                                <div class="muted" style="margin-top:8px;">
                                    @foreach ($question->options as $option)
                                        <div>{{ $option->is_correct ? '✓' : '•' }} {{ $option->option_text }}</div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="card">
            <h3>آخرین تلاش‌ها</h3>
            <div class="list">
                @forelse ($quiz->attempts as $attempt)
                    <div class="list-item">
                        <div>
                            <strong>{{ $attempt->user?->name ?? 'شاگرد' }}</strong>
                            <div class="muted">
                                امتیاز: {{ $attempt->score }} |
                                وضعیت: {{ $attempt->status }}
                                @if ($attempt->result)
                                    | درصد: {{ $attempt->result->percentage }}%
                                @endif
                            </div>
                        </div>
                        <span class="badge {{ $attempt->status === 'finished' ? 'ok' : '' }}">{{ $attempt->status }}</span>
                    </div>
                @empty
                    <div class="empty">هنوز کسی در این آزمون شرکت نکرده است.</div>
                @endforelse
            </div>
        </div>
    </section>
@endsection
