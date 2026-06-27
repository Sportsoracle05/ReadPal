@extends('layouts.admin')
@section('title', isset($resource) ? 'Edit Resource' : 'New Resource')
@section('page_title', isset($resource) ? 'Edit Resource' : 'New Resource')
@section('page_sub', 'Super Admin')

@section('content')
<div class="max-w-md mx-auto">
  <nav class="flex items-center gap-2 text-xs text-ink-700 mb-5">
    <a href="{{ route('admin.resources.index') }}" class="hover:text-ink-400 transition-colors">Resources</a>
    <span>›</span>
    <span class="text-ink-400">{{ isset($resource) ? 'Edit' : 'Create' }}</span>
  </nav>

  <div class="a-card">
    <h2 class="font-display text-lg font-bold text-white mb-5">
      {{ isset($resource) ? 'Edit Resource' : 'New Course Resource' }}
    </h2>
    <form method="POST"
          action="{{ isset($resource) ? route('admin.resources.update', $resource->id) : route('admin.resources.store') }}">
      @csrf
      @if(isset($resource)) @method('PUT') @endif

      <div class="mb-4">
        <label class="form-label">Course Code</label>
        <input type="text" name="course_code" required
               value="{{ old('course_code', $resource->course_code ?? '') }}"
               placeholder="e.g. SOC 303"
               class="form-input uppercase"/>
        @error('course_code')<p class="form-error">{{ $message }}</p>@enderror
      </div>

      <div class="mb-4">
        <label class="form-label">Course Title</label>
        <input type="text" name="course_title" required
               value="{{ old('course_title', $resource->course_title ?? '') }}"
               placeholder="e.g. Sociology of Crime and Delinquency"
               class="form-input"/>
        @error('course_title')<p class="form-error">{{ $message }}</p>@enderror
      </div>

      <div class="mb-4"> <label class="form-label"> Lecturer </label> 
             <input name="lecturer" type="text" placeholder="Enter lecturer name" class="form-input"/> 
            </div> 

      <div class="mb-5">
        <label class="form-label">Slug (auto-generated if blank)</label>
        <input type="text" name="slug"
               value="{{ old('slug', $resource->slug ?? '') }}"
               placeholder="soc-303"
               class="form-input font-mono text-xs"/>
      </div>

      <div class="flex gap-3">
        <button type="submit" class="btn-primary">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <path d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          {{ isset($resource) ? 'Save Changes' : 'Create Resource' }}
        </button>
        <a href="{{ route('admin.resources.index') }}" class="btn-outline">Cancel</a>
      </div>
    </form>
  </div>
</div>
@endsection