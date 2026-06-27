@extends('layouts.app')
@section('title', 'Materials')
@section('page_title', 'Materials')
@section('page_sub', 'All course notes and resources')

@section('content')

{{-- Header + Filters --}}
<div class="flex flex-col sm:flex-row sm:items-center gap-3 mb-6 fade-up">
  <div>
    <h2 class="font-display text-2xl font-bold text-white">Course Materials</h2>
    <p class="text-sm text-ink-500 mt-0.5">
      {{ $materials->total() }} material{{ $materials->total() !== 1 ? 's' : '' }} available
    </p>
  </div>
</div>

{{-- Search & Filter Bar --}}
<form method="GET" action="{{ route('materials.index') }}"
      class="flex flex-col sm:flex-row gap-2 mb-5 fade-up-d1">
  <div class="relative flex-1">
    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-ink-600 pointer-events-none"
         fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
      <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
    </svg>
    <input type="text" name="search" placeholder="Search materials…"
           value="{{ request('search') }}"
           class="w-full bg-ink-900 border border-ink-700 rounded-xl pl-9 pr-4 py-2.5
                  text-sm text-ink-100 placeholder-ink-600
                  focus:outline-none focus:border-forest-700 focus:ring-2 focus:ring-forest-700/20 transition-all"/>
  </div>

  <select name="course"
          class="bg-ink-900 border border-ink-700 rounded-xl px-4 py-2.5 text-sm text-ink-300
                 focus:outline-none focus:border-forest-700 cursor-pointer min-w-[160px]"
          onchange="this.form.submit()">
    <option value="all">All Courses</option>
    @foreach($courses as $code)
    <option value="{{ $code }}" {{ request('course') === $code ? 'selected' : '' }}>
      {{ $code }}
    </option>
    @endforeach
  </select>

  <button type="submit"
          class="px-5 py-2.5 rounded-xl bg-forest-800 border border-forest-700/50 text-forest-300
                 text-sm font-semibold hover:bg-forest-700 transition-colors">
    Search
  </button>

  @if(request('search') || (request('course') && request('course') !== 'all'))
  <a href="{{ route('materials.index') }}"
     class="px-4 py-2.5 rounded-xl border border-ink-700 text-ink-400 text-sm
            hover:border-ink-600 hover:text-ink-300 transition-colors">
    Clear
  </a>
  @endif
</form>

{{-- Materials Grid --}}
@if($materials->isEmpty())
<div class="app-card text-center py-16 fade-up">
  <div class="w-14 h-14 rounded-2xl bg-ink-800 border border-ink-700 flex items-center
              justify-center mx-auto mb-4">
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#334155" stroke-width="1.5">
      <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
    </svg>
  </div>
  <p class="text-ink-400 font-medium">No materials found</p>
  <p class="text-xs text-ink-600 mt-1">Try adjusting your search or filter</p>
</div>
@else
<div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3 mb-5">
  @foreach($materials as $i => $material)
  <div class="app-card stat-card fade-up group" style="animation-delay:{{ ($i % 6) * .04 }}s">
    {{-- Header --}}
    <div class="flex items-start justify-between mb-3">
      <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0
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

      @if($material->resource)
      <span class="rp-badge badge-blue text-xs">{{ $material->resource->course_code }}</span>
      @endif
    </div>

    <h3 class="font-semibold text-ink-100 text-sm leading-snug mb-1 group-hover:text-white transition-colors line-clamp-2">
      {{ $material->title }}
    </h3>
    <p class="text-xs text-ink-600 mb-4">
      Added {{ $material->created_at?->diffForHumans() ?? '—' }}
    </p>

    {{-- Actions --}}
    <div class="flex items-center gap-2 mt-auto">
      <a href="{{ route('lesson.material', ['resource' => $material->resource->slug, 'material' => $material->slug]) }}"
         class="flex-1 flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg
                bg-forest-950 border border-forest-900 text-forest-400 text-xs font-semibold
                hover:bg-forest-900 transition-colors">
        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        Read
      </a>
      <a href="{{ route('materials.quiz', $material->id) }}"
         class="flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg
                bg-ink-800 border border-ink-700 text-ink-400 text-xs font-semibold
                hover:border-violet-800 hover:text-violet-400 transition-colors">
        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"/>
        </svg>
        Quiz
      </a>
      @if($material->pdf_path)
      <a href="{{ route('material.download', ['resource' => $material->resource->slug, 'material' => $material->slug]) }}"
         class="flex items-center justify-center w-8 h-8 rounded-lg bg-ink-800 border border-ink-700
                text-ink-500 hover:border-forest-800 hover:text-forest-400 transition-colors">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
        </svg>
      </a>
      @endif
    </div>
  </div>
  @endforeach
</div>

{{-- Pagination --}}
<div class="flex items-center justify-between">
  <p class="text-xs text-ink-600">
    Showing {{ $materials->firstItem() }}–{{ $materials->lastItem() }} of {{ $materials->total() }}
  </p>
  <div class="flex items-center gap-1">
    @if($materials->onFirstPage())
    <span class="px-3 py-1.5 rounded-lg bg-ink-900 border border-ink-800 text-ink-700 text-xs cursor-not-allowed">← Prev</span>
    @else
    <a href="{{ $materials->previousPageUrl() }}"
       class="px-3 py-1.5 rounded-lg bg-ink-900 border border-ink-700 text-ink-400 text-xs hover:border-forest-800 hover:text-forest-400 transition-colors">
      ← Prev
    </a>
    @endif

    @foreach($materials->getUrlRange(max(1,$materials->currentPage()-1), min($materials->lastPage(),$materials->currentPage()+1)) as $page => $url)
    <a href="{{ $url }}"
       class="w-8 h-8 rounded-lg border text-xs font-mono flex items-center justify-center transition-colors
              {{ $page == $materials->currentPage()
                  ? 'bg-forest-900 border-forest-800 text-forest-300'
                  : 'bg-ink-900 border-ink-700 text-ink-400 hover:border-forest-800 hover:text-forest-400' }}">
      {{ $page }}
    </a>
    @endforeach

    @if($materials->hasMorePages())
    <a href="{{ $materials->nextPageUrl() }}"
       class="px-3 py-1.5 rounded-lg bg-ink-900 border border-ink-700 text-ink-400 text-xs hover:border-forest-800 hover:text-forest-400 transition-colors">
      Next →
    </a>
    @else
    <span class="px-3 py-1.5 rounded-lg bg-ink-900 border border-ink-800 text-ink-700 text-xs cursor-not-allowed">Next →</span>
    @endif
  </div>
</div>
@endif

@endsection