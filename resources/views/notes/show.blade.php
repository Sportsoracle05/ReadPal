{{-- ============================================================
  resources/views/notes/show.blade.php
  ============================================================ --}}
@extends('layouts.app')
@section('title', $note->title)
@section('page_title', $note->title)
@section('page_sub', 'Note · ' . $note->created_at->format('M j, Y'))

@section('content')

<div class="max-w-3xl mx-auto">

  <nav class="flex items-center gap-2 text-xs text-ink-600 mb-5 fade-up">
    <a href="{{ route('notes.index', ['firstname' => Auth::user()->firstname]) }}"
       class="hover:text-ink-300 transition-colors">My Notes</a>
    <span>›</span>
    <span class="text-ink-300 truncate max-w-[240px]">{{ $note->title }}</span>
  </nav>

  <div class="app-card fade-up-d1">
    {{-- Header --}}
    <div class="flex items-start justify-between gap-4 mb-5 pb-5 border-b border-ink-800">
      <div class="flex-1 min-w-0">
        <h1 class="font-display text-xl sm:text-2xl font-bold text-white leading-tight">
          {{ $note->title }}
        </h1>
        <p class="text-xs text-ink-500 mt-1.5">
          Created {{ $note->created_at->format('D, M j, Y · g:i A') }}
          @if($note->updated_at && $note->updated_at->ne($note->created_at))
            · Edited {{ $note->updated_at->diffForHumans() }}
          @endif
        </p>
      </div>
      <div class="flex items-center gap-2 flex-shrink-0">
        <a href="{{ route('notes.edit', ['firstname' => Auth::user()->firstname, 'note' => $note->id]) }}"
           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-950/30
                  border border-amber-900/30 text-amber-400 text-xs font-semibold
                  hover:bg-amber-950/50 transition-colors">
          <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <path d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487z"/>
          </svg>
          Edit
        </a>
        <form method="POST"
              action="{{ route('notes.destroy', ['firstname' => Auth::user()->firstname, 'note' => $note->id]) }}"
              onsubmit="return confirm('Delete this note permanently?')">
          @csrf @method('DELETE')
          <button type="submit"
                  class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-ink-800
                         border border-ink-700 text-ink-500 text-xs font-semibold
                         hover:border-red-900 hover:text-red-400 transition-colors">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <path d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
            </svg>
            Delete
          </button>
        </form>
      </div>
    </div>

    {{-- Content --}}
    <div class="text-sm text-ink-300 leading-[1.9] whitespace-pre-wrap font-body">{{ $note->content ?? $note->body }}</div>
  </div>

  {{-- Back link --}}
  <div class="mt-4 fade-up-d2">
    <a href="{{ route('notes.index', ['firstname' => Auth::user()->firstname]) }}"
       class="inline-flex items-center gap-1.5 text-xs text-ink-500 hover:text-forest-300 transition-colors">
      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
        <path d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
      </svg>
      Back to all notes
    </a>
  </div>
</div>

@endsection