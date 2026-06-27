{{-- ============================================================
  resources/views/resources/show.blade.php
  ============================================================ --}}
@extends('layouts.app')
@section('title', $resource->course_code ?? $resource->name)
@section('page_title', $resource->course_code ?? $resource->name)
@section('page_sub', $resource->name ?? 'Course Materials')

@section('content')

{{-- Header --}}
<div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-6 fade-up">
  <div>
    <span class="rp-badge badge-green mb-2 inline-block">{{ $resource->course_code }}</span>
    <h2 class="font-display text-2xl font-bold text-white">{{ $resource->name }}</h2>
    <p class="text-sm text-ink-500 mt-0.5">
      {{ $resource->materials->count() }} material{{ $resource->materials->count() !== 1 ? 's' : '' }} available
    </p>
  </div>
  <div class="flex justify-start sm:justify-end">
    <a href="{{ route('resources.full.show', $resource->slug) }}"
       class="group inline-flex items-center gap-2 px-4 py-2.5 rounded-xl
              bg-forest-800 border border-forest-700/50 text-forest-300 text-sm font-semibold
              hover:bg-forest-700 transition-all w-full sm:w-auto justify-center sm:justify-end">

        <span>View All</span>

        <!-- Improved right arrow icon -->
        <svg class="w-4 h-4 transition-transform group-hover:translate-x-1"
             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
             stroke-linecap="round" stroke-linejoin="round">

            <path d="M5 12h14"></path>
            <path d="M13 5l7 7-7 7"></path>
        </svg>
    </a>
</div>
  @if($resource->materials->where('pdf_path', '!=', null)->count() > 0)
  <div class="flex justify-start sm:justify-end">
    <a href="{{ route('resources.downloadAll', $resource->slug) }}"
       class="group inline-flex items-center gap-2 px-4 py-2.5 rounded-xl
              bg-forest-800 border border-forest-700/50 text-forest-300 text-sm font-semibold
              hover:bg-forest-700 transition-all w-full sm:w-auto justify-center sm:justify-end">

        <!-- Download Icon -->
        <svg class="w-4 h-4 transition-transform group-hover:translate-y-0.5"
             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
             stroke-linecap="round" stroke-linejoin="round">

            <path d="M12 3v12"></path>
            <path d="M7 10l5 5 5-5"></path>
            <path d="M5 21h14"></path>
        </svg>

        <span>Download All PDFs</span>
    </a>
</div>
  @endif
</div>

{{-- Materials List --}}
@if($resource->materials->isEmpty())
<div class="app-card text-center py-14 fade-up">
  <p class="text-ink-400 font-medium">No materials available for this course yet.</p>
</div>
@else
<div class="space-y-2">
  @foreach($resource->materials as $i => $material)
  <div class="app-card flex items-center gap-4 fade-up" style="animation-delay:{{ $i * .04 }}s">
    <div class="w-10 h-10 rounded-xl flex-shrink-0 flex items-center justify-center
                {{ $material->pdf_path ? 'bg-forest-950 border border-forest-900' : 'bg-sky-950/40 border border-sky-900/50' }}">
      @if($material->pdf_path)
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#4ade80" stroke-width="1.8">
        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m.75 12l3 3m0 0l3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
      </svg>
      @else
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#38bdf8" stroke-width="1.8">
        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
      </svg>
      @endif
    </div>

    <div class="flex-1 min-w-0">
      <p class="text-sm font-semibold text-ink-100">{{ $material->title }}</p>
      <p class="text-xs text-ink-600 mt-0.5">
        {{ $material->created_at?->format('M j, Y') }}
      </p>
    </div>

    <div class="flex items-center gap-2 flex-shrink-0">
      <a href="{{ route('lesson.material', ['resource' => $resource->slug, 'material' => $material->slug]) }}"
         class="px-3 py-1.5 rounded-lg bg-forest-950 border border-forest-900 text-forest-400
                text-xs font-semibold hover:bg-forest-900 transition-colors">
        Read
      </a>
      <a href="{{ route('materials.quiz', $material->id) }}"
         class="px-3 py-1.5 rounded-lg bg-violet-950/30 border border-violet-900/30
                text-violet-400 text-xs font-semibold hover:bg-violet-950/50 transition-colors">
        Quiz
      </a>
      @if($material->pdf_path)
      <a href="{{ route('material.download', ['resource' => $resource->slug, 'material' => $material->slug]) }}"
         class="w-8 h-8 flex items-center justify-center rounded-lg bg-ink-800 border border-ink-700
                text-ink-500 hover:border-forest-800 hover:text-forest-400 transition-colors">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <path d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
        </svg>
      </a>
      @endif
    </div>
  </div>
  @endforeach
</div>
@endif

@endsection