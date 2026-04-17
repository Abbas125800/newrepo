<?php

namespace App\Http\Controllers;

use App\Models\Answers;
use App\Models\Attempts;
use App\Models\Options;
use App\Models\Questions;
use App\Models\Quizzes;
use App\Models\Quiz_Results;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class QuizController extends Controller
{
    public function teacherIndex(): View
    {
        $quizzes = Quizzes::query()
            ->withCount(['questions', 'attempts'])
            ->where('created_by', auth()->id())
            ->latest()
            ->get();

        return view('teacher.quizzes.index', compact('quizzes'));
    }

    public function teacherCreate(): View
    {
        return view('teacher.quizzes.create');
    }

    public function teacherStore(Request $request): RedirectResponse
    {
        $validated = $this->validateQuizPayload($request);

        $quiz = DB::transaction(function () use ($validated) {
            $quiz = Quizzes::create([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'duration' => $validated['duration'],
                'starts_at' => $this->normalizeQuizStart($validated['starts_at']),
                'ends_at' => $this->normalizeQuizEnd($validated['starts_at'], $validated['duration']),
                'created_by' => auth()->id(),
                'is_published' => true,
                'cancelled_at' => null,
            ]);

            $this->syncQuestions($quiz, collect($validated['questions']));

            return $quiz;
        });

        return redirect()
            ->route('teacher.quizzes.show', $quiz)
            ->with('status', 'آزمون با موفقیت ساخته شد.');
    }

    public function teacherShow(Quizzes $quiz): View
    {
        abort_unless($quiz->created_by === auth()->id(), 403);

        $quiz->load([
            'questions.options',
            'attempts.user.result',
        ]);

        return view('teacher.quizzes.show', compact('quiz'));
    }

    public function teacherEdit(Quizzes $quiz): View
    {
        abort_unless($quiz->created_by === auth()->id(), 403);

        $quiz->load('questions.options');

        return view('teacher.quizzes.edit', compact('quiz'));
    }

    public function teacherUpdate(Request $request, Quizzes $quiz): RedirectResponse
    {
        abort_unless($quiz->created_by === auth()->id(), 403);

        $validated = $this->validateQuizPayload($request);

        DB::transaction(function () use ($quiz, $validated): void {
            $quiz->update([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'duration' => $validated['duration'],
                'starts_at' => $this->normalizeQuizStart($validated['starts_at']),
                'ends_at' => $this->normalizeQuizEnd($validated['starts_at'], $validated['duration']),
                'is_published' => true,
            ]);

            $this->syncQuestions($quiz, collect($validated['questions']));
        });

        return redirect()
            ->route('teacher.quizzes.show', $quiz)
            ->with('status', 'آزمون و سوالات با موفقیت ویرایش شدند.');
    }

    public function teacherDestroy(Quizzes $quiz): RedirectResponse
    {
        abort_unless($quiz->created_by === auth()->id(), 403);

        $quiz->delete();

        return redirect()
            ->route('teacher.quizzes.index')
            ->with('status', 'آزمون به طور کامل حذف شد.');
    }

    public function teacherCancel(Quizzes $quiz): RedirectResponse
    {
        abort_unless($quiz->created_by === auth()->id(), 403);

        $quiz->update([
            'is_published' => false,
            'cancelled_at' => now(),
        ]);

        return back()->with('status', 'آزمون کنسل شد و دیگر برای شاگردان قابل شروع نیست.');
    }

    public function teacherRestart(Quizzes $quiz): RedirectResponse
    {
        abort_unless($quiz->created_by === auth()->id(), 403);

        $quiz->update([
            'is_published' => true,
            'cancelled_at' => null,
        ]);

        return back()->with('status', 'آزمون دوباره فعال شد و شاگردان می‌توانند آن را شروع کنند.');
    }

    public function studentIndex(): View
    {
        $quizzes = Quizzes::query()
            ->with(['creator'])
            ->withCount('questions')
            ->where('is_published', true)
            ->whereNull('cancelled_at')
            ->whereNotNull('starts_at')
            ->whereNotNull('ends_at')
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>', now())
            ->latest()
            ->get();

        return view('student.quizzes.index', compact('quizzes'));
    }

    public function studentShow(Quizzes $quiz): View
    {
        abort_unless($quiz->isAvailableForStudents(), 404);

        $quiz->load(['creator', 'questions.options']);

        $existingAttempt = Attempts::query()
            ->where('user_id', auth()->id())
            ->where('quiz_id', $quiz->id)
            ->where('status', 'in_progress')
            ->latest()
            ->first();

        return view('student.quizzes.show', compact('quiz', 'existingAttempt'));
    }

    public function start(Quizzes $quiz): RedirectResponse
    {
        abort_unless($quiz->isAvailableForStudents(), 404);

        $attempt = Attempts::query()->firstOrCreate(
            [
                'user_id' => auth()->id(),
                'quiz_id' => $quiz->id,
                'status' => 'in_progress',
            ],
            [
                'score' => 0,
                'started_at' => now(),
            ]
        );

        return redirect()->route('student.attempts.show', $attempt);
    }

    public function attempt(Attempts $attempt): View|RedirectResponse
    {
        abort_unless($attempt->user_id === auth()->id(), 403);

        if ($attempt->quiz->hasEnded() || $attempt->quiz->isCancelled()) {
            if ($attempt->status !== 'finished') {
                $attempt->update([
                    'finished_at' => now(),
                    'status' => 'expired',
                ]);
            }

            return redirect()
                ->route('student.quizzes.index')
                ->with('status', 'زمان این آزمون به پایان رسیده و دیگر قابل شرکت نیست.');
        }

        if ($attempt->status === 'finished') {
            return redirect()->route('student.attempts.result', $attempt);
        }

        $attempt->load(['quiz.questions.options']);
        $selectedAnswers = $attempt->answers()->pluck('option_id', 'question_id');

        return view('student.quizzes.attempt', compact('attempt', 'selectedAnswers'));
    }

    public function submit(Request $request, Attempts $attempt): RedirectResponse
    {
        abort_unless($attempt->user_id === auth()->id(), 403);
        abort_if($attempt->status === 'finished', 403);

        $attempt->load(['quiz.questions.options']);

        $validated = $request->validate([
            'answers' => ['nullable', 'array'],
            'answers.*' => ['nullable'],
        ]);

        DB::transaction(function () use ($attempt, $validated): void {
            $attempt->answers()->delete();
            $attempt->result()->delete();

            $correctAnswers = 0;
            $wrongAnswers = 0;
            $totalQuestions = $attempt->quiz->questions->count();

            foreach ($attempt->quiz->questions as $question) {
                $submittedAnswer = $validated['answers'][$question->id] ?? null;

                if ($question->type === 'short_answer') {
                    $submittedText = trim((string) $submittedAnswer);

                    if ($submittedText === '') {
                        $wrongAnswers++;
                        continue;
                    }

                    Answers::create([
                        'attempt_id' => $attempt->id,
                        'question_id' => $question->id,
                        'option_id' => null,
                        'answer_text' => $submittedText,
                    ]);

                    $isCorrect = mb_strtolower($submittedText) === mb_strtolower(trim((string) $question->correct_text));

                    if ($isCorrect) {
                        $correctAnswers++;
                    } else {
                        $wrongAnswers++;
                    }

                    continue;
                }

                if (! $submittedAnswer) {
                    $wrongAnswers++;
                    continue;
                }

                $selectedOption = $question->options->firstWhere('id', (int) $submittedAnswer);

                if (! $selectedOption) {
                    $wrongAnswers++;
                    continue;
                }

                Answers::create([
                    'attempt_id' => $attempt->id,
                    'question_id' => $question->id,
                    'option_id' => $selectedOption->id,
                    'answer_text' => null,
                ]);

                if ($selectedOption->is_correct) {
                    $correctAnswers++;
                } else {
                    $wrongAnswers++;
                }
            }

            $percentage = $totalQuestions > 0
                ? round(($correctAnswers / $totalQuestions) * 100, 2)
                : 0;

            $attempt->update([
                'score' => $correctAnswers,
                'finished_at' => now(),
                'status' => 'finished',
            ]);

            Quiz_Results::create([
                'attempt_id' => $attempt->id,
                'total_questions' => $totalQuestions,
                'correct_answers' => $correctAnswers,
                'wrong_answers' => $wrongAnswers,
                'percentage' => $percentage,
            ]);
        });

        return redirect()
            ->route('student.attempts.result', $attempt)
            ->with('status', 'نتیجه امتحان شما فوراً محاسبه شد.');
    }

    public function result(Attempts $attempt): View
    {
        abort_unless($attempt->user_id === auth()->id(), 403);

        $attempt->load([
            'quiz.creator',
            'quiz.questions.options',
            'answers.option',
            'result',
        ]);

        abort_unless($attempt->status === 'finished', 404);

        $answersByQuestion = $attempt->answers->keyBy('question_id');

        return view('student.quizzes.result', compact('attempt', 'answersByQuestion'));
    }

    protected function validateQuizPayload(Request $request): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'duration' => ['required', 'integer', 'min:1', 'max:300'],
            'starts_at' => ['required', 'date'],
            'questions' => ['required', 'array', 'min:1'],
            'questions.*.question_text' => ['required', 'string'],
            'questions.*.type' => ['required', Rule::in(['mcq', 'true_false', 'short_answer'])],
            'questions.*.correct_text' => ['nullable', 'string', 'max:255'],
            'questions.*.options' => ['nullable', 'array'],
            'questions.*.options.*.option_text' => ['nullable', 'string', 'max:255'],
            'questions.*.correct_option' => ['nullable'],
        ]);

        $validator = Validator::make($validated, []);

        foreach ($validated['questions'] as $index => $question) {
            if ($question['type'] === 'short_answer') {
                if (blank($question['correct_text'] ?? null)) {
                    $validator->errors()->add("questions.$index.correct_text", 'برای سوال پاسخ کوتاه، پاسخ صحیح لازم است.');
                }

                continue;
            }

            $expectedOptionCount = $question['type'] === 'true_false' ? 2 : 4;
            $options = collect($question['options'] ?? [])
                ->pluck('option_text')
                ->filter(fn ($value) => filled($value));

            if ($options->count() !== $expectedOptionCount) {
                $validator->errors()->add("questions.$index.options", 'تعداد گزینه‌های سوال کامل نیست.');
            }

            $correctOption = $question['correct_option'] ?? null;

            if (! is_numeric($correctOption) || (int) $correctOption < 0 || (int) $correctOption >= $expectedOptionCount) {
                $validator->errors()->add("questions.$index.correct_option", 'پاسخ صحیح سوال انتخاب نشده است.');
            }
        }

        if ($validator->errors()->isNotEmpty()) {
            throw new ValidationException($validator);
        }

        return $validated;
    }

    protected function syncQuestions(Quizzes $quiz, Collection $questions): void
    {
        $quiz->questions()->delete();

        foreach ($questions as $questionData) {
            $question = Questions::create([
                'quiz_id' => $quiz->id,
                'question_text' => $questionData['question_text'],
                'type' => $questionData['type'],
                'correct_text' => $questionData['type'] === 'short_answer'
                    ? trim((string) ($questionData['correct_text'] ?? ''))
                    : null,
            ]);

            if ($questionData['type'] === 'short_answer') {
                continue;
            }

            $limit = $questionData['type'] === 'true_false' ? 2 : 4;

            foreach (array_slice($questionData['options'] ?? [], 0, $limit) as $index => $optionData) {
                Options::create([
                    'question_id' => $question->id,
                    'option_text' => $optionData['option_text'],
                    'is_correct' => $index === (int) $questionData['correct_option'],
                ]);
            }
        }
    }

    protected function normalizeQuizStart(string $value): Carbon
    {
        return Carbon::parse($value, config('app.timezone'));
    }

    protected function normalizeQuizEnd(string $value, int $duration): Carbon
    {
        return Carbon::parse($value, config('app.timezone'))
            ->addMinutes($duration);
    }
}
