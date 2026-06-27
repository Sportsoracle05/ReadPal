@extends('layouts.admin')
@section('title', isset($user) ? 'Edit User' : 'Add User')
@section('page_title', isset($user) ? 'Edit User' : 'Add User')
@section('page_sub', 'Super Admin · User Management')

@section('content')
<div class="max-w-md mx-auto">
  <nav class="flex items-center gap-2 text-xs text-ink-700 mb-5">
    <a href="{{ route('admin.users.index') }}" class="hover:text-ink-400 transition-colors">Users</a>
    <span>›</span>
    <span class="text-ink-400">{{ isset($user) ? 'Edit' : 'Add' }}</span>
  </nav>

  <div class="a-card">
    <h2 class="font-display text-lg font-bold text-white mb-5">
      {{ isset($user) ? 'Edit User' : 'Add New User' }}
    </h2>
    <form method="POST"
          action="{{ isset($user) ? route('admin.users.update', $user->id) : route('admin.users.store') }}">
      @csrf
      @if(isset($user)) @method('PUT') @endif

      <div class="grid grid-cols-2 gap-3 mb-4">
        <div>
          <label class="form-label">First Name</label>
          <input type="text" name="firstname" required
                 value="{{ old('firstname', $user->firstname ?? '') }}"
                 class="form-input"/>
          @error('firstname')<p class="form-error">{{ $message }}</p>@enderror
        </div>
        <div>
          <label class="form-label">Last Name</label>
          <input type="text" name="lastname"
                 value="{{ old('lastname', $user->lastname ?? '') }}"
                 class="form-input"/>
        </div>
      </div>

      <div class="mb-4">
        <label class="form-label">Email</label>
        <input type="email" name="email" required
               value="{{ old('email', $user->email ?? '') }}"
               class="form-input"/>
        @error('email')<p class="form-error">{{ $message }}</p>@enderror
      </div>

      <div class="mb-4">
        <label class="form-label">Matric Number</label>
        <input type="text" name="matric_number"
               value="{{ old('matric_number', $user->matric_number ?? '') }}"
               class="form-input font-mono text-xs"/>
      </div>

      <div class="mb-4">
        <label class="form-label">Role</label>
        <select name="role" class="form-input cursor-pointer">
          @foreach(['student','admin','rep','super'] as $r)
          <option value="{{ $r }}" {{ old('role', $user->role ?? 'student') === $r ? 'selected' : '' }}>
            {{ ucfirst($r) }}{{ $r==='super' ? ' Admin':'' }}
          </option>
          @endforeach
        </select>
      </div>

      <div class="mb-5">
        <label class="form-label">Password {{ isset($user) ? '(leave blank to keep)' : '' }}</label>
        <input type="password" name="password" {{ !isset($user) ? 'required' : '' }}
               placeholder="{{ isset($user) ? '••••••••' : 'Min. 8 characters' }}"
               class="form-input"/>
        @error('password')<p class="form-error">{{ $message }}</p>@enderror
      </div>

      <div class="flex gap-3">
        <button type="submit" class="btn-primary">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <path d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          {{ isset($user) ? 'Save Changes' : 'Create User' }}
        </button>
        <a href="{{ route('admin.users.index') }}" class="btn-outline">Cancel</a>
      </div>
    </form>
  </div>
</div>
@endsection