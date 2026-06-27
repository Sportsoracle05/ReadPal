{{--
    resources/views/ai/history.blade.php (Hybrid v2)
    Forest Green × Ink — SaaS Dashboard
    Now shows which AI provider answered each question
--}}
@extends('ai.layout')

@section('page-title', 'Question History')

@section('topbar-actions')
    <a href="{{ route('ai.chat') }}" class="topbar-btn primary">+ Ask Question</a>
@endsection

@section('head')
    @parent
<style>

.history-page {
    padding: 32px 36px;
    overflow-y: auto;
    height: 100%;
    max-width: 900px;
    scrollbar-width: thin;
    scrollbar-color: var(--border) transparent;
}
.history-page::-webkit-scrollbar { width: 3px; }
.history-page::-webkit-scrollbar-thumb { background: var(--border); }

/* ── Header ──────────────────────────────────────────────── */
.hist-header { margin-bottom: 28px; }

.hist-header h1 {
    font-family: var(--font-display);
    font-size: 26px;
    color: var(--text);
    margin: 0 0 5px;
    letter-spacing: -0.02em;
}

.hist-header p { font-size: 13px; color: var(--text-muted); margin: 0; }

/* ── Filter bar ──────────────────────────────────────────── */
.filter-bar {
    display: flex;
    gap: 8px;
    margin-bottom: 22px;
    flex-wrap: wrap;
}

.filter-search {
    flex: 1;
    min-width: 180px;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    padding: 8px 12px 8px 34px;
    font-family: var(--font-body);
    font-size: 12.5px;
    color: var(--text);
    outline: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='13' height='13' viewBox='0 0 24 24' fill='none' stroke='%234b5563' stroke-width='2'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cline x1='21' y1='21' x2='16.65' y2='16.65'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: 10px center;
    transition: border-color 0.15s;
}
.filter-search::placeholder { color: var(--ink-400); }
.filter-search:focus { border-color: rgba(34,197,94,0.3); outline: none; }

.filter-select {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    padding: 8px 26px 8px 10px;
    font-family: var(--font-mono);
    font-size: 11px;
    color: var(--text-muted);
    outline: none;
    cursor: pointer;
    appearance: none;
    -webkit-appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24' fill='none' stroke='%234b5563' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 8px center;
}
.filter-select:focus { border-color: rgba(34,197,94,0.3); }

/* ── Timeline ────────────────────────────────────────────── */
.date-group { margin-bottom: 24px; }

.date-divider {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 10px;
    font-family: var(--font-mono);
    font-size: 10px;
    font-weight: 500;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: var(--ink-400);
}

.date-divider::after {
    content: '';
    flex: 1;
    height: 1px;
    background: var(--border-subtle);
}

/* ── Conversation card ───────────────────────────────────── */
.hist-card {
    background: rgba(17,24,39,0.8);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    overflow: hidden;
    margin-bottom: 8px;
    transition: border-color 0.14s;
    animation: cardIn 0.25s ease both;
}

@keyframes cardIn {
    from { opacity:0; transform:translateY(5px); }
    to   { opacity:1; transform:translateY(0); }
}

.hist-card:hover { border-color: rgba(34,197,94,0.18); }

/* Card header (always visible) */
.hist-card-head {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    cursor: pointer;
    user-select: none;
    transition: background 0.1s;
}

.hist-card-head:hover { background: rgba(255,255,255,0.02); }

.hist-q-icon {
    width: 28px;
    height: 28px;
    background: var(--surface-2);
    border-radius: var(--radius-sm);
    display: grid;
    place-items: center;
    font-size: 12px;
    flex-shrink: 0;
    color: var(--ink-300);
}

.hist-q-text { flex: 1; min-width: 0; }

.hist-question {
    font-size: 13px;
    font-weight: 500;
    color: var(--text);
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    margin-bottom: 4px;
}

.hist-meta {
    display: flex;
    align-items: center;
    gap: 7px;
    flex-wrap: wrap;
}

.hist-time {
    font-family: var(--font-mono);
    font-size: 10px;
    color: var(--ink-400);
}

.hist-base {
    font-family: var(--font-mono);
    font-size: 9.5px;
    color: var(--ink-400);
    padding: 1px 5px;
    background: var(--surface-2);
    border-radius: 3px;
}

.hist-chevron {
    color: var(--ink-500);
    transition: transform 0.2s;
    flex-shrink: 0;
}

.hist-card.open .hist-chevron { transform: rotate(180deg); }

/* Expanded body */
.hist-body {
    display: none;
    padding: 0 16px 16px;
    border-top: 1px solid var(--border-subtle);
    animation: expandDown 0.18s ease;
}

@keyframes expandDown {
    from { opacity:0; transform:translateY(-4px); }
    to   { opacity:1; transform:translateY(0); }
}

.hist-card.open .hist-body { display: block; }

.hist-answer-label {
    font-family: var(--font-mono);
    font-size: 9.5px;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: var(--forest-700);
    margin: 14px 0 10px;
    display: flex;
    align-items: center;
    gap: 5px;
}

.hist-answer-text {
    font-size: 13px;
    line-height: 1.75;
    color: var(--text-muted);
}

.hist-answer-text p { margin: 0 0 10px; }
.hist-answer-text p:last-child { margin-bottom: 0; }
.hist-answer-text strong { color: var(--text); font-weight: 600; }

/* Keywords row */
.hist-keywords {
    font-family: var(--font-mono);
    font-size: 10px;
    color: var(--ink-500);
    margin-top: 12px;
    padding-top: 10px;
    border-top: 1px solid var(--border-subtle);
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
    align-items: center;
}

.hist-kw-label { color: var(--ink-400); margin-right: 2px; }

.hist-kw-chip {
    padding: 1px 6px;
    background: var(--surface-2);
    border: 1px solid var(--border-subtle);
    border-radius: 3px;
    font-size: 10px;
    color: var(--ink-300);
}

/* Footer row */
.hist-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 14px;
    flex-wrap: wrap;
    gap: 8px;
}

.hist-badges { display: flex; align-items: center; gap: 7px; flex-wrap: wrap; }

.reask-btn {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 5px 11px;
    background: none;
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    font-family: var(--font-mono);
    font-size: 10px;
    color: var(--ink-400);
    cursor: pointer;
    text-decoration: none;
    transition: all 0.14s;
}
.reask-btn:hover { background: var(--surface-2); color: var(--text); border-color: var(--ink-300); }

/* ── Empty ────────────────────────────────────────────────── */
.hist-empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 72px 24px;
    text-align: center;
}

.hist-empty-icon  { font-size: 40px; margin-bottom: 16px; opacity: 0.4; }
.hist-empty-title { font-family: var(--font-display); font-size: 21px; color: var(--text); margin-bottom: 8px; }
.hist-empty-text  { font-size: 13px; color: var(--text-muted); max-width: 280px; line-height: 1.65; margin-bottom: 24px; }

.cta-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 9px 18px;
    background: var(--forest-800);
    border: 1px solid var(--forest-700);
    border-radius: var(--radius-sm);
    color: var(--forest-300);
    font-size: 13px;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.15s;
}
.cta-btn:hover { background: var(--forest-700); color: var(--text); }

/* ── Pagination ───────────────────────────────────────────── */
.pagination-row {
    display: flex;
    justify-content: center;
    gap: 5px;
    margin-top: 28px;
    flex-wrap: wrap;
}

.page-btn {
    min-width: 34px;
    height: 34px;
    padding: 0 8px;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    font-family: var(--font-mono);
    font-size: 11px;
    color: var(--text-muted);
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.14s;
}

.page-btn:hover { background: var(--surface-2); color: var(--text); }

.page-btn.active {
    background: var(--accent-dim);
    border-color: var(--accent-border);
    color: var(--forest-400);
}

.page-btn.disabled { opacity: 0.35; pointer-events: none; }

/* ── Mobile Optimization Fixes ── */
@media (max-width: 768px) {
    .history-page {
        /* Reduce heavy padding that crushes content on small screens */
        padding: 20px 15px !important;
        width: 100% !important;
        max-width: 100vw !important;
        overflow-x: hidden !important;
    }

    /* Fix the "Stretching" Title */
    .hist-question {
        white-space: normal !important; /* Allow text to wrap */
        display: -webkit-box;
        -webkit-line-clamp: 2; /* Limit to 2 lines */
        -webkit-box-orient: vertical;
        overflow: hidden;
        font-size: 14px !important;
        padding-right: 10px;
    }

    /* Fix Meta Badges wrapping */
    .hist-meta {
        gap: 5px !important;
    }
    
    .hist-time, .hist-base, .conf-badge, .provider-badge {
        font-size: 9px !important;
        padding: 1px 4px !important;
    }

    /* Ensure the card header doesn't exceed screen width */
    .hist-card-head {
        padding: 12px 10px !important;
        width: 100% !important;
    }

    /* Fix Footer: Stack buttons to prevent horizontal squeeze */
    .hist-footer {
        flex-direction: column !important;
        align-items: flex-start !important;
        width: 100%;
    }

    .hist-badges {
        width: 100%;
    }

    .reask-btn {
        width: 100%; /* Easy tap target for thumbs */
        justify-content: center;
        padding: 10px !important;
        font-size: 11px !important;
    }

    /* Answer text safety */
    .hist-answer-text {
        font-size: 14px !important;
        word-break: break-word !important; /* Prevents long URLs from breaking layout */
    }

    /* Filter Bar Search Input */
    .filter-bar {
        flex-direction: column;
    }
    .filter-search {
        width: 100% !important;
        min-width: 0 !important;
    }
}

</style>

@endsection

@section('ai-content')
<div class="history-page">

    <div class="hist-header">
        <h1>Question History</h1>
        <p>Every question you've asked — tap any card to see the full answer and which AI answered it.</p>
    </div>

    @if($history->isNotEmpty())
    <div class="filter-bar">
        <input type="text" class="filter-search" placeholder="Filter questions…" oninput="filterCards(this.value)">
    </div>
    @endif

    @if($history->isEmpty())
    <div class="hist-empty">
        <div class="hist-empty-icon">◈</div>
        <h2 class="hist-empty-title">No questions yet</h2>
        <p class="hist-empty-text">Your conversation history will appear here once you start asking.</p>
        <a href="{{ route('ai.chat') }}" class="cta-btn">Ask your first question →</a>
    </div>

    @else

    {{-- Group by date --}}
    @php
        $grouped = $history->getCollection()->groupBy(fn($c) => $c->created_at->format('Y-m-d'));
    @endphp

    <div id="historyList">

        @foreach($grouped as $date => $conversations)
        <div class="date-group">
            <div class="date-divider">
                {{ \Carbon\Carbon::parse($date)->isToday() ? 'Today'
                    : (\Carbon\Carbon::parse($date)->isYesterday() ? 'Yesterday'
                    : \Carbon\Carbon::parse($date)->format('D, M j Y')) }}
            </div>

            @foreach($conversations as $conv)
            @php
                $score     = $conv->confidence_score ?? 0;
                $confLevel = $score >= 65 ? 'high' : ($score >= 35 ? 'medium' : 'low');
                $provider  = $conv->ai_provider ?? 'db_only';
            @endphp

            <div class="hist-card"
                 data-question="{{ strtolower($conv->question) }}"
                 data-conf="{{ $confLevel }}"
                 data-provider="{{ $provider }}"
                 style="animation-delay:{{ $loop->index * 0.03 }}s">

                <div class="hist-card-head" onclick="toggleCard(this.parentElement)">
                    <div class="hist-q-icon">?</div>
                    <div class="hist-q-text">
                        <div class="hist-question">{{ $conv->question }}</div>
                        <div class="hist-meta">
                            <span class="hist-time mono">{{ $conv->created_at->format('g:i A') }}</span>
                            @if($conv->knowledgeBase)
                            <span class="hist-base">{{ Str::limit($conv->knowledgeBase->title, 18) }}</span>
                            @endif
                            @if($score > 0)
                            <span class="conf-badge {{ $confLevel }}">
                                {{ $score >= 65 ? 'Strong' : ($score >= 35 ? 'Partial' : 'Weak') }}
                            </span>
                            @endif
                            <span class="provider-badge {{ $provider }}">
                                {{ ['gemini'=>'Gemini','groq'=>'Groq','openrouter'=>'OpenRouter','huggingface'=>'HF','db_only'=>'DB'][$provider] ?? $provider }}
                            </span>
                        </div>
                    </div>
                    <svg class="hist-chevron" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <polyline points="6 9 12 15 18 9"/>
                    </svg>
                </div>

                <div class="hist-body">
                    <div class="hist-answer-label">◈ Answer</div>
                    <div class="hist-answer-text">{!! nl2br(e($conv->answer)) !!}</div>

                    @if(!empty($conv->search_keywords))
                    <div class="hist-keywords">
                        <span class="hist-kw-label">KEYWORDS</span>
                        @foreach(explode(', ', $conv->search_keywords) as $kw)
                        <span class="hist-kw-chip">{{ $kw }}</span>
                        @endforeach
                    </div>
                    @endif

                    <div class="hist-footer">
                        <div class="hist-badges">
                            @if($conv->from_cache ?? false)
                            <span class="provider-badge" style="background:rgba(59,130,246,0.1);color:#60a5fa;border-color:rgba(59,130,246,0.25);">⚡ cached</span>
                            @endif
                            @if(!empty($conv->matched_paragraph_ids))
                            <span class="conf-badge medium">{{ count($conv->matched_paragraph_ids) }} source{{ count($conv->matched_paragraph_ids)>1?'s':'' }}</span>
                            @endif
                            @if(($conv->tokens_used ?? 0) > 0)
                            <span class="hist-time mono">{{ $conv->tokens_used }} tokens</span>
                            @endif
                        </div>
                        <a href="{{ route('ai.chat') }}?q={{ urlencode($conv->question) }}{{ $conv->knowledge_base_id ? '&base='.$conv->knowledge_base_id : '' }}"
                           class="reask-btn">↩ Ask again</a>
                    </div>
                </div>

            </div>
            @endforeach
        </div>
        @endforeach

    </div>

    {{-- Pagination --}}
    @if($history->hasPages())
    <div class="pagination-row">
        <a class="page-btn {{ $history->onFirstPage() ? 'disabled' : '' }}"
           href="{{ $history->previousPageUrl() }}">←</a>

        @foreach($history->getUrlRange(1, $history->lastPage()) as $page => $url)
        <a href="{{ $url }}" class="page-btn {{ $page === $history->currentPage() ? 'active' : '' }}">{{ $page }}</a>
        @endforeach

        <a class="page-btn {{ !$history->hasMorePages() ? 'disabled' : '' }}"
           href="{{ $history->nextPageUrl() }}">→</a>
    </div>
    @endif

    @endif

</div>
@endsection

@push('scripts')
<script>
function toggleCard(card) { card.classList.toggle('open'); }

function filterCards(q) {
    q = q.toLowerCase().trim();
    document.querySelectorAll('.hist-card').forEach(c => {
        const match = !q || (c.dataset.question || '').includes(q);
        c.style.display = match ? '' : 'none';
    });
    toggleEmptyGroups();
}

function filterByConf(lvl) {
    document.querySelectorAll('.hist-card').forEach(c => {
        c.style.display = !lvl || c.dataset.conf === lvl ? '' : 'none';
    });
    toggleEmptyGroups();
}

function filterByProvider(p) {
    document.querySelectorAll('.hist-card').forEach(c => {
        c.style.display = !p || c.dataset.provider === p ? '' : 'none';
    });
    toggleEmptyGroups();
}

function toggleEmptyGroups() {
    document.querySelectorAll('.date-group').forEach(g => {
        const hasVisible = [...g.querySelectorAll('.hist-card')].some(c => c.style.display !== 'none');
        g.style.display = hasVisible ? '' : 'none';
    });
}

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') document.querySelectorAll('.hist-card.open').forEach(c => c.classList.remove('open'));
});
</script>
@endpush
