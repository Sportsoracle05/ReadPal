{{-- Authenticated sidebar --}}
<aside id="sidebar" class="w-64 flex-shrink-0 hidden md:flex flex-col h-screen sticky top-0 overflow-y-auto">

    {{-- Logo --}}
    <div class="px-5 py-6 flex items-center gap-3">
        <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0"
             style="background: linear-gradient(135deg, #15803D, #A06015); box-shadow: 0 0 18px rgba(212,136,42,0.25);">
            <svg class="w-5 h-5 text-ink-900" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
            </svg>
        </div>
        <div>
            <span class="font-display text-lg font-semibold text-parch-100 leading-none">ReadPal</span>
            <span class="block text-xs text-amber/50 font-body leading-none mt-0.5">by Oracle Tech</span>
        </div>
    </div>

    <div class="px-3 mb-2">
        <hr class="border-amber/10">
    </div>

    {{-- User card --}}
    <div class="mx-3 my-2 p-3 rounded-xl" style="background: rgba(212,136,42,0.06); border: 1px solid rgba(212,136,42,0.1);">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold text-ink-900 flex-shrink-0"
                 style="background: linear-gradient(135deg, #15803D, #F0B050);">
                {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
            </div>
            <div class="overflow-hidden">
                <p class="text-parch-100 text-sm font-medium truncate">{{ auth()->user()->name ?? 'Student' }}</p>
                <p class="text-parch-100/40 text-xs truncate">{{ auth()->user()->matric_no ?? 'Sociology 300L' }}</p>
            </div>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 px-3 py-3 space-y-1">

        <p class="text-parch-100/25 text-xs uppercase tracking-widest px-4 pt-2 pb-1 font-medium">Main</p>

        <a href="{{ route('dashboard') }}" class="sidebar-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 7a2 2 0 012-2h4a2 2 0 012 2v4a2 2 0 01-2 2H5a2 2 0 01-2-2V7zM13 7a2 2 0 012-2h2a2 2 0 012 2v10a2 2 0 01-2 2h-2a2 2 0 01-2-2V7zM3 15a2 2 0 012-2h4a2 2 0 012 2v2a2 2 0 01-2 2H5a2 2 0 01-2-2v-2z"/>
            </svg>
            Dashboard
        </a>

        <a href="#" class="sidebar-item {{ request()->routeIs('materials*') ? 'active' : '' }}">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Lecture Notes
            <span class="badge">New</span>
        </a>

        <a href="#" class="sidebar-item {{ request()->routeIs('quizzes*') ? 'active' : '' }}">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
            </svg>
            Self-Assessment
        </a>

        <a href="#" class="sidebar-item {{ request()->routeIs('timetable*') ? 'active' : '' }}">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            Timetable & Alerts
        </a>

        <a href="#" class="sidebar-item {{ request()->routeIs('notes*') ? 'active' : '' }}">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            My Notes
        </a>

        <a href="#" class="sidebar-item {{ request()->routeIs('progress*') ? 'active' : '' }}">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            Progress Tracker
        </a>

        <p class="text-parch-100/25 text-xs uppercase tracking-widest px-4 pt-4 pb-1 font-medium">Support</p>

        <a href="{{ url('/feedback') }}" class="sidebar-item">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
            Feedback
        </a>

        <a href="{{ url('/contact') }}" class="sidebar-item">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            Contact Us
        </a>
    </nav>

    {{-- Logout --}}
    <div class="px-3 py-4 border-t border-amber/10">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="sidebar-item w-full text-red-400/70 hover:text-red-400 hover:bg-red-900/15">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                Sign Out
            </button>
        </form>
    </div>
</aside>
