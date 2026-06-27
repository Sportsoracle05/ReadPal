{{-- admin/lectures/index.blade.php --}}
@extends('layouts.admin')
@section('title','Lectures')
@section('page_title','Lecture Management')
@section('page_sub','Rep-only · Class timetable & live alerts')

@section('content')

<div class="flex items-center justify-between mb-5 fu">
  <div>
    <h2 class="font-display text-xl font-bold text-white">Lectures</h2>
    <p class="text-xs text-ink-600 mt-0.5">{{ $lectures->count() }} scheduled lectures</p>
  </div>
  <a href="{{ route('admin.lectures.create') }}" class="btn-primary btn-sm">
    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
      <path d="M12 4.5v15m7.5-7.5h-15"/>
    </svg> Add Lecture
  </a>
</div>

<div class="a-card fu1 overflow-x-auto p-0">
  <table class="w-full">
    <thead>
      <tr class="border-b border-ink-800">
        <th class="tbl-head text-left">Course</th>
        <th class="tbl-head text-left">Lecturer</th>
        <th class="tbl-head text-left">Hall</th>
        <th class="tbl-head text-left">Start Time</th>
        <th class="tbl-head text-center">Status</th>
        <th class="tbl-head text-right">Actions</th>
      </tr>
    </thead>
    <tbody>
      @forelse($lectures as $lecture)
      @php
        $isOngoing = $lecture->is_ongoing ?? false;
        $isPast    = !$isOngoing && ($lecture->start_time?->isPast() ?? false);
      @endphp
      <tr class="tbl-row">
        <td class="tbl-cell font-medium text-ink-200">
          <span class="rp-badge badge-green">{{ $lecture->resource->course_code ?? '—' }}</span>
          <p class="text-xs text-ink-600 mt-0.5">{{ $lecture->resource->name ?? '' }}</p>
        </td>
        <td class="tbl-cell">{{ $lecture->lecturer ?? '—' }}</td>
        <td class="tbl-cell">{{ $lecture->hall ?? '—' }}</td>
        <td class="tbl-cell text-xs font-mono">
          {{ $lecture->start_time?->format('D, M j · g:i A') ?? '—' }}
        </td>
        <td class="tbl-cell text-center">
          @if($isOngoing)
          <span class="rp-badge badge-green">Live</span>
          @elseif($isPast)
          <span class="rp-badge" style="background:#1e293b;border:1px solid #334155;color:#475569;">Ended</span>
          @else
          <span class="rp-badge badge-blue">Upcoming</span>
          @endif
        </td>
        <td class="tbl-cell text-right">
          <div class="flex items-center justify-end gap-1.5">
            <a href="{{ route('admin.lectures.edit', $lecture->id) }}" class="btn-outline btn-sm !px-2 !py-1 text-xs">Edit</a>
            <form method="POST" action="{{ route('admin.lectures.destroy', $lecture->id) }}"
                  onsubmit="return confirm('Delete this lecture?')">
              @csrf @method('DELETE')
              <button type="submit" class="btn-danger btn-sm !px-2 !py-1 text-xs">Del</button>
            </form>
          </div>
        </td>
      </tr>
      @empty
      <tr><td colspan="6" class="tbl-cell text-center text-ink-700 py-10">No lectures scheduled yet.</td></tr>
      @endforelse
    </tbody>
  </table>
  @if($lectures->hasPages())
  <div class="flex items-center justify-between px-4 py-3 border-t border-ink-800">
    <p class="text-xs text-ink-600">{{ $lectures->firstItem() }}–{{ $lectures->lastItem() }} of {{ $lectures->total() }}</p>
    <div class="flex gap-1">
      @if(!$lectures->onFirstPage())<a href="{{ $lectures->previousPageUrl() }}" class="btn-outline btn-sm">← Prev</a>@endif
      @if($lectures->hasMorePages())<a href="{{ $lectures->nextPageUrl() }}" class="btn-outline btn-sm">Next →</a>@endif
    </div>
  </div>
  @endif
</div>

@endsection