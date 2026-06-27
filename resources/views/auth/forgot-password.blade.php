@extends('layouts.guest')

@section('title', 'Forgot Password')

@section('content')
<section class="min-h-[80vh] flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">

        <div class="text-center mb-8 fade-up">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl mx-auto mb-5"
                 style="background: rgba(212,136,42,0.12); border: 1px solid rgba(212,136,42,0.3);">
                <svg class="w-7 h-7 text-amber" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                </svg>
            </div>
            <h1 class="font-display text-4xl font-semibold text-parch-100">Reset Password</h1>
            <p class="text-parch-100/50 text-sm mt-2 max-w-sm mx-auto">
                Enter your registered email and we'll send you a secure reset link.
            </p>
        </div>

        <div class="auth-card rounded-3xl p-8 shadow-amber-glow fade-up delay-1">

            @if(session('status'))
                <div class="rp-alert-success rounded-xl px-4 py-3 mb-5 text-sm flex items-center gap-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ session('status') }}
                </div>
            @endif

            @if($errors->any())
                <div class="rp-alert-error rounded-xl px-4 py-3 mb-5 text-sm">
                    @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                @csrf

                <div class="fade-up delay-2">
                    <label class="block text-xs font-medium text-parch-100/60 uppercase tracking-wider mb-2">
                        Email Address
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           placeholder="you@aaua.edu.ng"
                           class="rp-input w-full px-4 py-3 rounded-xl text-sm"
                           required autofocus>
                </div>

                <div class="fade-up delay-3 pt-1">
                    <button type="submit" class="rp-btn-primary w-full py-3.5 rounded-xl text-sm font-body">
                        Send Reset Link
                    </button>
                </div>
            </form>

            <div class="text-center mt-6 fade-up delay-4">
                <a href="{{ route('login') }}"
                   class="inline-flex items-center gap-1.5 text-sm text-parch-100/40 hover:text-amber transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Sign In
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
