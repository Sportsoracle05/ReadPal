@extends('layouts.admin')
@section('title', 'Academic')
@section('page_title', 'Academic Management')
@section('page_sub', 'Sessions, semesters & active period')

@section('content')

<div class="mb-6 fu">
  <h2 class="font-display text-xl font-bold text-white">Academic Management</h2>
  <p class="text-xs text-ink-600 mt-0.5">
    Configure the current academic session and semester for ReadPal content delivery.
  </p>
</div>

<div class="grid lg:grid-cols-3 gap-4">

  {{-- ── Active Semester Status ─────────────────────────────────── --}}
  <div class="lg:col-span-3 fu1">
    @if($activeSemester ?? null)
    <div class="a-card border-forest-900/50 flex flex-col sm:flex-row sm:items-center gap-4">
      <div class="w-10 h-10 rounded-xl bg-forest-950 border border-forest-900
                  flex items-center justify-center flex-shrink-0">
        <div class="w-2.5 h-2.5 rounded-full bg-forest-500"
             style="box-shadow:0 0 8px rgba(34,197,94,.7);animation:ping 2s ease-in-out infinite;"></div>
      </div>
      <div class="flex-1 min-w-0">
        <p class="text-xs font-semibold text-forest-600 uppercase tracking-widest mb-0.5">Currently Active</p>
        <p class="font-display text-base font-bold text-white">
          {{ $activeSemester->session->name ?? '—' }} ·
          {{ $activeSemester->name ?? '—' }} Semester
        </p>
        <p class="text-xs text-ink-500 mt-0.5">
          Started {{ $activeSemester->created_at?->format('M j, Y') ?? '—' }}
          · Materials and quizzes are live for this period.
        </p>
      </div>
      <span class="rp-badge badge-green flex-shrink-0">Active</span>
    </div>
    @else
    <div class="a-card border-amber-900/30 flex items-center gap-4">
      <div class="w-10 h-10 rounded-xl bg-amber-950/30 border border-amber-900/30
                  flex items-center justify-center flex-shrink-0">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#fbbf24" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126z"/>
        </svg>
      </div>
      <div>
        <p class="text-sm font-semibold text-amber-400">No active semester selected</p>
        <p class="text-xs text-ink-600 mt-0.5">Select a semester below to activate it for students.</p>
      </div>
    </div>
    @endif
  </div>

  {{-- ── Create Session ─────────────────────────────────────────── --}}
  <div class="a-card fu2">
    <div class="flex items-center gap-2 mb-4">
      <div class="w-8 h-8 rounded-lg bg-forest-950 border border-forest-900
                  flex items-center justify-center flex-shrink-0">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#4ade80" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
        </svg>
      </div>
      <h3 class="font-display text-sm font-bold text-white">Add Academic Session</h3>
    </div>
    <form method="POST" action="{{ route('admin.academic.session.store') }}">
      @csrf
      <div class="mb-4">
        <label class="form-label">Session Name</label>
        <input type="text" name="name" required
               placeholder="e.g. 2024/2025"
               class="form-input text-sm"/>
        <p class="text-xs text-ink-700 mt-1">Format: YYYY/YYYY</p>
      </div>
      <button type="submit" class="btn-primary w-full justify-center">
        Create Session
      </button>
    </form>

    {{-- Existing sessions --}}
    @if(($sessions ?? collect())->isNotEmpty())
    <div class="mt-4 pt-4 border-t border-ink-800">
      <p class="text-xs font-semibold text-ink-600 uppercase tracking-wider mb-2">Sessions</p>
      <div class="space-y-1.5">
        @foreach($sessions as $session)
        <div class="flex items-center justify-between px-2.5 py-2 rounded-lg bg-ink-800/50 border border-ink-800">
          <span class="text-xs font-mono text-ink-300">{{ $session->name }}</span>
          <span class="text-xs text-ink-600">{{ $session->semesters_count ?? 0 }} sem.</span>
        </div>
        @endforeach
      </div>
    </div>
    @endif
  </div>

  {{-- ── Create Semester ─────────────────────────────────────────── --}}
  <div class="a-card fu2">
    <div class="flex items-center gap-2 mb-4">
      <div class="w-8 h-8 rounded-lg bg-sky-950/40 border border-sky-900/40
                  flex items-center justify-center flex-shrink-0">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#38bdf8" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
        </svg>
      </div>
      <h3 class="font-display text-sm font-bold text-white">Add Semester</h3>
    </div>
    <form method="POST" action="{{ route('admin.academic.semester.store') }}">
      @csrf
      <div class="mb-4">
        <label class="form-label">Academic Session</label>
        <select name="session_id" required class="form-input cursor-pointer text-sm">
          <option value="" disabled selected>Select session…</option>
          @foreach($sessions ?? [] as $session)
          <option value="{{ $session->id }}">{{ $session->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="mb-4">
        <label class="form-label">Semester</label>
        <select name="name" required class="form-input cursor-pointer text-sm">
          <option value="1st" selected>1st Semester</option>
          <option value="2nd">2nd Semester</option>
        </select>
      </div>

      <div class="grid grid-cols-2 gap-3 mb-5">
        <div>
          <label class="form-label">Start Date</label>
          <input type="date" name="start_date" required
                
                 class="form-input"/>
          @error('start_time')<p class="form-error">{{ $message }}</p>@enderror
        </div>
        <div>
          <label class="form-label">End Date</label>
          <input type="date" name="end_date" required
                 
                 class="form-input"/>
        </div>
      </div>

      <button type="submit" class="btn-primary w-full justify-center">
        Add Semester
      </button>
    </form>
  </div>

  {{-- ── Select Active Semester ──────────────────────────────────── --}}
  <div class="a-card fu3">
    <div class="flex items-center gap-2 mb-4">
      <div class="w-8 h-8 rounded-lg bg-violet-950/30 border border-violet-900/30
                  flex items-center justify-center flex-shrink-0">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#a78bfa" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
        </svg>
      </div>
      <h3 class="font-display text-sm font-bold text-white">Activate Semester</h3>
    </div>

    <p class="text-xs text-ink-500 mb-3 leading-relaxed">
      The active semester determines which materials and schedules students see in the app.
      Only one semester can be active at a time.
    </p>

    <div class="space-y-2">
      @forelse($allSemesters ?? [] as $sem)
      <form method="POST" action="{{ route('admin.academic.semester.select', $sem->id) }}">
        @csrf
        <button type="submit"
                class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl
                       border transition-all duration-150 text-left
                       {{ ($activeSemester->id ?? null) === $sem->id
                          ? 'border-forest-800 bg-forest-950/40 text-forest-300'
                          : 'border-ink-700 bg-ink-800/30 text-ink-400 hover:border-forest-900 hover:text-forest-500' }}">
          <div>
            <p class="text-xs font-semibold">
              {{ $sem->session->name ?? '—' }} · {{ $sem->name }} Semester
            </p>
          </div>
          @if(($activeSemester->id ?? null) === $sem->id)
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#4ade80" stroke-width="2.5">
            <path d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          @else
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#475569" stroke-width="2">
            <circle cx="12" cy="12" r="9"/>
          </svg>
          @endif
        </button>
      </form>
      @empty
      <p class="text-xs text-ink-700 text-center py-4">No semesters created yet.</p>
      @endforelse
    </div>
  </div>

</div>

@endsection