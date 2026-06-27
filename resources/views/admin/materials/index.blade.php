{{-- ============================================================
  admin/materials/index.blade.php
  ============================================================ --}}
@extends('layouts.admin')
@section('title','Materials')
@section('page_title','Materials')
@section('page_sub','Manage all course materials')

@section('content')

<div class="flex flex-col sm:flex-row sm:items-center gap-3 mb-5 fu">
  <div class="flex-1">
    <h2 class="font-display text-xl font-bold text-white">Course Materials</h2>
    <p class="text-xs text-ink-600 mt-0.5">{{ $materials->total() }} total materials</p>
  </div>
  <a href="{{ route('admin.materials.create') }}" class="btn-primary btn-sm self-start sm:self-auto">
    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 4.5v15m7.5-7.5h-15"/></svg>
    Upload Material
  </a>
</div>

{{-- Search --}}
<form method="GET" class="flex gap-2 mb-4 fu1">
  <div class="relative flex-1 max-w-sm">
    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-ink-600 pointer-events-none"
         fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
      <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
    </svg>
    <input type="text" name="search" placeholder="Search materials…"
           value="{{ request('search') }}"
           class="form-input pl-9 py-2 text-xs"/>
  </div>
  <button type="submit" class="btn-outline btn-sm">Search</button>
  @if(request('search'))
  <a href="{{ route('admin.materials.index') }}" class="btn-outline btn-sm">Clear</a>
  @endif
</form>

<div class="a-card fu2 overflow-x-auto p-0">
  <table class="w-full">
    <thead>
      <tr class="border-b border-ink-800">
        <th class="tbl-head text-left">Title</th>
        <th class="tbl-head text-left">Course</th>
        <th class="tbl-head text-center">PDF</th>
        <th class="tbl-head text-center">Questions</th>
        <th class="tbl-head text-right">Added</th>
        <th class="tbl-head text-right">Actions</th>
      </tr>
    </thead>
    <tbody>
      @forelse($materials as $mat)
      <tr class="tbl-row">
        <td class="tbl-cell font-medium text-ink-200 max-w-xs">
          <p class="truncate">{{ $mat->title }}</p>
          <p class="text-xs text-ink-600 truncate mt-0.5">{{ $mat->slug }}</p>
        </td>
        <td class="tbl-cell">
          <span class="rp-badge badge-green">{{ $mat->resource->course_code ?? '—' }}</span>
        </td>
        <td class="tbl-cell text-center">
          @if($mat->pdf_path)
            <span class="rp-badge badge-blue">PDF ✓</span>
          @else
            <span class="text-ink-700 text-xs">—</span>
          @endif
        </td>
        <td class="tbl-cell text-center">
          @if($mat->has_questions ?? false)
            <span class="rp-badge badge-violet">Ready</span>
          @else
            <a href="{{ route('admin.materials.generate', $mat->id) }}"
               class="rp-badge badge-amber cursor-pointer hover:opacity-80">Generate</a>
          @endif
        </td>
        <td class="tbl-cell text-right text-xs font-mono text-ink-600">
          {{ $mat->created_at?->format('M j, Y') }}
        </td>
        <td class="tbl-cell text-right">
          <div class="flex items-center justify-end gap-1.5">
            <a href="{{ route('admin.materials.extract', $mat->id) }}" onclick="event.preventDefault();document.getElementById('extract-{{ $mat->id }}').submit();"
               class="btn-outline btn-sm !px-2 !py-1 text-xs">Extract</a>
            <form id="extract-{{ $mat->id }}" method="POST" action="{{ route('admin.materials.extract', $mat->id) }}" class="hidden">@csrf</form>

            <a href="{{ route('admin.materials.edit', $mat->slug) }}" class="btn-outline btn-sm !px-2 !py-1 text-xs">Edit</a>

            <form method="POST" action="{{ route('admin.materials.destroy', $mat->slug) }}"
                  onsubmit="return confirm('Delete {{ addslashes($mat->title) }}?')">
              @csrf @method('DELETE')
              <button type="submit" class="btn-danger btn-sm !px-2 !py-1 text-xs">Del</button>
            </form>
          </div>
        </td>
      </tr>
      @empty
      <tr><td colspan="6" class="tbl-cell text-center text-ink-700 py-10">No materials uploaded yet.</td></tr>
      @endforelse
    </tbody>
  </table>

  @if($materials->hasPages())
  <div class="flex items-center justify-between px-4 py-3 border-t border-ink-800">
    <p class="text-xs text-ink-600">{{ $materials->firstItem() }}–{{ $materials->lastItem() }} of {{ $materials->total() }}</p>
    <div class="flex gap-1">
      @if(!$materials->onFirstPage())
      <a href="{{ $materials->previousPageUrl() }}" class="btn-outline btn-sm">← Prev</a>
      @endif
      @if($materials->hasMorePages())
      <a href="{{ $materials->nextPageUrl() }}" class="btn-outline btn-sm">Next →</a>
      @endif
    </div>
  </div>
  @endif
</div>

@endsection