@extends('layouts.app')
@section('title', 'Dashboard')
@section('page_title', 'Dashboard')
@section('page_sub', 'Welcome back, ' . (Auth::user()->firstname ?? 'Student'))

@section('content')

{{-- ── Greeting Row ─────────────────────────────────────────────── --}}
<div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-6 fade-up">
  <div>
    <p class="text-xs font-semibold tracking-widest uppercase text-forest-600 mb-0.5">
      {{ now()->format('l, F j') }}
    </p>
    <h2 class="font-display text-2xl sm:text-3xl font-bold text-white leading-tight">
      Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening') }},
      <span class="text-forest-400">{{ Auth::user()->firstname ?? 'Scholar' }}</span> 👋
    </h2>
    <p class="text-ink-500 text-sm mt-0.5">Here's your academic snapshot for today.</p>
  </div>
  <a href="{{ route('notes.create', ['firstname' => Auth::user()->firstname]) }}"
     class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-forest-800
            border border-forest-700/50 text-forest-300 text-sm font-semibold
            hover:bg-forest-700 transition-all duration-150 self-start sm:self-auto">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
      <path d="M12 4.5v15m7.5-7.5h-15"/>
    </svg>
    New Note
  </a>
</div>

{{-- ── Stat Cards ───────────────────────────────────────────────── --}}
<div class="grid grid-cols-1 grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
  @foreach([
    ['Active Courses',     $totalResources,         'text-forest-400', 'bg-forest-950 border-forest-900', 'M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0118 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25'],
    ['Materials',          $totalMaterials,          'text-sky-400',    'bg-sky-950/40 border-sky-900/50',  'M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z'],
    ['Upcoming Lectures',  $totalLectures,           'text-amber-400',  'bg-amber-950/30 border-amber-900/40', 'M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0'],
    ['Quizzes Completed',  $totalCompletedQuizzes,   'text-violet-400', 'bg-violet-950/30 border-violet-900/40', 'M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z'],
  ] as $i => [$label, $value, $valColor, $iconBg, $path])
  <div class="app-card stat-card fade-up" style="animation-delay:{{ $i * .06 }}s">
    <div class="flex items-start justify-between mb-3">
      <div class="w-9 h-9 rounded-lg {{ $iconBg }} border flex items-center justify-center flex-shrink-0">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
     stroke="{{ str_contains($valColor, 'forest-400') ? '#4ade80' : (str_contains($valColor, 'sky-400') ? '#38bdf8' : (str_contains($valColor, 'amber-400') ? '#fbbf24' : '#a78bfa')) }}"
     stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">

          <path d="{{ $path }}"/>
        </svg>
      </div>
    </div>
    <p class="font-display text-3xl font-bold {{ $valColor }}">{{ $value }}</p>
    <p class="text-xs text-ink-500 mt-0.5">{{ $label }}</p>
  </div>
  @endforeach
</div>

{{-- ── Main Grid ─────────────────────────────────────────────────── --}}
<div class="grid lg:grid-cols-3 gap-4 mb-4">

  {{-- Recent Materials ──────────────────────────────────────────── --}}
  <div class="app-card fade-up-d1 lg:col-span-2">
    <div class="flex items-center justify-between mb-4">
      <h3 class="font-display text-base font-bold text-white">Recent Materials</h3>
      <a href="{{ route('materials.index') }}"
         class="text-xs text-forest-500 hover:text-forest-300 transition-colors font-medium">
        View all →
      </a>
    </div>

    <div class="space-y-1">
      @forelse($materials as $material)
      <a href="{{ route('lesson.material', ['resource' => $material->resource->slug, 'material' => $material->slug]) }}"
         class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-ink-800/60
                border border-transparent hover:border-ink-700 transition-all duration-150 group">
        <div class="w-8 h-8 rounded-lg flex-shrink-0 flex items-center justify-center
                    {{ $material->pdf_path ? 'bg-forest-950 border border-forest-900' : 'bg-sky-950/40 border border-sky-900/50' }}">
          @if($material->pdf_path)
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#4ade80" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
          </svg>
          @else
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#38bdf8" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
          </svg>
          @endif
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-sm font-medium text-ink-200 group-hover:text-white truncate transition-colors">
            {{ $material->title }}
          </p>
          <p class="text-xs text-ink-600">
            {{ $material->resource->course_code ?? '' }}
            · {{ $material->created_at?->diffForHumans() ?? '—' }}
          </p>
        </div>
        @if($material->pdf_path)
        <span class="rp-badge badge-green flex-shrink-0">PDF</span>
        @endif
        <svg class="w-3.5 h-3.5 text-ink-700 group-hover:text-forest-500 transition-colors flex-shrink-0"
             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
          <path d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
        </svg>
      </a>
      @empty
      <div class="py-8 text-center">
        <p class="text-sm text-ink-600">No materials available yet.</p>
      </div>
      @endforelse
    </div>
  </div>

  {{-- Quick Actions ─────────────────────────────────────────────── --}}
  <div class="app-card fade-up-d2">
    <h3 class="font-display text-base font-bold text-white mb-4">Quick Actions</h3>
    <div class="space-y-1.5">
      @foreach([
        [route('materials.index'),                                               'Browse Materials',  'text-forest-400',  '#4ade80',  'M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z'],
        [route('calender.index'),                                                'View Calendar',     'text-sky-400',     '#38bdf8',  'M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5'],
        [route('quiz.index'),                                                    'Take a Quiz',       'text-violet-400',  '#a78bfa',  'M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z'],
        [route('notes.create', ['firstname' => Auth::user()->firstname]),        'Create Note',       'text-amber-400',   '#fbbf24',  'M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z'],
        [route('profile.show', Auth::user()),                                    'My Profile',        'text-rose-400',    '#fb7185',  'M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z'],
      ] as [$href, $label, $color, $stroke, $path])
      <a href="{{ $href }}"
         class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl hover:bg-ink-800/60
                border border-transparent hover:border-ink-700 transition-all duration-150 group">
        <div class="w-7 h-7 rounded-lg bg-ink-800 border border-ink-700 flex items-center justify-center flex-shrink-0">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none"
               stroke="{{ $stroke }}" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="{{ $path }}"/>
          </svg>
        </div>
        <span class="text-sm text-ink-300 group-hover:text-white transition-colors">{{ $label }}</span>
        <svg class="w-3 h-3 ml-auto text-ink-700 group-hover:text-ink-400 transition-colors"
             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
          <path d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
        </svg>
      </a>
      @endforeach
    </div>
  </div>
</div>

{{-- ── Bottom Grid ──────────────────────────────────────────────── --}}
<div class="grid lg:grid-cols-3 gap-4">

  {{-- Upcoming Lectures ────────────────────────────────────────── --}}
  <div class="app-card fade-up-d3">
    <div class="flex items-center justify-between mb-4">
      <h3 class="font-display text-base font-bold text-white">Upcoming Lectures</h3>
      <a href="{{ route('calender.index') }}"
         class="text-xs text-forest-500 hover:text-forest-300 transition-colors">Full schedule →</a>
    </div>
    <div class="space-y-3">
      @forelse($lectures as $lecture)
      @php
        $isOngoing = $lecture->is_ongoing;
        $borderColor = $isOngoing ? 'border-forest-500' : 'border-sky-600';
        $timeText = $isOngoing ? 'Ongoing now' : ($lecture->start_time?->format('D, M j · g:i A') ?? '—');
      @endphp
      <div class="pl-3 border-l-2 {{ $borderColor }} py-0.5">
        <div class="flex items-center gap-1.5 mb-0.5">
          @if($isOngoing)
          <span class="rp-badge badge-green text-xs">Live</span>
          @endif
          <p class="text-xs text-ink-500">{{ $timeText }}</p>
        </div>
        <p class="text-sm font-semibold text-ink-100">
          {{ $lecture->resource->course_code ?? 'Unknown Course' }}
        </p>
        <p class="text-xs text-ink-500 mt-0.5">
          {{ $lecture->hall }} · {{ $lecture->lecturer }}
        </p>
      </div>
      @empty
      <p class="text-sm text-ink-600 py-4 text-center">No upcoming lectures scheduled.</p>
      @endforelse
    </div>
  </div>

  {{-- Calendar Embed ───────────────────────────────────────────── --}}
  <div class="app-card fade-up-d4 lg:col-span-2 overflow-hidden">
    <div class="flex items-center justify-between mb-3">
      <h3 class="font-display text-base font-bold text-white">Calendar</h3>
      <a href="{{ route('calender.index') }}"
         class="text-xs text-forest-500 hover:text-forest-300 transition-colors">Open full →</a>
    </div>
    <div class="rounded-xl overflow-hidden border border-ink-700" style="height:220px;">
      <iframe
        src=""
        style="border:0;filter:invert(90%) hue-rotate(175deg) saturate(.8) brightness(.9);"
        width="100%" height="220" frameborder="0" scrolling="no">
      </iframe>
    </div>
  </div>
</div>

@endsection
