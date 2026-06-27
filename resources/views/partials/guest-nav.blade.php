{{-- Guest top navigation --}}
<nav class="relative z-20 flex items-center justify-between px-6 md:px-10 py-5">
    {{-- Logo --}}
    <a href="{{ url('/') }}" class="flex items-center gap-3 group">
        <div class="w-9 h-9 rounded-xl flex items-center justify-center relative overflow-hidden"
             style="background: linear-gradient(135deg, #15803D, #A06015); box-shadow: 0 0 20px rgba(212,136,42,0.3);">
            <svg class="w-5 h-5 text-ink-900" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
            </svg>
        </div>
        <div>
            <span class="font-display text-xl font-semibold text-parch-100 tracking-tight">ReadPal</span>
            <span class="block text-xs text-amber/60 font-body font-medium leading-none -mt-0.5">by Oracle Tech</span>
        </div>
    </a>

    {{-- Nav links --}}
    <div class="hidden md:flex items-center gap-6">
        <a href="{{ route('login') }}" class="nav-link">Login</a>
        <a href="{{ url('/about') }}" class="nav-link">About</a>
        <a href="{{ url('/contact') }}" class="nav-link">Contact</a>
    </div>

    {{-- CTA --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('login') }}"
           class="hidden md:inline-flex rp-btn-ghost px-4 py-2 rounded-xl text-sm font-medium">
            Sign In
        </a>
        <a href="{{ route('signup') }}"
           class="rp-btn-primary px-4 py-2 rounded-xl text-sm font-body">
            Get Started
        </a>

        {{-- Mobile menu --}}
        <button id="mobile-menu-btn" class="md:hidden p-2 rounded-lg text-parch-100/60 hover:text-amber transition-colors ml-1">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
    </div>
</nav>

{{-- Mobile menu --}}
<div id="mobile-menu" class="hidden md:hidden relative z-20 mx-4 mb-3 rounded-2xl overflow-hidden"
     style="background: rgba(19,21,30,0.97); border: 1px solid rgba(212,136,42,0.15);">
    <div class="flex flex-col p-4 gap-1">
        <a href="{{ route('login') }}"   class="nav-link px-4 py-3 rounded-xl hover:bg-amber/10 block">Login</a>
        <a href="{{ route('signup') }}"  class="nav-link px-4 py-3 rounded-xl hover:bg-amber/10 block">Sign Up</a>
        <a href="{{ url('/about') }}"    class="nav-link px-4 py-3 rounded-xl hover:bg-amber/10 block">About</a>
        <a href="{{ url('/contact') }}"  class="nav-link px-4 py-3 rounded-xl hover:bg-amber/10 block">Contact</a>
        <a href="{{ url('/feedback') }}" class="nav-link px-4 py-3 rounded-xl hover:bg-amber/10 block">Feedback</a>
        <hr class="border-amber/10 my-2">
        <a href="{{ route('signup') }}"  class="rp-btn-primary w-full text-center px-4 py-2.5 rounded-xl text-sm font-body">Get Started Free</a>
    </div>
</div>

<script>
    document.getElementById('mobile-menu-btn').addEventListener('click', function() {
        const menu = document.getElementById('mobile-menu');
        menu.classList.toggle('hidden');
    });
</script>
