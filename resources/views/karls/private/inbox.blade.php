@extends('layouts.karls')

@section('karls_content')

<div class="karls-topbar">
  <div>
    <p style="font-family:'Playfair Display',serif;font-size:.9rem;font-weight:700;color:#fff;">
      Private Inbox
    </p>
    <p style="font-size:.7rem;color:#475569;margin-top:.05rem;">
      All karls reset every 24 hours once read ·
      <span style="color:#334155;font-family:'JetBrains Mono',monospace;">
        {{ now()->format('D, M j') }}
      </span>
    </p>
  </div>
  @if(($unreadCount ?? 0) > 0)
  <div style="display:flex;align-items:center;gap:.5rem;padding:.35rem .75rem;
              background:#052e16;border:1px solid #14532d;border-radius:999px;">
    <div style="width:6px;height:6px;border-radius:50%;background:#22c55e;
                animation:ping 2s ease-in-out infinite;"></div>
    <span style="font-size:.72rem;color:#4ade80;font-weight:600;">
      {{ $unreadCount }} unread
    </span>
  </div>
  @endif
</div>

<div class="karls-feed">

  {{-- 24hr reset info banner --}}
  <div style="padding:.7rem 1rem;background:rgba(212,168,83,.06);border:1px solid rgba(212,168,83,.15);
              border-radius:10px;display:flex;gap:.6rem;align-items:flex-start;">
    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#d4a853" stroke-width="2" style="flex-shrink:0;margin-top:.1rem;">
      <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <p style="font-size:.76rem;color:#a07830;line-height:1.6;">
      Private karls auto-delete daily at midnight. Unread karls persist; once you view a conversation,
      those karls are cleared on the next reset.
    </p>
  </div>

  @if($conversations->isEmpty())
  <div style="flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:.75rem;opacity:.45;padding:3rem 0;">
    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#334155" stroke-width="1.3">
      <path stroke-linecap="round" stroke-linejoin="round"
            d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
    </svg>
    <div style="text-align:center;">
      <p style="font-size:.88rem;color:#475569;font-weight:500;">No private karls yet</p>
      <p style="font-size:.75rem;color:#334155;margin-top:.25rem;">
        Click DM on someone's karl in a thread to start a conversation.
      </p>
    </div>
  </div>
  @else
  <div style="display:flex;flex-direction:column;gap:.6rem;">
    @foreach($conversations as $convo)
    @php $partner = $convo['user']; @endphp
    <a href="{{ route('karls.dm', $partner->username) }}" class="dm-list-item {{ $convo['unread'] > 0 ? 'unread' : '' }}">

      {{-- Avatar --}}
      <div style="width:40px;height:40px;border-radius:50%;flex-shrink:0;
                  background:{{ $convo['unread'] > 0 ? '#14532d' : '#1e293b' }};
                  border:1.5px solid {{ $convo['unread'] > 0 ? '#166534' : '#334155' }};
                  display:flex;align-items:center;justify-content:center;
                  font-family:'Playfair Display',serif;font-weight:700;
                  color:{{ $convo['unread'] > 0 ? '#4ade80' : '#475569' }};font-size:.95rem;">
        {{ strtoupper(substr($partner->firstname ?? 'U', 0, 1)) }}
      </div>

      {{-- Content --}}
      <div style="flex:1;min-width:0;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.2rem;">
          <p style="font-size:.84rem;font-weight:{{ $convo['unread'] > 0 ? '700' : '500' }};
                    color:{{ $convo['unread'] > 0 ? '#f1f5f9' : '#94a3b8' }};">
            {{ $partner->firstname }} {{ $partner->lastname ?? '' }}
          </p>
          <span style="font-size:.65rem;color:#334155;font-family:'JetBrains Mono',monospace;flex-shrink:0;">
            {{ $convo['latest']?->created_at->diffForHumans() ?? '' }}
          </span>
        </div>
        <p style="font-size:.78rem;color:{{ $convo['unread'] > 0 ? '#64748b' : '#334155' }};
                  white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
          {{ $convo['preview'] ?? 'Start of conversation' }}
        </p>
      </div>

      {{-- Unread badge --}}
      @if($convo['unread'] > 0)
      <div style="width:20px;height:20px;border-radius:50%;background:#14532d;border:1px solid #166534;
                  display:flex;align-items:center;justify-content:center;flex-shrink:0;">
        <span style="font-size:.62rem;color:#4ade80;font-weight:700;">{{ $convo['unread'] }}</span>
      </div>
      @endif
    </a>
    @endforeach
  </div>
  @endif
</div>

@endsection