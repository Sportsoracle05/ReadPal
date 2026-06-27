<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <title>@yield('title', 'ReadPal') · ReadPal</title>
    
     {{-- Open Graph --}}
    <meta property="og:title" content="@yield('title') - ReadPal Online">
    <meta property="og:type" content="article">
    <meta property="og:url" content="https://readpal.online">
    <meta property="og:image" content="https://readpal.online/ReadPalMain.png">
    <meta property="og:description" content="ReadPal is a dedicated mobile learning companion designed to streamline access to academic materials">
    <meta property="og:site_name" content="@yield('title') - ReadPal Online">
    <meta property="og:locale" content="en_US">

    {{-- Twitter --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title') - ReadPal Online">
    <meta name="twitter:description" content="ReadPal is a dedicated mobile learning companion designed to streamline access to academic materials">
    <meta name="twitter:image" content="https://readpal.online/ReadPalMain.png">

    {{-- Favicon --}}
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    <meta name="google-adsense-account" content="ca-pub-4261871524010781">

    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet"/>

    <!-- 1. Load the core App SDK first -->
    <script src="https://www.gstatic.com/firebasejs/10.7.1/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/10.7.1/firebase-messaging-compat.js"></script>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = {
        theme: {
          extend: {
            fontFamily: {
              display: ['"Playfair Display"','serif'],
              body:    ['"DM Sans"','sans-serif'],
              mono:    ['"JetBrains Mono"','monospace'],
            },
            colors: {
              forest:{ 300:'#86efac',400:'#4ade80',500:'#22c55e',600:'#16a34a',700:'#15803d',800:'#166534',900:'#14532d',950:'#052e16'},
              ink:   { 50:'#f8fafc',100:'#f1f5f9',200:'#e2e8f0',300:'#cbd5e1',400:'#94a3b8',500:'#64748b',600:'#475569',700:'#334155',800:'#1e293b',900:'#0f172a',950:'#020617'},
            }
          }
        }
      }
    </script>

    <style>
    /* Prevents horizontal scroll on mobile devices */
    html, body {
        max-width: 100%;
        overflow-x: hidden;
        position: relative;
    }

    /* Ensures grid items don't overflow their containers */
    .app-card {
        min-width: 0;
        word-wrap: break-word;
    }

      *, *::before, *::after { box-sizing: border-box; }
      body { font-family: 'DM Sans', sans-serif; }
      ::-webkit-scrollbar { width: 4px; height: 4px; }
      ::-webkit-scrollbar-track { background: #0f172a; }
      ::-webkit-scrollbar-thumb { background: #166534; border-radius: 4px; }

      /* Sidebar nav item */
      .nav-item {
        display: flex; align-items: center; gap: .65rem;
        padding: .55rem .85rem; border-radius: 9px;
        font-size: .84rem; font-weight: 500;
        color: #64748b;
        text-decoration: none;
        transition: all .18s;
        border: 1px solid transparent;
      }
      .nav-item:hover { color: #86efac; background: rgba(22,163,74,.08); }
      .nav-item.active {
        color: #4ade80; background: rgba(22,163,74,.12);
        border-color: rgba(22,163,74,.2);
      }
      .nav-item svg { flex-shrink: 0; }
      .nav-section-label {
        font-size: .63rem; font-weight: 600; letter-spacing: .16em;
        text-transform: uppercase; color: #334155; padding: .5rem .85rem .2rem;
        margin-top: .5rem;
      }

      /* Cards */
      .app-card {
        background: #0f172a; border: 1px solid #1e293b;
        border-radius: 14px; padding: 1.5rem;
        transition: border-color .2s;
      }
      .app-card:hover { border-color: #334155; }

      /* Stat card hover */
      .stat-card { transition: transform .2s, border-color .2s, box-shadow .2s; }
      .stat-card:hover {
        transform: translateY(-2px);
        border-color: #166534;
        box-shadow: 0 0 20px rgba(22,163,74,.1);
      }

      /* Badge */
      .rp-badge {
        display: inline-flex; align-items: center;
        padding: .18rem .6rem; border-radius: 999px;
        font-size: .68rem; font-weight: 600; letter-spacing: .04em;
      }
      .badge-green { background:#052e16; border:1px solid #14532d; color:#4ade80; }
      .badge-blue  { background:#0c1a2e; border:1px solid #1e3a5f; color:#60a5fa; }
      .badge-amber { background:#1c1205; border:1px solid #422006; color:#fbbf24; }

      /* Fade-in on load */
      @keyframes fadeUp {
        from { opacity:0; transform:translateY(14px); }
        to   { opacity:1; transform:translateY(0); }
      }
      .fade-up    { animation: fadeUp .55s ease both; }
      .fade-up-d1 { animation: fadeUp .55s ease .08s both; }
      .fade-up-d2 { animation: fadeUp .55s ease .16s both; }
      .fade-up-d3 { animation: fadeUp .55s ease .24s both; }
      .fade-up-d4 { animation: fadeUp .55s ease .32s both; }

      /* Sidebar overlay for mobile */
      #sidebar-overlay {
        display: none; position: fixed; inset: 0;
        background: rgba(2,6,23,.7); z-index: 30; backdrop-filter: blur(3px);
      }
      #sidebar-overlay.open { display: block; }

      /* Mobile sidebar */
      #sidebar { transition: transform .25s ease; }

      /* Notification dot */
      .notif-dot {
        width: 7px; height: 7px; border-radius: 50%;
        background: #22c55e;
        box-shadow: 0 0 6px rgba(34,197,94,.7);
        animation: pulse-dot 2s ease-in-out infinite;
      }
      @keyframes pulse-dot { 0%,100%{opacity:1;} 50%{opacity:.4;} }

      /* Progress bar */
      .progress-track {
        height: 5px; border-radius: 9px;
        background: #1e293b; overflow: hidden;
      }
      .progress-fill {
        height: 100%; border-radius: 9px;
        background: linear-gradient(90deg, #15803d, #4ade80);
        transition: width .6s ease;
      }
    </style>
    @yield('head')
    @stack('styles')
</head>

<body class="bg-ink-950 text-ink-100 min-h-screen flex">
<!-- Notification Popup Overlay -->
<div id="notif-popup" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
    <div class="bg-ink-900 border border-ink-700 w-full max-w-sm rounded-2xl p-6 shadow-2xl fade-up">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 bg-forest-900/30 rounded-xl flex items-center justify-center flex-shrink-0 border border-forest-500/30">
                <svg class="w-6 h-6 text-forest-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
            </div>
            <div>
                <h3 class="text-white font-bold text-lg">Stay Updated!</h3>
                <p class="text-ink-400 text-sm mt-1">Enable push notifications to receive new lecture alerts and materials.</p>
            </div>
        </div>

        <div class="flex gap-3 mt-6">
            <button onclick="handleNotifAction('allow')" class="flex-1 bg-forest-600 hover:bg-forest-500 text-white font-bold py-2.5 px-4 rounded-xl transition-all">
                Enable
            </button>
            <button onclick="handleNotifAction('cancel')" class="flex-1 bg-ink-800 hover:bg-ink-700 text-ink-300 font-semibold py-2.5 px-4 rounded-xl transition-all">
                Maybe Later
            </button>
        </div>

        <!-- Auto-cancel progress bar -->
        <div class="w-full bg-ink-800 h-1 mt-6 rounded-full overflow-hidden">
            <div id="notif-timer" class="bg-forest-500 h-full w-full transition-all duration-[5000ms] ease-linear"></div>
        </div>
    </div>
</div>

{{-- ════════════════════ SIDEBAR ════════════════════ --}}
<div id="sidebar-overlay" onclick="closeSidebar()"></div>

<aside id="sidebar"
       class="fixed top-0 left-0 h-full w-60 bg-ink-900 border-r border-ink-800
              flex flex-col z-40 -translate-x-full md:translate-x-0">

  {{-- Logo --}}
  <div class="flex items-center gap-2.5 px-4 py-4 border-b border-ink-800">
    <div class="w-8 h-8 bg-forest-800 border border-forest-700 rounded-lg
                flex items-center justify-center flex-shrink-0">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
           stroke="#4ade80" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
        <path d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0118 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
      </svg>
    </div>
    <span class="flex items-center gap-2 font-display text-base font-bold text-white">
    <span>Read<span class="text-forest-400">Pal</span></span>

        @if(auth()->user()->is_premium)
            <span class="inline-flex items-center rounded-full bg-gradient-to-r from-forest-500 to-forest-600 px-2 py-0.5 text-[10px] uppercase tracking-wider text-white shadow-sm ring-1 ring-forest-400/30">
                {{-- Tiny Crown Icon --}}
                <svg class="mr-1 h-2.5 w-2.5 fill-current" viewBox="0 0 24 24">
                    <path d="M5 16L3 5L8.5 10L12 4L15.5 10L21 5L19 16H5M19 19C19 19.6 18.6 20 18 20H6C5.4 20 5 19.6 5 19V18H19V19Z" />
                </svg>
                Premium
            </span>
        @endif
    </span>

    <button onclick="closeSidebar()" class="ml-auto md:hidden text-ink-500 hover:text-white">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
        <path d="M6 18L18 6M6 6l12 12"/>
      </svg>
    </button>
  </div>

  {{-- User pill --}}
  <div class="mx-3 mt-3 p-2.5 rounded-xl bg-ink-800 border border-ink-700 flex items-center gap-2.5">
    <div class="w-8 h-8 rounded-full bg-forest-900 border border-forest-800
                flex items-center justify-center text-forest-300 text-xs font-bold font-display flex-shrink-0">
      {{ strtoupper(substr(Auth::user()->firstname ?? 'U', 0, 1)) }}
    </div>
    <div class="min-w-0">
      <p class="text-xs font-semibold text-ink-100 truncate">{{ Auth::user()->firstname ?? 'Student' }}</p>
      <p class="text-xs text-ink-600 truncate">300L · Sociology</p>
    </div>
    <div class="notif-dot ml-auto flex-shrink-0"></div>
  </div>

  {{-- Nav --}}
  <nav class="flex-1 overflow-y-auto px-2 py-3 space-y-0.5">


    <p class="nav-section-label">Main</p>
    <a href="{{ route('dashboard') }}"
       class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>
      </svg>
      Dashboard
    </a>
    <a href="{{ route('materials.index') }}"
       class="nav-item {{ request()->routeIs('materials.*') ? 'active' : '' }}">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
      </svg>
      Materials
    </a>
    <a href="{{ route('quiz.index') }}"
       class="nav-item {{ request()->routeIs('quiz.*') ? 'active' : '' }}">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"/>
      </svg>
      Quizzes
    </a>
    <a href="{{ route('calender.index') }}"
       class="nav-item {{ request()->routeIs('calender.*') ? 'active' : '' }}">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
      </svg>
      Calendar
    </a>

    <p class="nav-section-label">Study</p>
    @php $isPremium = auth()->user()->hasActivePremium(); @endphp
    <a href="{{ route('ai.chat') }}"
       class="nav-item {{ request()->routeIs('ai.*') ? 'active' : '' }}">
        @if(!$isPremium)
            {{-- Padlock Icon for non-premium --}}
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="color:var(--ai-accent);">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
            </svg>
        @else
            {{-- Pen/AI Icon for premium --}}
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="11" width="18" height="10" rx="2"></rect>
                <circle cx="12" cy="5" r="2"></circle>
                <path d="M12 7v4"></path>
                <line x1="8" y1="16" x2="8" y2="16"></line>
                <line x1="16" y1="16" x2="16" y2="16"></line>
            </svg>

        @endif
        ReadPal AI
    </a>
    
    <a href="{{ route('assignments.index') }}"
       class="nav-item {{ request()->routeIs('assignments.*') ? 'active' : '' }}">
        @if(!$isPremium)
            {{-- Padlock Icon for non-premium --}}
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="color:var(--ai-accent);">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
            </svg>
        @else
            {{-- Assignment/Doc Icon for premium --}}
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
        @endif
        Assignments
    </a>


    <a href="{{ route('notes.index', ['firstname' => Auth::user()->firstname]) }}"
       class="nav-item {{ request()->routeIs('notes.*') ? 'active' : '' }}">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/>
      </svg>
      My Notes
    </a>
    
    <a href="{{ route('cgpa.dashboard') }}"
       class="nav-item {{ request()->routeIs('cgpa.*') ? 'active' : '' }}">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
        <rect x="4" y="2" width="16" height="20" rx="2" ry="2"></rect>
        <line x1="8" y1="6" x2="16" y2="6"></line>
        <line x1="16" y1="14" x2="16" y2="14"></line>
        <line x1="8" y1="10" x2="8" y2="10"></line>
        <line x1="12" y1="10" x2="12" y2="10"></line>
        <line x1="16" y1="10" x2="16" y2="10"></line>
        <line x1="8" y1="14" x2="8" y2="14"></line>
        <line x1="12" y1="14" x2="12" y2="14"></line>
        <line x1="8" y1="18" x2="8" y2="18"></line>
        <line x1="12" y1="18" x2="12" y2="18"></line>
        <line x1="16" y1="18" x2="16" y2="18"></line>
      </svg>
      Your CGPA Calculator
    </a>

    {{-- DM Inbox link --}}
      <p class="nav-section-label">Karls</p>
      <a href="{{ route('karls.inbox') }}"
         class="nav-item {{ request()->routeIs('karls.inbox') || request()->routeIs('karls.dm') ? 'active' : '' }}">
        <div class="thread-icon">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
          </svg>
        </div>
        <span class="thread-name">Inbox</span>
        @if(($unreadCount ?? 0) > 0)
        <span style="background:#14532d;border:1px solid #166534;color:#4ade80;padding:.08rem .45rem;
                     border-radius:999px;font-size:.62rem;font-weight:700;flex-shrink:0;">
          {{ $unreadCount }}
        </span>
        @endif
      </a>

      <a href="{{ route('karls.index') }}"
   class="nav-item {{ request()->routeIs('karls.index') ? 'active' : '' }}">
  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
    <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
  </svg>
  Threads
</a>


    <p class="nav-section-label">Account</p>
    <a href="{{ route('profile.show', Auth::user()) }}"
       class="nav-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
      </svg>
      Profile
    </a>
    <a href="{{ route('payment.plans') }}"
       class="nav-item {{ request()->routeIs('payment.*') ? 'active' : '' }}">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="4" width="18" height="16" rx="2" />
            <line x1="3" y1="10" x2="21" y2="10" />
            <line x1="7" y1="15" x2="7.01" y2="15" />
            <line x1="11" y1="15" x2="13" y2="15" />
        </svg>
        Subscription
    </a>
    <a href="{{ route('settings') }}"
       class="nav-item {{ request()->routeIs('settings') ? 'active' : '' }}">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z"/>
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
      </svg>
      Settings
    </a>
  </nav>

  {{-- Logout --}}
  <div class="px-3 py-3 border-t border-ink-800">
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit"
              class="nav-item w-full text-left hover:!text-red-400 hover:!bg-red-950/40 hover:!border-red-900/30">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/>
        </svg>
        Log Out
      </button>
    </form>
  </div>
</aside>

{{-- ════════════════════ MAIN AREA ════════════════════ --}}
<div class="flex-1 flex flex-col min-h-screen md:ml-60">

  {{-- Top Bar --}}
  <header class="sticky top-0 z-20 bg-ink-950/90 backdrop-blur border-b border-ink-800/60
                 flex items-center gap-3 px-4 py-3 h-14">

    {{-- Mobile hamburger --}}
    <button onclick="openSidebar()"
            class="md:hidden p-1.5 rounded-lg hover:bg-ink-800 text-ink-400">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
        <path d="M3 6h18M3 12h18M3 18h18"/>
      </svg>
    </button>

    {{-- Page title --}}
    <div class="flex-1 min-w-0">
      <h1 class="font-display text-sm font-bold text-white truncate">
        @yield('page_title', 'ReadPal')
      </h1>
      <p class="text-xs text-ink-600 leading-none mt-0.5">@yield('page_sub', 'Your academic learning companion...')</p>
    </div>

    {{-- Right actions --}}
    <div class="flex items-center gap-2">
      {{-- Date chip --}}
      <span id="header-date"
            class="hidden sm:flex items-center gap-1.5 px-2.5 py-1 rounded-lg
                   bg-ink-800 border border-ink-700 text-xs text-ink-400 font-mono">
      </span>

      {{-- New Note quick button --}}
      <a href="{{ route('notes.create', ['firstname' => Auth::user()->firstname]) }}"
         class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-forest-800
                border border-forest-700/50 text-xs font-semibold text-forest-300
                hover:bg-forest-700 transition-colors duration-150">
        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
          <path d="M12 4.5v15m7.5-7.5h-15"/>
        </svg>
        Note
      </a>

      {{-- Avatar --}}
      <a href="{{ route('profile.show', Auth::user()) }}"
         class="w-7 h-7 rounded-full bg-forest-900 border border-forest-800
                flex items-center justify-center text-forest-300 text-xs font-bold font-display">
        {{ strtoupper(substr(Auth::user()->firstname ?? 'U', 0, 1)) }}
      </a>
    </div>
  </header>
  
@unless(auth()->user()->hasActivePremium())
  {{-- Ads --}}
  <div class="w-full">
     <script>
        (function(vanm){
        var d = document,
            s = d.createElement('script'),
            l = d.scripts[d.scripts.length - 1];
        s.settings = vanm || {};
        s.src = "\/\/stale-father.com\/blXrVCsCd.G\/lm0\/YHWAcc\/Qe_mW9ZuoZ\/UxlDkxPoTvYQ5DNYDiMFw\/NXTWcEtRNSjhkD0xMUzKAA2yMuQC";
        s.async = true;
        s.referrerPolicy = 'no-referrer-when-downgrade';
        l.parentNode.insertBefore(s, l);
        })({})
    </script>
</div>
@endunless

  {{-- Safe Flash Message Check --}}
  @if(session('success') || session('error') || (isset($errors) && is_object($errors) && method_exists($errors, 'any') && $errors->any()))

  <div class="px-4 pt-3">
    @if(session('success'))
    <div class="flex items-center gap-2.5 px-4 py-2.5 rounded-xl bg-forest-950 border border-forest-900
                text-forest-300 text-sm mb-2">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
        <path d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
      </svg>
      {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="flex items-center gap-2.5 px-4 py-2.5 rounded-xl bg-red-950/50 border border-red-900/50
                text-red-300 text-sm mb-2">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
        <path d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126z"/>
      </svg>
      {{ session('error') }}
    </div>
    @endif
  </div>
  @endif

  {{-- Page Content --}}
  <main class="flex-1 px-4 py-6 max-w-7xl w-full mx-auto">
    @yield('content')
  </main>
@unless(auth()->user()->hasActivePremium())
  {{-- Ads --}}
 <div class="w-full">
     <script>
        (function(ypza){
        var d = document,
            s = d.createElement('script'),
            l = d.scripts[d.scripts.length - 1];
        s.settings = ypza || {};
        s.src = "\/\/stale-father.com\/b.XVVssBdpG\/lL0cY\/Wfcr\/Wetmm9NuCZqU\/l\/kaPPTxYj5VNuDWMbwPO\/D_U\/tYNUjBkT0OM-zUAB4jO\/Qs";
        s.async = true;
        s.referrerPolicy = 'no-referrer-when-downgrade';
        l.parentNode.insertBefore(s, l);
        })({})
    </script>
</div>
@endunless
{{-- Hide footer if the route belongs to AI or Karls --}}
@if(!request()->routeIs('ai.*') && !request()->routeIs('karls.*'))
  {{-- Footer --}}
  <footer class="border-t border-ink-800/40 px-4 py-3 text-center">
    <p class="text-xs text-ink-700">
      ReadPal &middot; Oracle Tech &middot; AAUA Sociology 300L
    </p>
  </footer>
 @endif
</div>

<script>
    // Ping every 5 minutes to keep the session active
    setInterval(() => {
        fetch("{{ route('heartbeat') }}")
            .then(response => {
                if (!response.ok && response.status === 419) {
                    // If the ping itself fails with 419, the session is already dead
                    window.location.reload();
                }
            })
            .catch(error => console.error('Heartbeat failed:', error));
    }, 5 * 60 * 1000);



  function openSidebar() {
    document.getElementById('sidebar').classList.remove('-translate-x-full');
    document.getElementById('sidebar-overlay').classList.add('open');
  }
  function closeSidebar() {
    document.getElementById('sidebar').classList.add('-translate-x-full');
    document.getElementById('sidebar-overlay').classList.remove('open');
  }

  // Header date
  const el = document.getElementById('header-date');
  if (el) {
    const now = new Date();
    el.textContent = now.toLocaleDateString('en-NG', {weekday:'short', month:'short', day:'numeric'});
  }


  
// We use optional chaining and nullish coalescing to prevent "Undefined variable" errors
  let lastId = {{ isset($karls) && $karls->count() > 0 ? $karls->max('id') : 0 }};
  
  // Only set a poll URL if we are actually inside a thread
  const pollUrl = "{{ isset($thread) ? route('karls.poll', $thread->slug) : '' }}";

  async function checkUpdates() {
    // If we aren't in a thread, we might still want to check for global unread DMs
    if (!pollUrl) return; 

    try {
      const response = await fetch(`${pollUrl}?since=${lastId}`);
      const data = await response.json();

      //Update Messages (only if we are on a chat page with the feed)
      if (data.karls && data.karls.length > 0) {
        const liveFeed = document.getElementById('live-feed');
        if (liveFeed) {
          data.karls.forEach(k => {
            if (!document.querySelector(`[data-karl-id="${k.id}"]`)) {
              liveFeed.insertAdjacentHTML('beforeend', buildKarlHtml(k));
            }
          });
          lastId = data.last_id;
          if (typeof scrollBottom === "function") scrollBottom();
        }
      }

      // Update ALL Unread Badges across the whole layout
      if (data.unreadCount !== undefined) {
        // This targets the badge in your sidebar AND any other layout
        document.querySelectorAll('.unread-badge, .js-unread-badge').forEach(badge => {
          badge.textContent = data.unreadCount;
          badge.style.display = data.unreadCount > 0 ? 'inline-flex' : 'none';
        });
      }

    } catch (e) {
      // Silently fail to avoid console clutter
    }
  }

  // Poll every 10 seconds
  if (pollUrl) {
    setInterval(checkUpdates, 10000);
  }
</script>

<script>
  let popupTimer;

// Show the popup on page load (or after 2 seconds)
window.addEventListener('load', () => {
    const lastShowTime = localStorage.getItem('notif_popup_next_show');
    const now = new Date().getTime();

    
    if (Notification.permission === 'default') {
        if (!lastShowTime || now > lastShowTime) {
            setTimeout(showNotifPopup, 3000); // Wait 3s after page load
        }
    }
});


function showNotifPopup() {
    const popup = document.getElementById('notif-popup');
    const bar = document.getElementById('notif-timer');
    
    popup.classList.remove('hidden');
    
    // Start progress bar animation
    setTimeout(() => { bar.style.width = '0%'; }, 50);

    // Auto-cancel after 5 seconds
    popupTimer = setTimeout(() => {
        handleNotifAction('cancel');
    }, 5000);
}

function handleNotifAction(action) {
    const popup = document.getElementById('notif-popup');
    clearTimeout(popupTimer);
    
    popup.classList.add('hidden');

    if (action === 'allow') {
        askForPermission();
    } else {
        // "Cancel" or "Auto-dismiss" logic:
        // Set a timestamp for 24 hours (86,400,000 milliseconds) from now
        const nextShow = new Date().getTime() + (24 * 60 * 60 * 1000);
        localStorage.setItem('notif_popup_next_show', nextShow);
        console.log("Popup snoozed for 24 hours.");
    }
}

</script>

<script>
    const firebaseConfig = {
        apiKey: "AIzaSyBm8RAM_wq5UqMhrMs9jmZt4zPDOph3Lw4",
        authDomain: "readpal-online.firebaseapp.com",
        projectId: "readpal-online",
        storageBucket: "readpal-online.firebasestorage.app",
        messagingSenderId: "557186880056",
        appId: "1:557186880056:web:d393023e4f3fbf0826cccd"
    };

    // 1. Safe Initialization
    if (typeof firebase !== 'undefined') {
        firebase.initializeApp(firebaseConfig);
        var messaging = firebase.messaging(); 
        console.log("Firebase Messaging initialized.");
    } else {
        console.error("Firebase library not found. Ensure script tags are in the <head>.");
    }

    async function askForPermission() {
        if (typeof messaging === 'undefined') {
            alert("Messaging is still loading. Please wait a moment.");
            return;
        }

        const permission = await Notification.requestPermission();
        
        if (permission === 'granted') {
            console.log('Notification permission granted.');

            try {
                // 2. Register the Service Worker
                const swUrl = "{{ asset('firebase-messaging-sw.js') }}";
                const registration = await navigator.serviceWorker.register(swUrl);
                
                // 3. CRITICAL: Wait for Service Worker to be 'active'
                // This prevents the 'no active Service Worker' AbortError
                while (registration.active === null) {
                    console.log("Waiting for Service Worker activation...");
                    await new Promise(resolve => setTimeout(resolve, 100));
                }
                
                console.log('Service Worker is active and ready.');

                // 4. Get the Token
                const currentToken = await messaging.getToken({ 
                    serviceWorkerRegistration: registration,
                    vapidKey: 'BIv0rG9yQB6VV2csWkb3cQwQNCUt99lyZ08w5x97GUbvFxb_4qrfbYfF7C_nc0MsjPGK9vHJbzwcCOl_zlBfssc' 
                });

                if (currentToken) {
                    console.log("Token:", currentToken);
                    
                    // 5. Send to App
                    const response = await fetch('{{ route('save-fcm-token') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ token: currentToken })
                    });
                    
                    const data = await response.json();
                    console.log('Server response:', data);
                } else {
                    console.warn('No token generated. Check Firebase Console configuration.');
                }
            } catch (err) {
                console.error('Token retrieval error:', err);
            }
        } else {
            console.warn('Permission denied.');
        }
    }
</script>


</script>
@stack('scripts')
</body>
</html>