@extends('layouts.admin')
@section('title', isset($rep) ? 'Edit Rep' : 'Assign Rep')
@section('page_title', isset($rep) ? 'Edit Rep' : 'Assign Class Rep')
@section('page_sub', 'Super Admin')

@section('content')
<div class="max-w-md mx-auto">
  <nav class="flex items-center gap-2 text-xs text-ink-700 mb-5">
    <a href="{{ route('admin.reps.index') }}" class="hover:text-ink-400">Reps</a>
    <span>›</span>
    <span class="text-ink-400">{{ isset($rep) ? 'Edit' : 'Assign' }}</span>
  </nav>
  <div class="a-card">
    <h2 class="font-display text-lg font-bold text-white mb-5">
      {{ isset($rep) ? 'Edit Rep' : 'Assign Class Rep' }}
    </h2>
    <form method="POST"
          action="{{ isset($rep) ? route('admin.reps.update', $rep->id) : route('admin.reps.store') }}">
      @csrf
      @if(isset($rep)) @method('PUT') @endif

      <div class="mb-4">
        <label class="form-label">Student / User</label>
        <select name="user_id" required class="form-input cursor-pointer">
          <option value="" disabled {{ !isset($rep) ? 'selected' : '' }}>Select user…</option>
          @foreach($users ?? [] as $u)
          <option value="{{ $u->id }}"
                  {{ old('user_id', $rep->user_id ?? '') == $u->id ? 'selected' : '' }}>
            {{ $u->firstname }} {{ $u->lastname ?? '' }} – {{ $u->email }}
          </option>
          @endforeach
        </select>
        @error('user_id')<p class="form-error">{{ $message }}</p>@enderror
      </div>

      <div class="mb-5">
        <label class="form-label">Year Level</label>
        <select name="year_level" class="form-input cursor-pointer">
          @foreach([100,200,300,400] as $lvl)
          <option value="{{ $lvl }}"
                  {{ old('year_level', $rep->year_level ?? 300) == $lvl ? 'selected' : '' }}>
            {{ $lvl }}L
          </option>
          @endforeach
        </select>
      </div>

      <div class="flex gap-3">
        <button type="submit" class="btn-primary">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <path d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          {{ isset($rep) ? 'Save Changes' : 'Assign Rep' }}
        </button>
        <a href="{{ route('admin.reps.index') }}" class="btn-outline">Cancel</a>
      </div>
    </form>
  </div>
</div>
@endsection