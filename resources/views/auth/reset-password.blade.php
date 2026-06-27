@extends('layouts.guest')

@section('title', 'Set New Password')

@section('content')
<section class="min-h-[80vh] flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">

        <div class="text-center mb-8 fade-up">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl mx-auto mb-5"
                 style="background: rgba(75,110,82,0.15); border: 1px solid rgba(75,110,82,0.35);">
                <svg class="w-7 h-7 text-sage-light" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <h1 class="font-display text-4xl font-semibold text-parch-100">New Password</h1>
            <p class="text-parch-100/50 text-sm mt-2">Choose a strong password for your ReadPal account</p>
        </div>

        <div class="auth-card rounded-3xl p-8 shadow-amber-glow fade-up delay-1">

            @if($errors->any())
                <div class="rp-alert-error rounded-xl px-4 py-3 mb-5 text-sm">
                    @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="fade-up delay-2">
                    <label class="block text-xs font-medium text-parch-100/60 uppercase tracking-wider mb-2">Email Address</label>
                    <input type="email" name="email" value="{{ old('email', request('email')) }}"
                           class="rp-input w-full px-4 py-3 rounded-xl text-sm"
                           required readonly>
                </div>

                <div class="fade-up delay-2">
                    <label class="block text-xs font-medium text-parch-100/60 uppercase tracking-wider mb-2">New Password</label>
                    <div class="relative">
                        <input type="password" name="password" id="pw-new"
                               placeholder="Min. 8 characters"
                               class="rp-input w-full px-4 py-3 pr-10 rounded-xl text-sm"
                               required autocomplete="new-password">
                        <button type="button" onclick="togglePassword('pw-new', this)"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-parch-100/30 hover:text-amber transition-colors">
                            <svg class="w-4 h-4 eye-off" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                            <svg class="w-4 h-4 eye-on hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                    </div>
                </div>

                <div class="fade-up delay-3">
                    <label class="block text-xs font-medium text-parch-100/60 uppercase tracking-wider mb-2">Confirm New Password</label>
                    <div class="relative">
                        <input type="password" name="password_confirmation" id="pw-confirm"
                               placeholder="Re-enter new password"
                               class="rp-input w-full px-4 py-3 pr-10 rounded-xl text-sm"
                               required autocomplete="new-password">
                        <button type="button" onclick="togglePassword('pw-confirm', this)"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-parch-100/30 hover:text-amber transition-colors">
                            <svg class="w-4 h-4 eye-off" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                            <svg class="w-4 h-4 eye-on hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                    </div>
                </div>

                {{-- Match indicator --}}
                <p id="pw-match" class="text-xs hidden mt-1"></p>

                <div class="fade-up delay-4 pt-1">
                    <button type="submit" class="rp-btn-primary w-full py-3.5 rounded-xl text-sm font-body">
                        Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
function togglePassword(id, btn) {
    const f = document.getElementById(id);
    const isP = f.type === 'password';
    f.type = isP ? 'text' : 'password';
    btn.querySelector('.eye-off').classList.toggle('hidden', isP);
    btn.querySelector('.eye-on').classList.toggle('hidden', !isP);
}

// Match check
document.getElementById('pw-confirm').addEventListener('input', function() {
    const indicator = document.getElementById('pw-match');
    const match = this.value === document.getElementById('pw-new').value;
    indicator.classList.remove('hidden');
    indicator.textContent = match ? '✓ Passwords match' : '✗ Passwords do not match';
    indicator.style.color = match ? '#73A67C' : '#f87171';
});
</script>
@endpush
