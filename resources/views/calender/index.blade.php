@extends('layouts.app')
@section('title', 'Calendar')
@section('page_title', 'Lecture Calendar')
@section('page_sub', 'Your class timetable & schedule')

@section('content')

<div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-6 fade-up">
  <div>
    <h2 class="font-display text-2xl font-bold text-white">Lecture Schedule</h2>
    <p class="text-sm text-ink-500 mt-0.5">{{ now()->format('F Y') }} · AAUA Sociology 300L</p>
  </div>
  <div class="flex items-center gap-3">
    <span class="flex items-center gap-1.5 text-xs text-ink-500">
      <span class="w-2.5 h-2.5 rounded-full bg-forest-500 inline-block"></span> Ongoing
    </span>
    <span class="flex items-center gap-1.5 text-xs text-ink-500">
      <span class="w-2.5 h-2.5 rounded-full bg-sky-500 inline-block"></span> Upcoming
    </span>
    <span class="flex items-center gap-1.5 text-xs text-ink-500">
      <span class="w-2.5 h-2.5 rounded-full bg-ink-600 inline-block"></span> Past
    </span>
  </div>
</div>

<div class="grid lg:grid-cols-3 gap-4">

  {{-- ── Lectures List ────────────────────────────────────────────── --}}
  <div class="lg:col-span-2 space-y-3">

    @forelse($groupedLectures as $day => $dayLectures)
    <div class="fade-up" style="animation-delay:{{ $loop->index * .05 }}s">
      {{-- Day header --}}
      <div class="flex items-center gap-3 mb-2">
        <p class="text-xs font-semibold text-ink-500 uppercase tracking-wider">{{ $day }}</p>
        <div class="flex-1 h-px bg-ink-800"></div>
        <span class="text-xs text-ink-700">{{ $dayLectures->count() }} lecture{{ $dayLectures->count() !== 1 ? 's':'' }}</span>
      </div>

      <div class="space-y-2">
        @foreach($dayLectures as $lecture)
        @php
          $isOngoing = $lecture->is_ongoing;
          $isPast    = !$isOngoing && $lecture->start_time?->isPast();
          $cardBorder = $isOngoing ? 'border-forest-800' : ($isPast ? 'border-ink-800' : 'border-sky-900/50');
          $barColor   = $isOngoing ? 'bg-forest-500' : ($isPast ? 'bg-ink-700' : 'bg-sky-600');
        @endphp
        <div class="app-card {{ $cardBorder }} flex gap-4 items-start">
          {{-- Color bar --}}
          <div class="w-1 self-stretch rounded-full {{ $barColor }} flex-shrink-0"></div>

          <div class="flex-1 min-w-0">
            <div class="flex items-start justify-between gap-2">
              <div class="min-w-0">
                <div class="flex items-center gap-2 mb-1">
                  @if($isOngoing)
                  <span class="rp-badge badge-green">Live Now</span>
                  @elseif($isPast)
                  <span class="rp-badge" style="background:#1e293b;border:1px solid #334155;color:#64748b;">Ended</span>
                  @else
                  <span class="rp-badge badge-blue">Upcoming</span>
                  @endif
                </div>
                <p class="font-display text-base font-bold text-white">
                  {{ $lecture->resource->course_code ?? 'Unknown Course' }}
                </p>
                <p class="text-xs text-ink-400 mt-0.5">{{ $lecture->resource->name ?? '' }}</p>
              </div>
              <div class="text-right flex-shrink-0">
                <p class="text-xs font-mono text-ink-300">
                  {{ $lecture->start_time?->format('g:i A') }}
                  @if($lecture->end_time)
                  – {{ $lecture->end_time->format('g:i A') }}
                  @endif
                </p>
              </div>
            </div>

            <div class="flex items-center gap-4 mt-2.5 pt-2.5 border-t border-ink-800/60">
              <div class="flex items-center gap-1.5 text-xs text-ink-500">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                </svg>
                {{ $lecture->hall ?? 'TBA' }}
              </div>
              <div class="flex items-center gap-1.5 text-xs text-ink-500">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                </svg>
                {{ $lecture->lecturer ?? 'TBA' }}
              </div>
            </div>
          </div>
        </div>
        @endforeach
      </div>
    </div>
    @empty
    <div class="app-card text-center py-16">
      <div class="w-14 h-14 rounded-2xl bg-ink-800 border border-ink-700 flex items-center justify-center mx-auto mb-4">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#334155" stroke-width="1.5">
          <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
        </svg>
      </div>
      <p class="text-ink-400 font-medium">No lectures scheduled</p>
      <p class="text-xs text-ink-600 mt-1">Check back closer to the week.</p>
    </div>
    @endforelse
  </div>

  {{-- ── Side panel ───────────────────────────────────────────────── --}}
  <div class="space-y-4 fade-up-d2">

    {{-- Calendar embed --}}
    <div class="app-card overflow-hidden p-0">
      <div class="px-4 py-3 border-b border-ink-800">
        <p class="text-xs font-semibold text-ink-500 uppercase tracking-wider">Full Calendar</p>
      </div>
      <div class="overflow-hidden" style="height:280px;">
        <iframe
          src=""
          style="border:0;filter:invert(88%) hue-rotate(175deg) saturate(.75) brightness(.9);"
          width="100%" height="280" frameborder="0" scrolling="no">
        </iframe>
      </div>
    </div>

    {{-- Today's summary --}}
    <div class="app-card">
      <p class="text-xs font-semibold text-ink-600 uppercase tracking-wider mb-3">Today</p>
      <p class="font-display text-xl font-bold text-white">{{ now()->format('D, M j') }}</p>
      <p class="text-xs text-ink-500 mt-0.5 mb-3">{{ now()->format('g:i A') }} WAT</p>
      <div class="progress-track"><div class="progress-fill" id="day-progress"></div></div>
      <p class="text-xs text-ink-600 mt-1.5">Day progress</p>
    </div>

    {{-- Notification status --}}
    <div class="app-card bg-forest-950/40 border-forest-900/50">
      <div class="flex items-start gap-3">
        <div class="w-8 h-8 rounded-lg bg-forest-900 border border-forest-800 flex items-center justify-center flex-shrink-0">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#4ade80" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
          </svg>
        </div>
        <div>
          <p class="text-xs font-semibold text-forest-300 mb-0.5">Live Alerts Active</p>
          <p class="text-xs text-ink-500 leading-relaxed">
            You'll receive push notifications 15 minutes before each scheduled lecture.
          </p>
        </div>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
  const now = new Date();
  const start = new Date(now); start.setHours(6,0,0,0);
  const end   = new Date(now); end.setHours(22,0,0,0);
  const pct   = Math.min(100, Math.max(0, ((now - start)/(end - start))*100));
  document.getElementById('day-progress').style.width = pct.toFixed(1) + '%';
</script>
@endpush

@endsection
