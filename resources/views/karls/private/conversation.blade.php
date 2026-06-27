@extends('layouts.karls')

@section('karls_content')

{{-- Top bar --}}
<div class="karls-topbar">
  <div style="display:flex;align-items:center;gap:.75rem;">
    <a href="{{ route('karls.inbox') }}"
       style="color:#475569;display:flex;align-items:center;gap:.3rem;text-decoration:none;
              font-size:.8rem;transition:color .15s;flex-shrink:0;"
       onmouseover="this.style.color='#86efac'" onmouseout="this.style.color='#475569'">
      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
        <path d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
      </svg>
    </a>

    <div style="width:34px;height:34px;border-radius:50%;background:#14532d;border:1.5px solid #166534;
                display:flex;align-items:center;justify-content:center;
                font-family:'Playfair Display',serif;font-size:.9rem;font-weight:700;color:#4ade80;flex-shrink:0;">
      {{ strtoupper(substr($user->firstname ?? 'U', 0, 1)) }}
    </div>
    <div>
      <p style="font-size:.88rem;font-weight:700;color:#fff;font-family:'Playfair Display',serif;">
        {{ $user->firstname }} {{ $user->lastname ?? '' }}
      </p>
      <p style="font-size:.68rem;color:#334155;margin-top:.05rem;">Private conversation</p>
    </div>
  </div>

  {{-- 24hr timer chip --}}
  <div style="display:flex;align-items:center;gap:.45rem;padding:.3rem .7rem;
              background:#1c1205;border:1px solid #422006;border-radius:999px;">
    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="#fbbf24" stroke-width="2.5">
      <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <span style="font-size:.65rem;color:#fbbf24;font-family:'JetBrains Mono',monospace;">
      Resets at midnight
    </span>
  </div>
</div>

{{-- Message feed --}}
<div class="karls-feed" id="dm-feed">

  @if($messages->isEmpty())
  <div style="flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;
              gap:.75rem;opacity:.5;">
    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#334155" stroke-width="1.4">
      <path stroke-linecap="round" stroke-linejoin="round"
            d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z"/>
    </svg>
    <div style="text-align:center;">
      <p style="font-size:.85rem;color:#475569;font-weight:500;">No karls yet</p>
      <p style="font-size:.75rem;color:#334155;margin-top:.2rem;">
        Say something to {{ $user->firstname }}!
      </p>
    </div>
  </div>
  @endif

  @foreach($messages as $msg)
  @php $isOwn = $msg->sender_id === Auth::id(); @endphp
  <div class="karl-row {{ $isOwn ? 'own' : '' }}">

    <div class="karl-avatar {{ $isOwn ? 'own-av' : 'named' }}">
      {{ strtoupper(substr($isOwn ? (Auth::user()->firstname ?? 'Y') : ($user->firstname ?? 'U'), 0, 1)) }}
    </div>

    <div class="karl-body">
      <div class="karl-meta">
        <span class="karl-name {{ $isOwn ? 'own' : '' }}">
          {{ $isOwn ? 'You' : $user->firstname }}
        </span>
        <span class="karl-time">{{ $msg->created_at->diffForHumans() }}</span>
        @if(!$isOwn && $msg->viewed_at)
        <span style="font-size:.6rem;color:#334155;font-family:'JetBrains Mono',monospace;">
          · seen
        </span>
        @endif
      </div>

      <div class="karl-bubble {{ $isOwn ? 'mine' : 'theirs' }}">
        {{ $msg->content }}
      </div>

      {{-- Read receipt + expiry note for own messages --}}
      @if($isOwn && $msg->viewed_at)
      <p style="font-size:.62rem;color:#22c55e;margin-top:.2rem;text-align:right;
                font-family:'JetBrains Mono',monospace;">
        ✓ Read · deletes at midnight
      </p>
      @endif
    </div>
  </div>
  @endforeach

  <div id="dm-bottom"></div>
</div>

{{-- Composer --}}
<div class="karls-composer">
  <form method="POST" action="{{ route('karls.dm.send', $user->username) }}" id="dm-form">
    @csrf

    <div class="composer-wrap">
      <textarea name="content" id="dm-input" class="composer-input"
                placeholder="Send a private karl to {{ $user->firstname }}…"
                rows="1" maxlength="800" required
                onkeydown="dmEnter(event)"></textarea>

      <button type="submit"
              style="width:38px;height:38px;border-radius:10px;background:#0c1a2e;
                     border:1px solid #1e3a5f;color:#60a5fa;cursor:pointer;
                     display:flex;align-items:center;justify-content:center;flex-shrink:0;
                     transition:background .15s;"
              onmouseover="this.style.background='#1e3a5f'" onmouseout="this.style.background='#0c1a2e'">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <path stroke-linecap="round" stroke-linejoin="round"
                d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/>
        </svg>
      </button>
    </div>

    <div style="display:flex;align-items:center;justify-content:space-between;margin-top:.45rem;">
      <p style="font-size:.7rem;color:#334155;display:flex;align-items:center;gap:.35rem;">
        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="#334155" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round"
                d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
        </svg>
        Private · only visible to you and {{ $user->firstname }}
      </p>
      <span id="dm-count" style="font-size:.63rem;color:#334155;font-family:'JetBrains Mono',monospace;">
        0/800
      </span>
    </div>
  </form>
</div>

@push('scripts')
<script>
  /* Scroll bottom on load */
  document.getElementById('dm-bottom').scrollIntoView();

  /* Resize textarea */
  const inp = document.getElementById('dm-input');
  const cnt = document.getElementById('dm-count');
  inp.addEventListener('input', () => {
    inp.style.height = 'auto';
    inp.style.height = Math.min(inp.scrollHeight, 120) + 'px';
    cnt.textContent = inp.value.length + '/800';
  });

  function dmEnter(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
      e.preventDefault();
      document.getElementById('dm-form').submit();
    }
  }
</script>
@endpush

@endsection