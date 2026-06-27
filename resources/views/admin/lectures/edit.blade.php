@extends('layouts.admin')
@section('title', isset($lecture) ? 'Edit Lecture' : 'Add Lecture')
@section('page_title', isset($lecture) ? 'Edit Lecture' : 'Add Lecture')
@section('page_sub', 'Rep-only · Lecture Timetable')

@section('content')
<div class="max-w-xl mx-auto">
  <nav class="flex items-center gap-2 text-xs text-ink-700 mb-5 fu">
    <a href="{{ route('admin.lectures.index') }}" class="hover:text-ink-400 transition-colors">Lectures</a>
    <span>›</span>
    <span class="text-ink-400">{{ isset($lecture) ? 'Edit' : 'Add New' }}</span>
  </nav>

  <div class="a-card fu1">
    <h2 class="font-display text-lg font-bold text-white mb-5">
      {{ isset($lecture) ? 'Edit Lecture' : 'Add New Lecture' }}
    </h2>

    <form method="POST"
          action="{{ isset($lecture) ? route('admin.lectures.update', $lecture->id) : route('admin.lectures.store') }}">
      @csrf
      @if(isset($lecture)) @method('PUT') @endif

      <div class="mb-4">
        <label class="form-label">Course / Resource</label>
        <select name="resource_id" required class="form-input cursor-pointer">
          <option value="" disabled {{ !isset($lecture) ? 'selected':'' }}>Select course…</option>
          @foreach($resources ?? [] as $res)
          <option value="{{ $res->id }}"
                  {{ old('resource_id', $lecture->resource_id ?? '') == $res->id ? 'selected':'' }}>
            {{ $res->course_code }} – {{ $res->course_title }}
          </option>
          @endforeach
        </select>
        @error('resource_id')<p class="form-error">{{ $message }}</p>@enderror
      </div>

      <div class="grid grid-cols-2 gap-3 mb-4">
        <div>
          <label class="form-label">Lecturer</label>
          <input type="text" name="lecturer"
                 value="{{ old('lecturer', $lecture->lecturer ?? '') }}"
                 placeholder="Dr. Smith"
                 class="form-input"/>
          @error('lecturer')<p class="form-error">{{ $message }}</p>@enderror
        </div>

        <div class="w-full px-2.5"> <label class="form-label"> Duration </label> 
             <input value="{{ $lecture->duration_minutes }}" name="duration_minutes" type="number" placeholder="Enter duration in minutes" class="form-input"> 
            </div> 


        <div>
          <label class="form-label">Hall / Venue</label>
          <input type="text" name="hall"
                 value="{{ old('hall', $lecture->hall ?? '') }}"
                 placeholder="LT 1"
                 class="form-input"/>
          @error('hall')<p class="form-error">{{ $message }}</p>@enderror
        </div>
      </div>

      <div class="grid grid-cols-2 gap-3 mb-5">
        <div>
          <label class="form-label">Start Date & Time</label>
          <input type="datetime-local" name="start_time" required
                 value="{{ old('start_time', isset($lecture->start_time) ? $lecture->start_time->format('Y-m-d\TH:i') : '') }}"
                 class="form-input"/>
          @error('start_time')<p class="form-error">{{ $message }}</p>@enderror
        </div>
        <div>
          <label class="form-label">End Time (optional)</label>
          <input type="datetime-local" name="end_time"
                 value="{{ old('end_time', isset($lecture->end_time) ? $lecture->end_time->format('Y-m-d\TH:i') : '') }}"
                 class="form-input"/>
        </div>
      </div>

      <div class="mb-5">
        <label class="flex items-center gap-2.5 cursor-pointer">
          <input type="checkbox" name="push_notification" value="1"
                 {{ old('push_notification', $lecture->push_notification ?? false) ? 'checked':'' }}
                 class="w-4 h-4 accent-forest-600"/>
          <span class="text-sm text-ink-300">Send push notification to students</span>
        </label>
        <p class="text-xs text-ink-600 mt-1 ml-6">Students will receive an alert 15 minutes before this lecture.</p>
      </div>

      <div class="flex gap-3">
        <button type="submit" class="btn-primary">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <path d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          {{ isset($lecture) ? 'Save Changes' : 'Add Lecture' }}
        </button>
        <a href="{{ route('admin.lectures.index') }}" class="btn-outline">Cancel</a>
      </div>
    </form>
  </div>
</div>
@endsection