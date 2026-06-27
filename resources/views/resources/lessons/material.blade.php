@extends('layouts.app')
@section('title', $material->title)
@section('page_title', $material->title)
@section('page_sub', $resource->course_code ?? 'Lesson')

@section('content')
<style>
  /* Force all content inside app-cards to respect the boundary */
  .app-card {
    min-width: 0; 
    word-wrap: break-word;
    overflow-wrap: break-word;
  }
  
  /* Ensure images/videos inside notes don't exceed screen width */
  .prose img, .prose video, .prose iframe {
    max-width: 100%;
    height: auto;
  }
</style>

<style id="fix1">
html, body {
    overflow-x: hidden;
}

* {
    max-width: 100%;
}
</style>

<style id="fix2">
.prose * {
    word-break: break-word;
}
</style>

{{-- Breadcrumb --}}
<nav class="flex items-center gap-2 text-xs text-ink-600 mb-5 fade-up">
  <a href="{{ route('materials.index') }}" class="hover:text-ink-300 transition-colors">Materials</a>
  <span>›</span>
  <a href="{{ route('resources.show', $resource->slug) }}" class="hover:text-ink-300 transition-colors">
    {{ $resource->course_code ?? $resource->name }}
  </a>
  <span>›</span>
  <span class="text-ink-300 truncate max-w-[180px]">{{ $material->title }}</span>
</nav>

<div class="grid grid-cols-1 lg:grid-cols-4 gap-4 w-full min-w-0">

  {{-- ── Lesson Content ──────────────────────────────────────────── --}}
  <div class="lg:col-span-3 space-y-4">

    {{-- Header Card --}}
    <div class="app-card fade-up">
      <div class="flex flex-col sm:flex-row sm:items-start gap-4 min-w-0">
        <div class="flex-1 min-w-0">
          <div class="flex items-center gap-2 mb-2">
            @if($resource->course_code)
            <span class="rp-badge badge-green">{{ $resource->course_code }}</span>
            @endif
            @if($material->pdf_path)
            <span class="rp-badge badge-blue">PDF Available</span>
            @endif
            <p class="text-xs text-ink-500 mt-1.5">{{ $resource->lecturer }}</p>
          </div>
          <h1 class="font-display text-xl sm:text-2xl font-bold text-white leading-tight">
            {{ $material->title }}
          </h1>
          <p class="text-xs text-ink-500 mt-1.5">
            Added {{ $material->created_at?->format('M j, Y') ?? '—' }}
            · {{ $resource->name ?? '' }}
          </p>
        </div>

        {{-- Action buttons --}}
        <div class="flex flex-wrap items-center gap-2 sm:flex-shrink-0 w-full sm:w-auto min-w-0">
          @if($material->pdf_path)
          <a href="{{ route('material.download', ['resource' => $resource->slug, 'material' => $material->slug]) }}"
             class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-forest-800
                    border border-forest-700/50 text-forest-300 text-xs font-semibold
                    hover:bg-forest-700 transition-colors">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <path d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
            </svg>
            Download PDF
          </a>
          @else
          <a href="{{ route('material.download', ['resource' => $resource->slug, 'material' => Str::slug($material->title)]) }}"
             class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-forest-800
                    border border-forest-700/50 text-forest-300 text-xs font-semibold
                    hover:bg-forest-700 transition-colors">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <path d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
            </svg>
            Download Page
          </a>
          @endif
          <a href="{{ route('materials.quiz', $material->id) }}"
             class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-violet-950/40
                    border border-violet-900/40 text-violet-400 text-xs font-semibold
                    hover:bg-violet-900/30 transition-colors">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"/>
            </svg>
            Take Quiz
          </a>
        </div>
      </div>
    </div>

    {{-- PDF Viewer --}}
    @if($material->pdf_path)
    <div class="app-card fade-up-d1 overflow-hidden p-0">
      <div class="px-5 py-3 border-b border-ink-800 flex items-center justify-between">
        <p class="text-xs font-semibold text-ink-400 uppercase tracking-wider">PDF Viewer</p>
        <a href="{{ route('materials.view', $material->id) }}"
           class="text-xs text-forest-500 hover:text-forest-300 transition-colors">
          Full screen →
        </a>
      </div>
      <iframe src="{{ asset('storage/' . $material->pdf_path) }}"
              class="w-full max-w-full" style="height:65vh;border:none;"></iframe>
    </div>
    @endif

    {{-- Note Text --}}
    @if($material->note_text)
    <div class="app-card fade-up-d2">
      <div class="flex items-center justify-between mb-4">
        <p class="text-xs font-semibold text-ink-500 uppercase tracking-wider">Lecture Notes</p>
      </div>
      <div class="prose prose-invert prose-sm max-w-full break-words overflow-hidden
        text-ink-300 leading-relaxed
        [&_h2]:text-white [&_h3]:text-forest-300
        [&_strong]:text-ink-100 [&_a]:text-forest-400
        [&_*]:max-w-full">
          {!! $material->note_text !!}
      </div>
    </div>
    @endif

  </div>

  {{-- ── Sidebar ─────────────────────────────────────────────────── --}}
  <div class="space-y-4 fade-up-d2">

    {{-- Course Info --}}
    <div class="app-card">
      <p class="text-xs font-semibold text-ink-600 uppercase tracking-wider mb-3">Course</p>
      <p class="font-display text-lg font-bold text-white">{{ $resource->course_code ?? '—' }}</p>
      <p class="text-xs text-ink-400 mt-0.5 mb-3">{{ $resource->name ?? '' }}</p>
      <a href="{{ route('resources.show', $resource->slug) }}"
         class="text-xs text-forest-500 hover:text-forest-300 transition-colors">
        All materials for this course →
      </a>
    </div>

    {{-- Progress --}}
    <div class="app-card">
      <p class="text-xs font-semibold text-ink-600 uppercase tracking-wider mb-3">Your Progress</p>
      <div class="space-y-2.5">
        <div class="flex justify-between text-xs mb-1">
          <span class="text-ink-400">Reading</span>
          <span class="text-forest-400 font-mono">—%</span>
        </div>
        <div class="progress-track"><div class="progress-fill" style="width:0%"></div></div>
        <div class="flex justify-between text-xs mt-2">
          <span class="text-ink-400">Quiz</span>
          <span class="text-violet-400 font-mono">Not started</span>
        </div>
      </div>
    </div>

    {{-- Quick Notes CTA --}}
    <div class="app-card bg-forest-950/50 border-forest-900/60">
      <p class="text-xs font-semibold text-forest-700 uppercase tracking-wider mb-2">Quick Note</p>
      <p class="text-xs text-ink-500 mb-3 leading-relaxed">
        Jot down key points from this lesson into your personal notes.
      </p>
      <a href="{{ route('notes.create', ['firstname' => Auth::user()->firstname]) }}"
         class="flex items-center justify-center gap-1.5 w-full px-3 py-2 rounded-xl
                bg-forest-800 border border-forest-700/50 text-forest-300 text-xs font-semibold
                hover:bg-forest-700 transition-colors">
        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <path d="M12 4.5v15m7.5-7.5h-15"/>
        </svg>
        New Note
      </a>
    </div>
  </div>

</div>

@endsection