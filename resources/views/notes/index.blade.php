{{--
  NOTES INDEX — resources/views/notes/index.blade.php
--}}
@extends('layouts.app')
@section('title', 'My Notes')
@section('page_title', 'My Notes')
@section('page_sub', 'Personal study notes')

@section('content')

<div class="flex items-end justify-between mb-6 fade-up">
  <div>
    <h2 class="font-display text-2xl font-bold text-white">My Notes</h2>
    <p class="text-sm text-ink-500 mt-0.5">{{ $notes->count() }} note{{ $notes->count() !== 1 ? 's' : '' }} saved</p>
  </div>
  <a href="{{ route('notes.create', ['firstname' => Auth::user()->firstname]) }}"
     class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-forest-800
            border border-forest-700/50 text-forest-300 text-sm font-semibold
            hover:bg-forest-700 transition-colors">
    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
      <path d="M12 4.5v15m7.5-7.5h-15"/>
    </svg>
    New Note
  </a>
</div>

@if($notes->isEmpty())
<div class="app-card text-center py-16 fade-up">
  <div class="w-14 h-14 rounded-2xl bg-amber-950/30 border border-amber-900/30 flex items-center justify-center mx-auto mb-4">
    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#fbbf24" stroke-width="1.5">
      <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/>
    </svg>
  </div>
  <p class="text-ink-400 font-medium">No notes yet</p>
  <p class="text-xs text-ink-600 mt-1 mb-4">Start capturing key insights from your lessons.</p>
  <a href="{{ route('notes.create', ['firstname' => Auth::user()->firstname]) }}"
     class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-forest-800 border border-forest-700/50
            text-forest-300 text-sm font-semibold hover:bg-forest-700 transition-colors">
    Create your first note
  </a>
</div>
@else
<div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3">
  @foreach($notes as $i => $note)
  <div class="app-card stat-card fade-up group" style="animation-delay:{{ ($i % 6) * .04 }}s">
    <div class="flex items-start justify-between mb-3">
      <div class="w-8 h-8 rounded-lg bg-amber-950/30 border border-amber-900/30
                  flex items-center justify-center flex-shrink-0">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#fbbf24" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/>
        </svg>
      </div>
      <span class="text-xs text-ink-600 font-mono">{{ $note->created_at->format('M j') }}</span>
    </div>

    <h3 class="font-semibold text-sm text-ink-100 group-hover:text-white transition-colors mb-1 line-clamp-2">
      {{ $note->title }}
    </h3>
    <p class="text-xs text-ink-600 line-clamp-3 leading-relaxed mb-4">
      {{ Str::limit(strip_tags($note->content ?? $note->body ?? ''), 100) }}
    </p>

    <div class="flex items-center gap-2 border-t border-ink-800/60 pt-3">
      <a href="{{ route('notes.show', ['firstname' => Auth::user()->firstname, 'note' => $note->id]) }}"
         class="flex-1 text-center px-3 py-1.5 rounded-lg bg-ink-800 border border-ink-700
                text-xs text-ink-300 hover:border-forest-800 hover:text-forest-400 transition-colors">
        View
      </a>
      <a href="{{ route('notes.edit', ['firstname' => Auth::user()->firstname, 'note' => $note->id]) }}"
         class="flex-1 text-center px-3 py-1.5 rounded-lg bg-ink-800 border border-ink-700
                text-xs text-ink-300 hover:border-amber-800 hover:text-amber-400 transition-colors">
        Edit
      </a>
      <form method="POST"
            action="{{ route('notes.destroy', ['firstname' => Auth::user()->firstname, 'note' => $note->id]) }}"
            onsubmit="return confirm('Delete this note? This cannot be undone.')">
        @csrf @method('DELETE')
        <button type="submit"
                class="w-8 h-8 flex items-center justify-center rounded-lg bg-ink-800 border border-ink-700
                       text-ink-600 hover:border-red-900 hover:text-red-400 transition-colors">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
          </svg>
        </button>
      </form>
    </div>
  </div>
  @endforeach
</div>
@endif

@endsection