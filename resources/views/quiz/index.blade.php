@extends('layouts.app')
@section('title', 'Quizzes')
@section('page_title', 'Self-Assessment')
@section('page_sub', '15-question tests per lesson')

@section('content')

<div class="flex items-end justify-between mb-6 fade-up">
  <div>
    <h2 class="font-display text-2xl font-bold text-white">Quizzes</h2>
    <p class="text-sm text-ink-500 mt-0.5">Test your knowledge with 15-question lesson assessments.</p>
  </div>
</div>

<div class="grid lg:grid-cols-3 gap-4">

  {{-- Quiz Cards --}}
  <div class="lg:col-span-2 space-y-3">
    @forelse($resources ?? [] as $i => $material)
    @php
      $result    = $attempts[$material->id] ?? null;
      // Force calculation against 15 questions
      $totalQuestions = 15;
      $score     = $result->score ?? 0;
      $percent   = ($score / $totalQuestions) * 100;
      $hasPassed = $percent >= 50;
    @endphp

    <div class="app-card stat-card fade-up" style="animation-delay:{{ $i * .05 }}s">
      <div class="flex items-start gap-4">
        <div class="w-10 h-10 rounded-xl bg-violet-950/40 border border-violet-900/40
                    flex items-center justify-center flex-shrink-0">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#a78bfa" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"/>
          </svg>
        </div>
        <div class="flex-1 min-w-0">
          <div class="flex items-center gap-2 mb-1">
            @if($material->resource)
            <span class="rp-badge badge-green">{{ $material->resource->course_code }}</span>
            @endif
            
            @if($result)
                <span class="rp-badge {{ $hasPassed ? 'badge-green' : 'badge-amber' }}">
                    {{ $score }}/{{ $totalQuestions }}
                    ({{ round($percent) }}%)
                </span>
            @endif
          </div>
          
          <h3 class="font-semibold text-ink-100 text-sm leading-snug mb-0.5">{{ $material->title }}</h3>
          <p class="text-xs text-ink-600">15 questions · Multiple choice</p>

          @if($result)
              <div class="mt-2.5">
                  <div class="progress-track">
                      <div class="progress-fill {{ !$hasPassed ? '[background:linear-gradient(90deg,#92400e,#fbbf24)]' : '' }}"
                          style="width:{{ $percent }}%"></div>
                  </div>
              </div>
          @endif
        </div>

        <a href="{{ route('materials.quiz', $material->id) }}"
           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl flex-shrink-0
                  {{ $result ? 'bg-ink-800 border border-ink-700 text-ink-300 hover:border-violet-800 hover:text-violet-400'
                             : 'bg-violet-950/40 border border-violet-900/40 text-violet-400 hover:bg-violet-900/30' }}
                  text-xs font-semibold transition-colors">
          {{ $result ? 'Retake' : 'Start' }}
          <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <path d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
          </svg>
        </a>
      </div>
    </div>
    @empty
        {{-- Empty state remains the same --}}
    @endforelse
  </div>

  {{-- Quiz Stats sidebar --}}
  <div class="space-y-4 fade-up-d2">
    <div class="app-card">
      <p class="text-xs font-semibold text-ink-600 uppercase tracking-wider mb-4">Your Stats</p>
      @php
        $completedCount = isset($attempts) ? count($attempts) : 0;
        $totalMaterials = isset($resources) ? $resources->total() : 0; 
        $avg = 0;

        if ($completedCount > 0 && isset($attempts)) {
          $avg = collect($attempts)->avg(function($r) {
              return ($r->score / 15) * 100;
          });
        }
      @endphp

      <div class="space-y-4">
        <div>
          <div class="flex justify-between text-xs mb-1.5">
            <span class="text-ink-400">Lessons Mastered</span>
            <span class="font-mono text-forest-400">{{ $completedCount }}/{{ $totalMaterials }}</span>
          </div>
          <div class="progress-track">
            <div class="progress-fill" style="width:{{ $totalMaterials > 0 ? round(($completedCount/$totalMaterials)*100) : 0 }}%"></div>
          </div>
        </div>
        <div class="flex items-center justify-between py-2.5 border-t border-ink-800">
          <span class="text-xs text-ink-500">Avg. Score</span>
          <span class="font-display text-lg font-bold {{ $avg >= 50 ? 'text-forest-400' : 'text-amber-400' }}">
            {{ round($avg) }}%
          </span>
        </div>
        <div class="flex items-center justify-between py-2.5 border-t border-ink-800">
          <span class="text-xs text-ink-500">Target Questions</span>
          <span class="font-mono text-sm text-ink-200">15 / Quiz</span>
        </div>
      </div>
    </div>

    <div class="app-card bg-forest-950/30 border-forest-900/50">
      <p class="text-xs font-semibold text-forest-700 uppercase tracking-wider mb-2">💡 Tip</p>
      <p class="text-xs text-ink-500 leading-relaxed">
        Each quiz consists of 15 focused questions. Aim for 70%+ to confirm strong understanding before exams. You can retake any quiz as many times as needed.
      </p>
    </div>
  </div>
</div>

@endsection
