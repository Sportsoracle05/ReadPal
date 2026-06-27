@extends('layouts.karls')

@section('karls_content')

<div class="karls-topbar">
  <div style="display:flex;align-items:center;gap:.75rem;">
    <a href="javascript:history.back()"
       style="color:#475569;display:flex;align-items:center;gap:.3rem;text-decoration:none;font-size:.8rem;
              transition:color .15s;"
       onmouseover="this.style.color='#86efac'" onmouseout="this.style.color='#475569'">
      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
        <path d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
      </svg>
      Back
    </a>
    <span style="color:#1e293b;">|</span>
    <p style="font-family:'Playfair Display',serif;font-size:.9rem;font-weight:700;color:#fff;">
      {{ $user->firstname }}'s Profile
    </p>
  </div>
  <a href="{{ route('karls.dm', $user->username) }}"
     style="display:inline-flex;align-items:center;gap:.5rem;padding:.5rem 1rem;
            border-radius:10px;background:#0c1a2e;border:1px solid #1e3a5f;
            color:#60a5fa;font-size:.8rem;font-weight:600;text-decoration:none;
            transition:all .15s;"
     onmouseover="this.style.background='#1e3a5f'" onmouseout="this.style.background='#0c1a2e'">
    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
      <path d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
    </svg>
    Send Private Karl
  </a>
</div>

<div class="karls-feed">

  {{-- Profile card --}}
  <div style="background:#0a1020;border:1px solid #1e293b;border-radius:16px;
              padding:1.5rem;display:flex;align-items:center;gap:1rem;margin-bottom:.5rem;">
    <div style="width:52px;height:52px;border-radius:50%;background:#14532d;border:2px solid #166534;
                display:flex;align-items:center;justify-content:center;
                font-family:'Playfair Display',serif;font-size:1.4rem;font-weight:700;color:#4ade80;flex-shrink:0;">
      {{ strtoupper(substr($user->firstname ?? 'U', 0, 1)) }}
    </div>
    <div class="flex-1 min-w-0">
      <p style="font-family:'Playfair Display',serif;font-size:1.1rem;font-weight:700;color:#fff;">
        {{ $user->firstname }} {{ $user->lastname ?? '' }}
      </p>
      <p style="font-size:.75rem;color:#475569;margin-top:.1rem;">
        300L · Sociology · AAUA
      </p>
    </div>
    <div style="text-align:right;">
      <p style="font-family:'JetBrains Mono',monospace;font-size:.7rem;color:#334155;">
        Member since {{ $user->created_at?->format('M Y') }}
      </p>
    </div>
  </div>

  {{-- Recent public karls --}}
  <p style="font-size:.65rem;font-weight:600;letter-spacing:.16em;text-transform:uppercase;
            color:#334155;margin:.25rem 0 .75rem .25rem;">
    Recent Karls
  </p>

  @forelse($publicKarls as $karl)
  <div style="padding:.75rem 1rem;background:#0a1020;border:1px solid #1e293b;border-radius:12px;">
    <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.4rem;">
      <span style="font-size:.7rem;font-family:'JetBrains Mono',monospace;
                   background:#1e293b;color:#334155;padding:.1rem .5rem;border-radius:6px;">
        #{{ $karl->thread->name ?? 'thread' }}
      </span>
      <span style="font-size:.65rem;color:#334155;">{{ $karl->created_at->diffForHumans() }}</span>
    </div>
    <p style="font-size:.875rem;color:#94a3b8;line-height:1.65;">{{ $karl->content }}</p>
  </div>
  @empty
  <div style="text-align:center;padding:3rem 0;opacity:.5;">
    <p style="font-size:.85rem;color:#475569;">No public karls from this user yet.</p>
  </div>
  @endforelse
</div>

@endsection