{{-- Top bar for authenticated layout --}}
<header id="topbar" class="sticky top-0 z-20 flex items-center justify-between px-6 md:px-8 h-16">

    {{-- Mobile hamburger --}}
    <button onclick="openSidebar()" class="md:hidden p-2 rounded-xl text-parch-100/50 hover:text-amber hover:bg-amber/10 transition-all">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
    </button>

    {{-- Page title (set via @section('page_title') in each view) --}}
    <div class="flex items-center gap-2">
        <span class="hidden md:block text-parch-100/30 text-sm">/</span>
        <h1 class="font-body font-600 text-parch-100/80 text-sm md:text-base">
            @yield('page_title', 'Dashboard')
        </h1>
    </div>

    {{-- Right side actions --}}
    <div class="flex items-center gap-2 md:gap-3">

        {{-- Search --}}
        <div class="hidden md:flex items-center gap-2 rp-input px-3 py-2 rounded-xl text-sm w-48">
            <svg class="w-4 h-4 text-parch-100/30 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" placeholder="Search materials..." class="bg-transparent border-0 outline-none text-parch-100/70 placeholder-parch-100/30 text-sm w-full">
        </div>

        {{-- Notifications --}}
        <button class="relative p-2 rounded-xl text-parch-100/50 hover:text-amber hover:bg-amber/10 transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            {{-- Notification dot --}}
            <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-amber rounded-full border-2 border-ink-800"></span>
        </button>

        {{-- Avatar dropdown --}}
        <div class="relative" x-data="{ open: false }" @click.away="open = false">
            <button onclick="this.parentElement.querySelector('.dropdown-menu').classList.toggle('hidden')"
                    class="flex items-center gap-2 p-1.5 rounded-xl hover:bg-amber/10 transition-all">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold text-ink-900"
                     style="background: linear-gradient(135deg, #15803D, #F0B050);">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
                </div>
                <svg class="w-3.5 h-3.5 text-parch-100/30 hidden md:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            {{-- Dropdown --}}
            <div class="dropdown-menu hidden absolute right-0 top-full mt-2 w-48 rounded-2xl overflow-hidden shadow-card z-50"
                 style="background: rgba(19,21,30,0.98); border: 1px solid rgba(212,136,42,0.15);">
                <div class="px-4 py-3 border-b border-amber/10">
                    <p class="text-parch-100 text-sm font-medium truncate">{{ auth()->user()->name ?? 'Student' }}</p>
                    <p class="text-parch-100/40 text-xs truncate">{{ auth()->user()->email ?? '' }}</p>
                </div>
                <div class="py-1">
                    <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-sm text-parch-100/60 hover:text-parch-100 hover:bg-amber/8 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        My Profile
                    </a>
                    <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-sm text-parch-100/60 hover:text-parch-100 hover:bg-amber/8 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Settings
                    </a>
                </div>
                <div class="py-1 border-t border-amber/10">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="flex items-center gap-3 px-4 py-2.5 text-sm text-red-400/70 hover:text-red-400 hover:bg-red-900/15 w-full transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            Sign Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
