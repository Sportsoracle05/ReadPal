@extends('layouts.karls')

@section('karls_content')

{{-- Top bar --}}
<div class="karls-topbar">
  <div class="flex items-center gap-2.5">
    <span style="font-size:1.1rem;color:#4ade80;font-weight:700;">#</span>
    <div>
      <p style="font-size:.9rem;font-weight:700;color:#fff;font-family:'Playfair Display',serif;">
        {{ $thread->name }}
        @if($thread->is_pinned)
        <span style="font-size:.62rem;font-family:'DM Sans',sans-serif;color:#334155;font-weight:500;
                     background:#1e293b;padding:.1rem .4rem;border-radius:4px;margin-left:.3rem;">pinned</span>
        @endif
      </p>
      @if($thread->description)
      <p style="font-size:.72rem;color:#475569;margin-top:.05rem;">{{ $thread->description }}</p>
      @endif
    </div>
  </div>
  <div style="display:flex;align-items:center;gap:1rem;">
    <span style="font-size:.72rem;color:#334155;font-family:'JetBrains Mono',monospace;">
      {{ $karls->total() }} karls
    </span>
    <div style="display:flex;align-items:center;gap:.4rem;">
      <div style="width:6px;height:6px;border-radius:50%;background:#22c55e;
                  box-shadow:0 0 5px rgba(34,197,94,.7);animation:ping 2s ease-in-out infinite;"></div>
      <span style="font-size:.7rem;color:#22c55e;font-weight:600;">Live</span>
    </div>
  </div>
</div>

{{-- Feed --}}
<div class="karls-feed" id="feed">

  @if($karls->isEmpty())
  <div style="flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:.75rem;opacity:.5;">
    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#334155" stroke-width="1.4">
      <path stroke-linecap="round" stroke-linejoin="round"
            d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z"/>
    </svg>
    <p style="font-size:.85rem;color:#475569;">Be the first to drop a karl here!</p>
  </div>
  @endif

  {{-- Paginator (older messages) --}}
  @if($karls->hasMorePages())
  <div style="text-align:center;margin-bottom:.5rem;">
    <a href="{{ $karls->nextPageUrl() }}"
       style="font-size:.75rem;color:#334155;text-decoration:none;padding:.4rem .8rem;
              background:#1e293b;border:1px solid #334155;border-radius:8px;
              hover:color:#4ade80;transition:color .15s;">
      Load older karls ↑
    </a>
  </div>
  @endif

  {{-- Karls --}}
  @foreach($karls->reverse() as $karl)
  @php
    $isOwn = $karl->user_id === Auth::id();
    $isAnon = $karl->is_anonymous;
  @endphp
  <div class="karl-row {{ $isOwn ? 'own' : '' }}" data-karl-id="{{ $karl->id }}">

    {{-- Avatar --}}
    <div class="karl-avatar {{ $isOwn ? 'own-av' : ($isAnon ? 'anon' : 'named') }}">
      @if($isAnon)
        ?
      @else
        {{ strtoupper(substr($karl->display_name, 0, 1)) }}
      @endif
    </div>

    {{-- Body --}}
    <div class="karl-body">
      <div class="karl-meta">
        <span class="karl-name {{ $isOwn ? 'own' : ($isAnon ? 'anon' : '') }}">
          @if($isOwn)
            You
          @elseif($isAnon)
            Anonymous
          @else
            <a href="{{ route('karls.user.profile', $karl->user->username) }}">{{ $karl->display_name }}</a>
            {{-- DM chip for non-anon, non-self --}}
            <a href="{{ route('karls.dm', $karl->user->username) }}" class="dm-chip">
              <svg width="8" height="8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
              </svg>
              DM
            </a>
          @endif
        </span>
        <span class="karl-time">{{ $karl->created_at->diffForHumans() }}</span>
      </div>

      <div class="karl-bubble {{ $isOwn ? 'mine' : 'theirs' }}">
        {{ $karl->content }}
        @if($isOwn)
        <form method="POST" action="{{ route('karls.karl.delete', $karl->id) }}" style="display:inline;">
          @csrf @method('DELETE')
          <button type="submit" class="karl-del-btn" title="Delete karl"
                  onclick="return confirm('Delete this karl?')">✕</button>
        </form>
        @endif
      </div>
    </div>
  </div>
  @endforeach

  {{-- Live-appended karls land here --}}
  <div id="live-feed"></div>

  {{-- Scroll anchor --}}
  <div id="feed-bottom"></div>
</div>

{{-- Composer --}}
<div class="karls-composer">
  <form method="POST" action="{{ route('karls.post', $thread->slug) }}" id="karl-form">
    @csrf

    <div class="composer-wrap">
      <textarea name="content" id="karl-input" class="composer-input"
                placeholder="Drop a karl in #{{ $thread->name }}…"
                rows="1" maxlength="1000" required
                onkeydown="handleEnter(event)"></textarea>

      <button type="submit"
              style="width:38px;height:38px;border-radius:10px;background:#166534;border:1px solid #15803d;
                     color:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;
                     flex-shrink:0;transition:background .15s;"
              onmouseover="this.style.background='#15803d'"
              onmouseout="this.style.background='#166534'">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/>
        </svg>
      </button>
    </div>

    <div style="display:flex;align-items:center;justify-content:space-between;margin-top:.55rem;">
      <label class="anon-toggle">
        <input type="checkbox" name="is_anonymous" value="1"
               id="anon-toggle" {{ old('is_anonymous') ? 'checked':'' }}/>
        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
        </svg>
        Post anonymously
      </label>
      <span id="char-count" style="font-size:.65rem;color:#334155;font-family:'JetBrains Mono',monospace;">
        0/1000
      </span>
    </div>
  </form>
</div>

@push('scripts')
<script>
/* ── Auto-resize textarea ─────────────────────────────────── */
const input   = document.getElementById('karl-input');
const counter = document.getElementById('char-count');

input.addEventListener('input', () => {
  input.style.height = 'auto';
  input.style.height = Math.min(input.scrollHeight, 120) + 'px';
  counter.textContent = input.value.length + '/1000';
});

/* ── Send on Enter (Shift+Enter = newline) ────────────────── */
function handleEnter(e) {
  if (e.key === 'Enter' && !e.shiftKey) {
    e.preventDefault();
    document.getElementById('karl-form').submit();
  }
}

/* ── Scroll to bottom on load ─────────────────────────────── */
const feed = document.getElementById('feed');
function scrollBottom() {
  const bottom = document.getElementById('feed-bottom');
  if (bottom) bottom.scrollIntoView({ behavior: 'smooth' });
}
scrollBottom();

window.onload = function() {
  setTimeout(() => {
    const bottom = document.getElementById('feed-bottom');
    if (bottom) bottom.scrollIntoView({ behavior: 'auto' });
  }, 100);
};


/* ── Live poll: fetch new karls every 15s ─────────────────── */
let lastId = {{ $karls->isEmpty() ? 0 : $karls->max('id') }}; 
const liveFeed  = document.getElementById('live-feed');
const pollUrl   = "{{ route('karls.poll', $thread->slug) }}";
const authId    = {{ Auth::id() }};

function buildKarlHtml(k) {
  const isOwn   = k.is_own;
  const isAnon  = k.is_anonymous;
  const avatarClass = isOwn ? 'own-av' : (isAnon ? 'anon' : 'named');
  const nameHtml = isOwn
    ? `<span class="karl-name own">You</span>`
    : isAnon
    ? `<span class="karl-name anon">Anonymous</span>`
    : `<span class="karl-name">
         <a href="/karls/user/${k.user_id}">${k.display_name}</a>
         <a href="/karls/dm/${k.user_id}" class="dm-chip">✉ DM</a>
       </span>`;

  return `
    <div class="karl-row ${isOwn ? 'own' : ''}" data-karl-id="${k.id}">
      <div class="karl-avatar ${avatarClass}">${k.initial}</div>
      <div class="karl-body">
        <div class="karl-meta">
          ${nameHtml}
          <span class="karl-time">${k.time}</span>
        </div>
        <div class="karl-bubble ${isOwn ? 'mine' : 'theirs'}">
          ${k.content}
        </div>
      </div>
    </div>`;
}

async function pollKarls() {
  try {
    const res  = await fetch(`${pollUrl}?since=${lastId}`);
    const data = await res.json();
    if (data.karls.length > 0) {
    data.karls.forEach(k => {
        const html = buildKarlHtml(k);
        liveFeed.insertAdjacentHTML('beforeend', html); // Put it at the very bottom
    });
    lastId = data.last_id;
    scrollBottom(); // Scroll down to see the new message
}
  } catch(e) { /* silent */ }
}

setInterval(pollKarls, 15000);

/* ── Anon toggle visual feedback ─────────────────────────── */
document.getElementById('anon-toggle').addEventListener('change', function() {
  const label = this.closest('.anon-toggle');
  label.style.color = this.checked ? '#4ade80' : '#475569';
});
</script>
@endpush

@endsection