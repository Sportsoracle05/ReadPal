{{-- ============================================================
  resources/views/profile/show.blade.php
  ============================================================ --}}
@extends('layouts.app')
@section('title', 'Profile')
@section('page_title', 'My Profile')
@section('page_sub', Auth::user()->email ?? '')

@section('content')

<div class="max-w-3xl mx-auto">

  {{-- Profile Header Card --}}
  <div class="app-card mb-4 fade-up">
    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-5">
      {{-- Avatar --}}
      <div class="w-16 h-16 rounded-2xl bg-forest-900 border-2 border-forest-800
                  flex items-center justify-center flex-shrink-0">
        <span class="font-display text-2xl font-bold text-forest-300">
          {{ strtoupper(substr($user->firstname ?? 'U', 0, 1)) }}
        </span>
      </div>

      <div class="flex-1 min-w-0">
        <div class="flex items-center gap-2 mt-2 flex-wrap">
        <h2 class="font-display text-2xl font-bold text-white">
          {{ $user->firstname }} {{ $user->lastname ?? '' }}
        </h2>
        @if(auth()->user()->is_premium)
            <span class="inline-flex items-center rounded-full bg-gradient-to-r from-forest-500 to-forest-600 px-2 py-0.5 text-[10px] uppercase tracking-wider text-white shadow-sm ring-1 ring-forest-400/30">
                {{-- Optional: Tiny Crown Icon --}}
                <svg class="mr-1 h-2.5 w-2.5 fill-current" viewBox="0 0 24 24">
                    <path d="M5 16L3 5L8.5 10L12 4L15.5 10L21 5L19 16H5M19 19C19 19.6 18.6 20 18 20H6C5.4 20 5 19.6 5 19V18H19V19Z" />
                </svg>
                Premium
            </span>
        @endif
        </div>
        <p class="text-sm text-ink-400 mt-0.5">{{ $user->email }}</p>
        <div class="flex items-center gap-2 mt-2 flex-wrap">
          <span class="rp-badge badge-green">300L · Sociology</span>
          <span class="rp-badge badge-blue">AAUA</span>
          @if($user->matric_number)
          <span class="rp-badge" style="background:#1e293b;border:1px solid #334155;color:#94a3b8;">
            {{ $user->matric_number }}
          </span>
          @endif
        </div>
      </div>

      <a href="{{ route('profile.edit', $user) }}"
         class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-ink-800
                border border-ink-700 text-ink-300 text-sm font-semibold
                hover:border-forest-800 hover:text-forest-300 transition-colors self-start">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <path d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487z"/>
        </svg>
        Edit Profile
      </a>
    </div>
  </div>

  {{-- Info Grid --}}
  <div class="grid sm:grid-cols-2 gap-4 mb-4">
    <div class="app-card fade-up-d1">
      <p class="text-xs font-semibold text-ink-600 uppercase tracking-wider mb-3">Account Info</p>
      @foreach([
        ['Full Name',   ($user->firstname ?? '') . ' ' . ($user->lastname ?? '')],
        ['Email',       $user->email],
        ['Matric No.',  $user->matric_number ?? '—'],
        ['Level',       '300 Level'],
        ['Department',  'Sociology'],
        ['Institution', 'AAUA'],
      ] as [$label, $value])
      <div class="flex py-2 border-b border-ink-800/60 last:border-0">
        <span class="w-28 text-xs text-ink-600 flex-shrink-0">{{ $label }}</span>
        <span class="text-xs text-ink-200 font-medium">{{ $value }}</span>
      </div>
      @endforeach
    </div>

    <div class="app-card fade-up-d2">
      <p class="text-xs font-semibold text-ink-600 uppercase tracking-wider mb-3">Activity</p>
      @php
        $noteCount = Auth::user()->notes?->count() ?? 0;
      @endphp
      @foreach([
        ['Member since',    $user->created_at->format('M j, Y')],
        ['Notes created',   $noteCount],
        ['Last login',      now()->format('M j, Y')],
      ] as [$label, $value])
      <div class="flex items-center justify-between py-2.5 border-b border-ink-800/60 last:border-0">
        <span class="text-xs text-ink-500">{{ $label }}</span>
        <span class="text-xs font-mono text-ink-200">{{ $value }}</span>
      </div>
      @endforeach

      <div class="mt-3 pt-3 border-t border-ink-800">
        <a href="{{ route('notes.index', ['firstname' => $user->firstname]) }}"
           class="text-xs text-forest-500 hover:text-forest-300 transition-colors">
          View my notes →
        </a>
      </div>
    </div>
  </div>

  {{-- Danger Zone --}}
  <div class="app-card border-red-900/30 fade-up-d3">
    <p class="text-xs font-semibold text-red-700 uppercase tracking-wider mb-2">Danger Zone</p>
    <p class="text-xs text-ink-600 mb-3">Permanently delete your account and all associated data.</p>
    <button type="button"
            onclick="document.getElementById('delete-modal').classList.remove('hidden')"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-red-900/40
                   text-red-500 text-xs font-semibold hover:bg-red-950/30 transition-colors">
      Delete Account
    </button>
  </div>
</div>

{{-- Delete confirmation modal --}}
<div id="delete-modal"
     class="hidden fixed inset-0 z-50 flex items-center justify-center bg-ink-950/80 backdrop-blur-sm p-4"
     onclick="if(event.target===this)this.classList.add('hidden')">
  <div class="w-full max-w-sm app-card border-red-900/40">
    <h3 class="font-display text-lg font-bold text-white mb-1">Delete Account?</h3>
    <p class="text-sm text-ink-400 mb-4">This will permanently remove all your data. This cannot be undone.</p>
    <div class="flex gap-3">
      <button onclick="document.getElementById('delete-modal').classList.add('hidden')"
              class="flex-1 px-4 py-2.5 rounded-xl border border-ink-700 text-ink-400 text-sm hover:bg-ink-800 transition-colors">
        Cancel
      </button>
      <form method="POST" action="{{ route('profile.destroy', $user) ?? '#' }}" class="flex-1">
        @csrf @method('DELETE')
        <button type="submit"
                class="w-full px-4 py-2.5 rounded-xl bg-red-900/40 border border-red-800/50
                       text-red-300 text-sm font-semibold hover:bg-red-900/60 transition-colors">
          Yes, Delete
        </button>
      </form>
    </div>
  </div>
</div>

@endsection