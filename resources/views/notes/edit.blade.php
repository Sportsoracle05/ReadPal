@extends('layouts.app')
@section('title', isset($note) ? 'Edit Note' : 'New Note')
@section('page_title', isset($note) ? 'Edit Note' : 'Create Note')
@section('page_sub', 'Personal study notes')

@section('content')

<div class="max-w-3xl mx-auto">

  {{-- Breadcrumb --}}
  <nav class="flex items-center gap-2 text-xs text-ink-600 mb-5 fade-up">
    <a href="{{ route('notes.index', ['firstname' => Auth::user()->firstname]) }}"
       class="hover:text-ink-300 transition-colors">My Notes</a>
    <span>›</span>
    <span class="text-ink-300">{{ isset($note) ? 'Edit: ' . Str::limit($note->title, 40) : 'New Note' }}</span>
  </nav>

  <div class="app-card fade-up-d1">
    <h2 class="font-display text-xl font-bold text-white mb-5">
      {{ isset($note) ? 'Edit Note' : 'New Note' }}
    </h2>

    <form method="POST"
          action="{{ isset($note)
            ? route('notes.update', ['firstname' => Auth::user()->firstname, 'note' => $note->id])
            : route('notes.store', ['firstname' => Auth::user()->firstname]) }}">
      @csrf
      @if(isset($note)) @method('PUT') @endif

      {{-- Title --}}
      <div class="mb-4">
        <label class="block text-xs font-semibold text-ink-500 uppercase tracking-widest mb-1.5">
          Note Title
        </label>
        <input type="text" name="title" required
               value="{{ old('title', $note->title ?? '') }}"
               placeholder="e.g. SOC 303 – Week 4 key points"
               class="w-full bg-ink-800 border border-ink-700 rounded-xl px-4 py-3
                      text-sm text-ink-100 placeholder-ink-600
                      focus:outline-none focus:border-forest-700 focus:ring-2 focus:ring-forest-700/20 transition-all"/>
        @error('title')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
      </div>

      {{-- Content --}}
      <div class="mb-5">
        <div class="flex items-center justify-between mb-1.5">
          <label class="text-xs font-semibold text-ink-500 uppercase tracking-widest">Content</label>
          <span id="char-count" class="text-xs text-ink-600 font-mono">0 chars</span>
        </div>
        <textarea name="{{ isset($note) && isset($note->body) ? 'body' : 'content' }}"
                  id="note-content"
                  rows="14"
                  required
                  placeholder="Write your notes here… summarize key points, theories, important names."
                  oninput="document.getElementById('char-count').textContent=this.value.length+' chars'"
                  class="w-full bg-ink-800 border border-ink-700 rounded-xl px-4 py-3
                         text-sm text-ink-200 placeholder-ink-600 leading-relaxed resize-y
                         focus:outline-none focus:border-forest-700 focus:ring-2 focus:ring-forest-700/20 transition-all
                         font-body">{{ old('content', $note->content ?? $note->body ?? '') }}</textarea>
        @error('content')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
        @error('body')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
      </div>

      {{-- Formatting hint --}}
      <div class="flex items-start gap-2 px-3 py-2.5 rounded-xl bg-ink-800/50 border border-ink-700/50 mb-5">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#4ade80" stroke-width="2" class="mt-0.5 flex-shrink-0">
          <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
        </svg>
        <p class="text-xs text-ink-500 leading-relaxed">
          Use plain text. Tip: structure with headings like "KEY THEORIES:", numbered points, and bullet marks (–) for easy review.
        </p>
      </div>

      {{-- Actions --}}
      <div class="flex items-center gap-3">
        <button type="submit"
                class="flex items-center gap-2 px-6 py-2.5 rounded-xl bg-forest-800
                       border border-forest-700/50 text-forest-300 text-sm font-bold
                       hover:bg-forest-700 transition-colors">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <path d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          {{ isset($note) ? 'Save Changes' : 'Save Note' }}
        </button>
        <a href="{{ route('notes.index', ['firstname' => Auth::user()->firstname]) }}"
           class="px-5 py-2.5 rounded-xl border border-ink-700 text-ink-400 text-sm
                  hover:border-ink-600 hover:text-ink-300 transition-colors">
          Cancel
        </a>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
  const ta = document.getElementById('note-content');
  if (ta) {
    document.getElementById('char-count').textContent = ta.value.length + ' chars';
  }
</script>
@endpush

@endsection