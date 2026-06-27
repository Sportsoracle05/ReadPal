@extends('layouts.app')
@section('title', 'CGPA Dashboard')
@section('page_title', 'CGPA Calculator')
@section('page_sub', 'AAUA 5.0 Scale · ' . Auth::user()->firstname)

@section('content')

{{-- ── Header ────────────────────────────────────────────────── --}}
<div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-6 fade-up">
  <div>
    <p class="text-xs font-semibold tracking-widest uppercase text-forest-600 mb-1">
      Academic Performance
    </p>
    <h2 class="font-display text-2xl sm:text-3xl font-bold text-white leading-tight">
      CGPA Overview
    </h2>
    <p class="text-sm text-ink-500 mt-0.5">
      Your cumulative performance across all logged semesters.
    </p>
  </div>
  <a href="{{ route('cgpa.semester.index') }}"
     class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-forest-800
            border border-forest-700/50 text-forest-300 text-sm font-semibold
            hover:bg-forest-700 transition-colors self-start sm:self-auto">
    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
      <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
    </svg>
    Manage Semesters
  </a>
</div>

{{-- ── CGPA Hero Card ──────────────────────────────────────────── --}}
@php
  $cgpa       = $stats['cgpa'];
  $gradeClass = $stats['grade_class'];
  $pct        = min(($cgpa / 5) * 100, 100);
  $cgpaColor  = match(true) {
    $cgpa >= 4.5 => ['text' => 'text-forest-400', 'bar' => 'bg-forest-500', 'glow' => '0 0 24px rgba(74,222,128,.4)'],
    $cgpa >= 3.5 => ['text' => 'text-green-400',  'bar' => 'bg-green-500',  'glow' => '0 0 24px rgba(74,222,128,.3)'],
    $cgpa >= 2.4 => ['text' => 'text-yellow-400', 'bar' => 'bg-yellow-500', 'glow' => '0 0 24px rgba(234,179,8,.3)'],
    $cgpa >= 1.5 => ['text' => 'text-orange-400', 'bar' => 'bg-orange-500', 'glow' => '0 0 24px rgba(249,115,22,.3)'],
    default      => ['text' => 'text-red-400',    'bar' => 'bg-red-500',    'glow' => '0 0 24px rgba(239,68,68,.3)'],
  };
@endphp

<div class="grid grid-cols-1 lg:grid-cols-4 gap-4 mb-5">

  {{-- CGPA main card --}}
  <div class="app-card stat-card fade-up lg:col-span-2"
       style="border-color: rgba(22,163,74,.25);">
    <div class="flex items-start justify-between mb-4">
      <div>
        <p class="text-xs font-semibold tracking-widest uppercase text-ink-600 mb-1">
          Cumulative GPA
        </p>
        <div class="flex items-end gap-3">
          <span class="font-display text-6xl font-bold {{ $cgpaColor['text'] }} leading-none"
                style="text-shadow: {{ $cgpaColor['glow'] }};">
            {{ number_format($cgpa, 2) }}
          </span>
          <span class="text-ink-600 text-lg mb-1 font-mono">/ 5.00</span>
        </div>
      </div>
      <div class="text-right">
        <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-bold
                     bg-forest-950/60 border border-forest-900/50 text-forest-400">
          {{ $gradeClass }}
        </span>
        <p class="text-xs text-ink-600 mt-1.5">{{ $stats['semester_count'] }}/8 semesters</p>
      </div>
    </div>
    <div class="progress-track">
      <div class="progress-fill {{ $cgpaColor['bar'] }}" style="width:{{ $pct }}%;background:unset;"
           class="{{ $cgpaColor['bar'] }}"></div>
    </div>
    <style>.progress-fill-dyn{height:5px;border-radius:9px;transition:width .7s ease;}</style>
    <div class="h-1.5 rounded-full bg-ink-800 overflow-hidden mt-3">
      <div class="h-full rounded-full transition-all duration-700 {{ $cgpaColor['bar'] }}"
           style="width:{{ $pct }}%;"></div>
    </div>
    <div class="flex justify-between text-xs mt-1.5">
      <span class="text-ink-700">0.00</span>
      <span class="text-ink-700">5.00</span>
    </div>
  </div>

  {{-- Total Units --}}
  <div class="app-card stat-card fade-up">
    <div class="w-9 h-9 rounded-lg bg-sky-950/40 border border-sky-900/40
                flex items-center justify-center mb-3">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#38bdf8" stroke-width="1.8">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z"/>
      </svg>
    </div>
    <p class="font-display text-4xl font-bold text-white">{{ $stats['total_units'] }}</p>
    <p class="text-xs text-ink-500 mt-0.5">Total Units Earned</p>
    <p class="text-xs text-ink-700 mt-2 font-mono">credit hours</p>
  </div>

  {{-- Total Quality Points --}}
  <div class="app-card stat-card fade-up-d1">
    <div class="w-9 h-9 rounded-lg bg-violet-950/30 border border-violet-900/30
                flex items-center justify-center mb-3">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#a78bfa" stroke-width="1.8">
        <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/>
      </svg>
    </div>
    <p class="font-display text-4xl font-bold text-white">{{ number_format($stats['total_quality_points']) }}</p>
    <p class="text-xs text-ink-500 mt-0.5">Quality Points</p>
    <p class="text-xs text-ink-700 mt-2 font-mono">Σ (unit × grade pt)</p>
  </div>
</div>

{{-- ── Semester Breakdown Table ─────────────────────────────────── --}}
<div class="grid lg:grid-cols-3 gap-4">

  <div class="app-card fade-up-d2 lg:col-span-2">
    <div class="flex items-center justify-between mb-4">
      <h3 class="font-display text-base font-bold text-white">Semester Breakdown</h3>
      <a href="{{ route('cgpa.semester.index') }}"
         class="text-xs text-forest-500 hover:text-forest-300 transition-colors">Manage →</a>
    </div>

    @if($breakdown->isEmpty())
    <div class="py-10 text-center">
      <p class="text-sm text-ink-600">No semesters recorded yet.</p>
      <a href="{{ route('cgpa.semester.index') }}"
         class="inline-flex items-center gap-1.5 mt-3 text-xs text-forest-500 hover:text-forest-300 transition-colors">
        Add your first semester →
      </a>
    </div>
    @else
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead>
          <tr class="border-b border-ink-800">
            <th class="text-left pb-2.5 text-xs font-semibold uppercase tracking-wider text-ink-600">Semester</th>
            <th class="text-center pb-2.5 text-xs font-semibold uppercase tracking-wider text-ink-600">Courses</th>
            <th class="text-center pb-2.5 text-xs font-semibold uppercase tracking-wider text-ink-600">Units</th>
            <th class="text-center pb-2.5 text-xs font-semibold uppercase tracking-wider text-ink-600">QP</th>
            <th class="text-center pb-2.5 text-xs font-semibold uppercase tracking-wider text-ink-600">GPA</th>
            <th class="pb-2.5"></th>
          </tr>
        </thead>
        <tbody class="divide-y divide-ink-800/50">
          @foreach($breakdown as $row)
          @php
            $gpa = $row['gpa'];
            $gc = match(true) {
              $gpa >= 4.5 => 'text-forest-400',
              $gpa >= 3.5 => 'text-green-400',
              $gpa >= 2.4 => 'text-yellow-400',
              $gpa >= 1.5 => 'text-orange-400',
              default     => 'text-red-400',
            };
          @endphp
          <tr class="hover:bg-ink-800/30 transition-colors">
            <td class="py-3 text-sm font-medium text-ink-200">{{ $row['semester']->label }}</td>
            <td class="py-3 text-center text-xs text-ink-500">{{ $row['course_count'] }}</td>
            <td class="py-3 text-center text-xs text-ink-500">{{ $row['total_units'] }}</td>
            <td class="py-3 text-center text-xs text-ink-500">{{ $row['quality_pts'] }}</td>
            <td class="py-3 text-center">
              <span class="font-mono font-bold text-sm {{ $gc }}">
                {{ number_format($gpa, 2) }}
              </span>
            </td>
            <td class="py-3 text-right">
              <a href="{{ route('cgpa.semester.show', $row['semester']) }}"
                 class="text-xs text-forest-600 hover:text-forest-400 transition-colors">View</a>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    @endif
  </div>

  {{-- Grade Scale Reference --}}
  <div class="app-card fade-up-d3">
    <p class="text-xs font-semibold uppercase tracking-wider text-ink-600 mb-3">
      AAUA Grade Scale
    </p>
    <div class="space-y-2">
      @foreach([
        ['A','5','70–100','text-forest-400','bg-forest-950/50 border-forest-900/50'],
        ['B','4','60–69', 'text-green-400', 'bg-green-950/30  border-green-900/30'],
        ['C','3','50–59', 'text-yellow-400','bg-yellow-950/20 border-yellow-900/20'],
        ['D','2','45–49', 'text-orange-400','bg-orange-950/20 border-orange-900/20'],
        ['E','1','40–44', 'text-red-400',   'bg-red-950/20    border-red-900/20'],
        ['F','0','0–39',  'text-ink-600',   'bg-ink-800/50    border-ink-700/40'],
      ] as [$letter, $pts, $range, $color, $bg])
      <div class="flex items-center gap-3 px-3 py-2 rounded-xl {{ $bg }} border">
        <span class="font-display text-xl font-bold {{ $color }} w-5 text-center">{{ $letter }}</span>
        <span class="flex-1 text-xs text-ink-400">{{ $range }}%</span>
        <span class="font-mono text-sm font-bold {{ $color }}">{{ $pts }} pts</span>
      </div>
      @endforeach
    </div>

    <div class="mt-4 pt-3 border-t border-ink-800">
      <p class="text-xs font-semibold uppercase tracking-wider text-ink-600 mb-2">Classification</p>
      @foreach([
        ['First Class',        '4.50–5.00'],
        ['2nd Class Upper',    '3.50–4.49'],
        ['2nd Class Lower',    '2.40–3.49'],
        ['Third Class',        '1.50–2.39'],
        ['Pass',               '1.00–1.49'],
      ] as [$class, $range])
      <div class="flex justify-between py-1">
        <span class="text-xs text-ink-400">{{ $class }}</span>
        <span class="text-xs font-mono text-ink-600">{{ $range }}</span>
      </div>
      @endforeach
    </div>
  </div>
</div>

@endsection