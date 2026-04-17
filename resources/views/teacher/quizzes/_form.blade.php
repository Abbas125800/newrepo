@php
    $quiz = $quiz ?? null;
    $questionsData = old('questions');

    if ($questionsData === null && $quiz) {
        $questionsData = $quiz->questions->map(function ($question) {
            return [
                'question_text' => $question->question_text,
                'type' => $question->type,
                'correct_text' => $question->correct_text,
                'correct_option' => $question->options->search(fn ($option) => $option->is_correct),
                'options' => $question->options->map(fn ($option) => ['option_text' => $option->option_text])->values()->all(),
            ];
        })->values()->all();
    }

    $questionsJson = json_encode($questionsData ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
@endphp

@if ($errors->any())
    <div class="alert error">{{ $errors->first() }}</div>
@endif

<form method="POST" action="{{ $action }}" id="quiz-form">
    @csrf
    @isset($method)
        @method($method)
    @endisset

    <section class="card" style="margin-bottom: 20px;">
        <div class="grid two">
            <div class="input-row">
                <label class="field-label">عنوان آزمون</label>
                <input class="input" type="text" name="title" value="{{ old('title', $quiz->title ?? '') }}" required>
            </div>
            <div class="input-row">
                <label class="field-label">مدت آزمون (دقیقه)</label>
                <input class="input" type="number" min="1" max="300" name="duration" value="{{ old('duration', $quiz->duration ?? 30) }}" required>
            </div>
        </div>

        <div class="input-row">
            <label class="field-label">تاریخ و ساعت شروع آزمون</label>
            <input
                class="input"
                type="datetime-local"
                name="starts_at"
                value="{{ old('starts_at', optional(($quiz->starts_at ?? null)?->clone()->timezone(config('app.timezone')))->format('Y-m-d\TH:i')) }}"
                required
            >
        </div>

        <div class="input-row">
            <label class="field-label">توضیحات</label>
            <textarea class="input" name="description" rows="4">{{ old('description', $quiz->description ?? '') }}</textarea>
        </div>

        <div class="muted">
            آزمون به صورت خودکار در زمان تعیین‌شده منتشر می‌شود و پس از پایان زمان، به صورت خودکار بسته خواهد شد.
        </div>
    </section>

    <div id="questions-container" class="grid" style="margin-bottom: 20px;"></div>

    <div class="inline-actions">
        <button class="button secondary" type="button" id="add-question">افزودن سوال</button>
        <button class="button primary" type="submit">{{ $submitLabel }}</button>
    </div>
</form>

<template id="question-template">
    <section class="card question-block">
        <div class="dashboard-header" style="margin-bottom: 16px;">
            <h3 class="question-title">سوال جدید</h3>
            <button class="button secondary remove-question" type="button">حذف سوال</button>
        </div>

        <div class="input-row">
            <label class="field-label">متن سوال</label>
            <textarea class="input question-text" rows="3" required></textarea>
        </div>

        <div class="input-row">
            <label class="field-label">نوع سوال</label>
            <select class="select question-type">
                <option value="mcq">چهارگزینه‌ای</option>
                <option value="true_false">صحیح / غلط</option>
                <option value="short_answer">پاسخ کوتاه</option>
            </select>
        </div>

        <div class="mcq-area">
            <div class="grid two options-container"></div>
        </div>

        <div class="short-answer-area" style="display:none;">
            <div class="input-row">
                <label class="field-label">پاسخ صحیح</label>
                <input class="input short-answer-correct" type="text">
            </div>
        </div>
    </section>
</template>

<script>
    const container = document.getElementById('questions-container');
    const template = document.getElementById('question-template');
    const addButton = document.getElementById('add-question');
    const initialQuestions = {!! $questionsJson !!} || [];

    function escapeHtml(value) {
        return String(value ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    function buildOptionBlock(questionIndex, optionIndex, value = '') {
        const wrapper = document.createElement('div');
        wrapper.className = 'input-row';
        wrapper.innerHTML = `
            <label class="field-label">گزینه ${optionIndex + 1}</label>
            <input class="input" type="text" name="questions[${questionIndex}][options][${optionIndex}][option_text]" value="${escapeHtml(value)}">
            <label style="display:flex; align-items:center; gap:10px; margin-top:10px; color:var(--muted);">
                <input type="radio" name="questions[${questionIndex}][correct_option]" value="${optionIndex}">
                <span>پاسخ صحیح</span>
            </label>
        `;
        return wrapper;
    }

    function refreshQuestionTitles() {
        document.querySelectorAll('.question-block').forEach((block, index) => {
            block.querySelector('.question-title').textContent = `سوال ${index + 1}`;
        });
    }

    function renderQuestionOptions(block, questionIndex, type, questionData = {}) {
        const mcqArea = block.querySelector('.mcq-area');
        const shortAnswerArea = block.querySelector('.short-answer-area');
        const optionsContainer = block.querySelector('.options-container');
        const shortAnswerInput = block.querySelector('.short-answer-correct');

        optionsContainer.innerHTML = '';
        shortAnswerInput.name = `questions[${questionIndex}][correct_text]`;
        shortAnswerInput.value = questionData.correct_text || '';

        if (type === 'short_answer') {
            mcqArea.style.display = 'none';
            shortAnswerArea.style.display = 'block';
            return;
        }

        mcqArea.style.display = 'block';
        shortAnswerArea.style.display = 'none';

        const optionCount = type === 'true_false' ? 2 : 4;
        const defaultOptions = type === 'true_false'
            ? [{ option_text: 'صحیح' }, { option_text: 'غلط' }]
            : [{ option_text: '' }, { option_text: '' }, { option_text: '' }, { option_text: '' }];
        const sourceOptions = questionData.options?.length ? questionData.options : defaultOptions;

        for (let optionIndex = 0; optionIndex < optionCount; optionIndex++) {
            const optionBlock = buildOptionBlock(questionIndex, optionIndex, sourceOptions[optionIndex]?.option_text || '');
            const textInput = optionBlock.querySelector('input[type="text"]');
            const radio = optionBlock.querySelector('input[type="radio"]');

            if (type === 'true_false') {
                textInput.readOnly = true;
            }

            radio.checked = Number(questionData.correct_option ?? 0) === optionIndex;
            optionsContainer.appendChild(optionBlock);
        }
    }

    function addQuestion(questionData = {}) {
        const questionIndex = container.children.length;
        const fragment = template.content.cloneNode(true);
        const block = fragment.querySelector('.question-block');
        const text = block.querySelector('.question-text');
        const type = block.querySelector('.question-type');

        text.name = `questions[${questionIndex}][question_text]`;
        text.value = questionData.question_text || '';
        type.name = `questions[${questionIndex}][type]`;
        type.value = questionData.type || 'mcq';

        renderQuestionOptions(block, questionIndex, type.value, questionData);

        type.addEventListener('change', () => {
            renderQuestionOptions(block, questionIndex, type.value, {});
        });

        block.querySelector('.remove-question').addEventListener('click', () => {
            block.remove();
            refreshQuestionTitles();
        });

        container.appendChild(block);
        refreshQuestionTitles();
    }

    addButton.addEventListener('click', () => addQuestion());

    if (initialQuestions.length) {
        initialQuestions.forEach(addQuestion);
    } else {
        addQuestion();
        addQuestion();
    }
</script>
