@extends('layouts.app')
@section('title', 'Edit Profile')
@section('page_title', 'Edit Profile')
@section('page_sub', 'Update your account information')

@section('content')

<div class="max-w-2xl mx-auto">

  <nav class="flex items-center gap-2 text-xs text-ink-600 mb-5 fade-up">
    <a href="{{ route('profile.show', $user) }}" class="hover:text-ink-300 transition-colors">Profile</a>
    <span>›</span>
    <span class="text-ink-300">Edit</span>
  </nav>

  <div class="app-card fade-up-d1">
    <h2 class="font-display text-xl font-bold text-white mb-5">Update Profile</h2>

    <form method="POST" action="{{ route('profile.update', $user) }}">
      @csrf @method('PUT')

      <div class="grid sm:grid-cols-2 gap-4 mb-4">
        <div>
          <label class="block text-xs font-semibold text-ink-500 uppercase tracking-widest mb-1.5">
            First Name
          </label>
          <input type="text" name="firstname" required
                 value="{{ old('firstname', $user->firstname) }}"
                 class="w-full bg-ink-800 border border-ink-700 rounded-xl px-4 py-3
                        text-sm text-ink-100 placeholder-ink-600
                        focus:outline-none focus:border-forest-700 focus:ring-2 focus:ring-forest-700/20 transition-all"/>
          @error('firstname')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
          <label class="block text-xs font-semibold text-ink-500 uppercase tracking-widest mb-1.5">
            Last Name
          </label>
          <input type="text" name="lastname"
                 value="{{ old('lastname', $user->lastname ?? '') }}"
                 class="w-full bg-ink-800 border border-ink-700 rounded-xl px-4 py-3
                        text-sm text-ink-100 placeholder-ink-600
                        focus:outline-none focus:border-forest-700 focus:ring-2 focus:ring-forest-700/20 transition-all"/>
          @error('lastname')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
      </div>

      <div class="mb-4">
        <label class="block text-xs font-semibold text-ink-500 uppercase tracking-widest mb-1.5">
          Email Address
        </label>
        <input type="email" name="email" required
               value="{{ old('email', $user->email) }}"
               class="w-full bg-ink-800 border border-ink-700 rounded-xl px-4 py-3
                      text-sm text-ink-100
                      focus:outline-none focus:border-forest-700 focus:ring-2 focus:ring-forest-700/20 transition-all"/>
        @error('email')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
      </div>

      <div class="mb-4">
        <label class="block text-xs font-semibold text-ink-500 uppercase tracking-widest mb-1.5">
          Matric Number
        </label>
        <input type="text" name="matric_number"
               value="{{ old('matric_number', $user->matric_number ?? '') }}"
               placeholder="e.g. 18/30DF/SOC/1234"
               class="w-full bg-ink-800 border border-ink-700 rounded-xl px-4 py-3
                      text-sm text-ink-100 placeholder-ink-600
                      focus:outline-none focus:border-forest-700 focus:ring-2 focus:ring-forest-700/20 transition-all"/>
      </div>

      <div class="border-t border-ink-800 pt-5 mt-1 mb-5">
        <p class="text-xs font-semibold text-ink-500 uppercase tracking-widest mb-3">Change Password</p>
        <p class="text-xs text-ink-600 mb-3">Leave blank to keep your current password.</p>

        <div class="grid sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-semibold text-ink-500 uppercase tracking-widest mb-1.5">
              New Password
            </label>
            <input type="password" name="password"
                   class="w-full bg-ink-800 border border-ink-700 rounded-xl px-4 py-3
                          text-sm text-ink-100 placeholder-ink-600
                          focus:outline-none focus:border-forest-700 focus:ring-2 focus:ring-forest-700/20 transition-all"/>
            @error('password')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
          </div>
          <div>
            <label class="block text-xs font-semibold text-ink-500 uppercase tracking-widest mb-1.5">
              Confirm Password
            </label>
            <input type="password" name="password_confirmation"
                   class="w-full bg-ink-800 border border-ink-700 rounded-xl px-4 py-3
                          text-sm text-ink-100
                          focus:outline-none focus:border-forest-700 focus:ring-2 focus:ring-forest-700/20 transition-all"/>
          </div>
        </div>
      </div>

      <div class="flex items-center gap-3">
        <button type="submit"
                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-forest-800
                       border border-forest-700/50 text-forest-300 text-sm font-bold
                       hover:bg-forest-700 transition-colors">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <path d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          Save Changes
        </button>
        <a href="{{ route('profile.show', $user) }}"
           class="px-5 py-2.5 rounded-xl border border-ink-700 text-ink-400 text-sm
                  hover:border-ink-600 hover:text-ink-300 transition-colors">
          Cancel
        </a>
      </div>
    </form>
  </div>
</div>

@endsection