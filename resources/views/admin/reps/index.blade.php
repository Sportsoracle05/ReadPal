@extends('layouts.admin')
@section('title','Reps')
@section('page_title','Class Representatives')
@section('page_sub','Super Admin · Rep management')

@section('content')

<div class="flex items-center justify-between mb-5 fu">
  <div>
    <h2 class="font-display text-xl font-bold text-white">Reps</h2>
    <p class="text-xs text-ink-600 mt-0.5">{{ $reps->count() }} class representatives</p>
  </div>
  <a href="{{ route('admin.reps.create') }}" class="btn-primary btn-sm">
    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 4.5v15m7.5-7.5h-15"/></svg>
    Assign Rep
  </a>
</div>

<div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3 fu1">
  @forelse($reps as $i => $rep)
  <div class="a-card" style="animation-delay:{{ $i*.04 }}s">
    <div class="flex items-center gap-3 mb-3">
      <div class="w-10 h-10 rounded-full bg-sky-950/40 border border-sky-900/40
                  flex items-center justify-center text-sky-400 font-bold font-display flex-shrink-0">
        {{ strtoupper(substr($rep->user->firstname ?? 'R', 0, 1)) }}
      </div>
      <div class="min-w-0">
        <p class="text-sm font-semibold text-ink-100 truncate">
          {{ $rep->user->firstname ?? '—' }} {{ $rep->user->lastname ?? '' }}
        </p>
        <p class="text-xs text-ink-600 truncate">{{ $rep->user->email ?? '' }}</p>
      </div>
    </div>
    <div class="flex items-center gap-2 mb-3">
      <span class="role-chip chip-rep">Class Rep</span>
      @if($rep->year_level)
      <span class="rp-badge badge-green">{{ $rep->year_level }}L</span>
      @endif
    </div>
    <div class="flex gap-2 border-t border-ink-800/60 pt-3">
      <a href="{{ route('admin.reps.edit', $rep->id) }}"
         class="btn-outline btn-sm flex-1 justify-center text-xs">Edit</a>
      <form method="POST" action="{{ route('admin.reps.destroy', $rep->id) }}"
            onsubmit="return confirm('Remove rep role?')">
        @csrf @method('DELETE')
        <button type="submit" class="btn-danger btn-sm !px-3 text-xs">Remove</button>
      </form>
    </div>
  </div>
  @empty
  <div class="a-card text-center py-10 col-span-full">
    <p class="text-ink-600">No reps assigned yet.</p>
  </div>
  @endforelse
</div>

@endsection