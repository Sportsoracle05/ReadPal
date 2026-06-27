<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="csrf-token" content="{{ csrf_token() }}"/>
  <title>@yield('title', 'Admin') · ReadPal Admin</title>

  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet"/>

  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            display:['"Playfair Display"','serif'],
            body:['"DM Sans"','sans-serif'],
            mono:['"JetBrains Mono"','monospace'],
          },
          colors: {
            forest:{300:'#86efac',400:'#4ade80',500:'#22c55e',600:'#16a34a',700:'#15803d',800:'#166534',900:'#14532d',950:'#052e16'},
            ink:{50:'#f8fafc',100:'#f1f5f9',200:'#e2e8f0',300:'#cbd5e1',400:'#94a3b8',500:'#64748b',600:'#475569',700:'#334155',800:'#1e293b',900:'#0f172a',950:'#020617'},
          }
        }
      }
    }
  </script>

  <style>
 

    *,*::before,*::after{box-sizing:border-box;}
    body{font-family:'DM Sans',sans-serif;}
    ::-webkit-scrollbar{width:4px;height:4px;}
    ::-webkit-scrollbar-track{background:#0f172a;}
    ::-webkit-scrollbar-thumb{background:#166534;border-radius:4px;}

    .nav-item{display:flex;align-items:center;gap:.6rem;padding:.5rem .8rem;border-radius:9px;
      font-size:.82rem;font-weight:500;color:#64748b;text-decoration:none;
      transition:all .18s;border:1px solid transparent;}
    .nav-item:hover{color:#86efac;background:rgba(22,163,74,.08);}
    .nav-item.active{color:#4ade80;background:rgba(22,163,74,.12);border-color:rgba(22,163,74,.2);}
    .nav-item svg{flex-shrink:0;}
    .nav-label{font-size:.62rem;font-weight:600;letter-spacing:.16em;text-transform:uppercase;
      color:#334155;padding:.5rem .8rem .2rem;margin-top:.4rem;}

    .a-card{background:#0f172a;border:1px solid #1e293b;border-radius:14px;padding:1.5rem;transition:border-color .2s;}
    .a-card:hover{border-color:#334155;}
    .a-card-sm{background:#0f172a;border:1px solid #1e293b;border-radius:10px;padding:1.1rem;}

    .stat-up{background:#052e16;border:1px solid #14532d;color:#4ade80;padding:.2rem .6rem;border-radius:999px;font-size:.68rem;font-weight:600;}
    .stat-dn{background:#1c0a0a;border:1px solid #7f1d1d;color:#f87171;padding:.2rem .6rem;border-radius:999px;font-size:.68rem;font-weight:600;}

    .rp-badge{display:inline-flex;align-items:center;padding:.18rem .6rem;border-radius:999px;font-size:.68rem;font-weight:600;}
    .badge-green{background:#052e16;border:1px solid #14532d;color:#4ade80;}
    .badge-blue{background:#0c1a2e;border:1px solid #1e3a5f;color:#60a5fa;}
    .badge-amber{background:#1c1205;border:1px solid #422006;color:#fbbf24;}
    .badge-red{background:#1c0505;border:1px solid #7f1d1d;color:#f87171;}
    .badge-violet{background:#1a0a2e;border:1px solid #3b1a6f;color:#a78bfa;}

    .form-input{width:100%;background:#1e293b;border:1px solid #334155;border-radius:10px;
      padding:.65rem 1rem;font-family:'DM Sans',sans-serif;font-size:.875rem;color:#f1f5f9;
      transition:border-color .2s,box-shadow .2s;outline:none;}
    .form-input:focus{border-color:#15803d;box-shadow:0 0 0 3px rgba(22,163,74,.12);}
    .form-input::placeholder{color:#475569;}
    .form-label{display:block;font-size:.72rem;font-weight:600;letter-spacing:.08em;
      text-transform:uppercase;color:#64748b;margin-bottom:.4rem;}
    .form-error{font-size:.75rem;color:#f87171;margin-top:.25rem;}

    .btn-primary{display:inline-flex;align-items:center;gap:.45rem;padding:.65rem 1.3rem;
      border-radius:10px;background:#166534;border:1px solid #15803d;color:#fff;
      font-size:.84rem;font-weight:600;cursor:pointer;transition:all .18s;text-decoration:none;}
    .btn-primary:hover{background:#15803d;color:#fff;box-shadow:0 4px 16px rgba(22,163,74,.25);transform:translateY(-1px);}
    .btn-outline{display:inline-flex;align-items:center;gap:.45rem;padding:.65rem 1.3rem;
      border-radius:10px;background:transparent;border:1px solid #334155;color:#94a3b8;
      font-size:.84rem;font-weight:600;cursor:pointer;transition:all .18s;text-decoration:none;}
    .btn-outline:hover{border-color:#475569;color:#fff;background:rgba(255,255,255,.04);}
    .btn-danger{display:inline-flex;align-items:center;gap:.45rem;padding:.65rem 1.3rem;
      border-radius:10px;background:rgba(127,29,29,.3);border:1px solid #991b1b;color:#f87171;
      font-size:.84rem;font-weight:600;cursor:pointer;transition:all .18s;text-decoration:none;}
    .btn-danger:hover{background:rgba(127,29,29,.5);}
    .btn-sm{padding:.4rem .85rem;font-size:.76rem;}

    .tbl-head{font-size:.65rem;font-weight:600;letter-spacing:.14em;text-transform:uppercase;
      color:#475569;padding:.65rem 1rem;border-bottom:1px solid #1e293b;white-space:nowrap;}
    .tbl-cell{padding:.7rem 1rem;border-bottom:1px solid #1e293b;font-size:.84rem;color:#94a3b8;vertical-align:middle;}
    .tbl-row:hover td{background:rgba(255,255,255,.02);}

    #overlay{display:none;position:fixed;inset:0;background:rgba(2,6,23,.7);z-index:30;backdrop-filter:blur(3px);}
    #overlay.open{display:block;}
    #sidebar{transition:transform .25s ease;}

    @keyframes fadeUp{from{opacity:0;transform:translateY(14px);}to{opacity:1;transform:translateY(0);}}
    .fu{animation:fadeUp .5s ease both;}
    .fu1{animation:fadeUp .5s ease .07s both;}
    .fu2{animation:fadeUp .5s ease .14s both;}
    .fu3{animation:fadeUp .5s ease .21s both;}
    .fu4{animation:fadeUp .5s ease .28s both;}

    .progress-track{height:4px;border-radius:9px;background:#1e293b;overflow:hidden;}
    .progress-fill{height:100%;border-radius:9px;background:linear-gradient(90deg,#15803d,#4ade80);}

    .live-dot{width:8px;height:8px;border-radius:50%;background:#22c55e;
      box-shadow:0 0 7px rgba(34,197,94,.7);animation:ping 2s ease-in-out infinite;}
    @keyframes ping{0%,100%{opacity:1;}50%{opacity:.3;}}

    .role-chip{display:inline-flex;align-items:center;gap:.3rem;padding:.2rem .65rem;
      border-radius:999px;font-size:.65rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;}
    .chip-super{background:rgba(212,168,83,.1);border:1px solid rgba(212,168,83,.3);color:#d4a853;}
    .chip-rep{background:rgba(96,165,250,.1);border:1px solid rgba(96,165,250,.3);color:#60a5fa;}
    .chip-admin{background:rgba(74,222,128,.1);border:1px solid rgba(74,222,128,.3);color:#4ade80;}
  </style>
  @yield('head')
  @stack('styles')
</head>

<body class="bg-ink-950 text-ink-300 min-h-screen flex">

<div id="overlay" onclick="closeSidebar()"></div>

{{-- ══════════════════ SIDEBAR ══════════════════ --}}
<aside id="sidebar"
       class="fixed top-0 left-0 h-full w-60 bg-ink-900 border-r border-ink-800
              flex flex-col z-40 -translate-x-full md:translate-x-0">

  {{-- Logo --}}
  <div class="flex items-center gap-2.5 px-4 py-4 border-b border-ink-800">
    <div class="w-8 h-8 bg-forest-800 border border-forest-700 rounded-lg
                flex items-center justify-center flex-shrink-0">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
           stroke="#4ade80" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0118 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
      </svg>
    </div>
    <div class="flex-1 min-w-0">
      <span class="font-display text-sm font-bold text-white">ReadPal <span class="text-forest-600">Admin</span></span>
    </div>
    <button onclick="closeSidebar()" class="md:hidden text-ink-600 hover:text-ink-300 p-0.5">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
        <path d="M6 18L18 6M6 6l12 12"/>
      </svg>
    </button>
  </div>

  {{-- Admin identity --}}
  <div class="mx-3 mt-3 p-2.5 rounded-xl bg-ink-800 border border-ink-700 flex items-center gap-2.5">
    <div class="w-8 h-8 rounded-full bg-forest-950 border border-forest-900
                flex items-center justify-center text-forest-400 text-xs font-bold font-display flex-shrink-0">
      {{ strtoupper(substr(Auth::user()->firstname ?? 'A', 0, 1)) }}
    </div>
    <div class="min-w-0 flex-1">
      <p class="text-xs font-semibold text-ink-100 truncate">{{ Auth::user()->firstname ?? 'Admin' }}</p>
      @php $role = Auth::user()->role ?? 'admin'; @endphp
      <span class="role-chip {{ $role === 'super' ? 'chip-super' : ($role === 'rep' ? 'chip-rep' : 'chip-admin') }}">
        {{ $role === 'super' ? '★ Super' : ($role === 'rep' ? 'Rep' : 'Admin') }}
      </span>
    </div>
  </div>

  {{-- Nav --}}
  <nav class="flex-1 overflow-y-auto px-2 py-3 space-y-0.5">

    <p class="nav-label">Overview</p>
    <a href="{{ route('admin.dashboard') }}"
       class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>
      </svg> Dashboard
    </a>
    <a href="{{ route('admin.events.index') }}"
       class="nav-item {{ request()->routeIs('admin.events.*') ? 'active' : '' }}">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
      </svg> Events
    </a>

    <p class="nav-label">Content</p>
    <a href="{{ route('admin.materials.index') }}"
       class="nav-item {{ request()->routeIs('admin.materials.*') ? 'active' : '' }}">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
      </svg> Materials
    </a>

    {{-- Rep-only --}}
    @if(in_array(Auth::user()->role ?? '', ['rep','super']))
    <a href="{{ route('admin.lectures.index') }}"
       class="nav-item {{ request()->routeIs('admin.lectures.*') ? 'active' : '' }}">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0118 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
      </svg> Lectures
      <span class="ml-auto role-chip chip-rep text-xs">Rep</span>
    </a>
    @endif

    {{-- Super Admin only --}}
    @if((Auth::user()->role ?? '') === 'super')
    <p class="nav-label">Super Admin</p>
    <a href="{{ route('admin.resources.index') }}"
       class="nav-item {{ request()->routeIs('admin.resources.*') ? 'active' : '' }}">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z"/>
      </svg> Resources
    </a>
    <a href="{{ route('admin.cgpa.index') }}"
   class="nav-item {{ request()->routeIs('admin.cgpa.*') ? 'active' : '' }}">
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
  CGPA Courses
</a>

    <a href="{{ route('admin.users.index') }}"
       class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
      </svg> Users
    </a>
    <a href="{{ route('admin.reps.index') }}"
       class="nav-item {{ request()->routeIs('admin.reps.*') ? 'active' : '' }}">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/>
      </svg> Reps
    </a>
    <a href="{{ route('admin.analytics.live') }}"
       class="nav-item {{ request()->routeIs('admin.analytics') ? 'active' : '' }}">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
      </svg> Analytics
      <div class="ml-auto live-dot flex-shrink-0"></div>
    </a>
    <a href="{{ route('admin.logs') }}"
       class="nav-item {{ request()->routeIs('admin.logs') ? 'active' : '' }} group">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <polyline points="4 17 10 11 4 5"></polyline>
        <line x1="12" y1="19" x2="20" y2="19"></line>
      </svg>
      <span>System Logs</span>
    </a>
    @endif
    
    <p class="nav-label">Payments</p>
    <a href="{{ route('admin.payments.index') }}"
       class="nav-item {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="4" width="18" height="16" rx="2" />
            <line x1="3" y1="10" x2="21" y2="10" />
            <line x1="7" y1="15" x2="7.01" y2="15" />
            <line x1="11" y1="15" x2="13" y2="15" />
        </svg> Subscriptions
    </a>
    <a href="{{ route('admin.payment-plans.index') }}"
       class="nav-item {{ request()->routeIs('admin.payment-plans.*') ? 'active' : '' }}">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="4" width="18" height="16" rx="2" />
            <line x1="3" y1="10" x2="21" y2="10" />
            <line x1="7" y1="15" x2="7.01" y2="15" />
            <line x1="11" y1="15" x2="13" y2="15" />
        </svg> Custom Fee
    </a>

    <p class="nav-label">Academic</p>
    <a href="{{ route('ai.knowledge-bases.index') }}" class="nav-item {{ request()->routeIs('ai.knowledge-bases.*') ? 'active' : '' }}">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><ellipse cx="12" cy="5" rx="9" ry="3"></ellipse><path d="M3 5V19A9 3 0 0 0 21 19V5"></path><path d="M3 12A9 3 0 0 0 21 12"></path></svg> 
    Knowledge-Bases
    </a>
    
    <a href="{{ route('admin.assignments.index') }}" class="nav-item {{ request()->routeIs('admin.assignments.*') ? 'active' : '' }}">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect><path d="M9 14l2 2 4-4"></path></svg> 
        Assignments
    </a>
    
    <a href="{{ route('admin.ai-monitor.index') }}" class="nav-item {{ request()->routeIs('admin.ai-monitor.*') ? 'active' : '' }}">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"></path></svg> 
        AI Check
    </a>

    <a href="{{ route('admin.academic.index') }}"
       class="nav-item {{ request()->routeIs('admin.academic.*') ? 'active' : '' }}">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342"/>
      </svg> Academic
    </a>

    <p class="nav-label">Google</p>
    <a href="{{ route('admin.google.auth') }}"
       class="nav-item">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v8m-4-4h8"/>
      </svg> Connect Calendar
    </a>
  </nav>

  {{-- Logout --}}
  <div class="px-3 py-3 border-t border-ink-800">
    <a href="{{ route('dashboard') }}"
       class="nav-item mb-1 text-sky-600 hover:!text-sky-400 hover:!bg-sky-950/30">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/>
      </svg> Student View
    </a>
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit" class="nav-item w-full text-left hover:!text-red-400 hover:!bg-red-950/30">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/>
        </svg> Sign Out
      </button>
    </form>
  </div>
</aside>

{{-- ══════════════════ MAIN ══════════════════ --}}
<div class="flex-1 flex flex-col min-h-screen md:ml-60">
  
  {{-- Safe Flash Message Check --}}
@if(session('success') || session('error') || (isset($errors) && is_object($errors) && method_exists($errors, 'any') && $errors->any()))

<div id="global-alert" 
     class="fixed top-6 right-6 z-[100] w-full max-w-sm fade-up"
     style="animation-duration: 0.3s;">
    
    @php
        $isError = session('error') || $errors->any();
        $message = session('success') ?? session('error') ?? $errors->first();
    @endphp

    <div class="flex items-start gap-3 p-4 rounded-2xl bg-ink-900 border {{ $isError ? 'border-red-500/30' : 'border-forest-800' }} shadow-2xl shadow-black/50">
        {{-- Icon --}}
        <div class="flex-shrink-0 w-8 h-8 rounded-lg {{ $isError ? 'bg-red-500/10 text-red-400' : 'bg-forest-900 text-forest-400' }} flex items-center justify-center border {{ $isError ? 'border-red-500/20' : 'border-forest-800' }}">
            @if($isError)
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
            @else
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M4.5 12.75l6 6 9-13.5"/></svg>
            @endif
        </div>

        {{-- Content --}}
        <div class="flex-1 min-w-0 pt-0.5">
            <p class="text-[.84rem] font-bold text-white leading-none mb-1">
                {{ $isError ? 'Action Failed' : 'Success' }}
            </p>
            <p class="text-[.76rem] text-ink-400 leading-relaxed">
                {{ $message }}
            </p>
        </div>

        {{-- Close --}}
        <button onclick="document.getElementById('global-alert').remove()" class="text-ink-600 hover:text-ink-300 transition-colors">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
</div>

<script>
    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        const alert = document.getElementById('global-alert');
        if(alert) alert.style.opacity = '0';
        setTimeout(() => alert?.remove(), 500);
    }, 5000);
</script>
@endif

  {{-- Top bar --}}
  <header class="sticky top-0 z-20 bg-ink-950/90 backdrop-blur border-b border-ink-800/60
                 flex items-center gap-3 px-4 h-14">
    <button onclick="openSidebar()" class="md:hidden p-1.5 rounded-lg hover:bg-ink-800 text-ink-400">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
        <path d="M3 6h18M3 12h18M3 18h18"/>
      </svg>
    </button>
    <div class="flex-1 min-w-0">
      <h1 class="font-display text-sm font-bold text-white truncate">@yield('page_title','Dashboard')</h1>
      <p class="text-xs text-ink-700 leading-none mt-0.5">@yield('page_sub','ReadPal Admin Panel')</p>
    </div>
    <div class="flex items-center gap-2">
      <span id="hdr-date" class="hidden sm:block font-mono text-xs text-ink-600 px-2.5 py-1
             rounded-lg bg-ink-800 border border-ink-800"></span>
      <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-forest-950 border border-forest-900">
        <div class="live-dot w-1.5 h-1.5"></div>
        <span class="text-xs text-forest-500 font-semibold">Admin</span>
      </div>
    </div>
  </header>

  {{-- Flash --}}
  @if(session('success')||session('error'))
  <div class="px-4 pt-3">
    @if(session('success'))
    <div class="flex items-center gap-2.5 px-4 py-2.5 rounded-xl bg-forest-950 border border-forest-900 text-forest-300 text-sm mb-2">
      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="flex items-center gap-2.5 px-4 py-2.5 rounded-xl bg-red-950/50 border border-red-900/50 text-red-300 text-sm mb-2">
      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126z"/></svg>
      {{ session('error') }}
    </div>
    @endif
  </div>
  @endif

  <main class="flex-1 px-4 py-6 max-w-screen-2xl w-full mx-auto">
    @yield('content')
  </main>

  <footer class="border-t border-ink-800/40 px-4 py-3 text-center">
    <p class="text-xs text-ink-800">ReadPal Admin Panel · Oracle Tech · AAUA</p>
  </footer>
</div>

<script>
  function openSidebar(){document.getElementById('sidebar').classList.remove('-translate-x-full');document.getElementById('overlay').classList.add('open');}
  function closeSidebar(){document.getElementById('sidebar').classList.add('-translate-x-full');document.getElementById('overlay').classList.remove('open');}
  const hd=document.getElementById('hdr-date');
  if(hd){const n=new Date();hd.textContent=n.toLocaleDateString('en-NG',{weekday:'short',month:'short',day:'numeric'});}
</script>

@stack('scripts')
</body>
</html>