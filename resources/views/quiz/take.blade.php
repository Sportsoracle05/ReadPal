@extends('layouts.app')
@section('title', 'Quiz – ' . ($material->title ?? 'Quiz'))
@section('page_title', 'Self-Assessment')
@section('page_sub', ($material->resource->course_code ?? '') . ' · ' . count($questions) . ' Questions')

@section('content')
<div class="max-w-3xl mx-auto">
    {{-- Header --}}
    <div class="app-card mb-4 fade-up">
        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-1.5">
                    @if($material->resource)
                    <span class="rp-badge badge-green">{{ $material->resource->course_code }}</span>
                    @endif
                    <span class="rp-badge badge-blue">{{ count($questions) }} Questions</span>
                </div>
                <h2 class="font-display text-lg font-bold text-white">{{ $material->title }}</h2>
            </div>
            {{-- Timer --}}
            <div class="flex-shrink-0 text-center px-4 py-2.5 rounded-xl bg-ink-800 border border-ink-700">
                <p class="text-xs text-ink-600 mb-0.5">Time Left</p>
                <p id="quiz-timer" class="font-mono text-xl font-bold text-amber-400">00:20</p>
            </div>
        </div>

        {{-- Progress bar --}}
        <div class="mt-4">
            <div class="flex justify-between text-xs text-ink-600 mb-1.5">
                <span id="q-counter">Question 1 of {{ count($questions) }}</span>
                <span id="q-answered">0 correct</span>
            </div>
            <div class="progress-track">
                <div class="progress-fill" id="quiz-progress" style="width:{{ 100 / count($questions) }}%;"></div>
            </div>
        </div>
    </div>

    {{-- Questions --}}
    <div id="questions-container" class="fade-up-d1">
        @foreach($questions as $qi => $question)
        <div class="question-block app-card mb-3 {{ $qi > 0 ? 'hidden' : '' }}" data-index="{{ $qi }}" data-correct="{{ $question['correct_answer'] ?? '' }}">
            <p class="text-xs font-mono text-ink-600 mb-2">Q{{ $qi + 1 }} of {{ count($questions) }}</p>
            <p class="text-base font-semibold text-white leading-snug mb-4">
                {{ $question['question'] ?? $question->question_text }}
            </p>

            <div class="space-y-2 options-group">
                @foreach($question['options'] as $optLabel => $option)
                <label class="option-label flex items-center gap-3 p-3 rounded-xl border border-ink-700 bg-ink-800/40 cursor-pointer transition-all duration-150 group">
                    <input type="radio" name="q{{ $qi }}" value="{{ $optLabel }}" class="sr-only quiz-radio" onchange="handleSelection(this, '{{ $optLabel }}', '{{ $question['correct_answer'] }}')">
                    <div class="w-7 h-7 rounded-lg border border-ink-600 bg-ink-800 flex items-center justify-center flex-shrink-0 text-xs font-mono text-ink-500 group-hover:border-forest-700 option-circle">
                        {{ $optLabel }}
                    </div>
                    <span class="text-sm text-ink-300 group-hover:text-ink-100">{{ is_array($option) ? $option['text'] : $option }}</span>
                </label>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- Result Modal (Hidden by default) --}}
<div id="result-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-ink-950/80 backdrop-blur-sm">
    <div class="app-card max-w-sm w-full text-center shadow-2xl border-forest-900/50">
        <h2 class="font-display text-2xl font-bold text-white mb-2">Quiz Completed!</h2>
        <p class="text-ink-500 text-sm mb-6">Your performance summary</p>
        
        <div class="py-8 bg-ink-900/50 rounded-2xl mb-6 border border-ink-700/50">
            <span id="final-score" class="text-6xl font-display font-black text-forest-400">0</span>
            <span class="text-2xl text-ink-600 font-mono">/ {{ count($questions) }}</span>
        </div>

        <button onclick="submitResults()" class="w-full py-3 bg-forest-800 hover:bg-forest-700 text-white font-bold rounded-xl transition-all mb-3">
            Save & Finish
        </button>
        
        <form id="quizResultForm" method="POST" action="{{ route('quiz.storeResult', $material->id) }}" class="hidden">
            @csrf
            <input type="hidden" name="score" id="db-score">
            <input type="hidden" name="attempt" id="db-attempt" value="{{ count($questions) }}">
        </form>
    </div>
</div>

@push('scripts')
<script>
    const totalQ = {{ count($questions) }};
    let current = 0;
    let correctCount = 0;
    let timeLeft = 20; // 15 seconds per question logic
    let timerInterval;

    function startTimer() {
        clearInterval(timerInterval);
        timeLeft = 20;
        const timerEl = document.getElementById('quiz-timer');
        
        timerInterval = setInterval(() => {
            timeLeft--;
            timerEl.textContent = `00:${timeLeft.toString().padStart(2, '0')}`;
            
            if (timeLeft <= 5) timerEl.classList.add('text-red-400');
            else timerEl.classList.remove('text-red-400');

            if (timeLeft <= 0) {
                clearInterval(timerInterval);
                autoAdvance();
            }
        }, 1000);
    }

    function handleSelection(input, selected, correct) {
        clearInterval(timerInterval);
        const block = input.closest('.question-block');
        const options = block.querySelectorAll('.option-label');
        
        // Disable further clicking for this question
        block.querySelectorAll('input').forEach(i => i.disabled = true);

        if (selected === correct) {
            correctCount++;
            input.closest('.option-label').classList.add('border-forest-500', 'bg-forest-900/30');
            input.nextElementSibling.classList.add('bg-forest-800', 'border-forest-500', 'text-forest-200');
        } else {
            input.closest('.option-label').classList.add('border-red-900', 'bg-red-950/20');
            input.nextElementSibling.classList.add('bg-red-900', 'border-red-700', 'text-red-200');
            // Show the correct one
            block.querySelector(`input[value="${correct}"]`).closest('.option-label').classList.add('border-forest-800/50', 'bg-forest-950/20');
        }

        document.getElementById('q-answered').textContent = `${correctCount} correct`;

        setTimeout(() => {
            if (current < totalQ - 1) {
                current++;
                showQuestion(current);
            } else {
                finishQuiz();
            }
        }, 1000);
    }

    function autoAdvance() {
        if (current < totalQ - 1) {
            current++;
            showQuestion(current);
        } else {
            finishQuiz();
        }
    }

    function showQuestion(idx) {
        document.querySelectorAll('.question-block').forEach((el, i) => {
            el.classList.toggle('hidden', i !== idx);
        });
        
        document.getElementById('q-counter').textContent = `Question ${idx + 1} of ${totalQ}`;
        const pct = ((idx + 1) / totalQ * 100).toFixed(1);
        document.getElementById('quiz-progress').style.width = pct + '%';
        
        startTimer();
    }

    function finishQuiz() {
        document.getElementById('final-score').textContent = correctCount;
        document.getElementById('db-score').value = correctCount;
        document.getElementById('result-modal').classList.remove('hidden');
    }

    function submitResults() {
        document.getElementById('quizResultForm').submit();
    }

    // Start the first timer on load
    window.onload = startTimer;
</script>
@endpush
@endsection
