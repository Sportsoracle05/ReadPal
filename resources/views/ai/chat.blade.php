@extends('ai.layout')

@section('page-title')
    {{ isset($currentBase) ? $currentBase->title : 'Ask Anything' }}
@endsection

@section('head')
    @parent
<style>
/* ─── Mobile Responsiveness (Phones & Tablets) ─── */
@media (max-width: 768px) {
    
    /* 1. Adjust Shell for mobile viewport */
    .chat-shell {
        height: calc(100vh - 56px); /* Ensure navbar doesn't cause overflow */
        display: flex;
        flex-direction: column;
    }

    .chat-feed {
        padding: 15px;
    }

    /* 2. Welcome Screen tweaks */
    .welcome-title {
        font-size: 24px !important;
        margin-top: 15px;
        padding: 0 10px;
    }
    
    .welcome-subtitle {
        font-size: 14px;
        margin-bottom: 25px;
    }

    /* 3. Suggestion Grid: Change from 2x2 to a scrollable row or stacked list */
    .suggestion-grid {
        display: flex !important;
        flex-direction: column; /* Stacked is better for reachability on mobile */
        gap: 10px;
        width: 100%;
        max-width: 100%;
    }

    .suggestion-card {
        padding: 12px !important;
        text-align: left;
        width: 100%;
    }

    .sugg-text {
        font-size: 13px !important;
    }

    /* 4. Input Zone: Fix to bottom and improve touch targets */
    .input-zone {
        padding: 12px 10px env(safe-area-inset-bottom); /* Handles iPhone notches */
        background: var(--ai-bg);
        border-top: 1px solid var(--ai-border);
    }

    .input-container {
        padding: 6px 8px; /* Slimmer for mobile */
        border-radius: 20px; /* More rounded "pill" look */
    }

    .input-box {
        font-size: 16px !important; /* Prevents iOS from auto-zooming on focus */
        max-height: 120px;
    }

    .send-btn {
        width: 36px;
        height: 36px;
        min-width: 36px;
    }

    /* 5. Hide desktop-only hints */
    .input-footer {
        justify-content: center;
        margin-top: 8px;
    }

    .input-hint {
        display: none; /* "Press Enter" isn't relevant for mobile users */
    }

    .context-indicator {
        flex-wrap: wrap;
        justify-content: center;
        font-size: 11px;
        gap: 6px;
    }
}


.chat-shell {
    flex: 1;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    height: 100%;
}

/* ── Feed ─────────────────────────────────────────────────── */
.chat-feed {
    flex: 1;
    overflow-y: auto;
    scroll-behavior: smooth;
    scrollbar-width: thin;
    scrollbar-color: var(--border) transparent;
    padding: 0;
}

.chat-feed::-webkit-scrollbar { width: 3px; }
.chat-feed::-webkit-scrollbar-thumb { background: var(--border); }

/* ── Welcome Screen ─────────────────────────────────────── */
.chat-welcome {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 100%;
    padding: 48px 24px 24px;
    text-align: center;
    animation: riseIn 0.5s ease;
}

@keyframes riseIn {
    from { opacity:0; transform:translateY(20px); }
    to   { opacity:1; transform:translateY(0); }
}

.welcome-orb {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: radial-gradient(circle at 35% 35%, var(--forest-700), var(--forest-950));
    border: 1px solid var(--forest-800);
    display: grid;
    place-items: center;
    margin-bottom: 24px;
    box-shadow: 0 0 40px rgba(34,197,94,0.15), 0 0 80px rgba(34,197,94,0.05);
    animation: orbPulse 4s ease-in-out infinite;
}

@keyframes orbPulse {
    0%, 100% { box-shadow: 0 0 40px rgba(34,197,94,0.15), 0 0 80px rgba(34,197,94,0.05); }
    50%       { box-shadow: 0 0 60px rgba(34,197,94,0.25), 0 0 100px rgba(34,197,94,0.08); }
}

.welcome-title {
    font-family: var(--font-display);
    font-size: 30px;
    color: var(--text);
    margin: 0 0 10px;
    line-height: 1.2;
    letter-spacing: -0.02em;
}

.welcome-title em {
    font-style: italic;
    color: var(--forest-400);
}

.welcome-subtitle {
    font-size: 13.5px;
    color: var(--text-muted);
    max-width: 400px;
    line-height: 1.75;
    margin: 0 0 36px;
}

/* ── Context indicator ─────────────────────────────────── */
.context-indicator {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 12px;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 20px;
    font-family: var(--font-mono);
    font-size: 11px;
    color: var(--text-muted);
    margin-bottom: 28px;
}

.context-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: var(--forest-500);
    animation: contextPulse 2s ease-in-out infinite;
}

@keyframes contextPulse {
    0%,100% { opacity:1; }
    50%      { opacity:0.4; }
}

/* ── Suggestion grid ───────────────────────────────────── */
.suggestion-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 10px;
    max-width: 540px;
    width: 100%;
}

.suggestion-card {
    padding: 14px 16px;
    background: rgba(255,255,255,0.02);
    border: 1px solid var(--border-subtle);
    border-radius: var(--radius);
    text-align: left;
    cursor: pointer;
    transition: all 0.15s ease;
    line-height: 1.5;
    position: relative;
    overflow: hidden;
}

.suggestion-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent, var(--forest-800), transparent);
    opacity: 0;
    transition: opacity 0.2s;
}

.suggestion-card:hover {
    background: rgba(34,197,94,0.04);
    border-color: rgba(34,197,94,0.2);
    transform: translateY(-1px);
    box-shadow: 0 4px 16px rgba(0,0,0,0.3), 0 0 12px rgba(34,197,94,0.06);
}

.suggestion-card:hover::before { opacity: 1; }

.sugg-tag {
    display: block;
    font-family: var(--font-mono);
    font-size: 9.5px;
    font-weight: 600;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: var(--forest-600);
    margin-bottom: 5px;
}

.sugg-text {
    font-size: 12.5px;
    color: var(--text-muted);
}

/* ═══════════════════════════════════════════════════════════
   MESSAGES
═══════════════════════════════════════════════════════════ */
.messages-list {
    padding: 16px 0 8px;
}

.msg-row {
    max-width: 800px;
    margin: 0 auto;
    padding: 6px 20px;
    animation: msgIn 0.2s ease;
}

@keyframes msgIn {
    from { opacity:0; transform:translateY(6px); }
    to   { opacity:1; transform:translateY(0); }
}

/* ── User bubble ────────────────────────────────────────── */
.msg-row.user {
    display: flex;
    justify-content: flex-end;
}

.user-bubble {
    background: rgba(255,255,255,0.04);
    border: 1px solid var(--border);
    border-radius: 14px 14px 3px 14px;
    padding: 11px 16px;
    max-width: 72%;
    font-size: 13.5px;
    line-height: 1.65;
    color: var(--text);
    word-wrap: break-word;
}

/* ── AI message ─────────────────────────────────────────── */
.msg-row.ai {
    display: flex;
    gap: 12px;
    align-items: flex-start;
}

.ai-avatar {
    width: 30px;
    height: 30px;
    border-radius: var(--radius-sm);
    background: var(--forest-950);
    border: 1px solid var(--forest-900);
    display: grid;
    place-items: center;
    flex-shrink: 0;
    margin-top: 3px;
    position: relative;
}

.ai-avatar::after {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: inherit;
    background: radial-gradient(circle at 40% 30%, rgba(34,197,94,0.3), transparent 65%);
}

.ai-body { flex: 1; min-width: 0; }

.ai-text {
    font-size: 13.5px;
    line-height: 1.78;
    color: var(--text);
    word-wrap: break-word;
}

.ai-text p       { margin: 0 0 12px; }
.ai-text p:last-child { margin-bottom: 0; }
.ai-text strong  { color: var(--text); font-weight: 600; }
.ai-text em      { color: var(--text-muted); font-style: italic; }
.ai-text ul      { margin: 6px 0 12px; padding-left: 18px; }
.ai-text li      { margin-bottom: 5px; line-height: 1.65; }
.ai-text li::marker { color: var(--forest-600); }

/* Meta row under AI message */
.ai-meta {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 10px;
    flex-wrap: wrap;
}

.meta-time {
    font-family: var(--font-mono);
    font-size: 10px;
    color: var(--ink-400);
}

.meta-sources {
    font-family: var(--font-mono);
    font-size: 10px;
    color: var(--ink-400);
    display: flex;
    align-items: center;
    gap: 3px;
}

.meta-cached {
    font-family: var(--font-mono);
    font-size: 10px;
    color: var(--info);
    opacity: 0.7;
}

/* ── Typing animation ───────────────────────────────────── */
.typing-row {
    display: flex;
    gap: 12px;
    align-items: center;
    max-width: 800px;
    margin: 0 auto;
    padding: 8px 20px;
}

.typing-dots {
    display: flex;
    gap: 5px;
    align-items: center;
    padding: 10px 14px;
    background: rgba(255,255,255,0.02);
    border: 1px solid var(--border-subtle);
    border-radius: 10px;
}

.typing-dot {
    width: 6px;
    height: 6px;
    background: var(--forest-600);
    border-radius: 50%;
    animation: typingBounce 1.2s ease-in-out infinite;
}
.typing-dot:nth-child(2) { animation-delay: 0.2s; }
.typing-dot:nth-child(3) { animation-delay: 0.4s; }

@keyframes typingBounce {
    0%,100% { transform:translateY(0); opacity:0.4; }
    50%      { transform:translateY(-5px); opacity:1; }
}

.typing-status {
    font-family: var(--font-mono);
    font-size: 11px;
    color: var(--ink-400);
    animation: statusPulse 1.5s ease-in-out infinite;
}

@keyframes statusPulse { 0%,100%{opacity:0.5;} 50%{opacity:1;} }

/* ═══════════════════════════════════════════════════════════
   INPUT AREA
═══════════════════════════════════════════════════════════ */
.input-zone {
    padding: 14px 20px 18px;
    background: rgba(12,17,32,0.7);
    backdrop-filter: blur(8px);
    border-top: 1px solid var(--border-subtle);
    flex-shrink: 0;
}

.input-container {
    max-width: 800px;
    margin: 0 auto;
    position: relative;
}

.no-material-warn {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 14px;
    background: rgba(239,68,68,0.06);
    border: 1px solid rgba(239,68,68,0.18);
    border-radius: var(--radius-sm);
    font-size: 12.5px;
    color: #f87171;
    margin-bottom: 12px;
}

.input-box {
    width: 100%;
    background: rgba(28,38,57,0.9);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 13px 52px 13px 18px;
    font-family: var(--font-body);
    font-size: 13.5px;
    color: var(--text);
    resize: none;
    outline: none;
    min-height: 50px;
    max-height: 160px;
    overflow-y: auto;
    line-height: 1.55;
    transition: border-color 0.15s, box-shadow 0.15s;
    scrollbar-width: thin;
    scrollbar-color: var(--border) transparent;
}

.input-box::placeholder { color: var(--ink-400); }

.input-box:focus {
    border-color: rgba(34,197,94,0.3);
    box-shadow: 0 0 0 3px rgba(34,197,94,0.06), 0 0 20px rgba(34,197,94,0.05);
}

.send-btn {
    position: absolute;
    right: 9px;
    bottom: 9px;
    width: 32px;
    height: 32px;
    background: var(--forest-700);
    border: 1px solid var(--forest-600);
    border-radius: 8px;
    color: white;
    cursor: pointer;
    display: grid;
    place-items: center;
    transition: all 0.15s;
}

.send-btn:hover {
    background: var(--forest-600);
    border-color: var(--forest-500);
    box-shadow: 0 0 12px rgba(34,197,94,0.2);
}

.send-btn:disabled {
    background: var(--surface-3);
    border-color: var(--border);
    cursor: not-allowed;
    box-shadow: none;
}

.input-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 9px;
}

.input-hint {
    font-family: var(--font-mono);
    font-size: 10px;
    color: var(--ink-400);
}

.input-hint kbd {
    display: inline-block;
    padding: 1px 4px;
    background: var(--surface-2);
    border: 1px solid var(--border);
    border-radius: 3px;
    font-family: var(--font-mono);
}

.ai-status-row {
    display: flex;
    align-items: center;
    gap: 6px;
    font-family: var(--font-mono);
    font-size: 10px;
    color: var(--ink-400);
}

.ai-status-dot {
    width: 5px;
    height: 5px;
    border-radius: 50%;
    background: var(--forest-500);
    animation: contextPulse 2s ease-in-out infinite;
}

.input-container.is-locked {
    background: var(--ai-surface-2) !important;
    border: 1px dashed var(--ai-accent-border) !important;
    cursor: pointer;
    padding: 0 !important; /* Remove internal padding to let the trigger fill space */
}

.locked-input-trigger {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    width: 100%;
    height: 48px; /* Matches standard input height */
    color: var(--ai-accent);
    font-size: 14px;
    font-weight: 600;
}

.locked-input-trigger:hover {
    background: var(--ai-accent-bg);
}

.locked-input-trigger span {
    letter-spacing: 0.01em;
}
</style>
@endsection

@section('ai-content')
@php $isPremium = auth()->user()->hasActivePremium(); @endphp
<div class="chat-shell">

    <div class="chat-feed" id="chatFeed">

        {{-- Welcome / Empty --}}
        <div class="chat-welcome" id="welcomeScreen">

            <div class="welcome-orb">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="color:var(--forest-400);position:relative;z-index:1;">
                    <path d="M9.5 2A2.5 2.5 0 0 1 12 4.5v15a2.5 2.5 0 0 1-4.96-.44l-1.4-8.4a4 4 0 0 1 3.86-4.66z"/>
                    <path d="M14.5 2A2.5 2.5 0 0 0 12 4.5v15a2.5 2.5 0 0 0 4.96-.44l1.4-8.4a4 4 0 0 0-3.86-4.66z"/>
                </svg>
            </div>

            <h1 class="welcome-title">Ask your <em>materials</em></h1>

            <p class="welcome-subtitle">
                Your notes get searched first. Then AI shapes the answer
                into something clear and easy to understand.
            </p>

            <div class="context-indicator">
                <span class="context-dot"></span>
                @if(isset($currentBase))
                    <span>Searching: {{ $currentBase->title }}</span>
                @else
                    <span>Searching all your materials</span>
                @endif
                <span>·</span>
                <span>Google enhanced</span>
            </div>

            <div class="suggestion-grid">
                @if(isset($currentBase) && str_contains(strtolower($currentBase->subject ?? ''), 'sociol'))
                <button class="suggestion-card" onclick="suggest(this)">
                    <span class="sugg-tag">Theory</span>
                    <span class="sugg-text">Explain Weber's theory of social action</span>
                </button>
                <button class="suggestion-card" onclick="suggest(this)">
                    <span class="sugg-tag">Classical</span>
                    <span class="sugg-text">What did Durkheim mean by anomie?</span>
                </button>
                <button class="suggestion-card" onclick="suggest(this)">
                    <span class="sugg-tag">Research</span>
                    <span class="sugg-text">Difference between qualitative and quantitative research</span>
                </button>
                <button class="suggestion-card" onclick="suggest(this)">
                    <span class="sugg-tag">Concept</span>
                    <span class="sugg-text">Define social stratification and its types</span>
                </button>
                @else
                <button class="suggestion-card" onclick="suggest(this)">
                    <span class="sugg-tag">Explain</span>
                    <span class="sugg-text">Summarize the key ideas in my materials</span>
                </button>
                <button class="suggestion-card" onclick="suggest(this)">
                    <span class="sugg-tag">Define</span>
                    <span class="sugg-text">What does [concept] mean?</span>
                </button>
                <button class="suggestion-card" onclick="suggest(this)">
                    <span class="sugg-tag">Compare</span>
                    <span class="sugg-text">Compare two approaches from my notes</span>
                </button>
                <button class="suggestion-card" onclick="suggest(this)">
                    <span class="sugg-tag">Review</span>
                    <span class="sugg-text">What are the main arguments in this topic?</span>
                </button>
                @endif
            </div>

        </div>

        {{-- Messages injected here --}}
        <div class="messages-list" id="messagesList" style="display:none;"></div>

    </div>

    {{-- Input --}}
    <div class="input-zone">
        <div class="input-container {{ !$isPremium ? 'is-locked' : '' }}">
            @if(!$isPremium)
                {{-- Locked State --}}
                <div class="locked-input-trigger" onclick="window.location.href='{{ route('payment.plans') }}'">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                    <span>Upgrade to Premium to ask questions</span>
                </div>
            @else
                {{-- Standard Input --}}
                <textarea
                    id="questionInput"
                    class="input-box"
                    placeholder="Ask about your study materials…"
                    rows="1"></textarea>
                <button id="sendBtn" class="send-btn" onclick="sendQuestion()">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <line x1="22" y1="2" x2="11" y2="13"/>
                        <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                    </svg>
                </button>
            @endif
        </div>
        <div class="input-footer">
            <div class="input-hint">
                <kbd>Enter</kbd> send &nbsp;·&nbsp; <kbd>Shift+Enter</kbd> new line
            </div>
            <div class="ai-status-row">
                <span class="ai-status-dot"></span>
                <span id="aiStatusLabel">AI ready</span>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
@if(auth()->user()->is_premium)
<script>
const CSRF   = '{{ csrf_token() }}';
const BASE_ID = '{{ optional($currentBase)->id }}' || null;
let   busy   = false;

// ── Auto-resize ───────────────────────────────────────────────
const inp = document.getElementById('questionInput');

inp.addEventListener('input', function() {
    this.style.height = 'auto';
    this.style.height = Math.min(this.scrollHeight, 160) + 'px';
});

inp.addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendQuestion(); }
});

// ── Suggestion chips ──────────────────────────────────────────
function suggest(btn) {
    inp.value = btn.querySelector('.sugg-text').textContent.trim();
    inp.dispatchEvent(new Event('input'));
    inp.focus();
}

// ── Switch base ───────────────────────────────────────────────
function switchBase(id) {
    window.location.href = id
        ? `{{ route('ai.chat') }}?base=${id}`
        : `{{ route('ai.chat') }}`;
}

// ── Send question ─────────────────────────────────────────────
async function sendQuestion() {
    const q = inp.value.trim();
    if (!q || busy) return;

    // Hide welcome, show messages
    document.getElementById('welcomeScreen').style.display = 'none';
    const list = document.getElementById('messagesList');
    list.style.display = 'block';

    addMessage('user', q);
    inp.value = ''; inp.style.height = 'auto';

    const typingId = addTyping();
    busy = true;
    document.getElementById('sendBtn').disabled = true;
    updateStatus('Searching your materials…');

    try {
        const res = await fetch('{{ route('ai.ask') }}', {
            method:  'POST',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN':CSRF, 'Accept':'application/json' },
            body: JSON.stringify({ question: q, knowledge_base_id: BASE_ID || null }),
        });

        const data = await res.json();
        removeTyping(typingId);

        if (!res.ok) {
            addMessage('ai', data?.message || 'Something went wrong.', { error: true });
        } else {
            updateStatus(providerLabel(data.ai_provider));
            addMessage('ai', data.answer, {
                confidence:   data.confidence,
                provider:     data.ai_provider,
                sources:      data.matched_paragraphs,
                fromCache:    data.from_cache,
            });
        }
    } catch {
        removeTyping(typingId);
        addMessage('ai', 'Connection error. Please check your network.', { error: true });
    }

    busy = false;
    document.getElementById('sendBtn').disabled = false;
    inp.focus();
}

// ── Render message ────────────────────────────────────────────
function addMessage(role, text, meta = {}) {
    const list = document.getElementById('messagesList');
    const div  = document.createElement('div');
    div.className = `msg-row ${role}`;

    if (role === 'user') {
        div.innerHTML = `<div class="user-bubble">${esc(text)}</div>`;
    } else {
        const provider   = meta.provider ? providerBadge(meta.provider) : '';
        const conf       = meta.confidence !== undefined ? confBadge(meta.confidence) : '';
        const sources    = meta.sources > 0
            ? `<span class="meta-sources">◈ ${meta.sources} source${meta.sources>1?'s':''}</span>` : '';
        const cached     = meta.fromCache ? `<span class="meta-cached">⚡ cached</span>` : '';
        const now        = new Date().toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'});

        div.innerHTML = `
            <div class="ai-avatar">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="color:var(--forest-400);position:relative;z-index:1;">
                    <path d="M9.5 2A2.5 2.5 0 0 1 12 4.5v15a2.5 2.5 0 0 1-4.96-.44l-1.4-8.4a4 4 0 0 1 3.86-4.66z"/>
                    <path d="M14.5 2A2.5 2.5 0 0 0 12 4.5v15a2.5 2.5 0 0 0 4.96-.44l1.4-8.4a4 4 0 0 0-3.86-4.66z"/>
                </svg>
            </div>
            <div class="ai-body">
                <div class="ai-text ${meta.error ? 'error-text' : ''}">${formatAnswer(text)}</div>
                <div class="ai-meta">
                    ${conf}${provider}${sources}${cached}
                    <span class="meta-time mono">${now}</span>
                </div>
            </div>`;
    }

    list.appendChild(div);
    scrollBottom();
}

function addTyping() {
    const id   = 'typing-' + Date.now();
    const list = document.getElementById('messagesList');
    const div  = document.createElement('div');
    div.id     = id;
    div.className = 'typing-row';
    div.innerHTML = `
        <div class="ai-avatar" style="margin-top:0;">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="color:var(--forest-400);position:relative;z-index:1;">
                <path d="M9.5 2A2.5 2.5 0 0 1 12 4.5v15a2.5 2.5 0 0 1-4.96-.44l-1.4-8.4a4 4 0 0 1 3.86-4.66z"/>
                <path d="M14.5 2A2.5 2.5 0 0 0 12 4.5v15a2.5 2.5 0 0 0 4.96-.44l1.4-8.4a4 4 0 0 0-3.86-4.66z"/>
            </svg>
        </div>
        <div class="typing-dots">
            <span class="typing-dot"></span>
            <span class="typing-dot"></span>
            <span class="typing-dot"></span>
        </div>
        <span class="typing-status" id="typingStatus">Searching materials…</span>`;
    list.appendChild(div);
    scrollBottom();
    return id;
}

function removeTyping(id) { document.getElementById(id)?.remove(); }

// ── Format AI answer (light markdown → HTML) ──────────────────
function formatAnswer(text) {
    let h = esc(text);
    h = h.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
    h = h.replace(/_(.*?)_/g,       '<em>$1</em>');
    h = h.replace(/^• (.+)$/gm,     '<li>$1</li>');
    h = h.replace(/(<li>[\s\S]*?<\/li>)+/, s => `<ul>${s}</ul>`);
    h = h.replace(/\n\n/g, '</p><p>');
    h = h.replace(/\n/g,   '<br>');
    return `<p>${h}</p>`;
}

// ── Badges ────────────────────────────────────────────────────
function providerBadge(p) {
    const labels = { gemini:'Gemini', groq:'Groq', openrouter:'OpenRouter', huggingface:'HuggingFace', db_only:'DB Only' };
    return `<span class="provider-badge ${p}">${labels[p] || p}</span>`;
}

function confBadge(score) {
    if (!score) return '';
    const lvl   = score>=65?'high':score>=35?'medium':'low';
    const label = score>=65?'Strong':score>=35?'Partial':'Weak';
    return `<span class="conf-badge ${lvl}">${label} match</span>`;
}

function providerLabel(p) {
    return ({ gemini:'Gemini ready', groq:'Groq ready', openrouter:'OpenRouter ready', huggingface:'HuggingFace ready', db_only:'DB search complete' })[p] || 'Ready';
}

function updateStatus(text) {
    const el = document.getElementById('aiStatusLabel');
    if (el) el.textContent = text;
}

// ── Helpers ───────────────────────────────────────────────────
function scrollBottom() {
    const feed = document.getElementById('chatFeed');
    feed.scrollTo({ top: feed.scrollHeight, behavior:'smooth' });
}

function esc(t) {
    const d = document.createElement('div');
    d.appendChild(document.createTextNode(t));
    return d.innerHTML;
}

function loadHistory(id) { /* extend: fetch /ai/conversations/{id} */ }

// Pre-fill from ?q= param
const prefill = '{{ $prefillQuestion ?? '' }}';
if (prefill) { inp.value = prefill; inp.dispatchEvent(new Event('input')); }
</script>
@endif
@endpush
