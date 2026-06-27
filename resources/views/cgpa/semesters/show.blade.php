@extends('layouts.app')
@section('title', $semester->label)
@section('page_title', $semester->label)
@section('page_sub', 'CGPA · Course Entry')

@section('content')

{{-- Breadcrumb --}}
<nav class="flex items-center gap-2 text-xs text-ink-700 mb-5 fade-up">
  <a href="{{ route('cgpa.dashboard') }}" class="hover:text-ink-400 transition-colors">CGPA</a>
  <span>›</span>
  <a href="{{ route('cgpa.semester.index') }}" class="hover:text-ink-400 transition-colors">Semesters</a>
  <span>›</span>
  <span class="text-ink-400">{{ $semester->label }}</span>
</nav>

{{-- ── Page Header ─────────────────────────────────────────────── --}}
<div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-5 fade-up">
  <div>
    <p class="text-xs font-semibold tracking-widest uppercase text-forest-600 mb-1">
      {{ $semester->level }}L &bull; {{ $semester->semester_type === 1 ? 'First' : 'Second' }} Semester
    </p>
    <h2 class="font-display text-2xl font-bold text-white">{{ $semester->label }}</h2>
  </div>
  <button onclick="document.getElementById('add-course-modal').classList.remove('hidden')"
          class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-forest-800
                 border border-forest-700/50 text-forest-300 text-sm font-semibold
                 hover:bg-forest-700 transition-colors self-start sm:self-auto">
    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
      <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
    </svg>
    Add Course
  </button>
</div>

{{-- ── Stats Row ───────────────────────────────────────────────── --}}
@php
  $gpaColor = match(true) {
    $gpa >= 4.5 => 'text-forest-400',
    $gpa >= 3.5 => 'text-green-400',
    $gpa >= 2.4 => 'text-yellow-400',
    $gpa >= 1.5 => 'text-orange-400',
    default     => 'text-red-400',
  };
@endphp
<div class="grid grid-cols-1 sm:grid-cols-4 gap-3 mb-5 fade-up-d1">
  @foreach([
    ['Semester GPA',   number_format($gpa, 2),                        $gpaColor],
    ['Overall CGPA',   number_format($cgpa, 2),                       'text-forest-300'],
    ['Total Units',    $semester->total_units,                        'text-ink-200'],
    ['Quality Points', $semester->total_quality_points,               'text-ink-200'],
  ] as [$label, $val, $color])
  <div class="app-card !py-3.5">
    <p class="text-xs text-ink-600 uppercase tracking-wider mb-1">{{ $label }}</p>
    <p class="font-mono text-2xl font-bold {{ $color }}">{{ $val }}</p>
  </div>
  @endforeach
</div>

{{-- ── Courses Table ───────────────────────────────────────────── --}}
<div class="w-full app-card !p-0 overflow-hidden fade-up-d2">
  <div class="px-5 py-3.5 border-b border-ink-800 flex items-center justify-between">
    <h3 class="font-display text-sm font-bold text-white">
      Courses
      <span class="ml-2 px-2 py-0.5 rounded-full text-xs font-mono font-normal bg-ink-800 text-ink-500">
        {{ $semester->courses->count() }}
      </span>
    </h3>
    <p class="text-xs text-ink-700 font-mono">GPA = Σ(unit × grade pt) / Σ units</p>
  </div>

  @if($semester->courses->isEmpty())
  <div class="py-14 text-center">
    <div class="w-12 h-12 rounded-xl bg-ink-800 border border-ink-700 flex items-center justify-center mx-auto mb-3">
      <svg class="w-5 h-5 text-ink-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0118 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
      </svg>
    </div>
    <p class="text-sm text-ink-500 font-medium">No courses added yet</p>
    <p class="text-xs text-ink-700 mt-1">Click "Add Course" to start logging your results.</p>
  </div>
  @else
{{-- THE SCROLLABLE FRAME --}}
<div class="w-full overflow-x-auto border-t border-ink-800">
  <table class="w-full text-sm border-collapse table-auto">
      <thead>
        <tr class="border-b border-ink-800">
          <th class="hidden sm:table-cell text-left px-3 sm:px-5 py-3 text-xs font-semibold uppercase tracking-wider text-ink-600">#</th>
          <th class="text-left px-3 sm:px-5 py-3 text-xs font-semibold uppercase tracking-wider text-ink-600">Course</th>
          <th class="hidden sm:table-cell text-center px-3 sm:px-5 py-3 text-xs font-semibold uppercase tracking-wider text-ink-600">Units</th>
          <th class="text-center px-3 sm:px-5 py-3 text-xs font-semibold uppercase tracking-wider text-ink-600">Grade</th>
          <th class="hidden sm:table-cell text-center px-3 sm:px-5 py-3 text-xs font-semibold uppercase tracking-wider text-ink-600">Pts</th>
          <th class="hidden sm:table-cell text-center px-3 sm:px-5 py-3 text-xs font-semibold uppercase tracking-wider text-ink-600">QP</th>
          <th class="px-3 sm:px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-ink-600">Actions</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-ink-800/50">
        @foreach($semester->courses as $i => $course)
        @php
          $gc = match($course->grade_letter) {
            'A'  => 'text-forest-400 bg-forest-950/40 border-forest-800/60',
            'B'  => 'text-green-400  bg-green-950/30  border-green-800/40',
            'C'  => 'text-yellow-400 bg-yellow-950/20 border-yellow-800/30',
            'D'  => 'text-orange-400 bg-orange-950/20 border-orange-800/30',
            'E'  => 'text-red-400    bg-red-950/20    border-red-800/30',
            default => 'text-ink-600 bg-ink-800/50    border-ink-700/40',
          };
        @endphp
        <tr class="hover:bg-ink-800/20 transition-colors group">
          <td class="hidden sm:table-cell px-3 sm:px-4 py-3.5 text-xs font-mono text-ink-700">
            {{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}
          </td>
          <td class="px-3 sm:px-4 py-3.5">
            <p class="font-mono font-semibold text-ink-100 text-sm">{{ $course->course_code }}</p>
            @php
              $courseTitle = $courseOptions[$course->course_code]['title'] ?? null;
            @endphp
            @if($courseTitle)
            <p class="text-xs text-ink-600 mt-0.5 max-w-[140px] sm:max-w-[220px] truncate">{{ $courseTitle }}</p>
            @endif
          </td>
          <td class="hidden sm:table-cell px-4 py-3.5 text-center font-mono text-ink-300">{{ $course->unit }}</td>
          <td class="px-3 sm:px-4 py-3.5 text-center">
            <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg border
                         font-display font-bold text-base {{ $gc }}">
              {{ $course->grade_letter }}
            </span>
          </td>
          <td class="hidden sm:table-cell px-3 sm:px-4 py-3.5 text-center font-mono text-ink-400 text-xs">{{ $course->grade_point }}</td>
          <td class="hidden sm:table-cell px-3 sm:px-4 py-3.5 text-center font-mono font-semibold text-ink-200">
            {{ $course->quality_point }}
          </td>
          <td class="px-3 sm:px-4 py-3.5 text-right">
            <div class="flex items-center justify-end gap-1.5 sm:opacity-0 sm:group-hover:opacity-100 transition-opacity">
              <button onclick="openEditModal({{ $course->id }}, '{{ $course->course_code }}',
                               {{ $course->unit }}, '{{ $course->grade_letter }}')"
                      class="px-2.5 py-1.5 rounded-lg bg-ink-800 border border-ink-700 text-ink-400
                             hover:border-forest-800 hover:text-forest-400 text-xs font-medium transition-colors">
                Edit
              </button>
              <form method="POST"
                    action="{{ route('cgpa.semester.course.destroy', [$semester, $course]) }}"
                    onsubmit="return confirm('Remove {{ $course->course_code }}?')">
                @csrf @method('DELETE')
                <button type="submit"
                        class="px-2.5 py-1.5 rounded-lg bg-ink-800 border border-ink-700 text-ink-600
                               hover:border-red-900 hover:text-red-400 text-xs font-medium transition-colors">
                  ✕
                </button>
              </form>
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>

      {{-- Totals Footer --}}
      <tfoot>
        <tr class="border-t-2 border-ink-700 bg-ink-800/20">
          <td colspan="2" class="px-5 py-3.5 text-xs font-semibold text-ink-500 uppercase tracking-wider">
            Totals
          </td>
          <td class="px-4 py-3.5 text-center font-mono font-bold text-ink-200">
            {{ $semester->total_units }}
          </td>
          <td colspan="2" class="px-4 py-3.5"></td>
          <td class="px-4 py-3.5 text-center font-mono font-bold text-ink-200">
            {{ $semester->total_quality_points }}
          </td>
          <td class="px-4 py-3.5 text-right">
            <span class="font-mono font-bold text-sm {{ $gpaColor }}">
              GPA: {{ number_format($gpa, 2) }}
            </span>
          </td>
        </tr>
      </tfoot>
    </table>
  </div>
  @endif
</div>


{{-- ════════════════════════ ADD COURSE MODAL ══════════════════════ --}}
<div id="add-course-modal"
     class="hidden fixed inset-0 z-50 flex items-center justify-center bg-ink-950/80 backdrop-blur-sm p-4"
     onclick="if(event.target===this)this.classList.add('hidden')">

  <div class="w-full max-w-md rounded-2xl bg-ink-900 border border-ink-700 shadow-2xl">
    <div class="flex items-center justify-between px-6 py-4 border-b border-ink-800">
      <h3 class="font-display text-lg font-bold text-white">Add Course</h3>
      <button type="button" onclick="document.getElementById('add-course-modal').classList.add('hidden')"
              class="w-8 h-8 rounded-lg bg-ink-800 hover:bg-ink-700 flex items-center justify-center
                     text-ink-500 hover:text-white transition-colors">
        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
          <path d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>

    <form method="POST"
          action="{{ route('cgpa.semester.course.store', $semester) }}"
          class="px-6 py-5 space-y-4">
      @csrf

      {{-- Course Code dropdown + manual fallback --}}
      <div>
        <label class="block text-xs font-semibold text-ink-500 uppercase tracking-widest mb-1.5">
          Course Code
        </label>

        @if(!empty($courseOptions))
        {{-- Predefined dropdown --}}
        <select id="course-code-select" name="course_code" required
                class="w-full bg-ink-800 border border-ink-700 text-ink-100 rounded-xl px-4 py-3
                       text-sm focus:outline-none focus:ring-2 focus:ring-forest-700/40
                       focus:border-forest-700 transition-all cursor-pointer"
                onchange="autofillCourse(this.value)">
          <option value="" disabled selected>Select course…</option>
          @foreach($courseOptions as $code => $info)
          <option value="{{ $code }}"
                  data-unit="{{ $info['unit'] }}"
                  data-title="{{ $info['title'] }}">
            {{ $code }} — {{ $info['title'] }}
          </option>
          @endforeach
          <option value="__custom__">➕ Enter manually…</option>
        </select>

        {{-- Manual input (hidden by default) --}}
        <input type="text" id="course-code-manual" name="course_code_manual"
               placeholder="e.g. SOC 399"
               class="hidden mt-2 w-full bg-ink-800 border border-ink-700 text-ink-100 rounded-xl
                      px-4 py-3 text-sm uppercase placeholder-ink-600
                      focus:outline-none focus:ring-2 focus:ring-forest-700/40 focus:border-forest-700 transition-all"/>
        @else
        {{-- No predefined options → always manual --}}
        <input type="text" id="course-code-manual" name="course_code" required
               placeholder="e.g. SOC 301"
               class="w-full bg-ink-800 border border-ink-700 text-ink-100 rounded-xl px-4 py-3
                      text-sm uppercase placeholder-ink-600
                      focus:outline-none focus:ring-2 focus:ring-forest-700/40 focus:border-forest-700 transition-all"/>
        @endif
        @error('course_code')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
      </div>

      {{-- Credit Units --}}
      <div>
        <label class="block text-xs font-semibold text-ink-500 uppercase tracking-widest mb-1.5">
          Credit Units
        </label>
        <select name="unit" id="unit-select" required
                class="w-full bg-ink-800 border border-ink-700 text-ink-100 rounded-xl px-4 py-3
                       text-sm focus:outline-none focus:ring-2 focus:ring-forest-700/40
                       focus:border-forest-700 transition-all cursor-pointer">
          @for($u = 1; $u <= 6; $u++)
          <option value="{{ $u }}" {{ old('unit', 2) == $u ? 'selected' : '' }}>
            {{ $u }} Unit{{ $u > 1 ? 's' : '' }}
          </option>
          @endfor
        </select>
        @error('unit')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
      </div>

      {{-- Grade Dropdown --}}
      <div>
        <label class="block text-xs font-semibold text-ink-500 uppercase tracking-widest mb-1.5">
          Grade
        </label>
        <select name="grade_letter" required
                class="w-full bg-ink-800 border border-ink-700 text-ink-100 rounded-xl px-4 py-3
                       text-sm focus:outline-none focus:ring-2 focus:ring-forest-700/40
                       focus:border-forest-700 transition-all cursor-pointer">
          @foreach($gradeOptions as $letter => $pts)
          <option value="{{ $letter }}" {{ old('grade_letter') === $letter ? 'selected' : '' }}>
            {{ $letter }} — {{ $pts }} pts
            ({{ match($letter) { 'A'=>'70–100%','B'=>'60–69%','C'=>'50–59%','D'=>'45–49%','E'=>'40–44%','F'=>'0–39%' } }})
          </option>
          @endforeach
        </select>
        @error('grade_letter')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
      </div>

      {{-- Quality Point Preview --}}
      <div class="flex items-center justify-between px-4 py-2.5 rounded-xl
                  bg-ink-800/50 border border-ink-700/50">
        <span class="text-xs text-ink-500">Quality Points Preview</span>
        <span id="qp-preview" class="font-mono font-bold text-forest-400 text-sm">—</span>
      </div>

      <div class="flex gap-2.5 pt-1">
        <button type="button"
                onclick="document.getElementById('add-course-modal').classList.add('hidden')"
                class="flex-1 px-4 py-2.5 rounded-xl border border-ink-700 text-ink-400
                       text-sm font-medium hover:bg-ink-800 transition-colors">
          Cancel
        </button>
        <button type="submit"
                class="flex-1 px-4 py-2.5 rounded-xl bg-forest-800 hover:bg-forest-700
                       text-forest-300 text-sm font-bold transition-colors border border-forest-700/50">
          Add Course
        </button>
      </div>
    </form>
  </div>
</div>


{{-- ════════════════════════ EDIT COURSE MODAL ════════════════════ --}}
<div id="edit-course-modal"
     class="hidden fixed inset-0 z-50 flex items-center justify-center bg-ink-950/80 backdrop-blur-sm p-4"
     onclick="if(event.target===this)this.classList.add('hidden')">

  <div class="w-full max-w-md rounded-2xl bg-ink-900 border border-ink-700 shadow-2xl">
    <div class="flex items-center justify-between px-6 py-4 border-b border-ink-800">
      <h3 class="font-display text-lg font-bold text-white">Edit Course</h3>
      <button type="button" onclick="document.getElementById('edit-course-modal').classList.add('hidden')"
              class="w-8 h-8 rounded-lg bg-ink-800 hover:bg-ink-700 flex items-center justify-center
                     text-ink-500 hover:text-white transition-colors">
        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
          <path d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>

    <form id="edit-course-form" method="POST" action="" class="px-6 py-5 space-y-4">
      @csrf @method('PUT')

      {{-- Edit: show course code as read-only display + hidden input --}}
      <div>
        <label class="block text-xs font-semibold text-ink-500 uppercase tracking-widest mb-1.5">
          Course Code
        </label>
        <div class="flex gap-2">
          <div class="flex-1 px-4 py-3 rounded-xl bg-ink-800/50 border border-ink-700/50
                      font-mono text-sm text-ink-300" id="edit-code-display">—</div>
          <input type="hidden" id="edit-course-code" name="course_code"/>
        </div>
        <p class="text-xs text-ink-700 mt-1">Course code cannot be changed. Delete and re-add if needed.</p>
      </div>

      <div>
        <label class="block text-xs font-semibold text-ink-500 uppercase tracking-widest mb-1.5">
          Credit Units
        </label>
        <select id="edit-unit" name="unit" required
                class="w-full bg-ink-800 border border-ink-700 text-ink-100 rounded-xl px-4 py-3
                       text-sm focus:outline-none focus:ring-2 focus:ring-forest-700/40
                       focus:border-forest-700 transition-all cursor-pointer">
          @for($u = 1; $u <= 6; $u++)
          <option value="{{ $u }}">{{ $u }} Unit{{ $u > 1 ? 's' : '' }}</option>
          @endfor
        </select>
      </div>

      <div>
        <label class="block text-xs font-semibold text-ink-500 uppercase tracking-widest mb-1.5">
          Grade
        </label>
        <select id="edit-grade" name="grade_letter" required
                class="w-full bg-ink-800 border border-ink-700 text-ink-100 rounded-xl px-4 py-3
                       text-sm focus:outline-none focus:ring-2 focus:ring-forest-700/40
                       focus:border-forest-700 transition-all cursor-pointer">
          @foreach($gradeOptions as $letter => $pts)
          <option value="{{ $letter }}">{{ $letter }} — {{ $pts }} pts</option>
          @endforeach
        </select>
      </div>

      <div class="flex gap-2.5 pt-1">
        <button type="button"
                onclick="document.getElementById('edit-course-modal').classList.add('hidden')"
                class="flex-1 px-4 py-2.5 rounded-xl border border-ink-700 text-ink-400
                       text-sm font-medium hover:bg-ink-800 transition-colors">
          Cancel
        </button>
        <button type="submit"
                class="flex-1 px-4 py-2.5 rounded-xl bg-forest-800 hover:bg-forest-700
                       text-forest-300 text-sm font-bold transition-colors border border-forest-700/50">
          Save Changes
        </button>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
const GRADE_MAP = { A:5, B:4, C:3, D:2, E:1, F:0 };
const COURSE_OPTIONS = @json($courseOptionsJson ? json_decode($courseOptionsJson, true) : []);

/* ── Add modal: QP preview ─────────────────────────────────── */
function updatePreview() {
  const unitEl  = document.getElementById('unit-select');
  const gradeEl = document.querySelector('#add-course-modal select[name="grade_letter"]');
  if (!unitEl || !gradeEl) return;
  const qp = parseInt(unitEl.value || 0) * (GRADE_MAP[gradeEl.value] ?? 0);
  document.getElementById('qp-preview').textContent = qp ? qp + ' QP' : '—';
}
document.getElementById('unit-select')?.addEventListener('change', updatePreview);
document.querySelector('#add-course-modal select[name="grade_letter"]')?.addEventListener('change', updatePreview);

/* ── Course code dropdown: auto-fill unit ──────────────────── */
function autofillCourse(code) {
  const manual = document.getElementById('course-code-manual');
  const select = document.getElementById('course-code-select');
  const unitSel = document.getElementById('unit-select');

  if (code === '__custom__') {
    manual.classList.remove('hidden');
    manual.required = true;
    manual.name = 'course_code';
    select.name = ''; 
    
    // Manual Mode: Fully editable
    unitSel.readOnly = false; // Allow changes
    unitSel.classList.remove('opacity-60', 'cursor-not-allowed', 'bg-ink-900');
    
    manual.focus();
    updatePreview();
    return;
  }

  manual.classList.add('hidden');
  manual.required = false;
  manual.name = 'course_code_manual';
  select.name = 'course_code';

  const option = COURSE_OPTIONS[code];
  if (option) {
    unitSel.value = option.unit;
    
    // Database Mode: Set to Read-Only
    unitSel.readOnly = true; // Locks the field but allows submission
    unitSel.classList.add('opacity-60', 'cursor-not-allowed', 'bg-ink-900');
    updatePreview();
  } else {
    unitSel.readOnly = false;
    unitSel.classList.remove('opacity-60', 'cursor-not-allowed', 'bg-ink-900');
  }
}

/* ── Edit modal: Lock logic ────────────────────────────────── */
function openEditModal(courseId, courseCode, unit, gradeLetter) {
  const baseUrl = "{{ route('cgpa.semester.course.update', [$semester, '__ID__']) }}";
  const unitSel = document.getElementById('edit-unit');
  
  document.getElementById('edit-course-form').action = baseUrl.replace('__ID__', courseId);
  document.getElementById('edit-course-code').value  = courseCode;
  document.getElementById('edit-code-display').textContent = courseCode;
  unitSel.value = unit;
  document.getElementById('edit-grade').value = gradeLetter;

  // Check if this course code exists in our predefined database options
  const isPredefined = COURSE_OPTIONS.hasOwnProperty(courseCode);
  lockUnitField(unitSel, isPredefined);

  document.getElementById('edit-course-modal').classList.remove('hidden');
}

/* ── Helper to visually & functionally lock the select ────── */
function lockUnitField(element, shouldLock) {
  if (shouldLock) {
    element.classList.add('opacity-60', 'cursor-not-allowed', 'bg-ink-900', 'pointer-events-none');
    element.setAttribute('tabindex', '-1');
  } else {
    element.classList.remove('opacity-60', 'cursor-not-allowed', 'bg-ink-900', 'pointer-events-none');
    element.removeAttribute('tabindex');
  }
}

/* ── Auto-open add modal on course_code validation error ───── */
@if($errors->has('course_code'))
document.getElementById('add-course-modal').classList.remove('hidden');
@endif
</script>
@endpush

@endsection