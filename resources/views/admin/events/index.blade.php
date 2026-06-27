{{-- admin/events/index.blade.php --}}
@extends('layouts.admin')
@section('title','Events')
@section('page_title','Events')
@section('page_sub','Manage academic events & Google Calendar')

@section('content')

<div class="flex items-center justify-between mb-5 fu">
  <div>
    <h2 class="font-display text-xl font-bold text-white">Events</h2>
    <p class="text-xs text-ink-600 mt-0.5">{{ $events->count() }} events</p>
  </div>
  <div class="flex items-center gap-2">
    <a href="{{ route('admin.google.auth') }}"
       class="btn-outline btn-sm">
      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="12" cy="12" r="10"/><path d="M12 8v8m-4-4h8"/>
      </svg>
      Sync Google
    </a>
    <a href="{{ route('admin.events.create') }}" class="btn-primary btn-sm">
      <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 4.5v15m7.5-7.5h-15"/></svg>
      New Event
    </a>
  </div>
</div>

<div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3 fu1">
  @forelse($events as $i => $event)
  @php
    $isPast = $event->start_date?->isPast() ?? false;
    $isToday = $event->start_date?->isToday() ?? false;
  @endphp
  <div class="a-card" style="animation-delay:{{ $i*.04 }}s">
    <div class="flex items-start justify-between mb-3">
      <div class="w-10 h-10 rounded-xl bg-ink-800 border border-ink-700 flex flex-col items-center justify-center flex-shrink-0 text-center">
        <p class="font-mono text-xs font-bold text-forest-400 leading-none">
          {{ $event->start_date?->format('M') ?? '—' }}
        </p>
        <p class="font-display text-base font-bold text-white leading-none">
          {{ $event->start_date?->format('d') ?? '—' }}
        </p>
      </div>
      @if($isToday)
      <span class="rp-badge badge-green">Today</span>
      @elseif($isPast)
      <span class="rp-badge" style="background:#1e293b;border:1px solid #334155;color:#475569;">Past</span>
      @else
      <span class="rp-badge badge-blue">Upcoming</span>
      @endif
    </div>

    <h3 class="font-semibold text-ink-100 text-sm leading-snug mb-1">{{ $event->title }}</h3>
    @if($event->description)
    <p class="text-xs text-ink-600 line-clamp-2 mb-3">{{ $event->description }}</p>
    @endif

    <p class="text-xs font-mono text-ink-500 mb-3">
      {{ $event->start_date?->format('D, M j, Y') }}
      @if($event->start_time) · {{ $event->start_time }} @endif
    </p>

    <div class="flex gap-2 border-t border-ink-800/60 pt-3">
      <a href="{{ route('admin.events.edit', $event->id) }}"
         class="btn-outline btn-sm flex-1 justify-center text-xs">Edit</a>
      <form method="POST" action="{{ route('admin.events.destroy', $event->id) }}"
            onsubmit="return confirm('Delete event?')">
        @csrf @method('DELETE')
        <button type="submit" class="btn-danger btn-sm !px-3 text-xs">Del</button>
      </form>
    </div>
  </div>
  @empty
  <div class="a-card text-center py-10 col-span-full">
    <p class="text-ink-600">No events created yet.</p>
  </div>
  @endforelse
</div>

@endsection