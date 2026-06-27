{{--
  layouts/karls.blade.php
  Wraps the ReadPal app layout with a dedicated Karls sidebar.
  Uses the same ink/forest-green token set.
--}}
@extends('layouts.app')

@section('page_title', 'Karls')
@section('page_sub', 'Threads · ReadPal')

@section('content')

<style>
  /* ── Karls two-pane shell ──────────────────────────────────── */
  .karls-shell{display:grid;grid-template-columns:220px 1fr;gap:0;
    height:calc(100vh - 120px);min-height:520px;border-radius:16px;
    overflow:hidden;border:1px solid #1e293b;background:#0f172a;}

  /* Left: thread list */
  .karls-sidebar{background:#0a1020;border-right:1px solid #1e293b;
    display:flex;flex-direction:column;overflow:hidden;}
  .karls-sidebar-inner{flex:1;overflow-y:auto;padding:.5rem;}
  .thread-link{display:flex;align-items:center;gap:.55rem;
    padding:.5rem .75rem;border-radius:9px;font-size:.82rem;font-weight:500;
    color:#64748b;text-decoration:none;transition:all .15s;
    border:1px solid transparent;white-space:nowrap;overflow:hidden;}
  .thread-link:hover{color:#86efac;background:rgba(22,163,74,.07);}
  .thread-link.active{color:#4ade80;background:rgba(22,163,74,.11);
    border-color:rgba(22,163,74,.18);}
  .thread-link .thread-icon{width:22px;height:22px;border-radius:6px;
    background:#1e293b;border:1px solid #334155;
    display:flex;align-items:center;justify-content:center;flex-shrink:0;}
  .thread-link .thread-name{flex:1;truncate:true;overflow:hidden;text-overflow:ellipsis;}
  .k-section-label{font-size:.6rem;font-weight:700;letter-spacing:.18em;
    text-transform:uppercase;color:#334155;padding:.6rem .75rem .2rem;}

  /* Right: main content */
  .karls-main{display:flex;flex-direction:column;overflow:hidden;background:#0f172a;}
  .karls-topbar{border-bottom:1px solid #1e293b;padding:.75rem 1.25rem;
    display:flex;align-items:center;justify-content:space-between;flex-shrink:0;}
  .karls-feed{flex:1;overflow-y:auto;padding:1rem 1.25rem;display:flex;flex-direction:column;gap:.75rem;}
  .karls-composer{border-top:1px solid #1e293b;padding:.85rem 1.25rem;flex-shrink:0;}

  /* Karl (message) bubble */
  .karl-row{display:flex;gap:.75rem;align-items:flex-start;
    animation:karlIn .25s ease both;}
  @keyframes karlIn{from{opacity:0;transform:translateY(6px);}to{opacity:1;transform:translateY(0);}}
  .karl-row.own{flex-direction:row-reverse;}

  .karl-avatar{width:32px;height:32px;border-radius:50%;flex-shrink:0;
    display:flex;align-items:center;justify-content:center;
    font-size:.75rem;font-weight:700;font-family:'Playfair Display',serif;}
  .karl-avatar.anon{background:#1e293b;border:1px solid #334155;color:#475569;}
  .karl-avatar.named{background:#14532d;border:1px solid #166534;color:#4ade80;}
  .karl-avatar.own-av{background:#0c1a2e;border:1px solid #1e3a5f;color:#60a5fa;}

  .karl-body{max-width:72%;min-width:0;}
  .karl-meta{display:flex;align-items:center;gap:.5rem;margin-bottom:.2rem;}
  .karl-row.own .karl-meta{flex-direction:row-reverse;}
  .karl-name{font-size:.74rem;font-weight:600;color:#4ade80;}
  .karl-name.anon{color:#475569;}
  .karl-name.own{color:#60a5fa;}
  .karl-name a{text-decoration:none;color:inherit;transition:color .15s;}
  .karl-name a:hover{color:#86efac;}
  .karl-time{font-size:.65rem;color:#334155;font-family:'JetBrains Mono',monospace;}
  .karl-bubble{padding:.55rem .85rem;border-radius:12px;font-size:.855rem;
    line-height:1.65;word-break:break-word;position:relative;}
  .karl-bubble.theirs{background:#1e293b;border:1px solid #334155;color:#cbd5e1;border-top-left-radius:3px;}
  .karl-bubble.mine{background:rgba(22,163,74,.1);border:1px solid rgba(22,163,74,.22);
    color:#cbd5e1;border-top-right-radius:3px;}
  .karl-del-btn{position:absolute;top:4px;right:6px;opacity:0;cursor:pointer;
    background:none;border:none;color:#475569;font-size:.7rem;transition:opacity .15s;}
  .karl-bubble:hover .karl-del-btn{opacity:1;}
  .karl-del-btn:hover{color:#f87171;}

  /* DM profile action */
  .dm-chip{display:inline-flex;align-items:center;gap:.3rem;margin-left:.25rem;
    padding:.1rem .5rem;border-radius:999px;font-size:.63rem;font-weight:600;
    background:rgba(96,165,250,.1);border:1px solid rgba(96,165,250,.25);
    color:#60a5fa;cursor:pointer;text-decoration:none;transition:all .15s;}
  .dm-chip:hover{background:rgba(96,165,250,.2);color:#93c5fd;}

  /* Composer */
  .composer-wrap{display:flex;gap:.65rem;align-items:flex-end;}
  .composer-input{flex:1;background:#1e293b;border:1px solid #334155;border-radius:12px;
    padding:.65rem 1rem;font-family:'DM Sans',sans-serif;font-size:.875rem;color:#f1f5f9;
    resize:none;min-height:42px;max-height:120px;transition:border-color .2s,box-shadow .2s;outline:none;
    line-height:1.5;}
  .composer-input:focus{border-color:#15803d;box-shadow:0 0 0 3px rgba(22,163,74,.1);}
  .composer-input::placeholder{color:#475569;}
  .anon-toggle{display:flex;align-items:center;gap:.4rem;cursor:pointer;
    font-size:.73rem;color:#475569;user-select:none;transition:color .15s;white-space:nowrap;}
  .anon-toggle:hover{color:#94a3b8;}
  .anon-toggle input{accent-color:#15803d;width:13px;height:13px;}

  /* Inbox / DM views */
  .dm-list-item{display:flex;align-items:center;gap:.75rem;padding:.85rem 1rem;
    border-radius:12px;background:#0a1020;border:1px solid #1e293b;
    text-decoration:none;transition:all .18s;cursor:pointer;}
  .dm-list-item:hover{border-color:#334155;background:#0f172a;}
  .dm-list-item.unread{border-color:rgba(22,163,74,.2);background:rgba(22,163,74,.04);}

  /* Mobile stacking */
  @media(max-width:700px){
    .karls-shell{grid-template-columns:1fr;height:auto;}
    .karls-sidebar{display:none;}
    .karls-sidebar.mobile-open{display:flex;position:fixed;inset:0;z-index:50;width:240px;}
    .karl-body{max-width:90%;}
  }
</style>

<div class="karls-shell">

  {{-- ══ THREAD SIDEBAR ════════════════════════════════════ --}}
  <div class="karls-sidebar">

    {{-- Header --}}
    <div style="padding:.75rem 1rem;border-bottom:1px solid #1e293b;flex-shrink:0;">
      <p style="font-family:'Playfair Display',serif;font-size:.9rem;font-weight:700;color:#fff;letter-spacing:-.01em;">
        Karls<span style="color:#4ade80;">·</span>Space
      </p>
      <p style="font-size:.64rem;color:#334155;margin-top:.1rem;">ReadPal Community</p>
    </div>

    <div class="karls-sidebar-inner">

      {{-- DM Inbox link --}}
      <p class="k-section-label">Direct</p>
      <a href="{{ route('karls.inbox') }}"
         class="thread-link {{ request()->routeIs('karls.inbox') || request()->routeIs('karls.dm') ? 'active' : '' }}">
        <div class="thread-icon">
          <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
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

      {{-- Threads --}}
      <p class="k-section-label" style="margin-top:.75rem;">Threads</p>
      @foreach($threads ?? [] as $thread)
      <a href="{{ route('karls.thread', $thread->slug) }}"
         class="thread-link {{ (request()->route('thread') && request()->route('thread')->slug === $thread->slug) ? 'active' : (request()->routeIs('karls.index') && $thread->type === 'general' ? 'active' : '') }}">
        <div class="thread-icon">
          @if($thread->isGeneral())
          <span style="font-size:.65rem;color:#4ade80;font-weight:700;">#</span>
          @else
          <span style="font-size:.65rem;color:#64748b;font-weight:700;">#</span>
          @endif
        </div>
        <span class="thread-name">{{ $thread->name }}</span>
        @if($thread->is_pinned)
        <svg width="9" height="9" viewBox="0 0 24 24" fill="#334155" style="flex-shrink:0;">
          <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
        </svg>
        @endif
      </a>
      @endforeach
    </div>

    {{-- Bottom: my profile --}}
    <div style="padding:.75rem;border-top:1px solid #1e293b;flex-shrink:0;">
      <div style="display:flex;align-items:center;gap:.6rem;padding:.5rem .6rem;
                  background:#1e293b;border-radius:9px;border:1px solid #334155;">
        <div style="width:26px;height:26px;border-radius:50%;background:#14532d;border:1px solid #166534;
                    display:flex;align-items:center;justify-content:center;
                    font-family:'Playfair Display',serif;font-size:.72rem;color:#4ade80;font-weight:700;flex-shrink:0;">
          {{ strtoupper(substr(Auth::user()->firstname ?? 'U', 0, 1)) }}
        </div>
        <div style="flex:1;min-width:0;">
          <p style="font-size:.75rem;font-weight:600;color:#cbd5e1;truncate:true;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
            {{ Auth::user()->firstname ?? 'You' }}
          </p>
          <p style="font-size:.62rem;color:#334155;">Online</p>
        </div>
      </div>
    </div>
  </div>

  {{-- ══ MAIN CONTENT ════════════════════════════════════════ --}}
  <div class="karls-main">
    @yield('karls_content')
  </div>
</div>

@endsection