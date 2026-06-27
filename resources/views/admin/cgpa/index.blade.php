@extends('layouts.admin')
@section('title', 'CGPA Course Options')
@section('page_title', 'CGPA Course Options')
@section('page_sub', 'Admin · Manage predefined course codes per level/semester')

@section('content')

<div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-5 fu">
  <div>
    <h2 class="font-display text-xl font-bold text-white">Course Options</h2>
    <p class="text-xs text-ink-600 mt-0.5">
      Predefined course codes shown to students in the CGPA calculator dropdown.
    </p>
  </div>
</div>

{{-- ── Level / Semester Filter Tabs ─────────────────────────────── --}}
<div class="flex flex-wrap gap-2 mb-5 fu1">
  @foreach($allLevels as $lvl)
  @foreach($allSemesterTypes as $semVal => $semLabel)
  <a href="{{ route('admin.cgpa.index', ['level' => $lvl, 'semester_type' => $semVal]) }}"
     class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all duration-150 border
            {{ $level === $lvl && $semesterType === $semVal
               ? 'bg-forest-900 border-forest-700 text-forest-300'
               : 'bg-ink-900 border-ink-700 text-ink-500 hover:border-ink-600 hover:text-ink-300' }}">
    {{ $lvl }}L · {{ $semVal === 1 ? '1st' : '2nd' }}
  </a>
  @endforeach
  @endforeach
</div>

<div class="grid lg:grid-cols-3 gap-4">

  {{-- ── Course List ─────────────────────────────────────────────── --}}
  <div class="lg:col-span-2 a-card !p-0 overflow-hidden fu2">
    <div class="px-5 py-3.5 border-b border-ink-800 flex items-center justify-between">
      <h3 class="font-display text-sm font-bold text-white">
        {{ $level }}L · {{ $allSemesterTypes[$semesterType] }}
        <span class="ml-2 text-xs font-mono font-normal text-ink-600">{{ $options->count() }} courses</span>
      </h3>
    </div>

    @if($options->isEmpty())
    <div class="py-10 text-center">
      <p class="text-sm text-ink-600">No courses defined for this level/semester yet.</p>
    </div>
    @else
    <table class="w-full">
      <thead>
        <tr class="border-b border-ink-800">
          <th class="tbl-head text-left">Code</th>
          <th class="tbl-head text-left">Course Title</th>
          <th class="tbl-head text-center">Unit</th>
          <th class="tbl-head text-center">Active</th>
          <th class="tbl-head text-right">Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($options as $opt)
        <tr class="tbl-row group">
          <td class="tbl-cell font-mono font-semibold text-forest-400 text-xs">
            {{ $opt->course_code }}
          </td>
          <td class="tbl-cell">
            <form method="POST" action="{{ route('admin.cgpa.update', $opt->id) }}"
                  id="edit-opt-{{ $opt->id }}">
              @csrf @method('PUT')
              <input type="hidden" name="credit_unit" value="{{ $opt->credit_unit }}"/>
              <input type="hidden" name="is_active"   value="{{ $opt->is_active ? 1 : 0 }}"/>
              <input type="text" name="course_title"
                     value="{{ $opt->course_title }}"
                     class="bg-transparent border-none text-xs text-ink-300 w-full outline-none
                            focus:text-ink-100 rounded px-1 py-0.5
                            focus:bg-ink-800 focus:ring-1 focus:ring-forest-800 transition-all"
                     onblur="this.closest('form').requestSubmit()"/>
            </form>
          </td>
          <td class="tbl-cell text-center">
            <form method="POST" action="{{ route('admin.cgpa.update', $opt->id) }}">
              @csrf @method('PUT')
              <input type="hidden" name="course_title" value="{{ $opt->course_title }}"/>
              <input type="hidden" name="is_active"    value="{{ $opt->is_active ? 1 : 0 }}"/>
              <select name="credit_unit"
                      onchange="this.form.submit()"
                      class="bg-ink-800 border border-ink-700 rounded-lg px-2 py-1
                             text-xs text-ink-300 cursor-pointer outline-none
                             focus:border-forest-700 transition-colors">
                @for($u = 1; $u <= 6; $u++)
                <option value="{{ $u }}" {{ $opt->credit_unit === $u ? 'selected':'' }}>{{ $u }}</option>
                @endfor
              </select>
            </form>
          </td>
          <td class="tbl-cell text-center">
            <form method="POST" action="{{ route('admin.cgpa.update', $opt->id) }}">
              @csrf @method('PUT')
              <input type="hidden" name="course_title" value="{{ $opt->course_title }}"/>
              <input type="hidden" name="credit_unit"  value="{{ $opt->credit_unit }}"/>
              <button type="submit" name="is_active" value="{{ $opt->is_active ? 0 : 1 }}"
                      class="px-2 py-1 rounded-lg text-xs font-semibold transition-colors
                             {{ $opt->is_active
                                ? 'bg-forest-950 border border-forest-900 text-forest-400 hover:bg-forest-900/50'
                                : 'bg-ink-800 border border-ink-700 text-ink-600 hover:border-ink-600' }}">
                {{ $opt->is_active ? 'On' : 'Off' }}
              </button>
            </form>
          </td>
          <td class="tbl-cell text-right">
            <form method="POST" action="{{ route('admin.cgpa.destroy', $opt->id) }}"
                  onsubmit="return confirm('Remove {{ addslashes($opt->course_code) }}?')">
              @csrf @method('DELETE')
              <button type="submit"
                      class="btn-danger btn-sm !px-2 !py-1 text-xs opacity-0 group-hover:opacity-100 transition-opacity">
                Del
              </button>
            </form>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
    @endif
  </div>

  {{-- ── Add New Course Option ────────────────────────────────────── --}}
  <div class="a-card fu3">
    <h3 class="font-display text-sm font-bold text-white mb-4">Add Course to List</h3>

    <form method="POST" action="{{ route('admin.cgpa.store') }}" class="space-y-3.5">
      @csrf

      {{-- Level (pre-filled from filter) --}}
      <div>
        <label class="form-label">Level</label>
        <select name="level" required class="form-input text-sm cursor-pointer">
          @foreach($allLevels as $lvl)
          <option value="{{ $lvl }}" {{ $level === $lvl ? 'selected' : '' }}>{{ $lvl }} Level</option>
          @endforeach
        </select>
        @error('level')<p class="form-error">{{ $message }}</p>@enderror
      </div>

      {{-- Semester Type --}}
      <div>
        <label class="form-label">Semester</label>
        <select name="semester_type" required class="form-input text-sm cursor-pointer">
          @foreach($allSemesterTypes as $val => $label)
          <option value="{{ $val }}" {{ $semesterType === $val ? 'selected' : '' }}>{{ $label }}</option>
          @endforeach
        </select>
        @error('semester_type')<p class="form-error">{{ $message }}</p>@enderror
      </div>

      {{-- Course Code --}}
      <div>
        <label class="form-label">Course Code</label>
        <input type="text" name="course_code" required maxlength="20"
               placeholder="e.g. SOC 315"
               value="{{ old('course_code') }}"
               class="form-input text-sm uppercase"/>
        @error('course_code')<p class="form-error">{{ $message }}</p>@enderror
      </div>

      {{-- Course Title --}}
      <div>
        <label class="form-label">Course Title</label>
        <input type="text" name="course_title" required maxlength="120"
               placeholder="e.g. Sociological Perspectives on Media"
               value="{{ old('course_title') }}"
               class="form-input text-sm"/>
        @error('course_title')<p class="form-error">{{ $message }}</p>@enderror
      </div>

      {{-- Credit Unit --}}
      <div>
        <label class="form-label">Default Credit Unit</label>
        <select name="credit_unit" required class="form-input text-sm cursor-pointer">
          @for($u = 1; $u <= 6; $u++)
          <option value="{{ $u }}" {{ old('credit_unit', 2) == $u ? 'selected' : '' }}>
            {{ $u }} Unit{{ $u > 1 ? 's' : '' }}
          </option>
          @endfor
        </select>
        @error('credit_unit')<p class="form-error">{{ $message }}</p>@enderror
      </div>

      <button type="submit" class="btn-primary w-full justify-center mt-1">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <path d="M12 4.5v15m7.5-7.5h-15"/>
        </svg>
        Add to List
      </button>
    </form>

    <div class="mt-4 pt-4 border-t border-ink-800">
      <p class="text-xs text-ink-600 leading-relaxed">
        <strong class="text-ink-400">Tip:</strong> Courses set to
        <span class="text-forest-500">Active = On</span> appear in the student
        dropdown. Toggle them off to hide without deleting.
      </p>
    </div>
  </div>
</div>

@endsection