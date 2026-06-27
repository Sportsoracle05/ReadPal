@extends('layouts.app')
@section('title', 'My Semesters')
@section('page_title', 'My Semesters')
@section('page_sub', 'CGPA · Academic Record')

@section('content')

{{-- Flash errors --}}
@if($errors->has('duplicate'))
<div class="mb-4 flex items-center gap-2.5 px-4 py-2.5 rounded-xl bg-red-950/50 border border-red-900/50 text-red-300 text-sm fade-up">
  <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
    <path d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126z"/>
  </svg>
  {{ $errors->first('duplicate') }}
</div>
@endif

{{-- ── Header ──────────────────────────────────────────────────── --}}
<div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-6 fade-up">
  <div>
    <p class="text-xs font-semibold tracking-widest uppercase text-forest-600 mb-1">Academic Record</p>
    <h2 class="font-display text-2xl sm:text-3xl font-bold text-white">My Semesters</h2>
    <p class="text-sm text-ink-500 mt-0.5">
      CGPA:
      <span class="font-mono font-bold text-forest-300 text-base">{{ number_format($cgpa, 2) }}/5.00</span>
    </p>
  </div>
  <div class="flex items-center gap-2 self-start sm:self-auto">
    <a href="{{ route('cgpa.dashboard') }}"
       class="px-4 py-2.5 rounded-xl border border-ink-700 text-ink-400 text-sm font-medium
              hover:border-ink-600 hover:text-ink-200 transition-colors">
      ← Dashboard
    </a>
    <button onclick="document.getElementById('add-sem-modal').classList.remove('hidden')"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-forest-800
                   border border-forest-700/50 text-forest-300 text-sm font-semibold
                   hover:bg-forest-700 transition-colors">
      <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
      </svg>
      Add Semester
    </button>
  </div>
</div>

{{-- ── Level Grid ──────────────────────────────────────────────── --}}
@php
  $semestersByLevel = $breakdown->groupBy(fn($r) => $r['semester']->level);
  $levels      = [100, 200, 300, 400];
  $levelLabels = [100 => '100 Level', 200 => '200 Level', 300 => '300 Level', 400 => '400 Level'];
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
  @foreach($levels as $level)
  <div class="app-card !p-0 overflow-hidden fade-up" style="animation-delay:{{ ($loop->index) * .06 }}s">

    {{-- Level header --}}
    <div class="px-5 py-3.5 bg-ink-800/50 border-b border-ink-800 flex items-center justify-between">
      <div class="flex items-center gap-2.5">
        <div class="w-8 h-8 rounded-lg bg-forest-950 border border-forest-900
                    flex items-center justify-center text-forest-400 font-mono text-xs font-bold">
          {{ substr($level, 0, 1) }}
        </div>
        <h3 class="font-display text-base font-semibold text-white">{{ $levelLabels[$level] }}</h3>
      </div>
      <span class="text-xs text-ink-600 font-mono">
        {{ ($semestersByLevel[$level] ?? collect())->count() }}/2
      </span>
    </div>

    {{-- Semester rows --}}
    <div class="p-4 space-y-2.5">
      @forelse($semestersByLevel[$level] ?? collect() as $row)
      @php
        $sem = $row['semester'];
        $gpa = $row['gpa'];
        $c = match(true) {
          $gpa >= 4.5 => ['ring'=>'ring-forest-700/40','text'=>'text-forest-400'],
          $gpa >= 3.5 => ['ring'=>'ring-green-700/40', 'text'=>'text-green-400'],
          $gpa >= 2.4 => ['ring'=>'ring-yellow-700/40','text'=>'text-yellow-400'],
          $gpa >= 1.5 => ['ring'=>'ring-orange-700/40','text'=>'text-orange-400'],
          default     => ['ring'=>'ring-red-800/40',   'text'=>'text-red-400'],
        };
      @endphp
      <div class="flex items-center gap-3 p-3.5 rounded-xl bg-ink-800/30
                  ring-1 {{ $c['ring'] }} hover:bg-ink-800/60 transition-colors">
        <div class="flex-1 min-w-0">
          <p class="text-sm font-semibold text-ink-200">
            {{ $sem->semester_type === 1 ? '1st Semester' : '2nd Semester' }}
          </p>
          <p class="text-xs text-ink-600 mt-0.5 flex items-center gap-1.5">
            <span>{{ $row['course_count'] }} course{{ $row['course_count'] !== 1 ? 's' : '' }}</span>
            <span class="text-ink-800">·</span>
            <span>{{ $row['total_units'] }} units</span>
            <span class="text-ink-800">·</span>
            <span>{{ $row['quality_pts'] }} QP</span>
          </p>
        </div>

        <div class="text-right flex-shrink-0 mr-1">
          <p class="text-xs text-ink-600 leading-none mb-0.5">GPA</p>
          <p class="font-mono font-bold text-lg {{ $c['text'] }} leading-none">
            {{ number_format($gpa, 2) }}
          </p>
        </div>

        <a href="{{ route('cgpa.semester.show', $sem) }}"
           class="w-8 h-8 rounded-lg bg-ink-700/50 hover:bg-forest-900 flex items-center
                  justify-center transition-colors flex-shrink-0">
          <svg class="w-3.5 h-3.5 text-ink-400 hover:text-forest-300" fill="none"
               viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
          </svg>
        </a>

        <form method="POST" action="{{ route('cgpa.semester.destroy', $sem) }}"
              onsubmit="return confirm('Delete {{ $sem->label }} and ALL its courses?')">
          @csrf @method('DELETE')
          <button type="submit"
                  class="w-8 h-8 rounded-lg bg-ink-700/50 hover:bg-red-950/50 flex items-center
                         justify-center transition-colors flex-shrink-0 group">
            <svg class="w-3 h-3 text-ink-600 group-hover:text-red-400" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round"
                    d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
            </svg>
          </button>
        </form>
      </div>
      @empty
      <div class="py-6 text-center">
        <p class="text-xs text-ink-700 italic">No semesters for this level yet.</p>
      </div>
      @endforelse
    </div>
  </div>
  @endforeach
</div>

{{-- ════════════════════════════════════ ADD SEMESTER MODAL ══════ --}}
<div id="add-sem-modal"
     class="hidden fixed inset-0 z-50 flex items-center justify-center bg-ink-950/80 backdrop-blur-sm p-4"
     onclick="if(event.target===this)this.classList.add('hidden')">

  <div class="w-full max-w-md rounded-2xl bg-ink-900 border border-ink-700 shadow-2xl">
    <div class="flex items-center justify-between px-6 py-4 border-b border-ink-800">
      <h3 class="font-display text-lg font-bold text-white">Add New Semester</h3>
      <button type="button" onclick="document.getElementById('add-sem-modal').classList.add('hidden')"
              class="w-8 h-8 rounded-lg bg-ink-800 hover:bg-ink-700 flex items-center justify-center
                     text-ink-500 hover:text-white transition-colors">
        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
          <path d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>

    <form method="POST" action="{{ route('cgpa.semester.store') }}" class="px-6 py-5 space-y-4">
      @csrf

      {{-- Level Select --}}
      <div>
        <label class="block text-xs font-semibold text-ink-500 uppercase tracking-widest mb-1.5">
          Academic Level
        </label>
        <select name="level" id="modal-level" required
                class="w-full bg-ink-800 border border-ink-700 text-ink-100 rounded-xl px-4 py-3
                       text-sm focus:outline-none focus:ring-2 focus:ring-forest-700/40
                       focus:border-forest-700 transition-all cursor-pointer">
          <option value="" disabled selected>Select level…</option>
          @foreach([100, 200, 300, 400] as $lvl)
          <option value="{{ $lvl }}">{{ $lvl }} Level</option>
          @endforeach
        </select>
      </div>

      {{-- Semester Type Select (styled radio-look buttons) --}}
      <div>
        <label class="block text-xs font-semibold text-ink-500 uppercase tracking-widest mb-1.5">
          Semester
        </label>
        <div class="grid grid-cols-2 gap-2.5" id="sem-type-group">
          @foreach([1 => '1st Semester', 2 => '2nd Semester'] as $val => $label)
          <label class="cursor-pointer">
            <input type="radio" name="semester_type" value="{{ $val }}" class="sr-only sem-radio"/>
            <div class="px-4 py-3 rounded-xl border border-ink-700 text-center text-sm font-medium
                        text-ink-400 hover:border-forest-700 hover:bg-forest-950/30
                        transition-all duration-150 sem-btn">
              {{ $label }}
            </div>
          </label>
          @endforeach
        </div>
      </div>

      <div class="flex gap-2.5 pt-1">
        <button type="button"
                onclick="document.getElementById('add-sem-modal').classList.add('hidden')"
                class="flex-1 px-4 py-2.5 rounded-xl border border-ink-700 text-ink-400
                       text-sm font-medium hover:bg-ink-800 transition-colors">
          Cancel
        </button>
        <button type="submit"
                class="flex-1 px-4 py-2.5 rounded-xl bg-forest-800 hover:bg-forest-700
                       text-forest-300 text-sm font-bold transition-colors border border-forest-700/50">
          Create Semester
        </button>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
  // Radio-look semester type buttons
  document.querySelectorAll('.sem-radio').forEach(radio => {
    radio.addEventListener('change', function() {
      document.querySelectorAll('.sem-btn').forEach(b => {
        b.classList.remove('border-forest-600','bg-forest-950/50','text-forest-300');
        b.classList.add('border-ink-700','text-ink-400');
      });
      if (this.checked) {
        const btn = this.nextElementSibling;
        btn.classList.add('border-forest-600','bg-forest-950/50','text-forest-300');
        btn.classList.remove('border-ink-700','text-ink-400');
      }
    });
  });
</script>
@endpush

@endsection