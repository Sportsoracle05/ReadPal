{{--
    resources/views/assignments/workspace.blade.php
    The main assignment writing workspace — mobile-first, AI-powered
--}}
@extends('layouts.app')

@section('head')
<link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=JetBrains+Mono:wght@400;500&family=Geist:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
/* ═══════════════════════════════════════════════════════════
   ASSIGNMENT WORKSPACE — Mobile-first
   Forest Green × Ink SaaS
═══════════════════════════════════════════════════════════ */
:root{
    --ink-950:#030712;--ink-900:#0c1120;--ink-800:#111827;--ink-700:#1c2639;
    --ink-600:#243044;--ink-500:#374151;--ink-400:#4b5563;--ink-300:#6b7280;
    --ink-200:#9ca3af;--ink-100:#d1d5db;
    --forest-950:#052e16;--forest-900:#14532d;--forest-800:#166534;
    --forest-700:#15803d;--forest-600:#16a34a;--forest-500:#22c55e;--forest-400:#4ade80;
    --forest-300:#86efac;
    --bg:var(--ink-950);--surface:var(--ink-900);--surface-2:var(--ink-800);--surface-3:var(--ink-700);
    --border:var(--ink-600);--border-sub:var(--ink-700);
    --text:#f0f4f8;--text-m:var(--ink-200);--text-d:var(--ink-300);
    --accent:var(--forest-500);--accent-dim:rgba(34,197,94,.10);
    --accent-border:rgba(34,197,94,.22);
    --danger:#ef4444;
    --font-d:'Instrument Serif',Georgia,serif;
    --font-b:'Geist',system-ui,sans-serif;
    --font-m:'JetBrains Mono',monospace;
    --r:10px;--r-sm:6px;--r-lg:14px;
    --shadow:0 8px 32px rgba(0,0,0,.5)
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{background:var(--bg);color:var(--text);font-family:var(--font-b);min-height:100vh}

/* ─── TOP BAR ──────────────────────────────────────────────── */
.ws-topbar{
    position:sticky;top:0;z-index:50;
    background:rgba(12,17,32,.90);backdrop-filter:blur(10px);
    border-bottom:1px solid var(--border);
    padding:10px 14px;
    display:flex;align-items:center;gap:10px
}

.ws-back{
    width:34px;height:34px;flex-shrink:0;
    background:none;border:1px solid var(--border);border-radius:var(--r-sm);
    color:var(--text-m);cursor:pointer;text-decoration:none;
    display:grid;place-items:center;transition:all .14s;font-size:16px
}
.ws-back:hover{background:var(--surface-2);color:var(--text)}

.ws-title-block{flex:1;min-width:0}
.ws-title{
    font-family:var(--font-d);font-size:16px;letter-spacing:-.01em;
    overflow:hidden;text-overflow:ellipsis;white-space:nowrap
}
.ws-course{
    font-family:var(--font-m);font-size:10px;color:var(--text-d)
}

.ws-topbar-actions{display:flex;align-items:center;gap:7px;flex-shrink:0}

/* Save status indicator */
.save-status{
    font-family:var(--font-m);font-size:10px;
    display:flex;align-items:center;gap:4px;
    color:var(--text-d);transition:color .3s
}
.save-status.saving{color:var(--forest-600)}
.save-status.saved{color:var(--forest-500)}
.save-status.error{color:#f87171}
.save-dot{width:5px;height:5px;border-radius:50%;background:currentColor}

/* ─── PROGRESS BAR ─────────────────────────────────────────── */
.progress-bar-wrap{
    background:var(--surface);border-bottom:1px solid var(--border-sub);
    padding:10px 14px
}
.progress-label-row{
    display:flex;justify-content:space-between;align-items:center;
    margin-bottom:7px;font-family:var(--font-m);font-size:10px;color:var(--text-d)
}
.progress-pct{color:var(--forest-400);font-weight:600}
.progress-track{height:5px;background:var(--surface-2);border-radius:3px;overflow:hidden}
.progress-fill{height:100%;background:linear-gradient(90deg,var(--forest-800),var(--forest-500));border-radius:3px;transition:width .5s ease}

/* ─── SECTIONS NAV (horizontal scroll on mobile) ──────────── */
.sections-nav{
    background:var(--surface);border-bottom:1px solid var(--border-sub);
    overflow-x:auto;display:flex;gap:0;
    scrollbar-width:none;-ms-overflow-style:none
}
.sections-nav::-webkit-scrollbar{display:none}

.nav-tab{
    padding:10px 16px;font-size:12px;font-family:var(--font-m);
    color:var(--text-d);cursor:pointer;border:none;background:none;
    white-space:nowrap;position:relative;transition:color .15s;
    border-bottom:2px solid transparent;flex-shrink:0
}
.nav-tab:hover{color:var(--text)}
.nav-tab.active{color:var(--forest-400);border-bottom-color:var(--forest-500)}
.nav-tab .tab-done{
    display:inline-block;width:6px;height:6px;border-radius:50%;
    background:var(--forest-500);margin-left:5px;vertical-align:middle;flex-shrink:0
}

/* ─── MAIN CONTENT AREA ─────────────────────────────────────── */
.ws-body{
    max-width:720px;margin:0 auto;padding:16px 14px 120px
}

/* ─── SECTION CARD ──────────────────────────────────────────── */
.section-card{
    display:none;/* hidden by default, shown by JS */
    animation:sectionIn .2s ease
}
.section-card.active{display:block}

@keyframes sectionIn{from{opacity:0;transform:translateY(6px)}to{opacity:1;transform:translateY(0)}}

.section-head{
    display:flex;align-items:flex-start;justify-content:space-between;
    gap:12px;margin-bottom:14px
}

.section-title-text{
    font-family:var(--font-d);font-size:22px;letter-spacing:-.01em;
    color:var(--text);line-height:1.2
}

.section-num-badge{
    font-family:var(--font-m);font-size:10px;font-weight:600;
    background:var(--accent-dim);border:1px solid var(--accent-border);
    border-radius:3px;padding:3px 8px;color:var(--forest-400);
    flex-shrink:0;white-space:nowrap
}

/* Questions accordion */
.questions-accordion{
    background:rgba(34,197,94,.04);
    border:1px solid rgba(34,197,94,.12);
    border-radius:var(--r-sm);
    margin-bottom:14px;overflow:hidden
}

.questions-toggle{
    display:flex;align-items:center;justify-content:space-between;
    padding:10px 14px;cursor:pointer;user-select:none;
    font-family:var(--font-m);font-size:11px;font-weight:600;
    letter-spacing:.06em;text-transform:uppercase;color:var(--forest-600);
    background:none;border:none;width:100%;text-align:left;transition:color .14s
}
.questions-toggle:hover{color:var(--forest-400)}

.questions-chevron{transition:transform .2s;font-size:12px}
.questions-accordion.open .questions-chevron{transform:rotate(180deg)}

.questions-body{
    display:none;padding:0 14px 12px;
    border-top:1px solid rgba(34,197,94,.1)
}
.questions-accordion.open .questions-body{display:block}

.question-item{
    display:flex;align-items:flex-start;gap:8px;
    padding:8px 0;border-bottom:1px solid rgba(255,255,255,.04);
    font-size:13px;color:var(--text-m);line-height:1.6
}
.question-item:last-child{border-bottom:none;padding-bottom:0}
.question-num{
    font-family:var(--font-m);font-size:10px;color:var(--forest-700);
    background:var(--forest-950);border:1px solid var(--forest-900);
    border-radius:3px;padding:1px 5px;flex-shrink:0;margin-top:1px
}

/* Guidance note */
.guidance-note{
    font-size:12.5px;color:var(--text-d);font-style:italic;
    padding:8px 12px;background:rgba(255,255,255,.02);
    border-left:2px solid var(--forest-900);border-radius:0 var(--r-sm) var(--r-sm) 0;
    margin-bottom:14px;line-height:1.6
}

/* ─── EDITOR ──────────────────────────────────────────────── */
.editor-wrap{position:relative;margin-bottom:12px}

.section-editor{
    width:100%;min-height:180px;
    background:rgba(28,38,57,.7);
    border:1px solid var(--border);border-radius:var(--r);
    padding:14px 16px;
    font-family:var(--font-b);font-size:14px;color:var(--text);
    line-height:1.75;resize:vertical;outline:none;
    transition:border-color .15s,box-shadow .15s
}
.section-editor::placeholder{color:var(--ink-400)}
.section-editor:focus{
    border-color:rgba(34,197,94,.25);
    box-shadow:0 0 0 3px rgba(34,197,94,.05)
}

/* word counter */
.editor-footer{
    display:flex;align-items:center;justify-content:space-between;
    font-family:var(--font-m);font-size:10px;color:var(--text-d);
    padding:0 2px
}
.word-count{transition:color .3s}
.word-count.good{color:var(--forest-500)}

/* ─── AI ACTION BUTTONS ───────────────────────────────────── */
.ai-actions{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:8px}

.ai-btn{
    flex:1;min-width:0;
    display:flex;align-items:center;justify-content:center;gap:7px;
    padding:11px 14px;border-radius:var(--r-sm);
    font-family:var(--font-b);font-size:13px;font-weight:500;
    cursor:pointer;border:1px solid;transition:all .15s;
    white-space:nowrap;overflow:hidden;text-overflow:ellipsis;
    min-height:44px; /* touch-friendly */
    position:relative
}

.ai-btn-generate{
    background:var(--forest-900);border-color:var(--forest-800);
    color:var(--forest-300)
}
.ai-btn-generate:hover{background:var(--forest-800);border-color:var(--forest-700);color:var(--text)}

.ai-btn-improve{
    background:var(--accent-dim);border-color:var(--accent-border);
    color:var(--forest-400)
}
.ai-btn-improve:hover{background:rgba(34,197,94,.16);color:var(--forest-300)}

.ai-btn:disabled{
    opacity:.5;cursor:not-allowed;transform:none !important
}

/* spinner inside button */
.btn-spinner{
    width:14px;height:14px;border:2px solid currentColor;border-top-color:transparent;
    border-radius:50%;animation:spin .7s linear infinite;flex-shrink:0
}
@keyframes spin{to{transform:rotate(360deg)}}

/* AI result preview */
.ai-result-preview{
    background:rgba(34,197,94,.04);
    border:1px solid rgba(34,197,94,.15);
    border-radius:var(--r-sm);
    padding:14px 16px;margin-bottom:12px;
    font-size:13px;color:var(--text-m);line-height:1.75;
    display:none;position:relative
}

.ai-result-preview.visible{display:block;animation:fadeIn .2s ease}
@keyframes fadeIn{from{opacity:0}to{opacity:1}}

.ai-result-label{
    font-family:var(--font-m);font-size:9.5px;letter-spacing:.1em;
    text-transform:uppercase;color:var(--forest-700);margin-bottom:8px;
    display:flex;align-items:center;gap:5px
}
.ai-result-text{white-space:pre-wrap}

.ai-result-actions{
    display:flex;gap:8px;margin-top:12px;padding-top:10px;
    border-top:1px solid rgba(34,197,94,.1)
}
.ai-result-btn{
    padding:7px 13px;border-radius:var(--r-sm);
    font-family:var(--font-b);font-size:12px;font-weight:500;
    cursor:pointer;border:1px solid;transition:all .14s
}
.ai-result-btn.accept{
    background:var(--forest-800);border-color:var(--forest-700);color:var(--forest-300)
}
.ai-result-btn.accept:hover{background:var(--forest-700);color:var(--text)}
.ai-result-btn.discard{
    background:none;border-color:var(--border);color:var(--text-d)
}
.ai-result-btn.discard:hover{background:var(--surface-2)}

/* provider tag */
.provider-tag{
    font-family:var(--font-m);font-size:9.5px;padding:1px 6px;
    border-radius:2px;border:1px solid
}
.provider-tag.gemini{background:rgba(66,133,244,.1);color:#60a5fa;border-color:rgba(66,133,244,.2)}
.provider-tag.groq{background:rgba(245,158,11,.1);color:#fbbf24;border-color:rgba(245,158,11,.2)}
.provider-tag.openrouter{background:rgba(167,139,250,.1);color:#a78bfa;border-color:rgba(167,139,250,.2)}
.provider-tag.db_only{background:rgba(107,114,128,.1);color:#9ca3af;border-color:rgba(107,114,128,.2)}

/* KB selector */
.kb-selector-wrap{
    display:flex;align-items:center;gap:8px;margin-bottom:12px
}
.kb-selector-label{font-family:var(--font-m);font-size:10px;color:var(--text-d);flex-shrink:0}
.kb-selector{
    flex:1;background:var(--surface-2);border:1px solid var(--border);
    border-radius:var(--r-sm);padding:7px 28px 7px 10px;
    font-family:var(--font-m);font-size:11px;color:var(--text-m);
    cursor:pointer;appearance:none;
    background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
    background-repeat:no-repeat;background-position:right 8px center;outline:none
}
.kb-selector:focus{border-color:var(--accent-border)}

/* ─── SECTION NAV (prev/next) ─────────────────────────────── */
.section-nav-row{
    display:flex;justify-content:space-between;gap:10px;margin-top:16px
}

.section-nav-btn{
    display:flex;align-items:center;gap:6px;
    padding:10px 16px;background:var(--surface);border:1px solid var(--border);
    border-radius:var(--r-sm);font-family:var(--font-b);font-size:13px;
    color:var(--text-m);cursor:pointer;transition:all .14s;
    min-height:44px;flex:1;justify-content:center;
    max-width:48%
}
.section-nav-btn:hover{background:var(--surface-2);color:var(--text)}
.section-nav-btn:disabled{opacity:.4;cursor:not-allowed}
.section-nav-btn.next-btn{
    background:var(--forest-900);border-color:var(--forest-800);color:var(--forest-300)
}
.section-nav-btn.next-btn:hover{background:var(--forest-800);color:var(--text)}

/* ─── FIXED BOTTOM BAR ─────────────────────────────────────── */
.bottom-bar{
    position:fixed;bottom:0;left:0;right:0;z-index:50;
    background:rgba(12,17,32,.92);backdrop-filter:blur(10px);
    border-top:1px solid var(--border);
    padding:10px 14px;
    display:flex;gap:8px;align-items:center
}

.save-all-btn{
    flex:1;display:flex;align-items:center;justify-content:center;gap:7px;
    padding:12px;background:var(--forest-800);border:1px solid var(--forest-700);
    border-radius:var(--r-sm);color:var(--forest-300);
    font-family:var(--font-b);font-size:13px;font-weight:500;
    cursor:pointer;transition:all .14s;min-height:46px
}
.save-all-btn:hover{background:var(--forest-700);color:var(--text)}
.save-all-btn:disabled{opacity:.5;cursor:not-allowed}

.pdf-btn{
    width:46px;height:46px;flex-shrink:0;
    display:grid;place-items:center;
    background:none;border:1px solid var(--border);border-radius:var(--r-sm);
    color:var(--text-m);cursor:pointer;text-decoration:none;transition:all .14s;
    font-size:18px
}
.pdf-btn:hover{background:var(--surface-2);color:var(--text)}



.asgn-card.is-locked {
    border-color: var(--ai-border-subtle);
    background: rgba(22, 27, 34, 0.6); 
}

.asgn-card.is-locked .card-title {
    color: var(--ai-text-muted);
}

.asgn-card.is-locked .upgrade-btn {
    background: var(--ai-accent-bg) !important;
    color: var(--ai-accent) !important;
    border: 1px solid var(--ai-accent-border) !important;
    font-weight: 600;
}

.asgn-card.is-locked:hover {
    border-color: var(--ai-accent-border);
    transform: translateY(-2px);
    background: var(--ai-surface);
}

.ai-btn.is-locked {
    opacity: 0.7;
    cursor: pointer;
    background: var(--ai-surface-3) !important;
    border-color: var(--ai-border) !important;
    color: var(--ai-text-dim) !important;
}

.ai-btn.is-locked:hover {
    background: var(--ai-accent-bg) !important;
    color: var(--ai-accent) !important;
    border-color: var(--ai-accent-border) !important;
    opacity: 1;
}

.bottom-bar .is-locked {
    background: var(--ai-surface-3) !important;
    border-color: var(--ai-border) !important;
    color: var(--ai-text-dim) !important;
    cursor: pointer;
}

.bottom-bar .pdf-btn.is-locked {
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0; 
}

.editor-wrap {
    position: relative;
}

.editor-wrap.is-locked .section-editor {
    background: var(--ai-bg);
    color: var(--ai-text-dim);
    cursor: not-allowed;
    filter: blur(1px); /* Optional: makes content harder to read to encourage upgrade */
    user-select: none;
}

.editor-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(13, 17, 23, 0.6);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 12px;
    cursor: pointer;
    border-radius: var(--ai-radius);
    z-index: 5;
    transition: background 0.2s;
}

.editor-overlay:hover {
    background: rgba(13, 17, 23, 0.4);
}

.editor-overlay span {
    padding: 6px 16px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 700;
}
</style>
@endsection

@section('content')
@php $isPremium = auth()->user()->hasActivePremium(); @endphp

{{-- TOP BAR --}}
<div class="ws-topbar">
    <a href="{{ route('assignments.index') }}" class="ws-back">←</a>
    <div class="ws-title-block">
        <div class="ws-title">{{ $assignment->title }}</div>
        @if($assignment->course)
        <div class="ws-course mono">{{ $assignment->course }}</div>
        @endif
    </div>
    <div class="ws-topbar-actions">
        <div class="save-status" id="saveStatus">
            <span class="save-dot"></span>
            <span id="saveStatusText">Auto-save on</span>
        </div>
    </div>
</div>

{{-- PROGRESS BAR --}}
<div class="progress-bar-wrap">
    <div class="progress-label-row">
        <span>Progress</span>
        <span class="progress-pct" id="progressPct">{{ $userAssignment->getProgressPercent() }}%</span>
    </div>
    <div class="progress-track">
        <div class="progress-fill" id="progressFill"
             style="width:{{ $userAssignment->getProgressPercent() }}%"></div>
    </div>
</div>

{{-- SECTION TABS --}}
<nav class="sections-nav" id="sectionsNav">
    @foreach($sections as $i => $section)
    @php $hasContent = isset($contents[$section->id]) && ($contents[$section->id]->word_count ?? 0) > 10; @endphp
    <button class="nav-tab {{ $i === 0 ? 'active' : '' }}"
            data-section="{{ $section->id }}"
            onclick="switchSection({{ $section->id }}, this)">
        {{ Str::limit($section->title, 16) }}
        @if($hasContent)<span class="tab-done"></span>@endif
    </button>
    @endforeach
</nav>

{{-- WORKSPACE BODY --}}
<div class="ws-body">

    {{-- Knowledge base selector (shown once at top) --}}
    @if(isset($knowledgeBases) && $knowledgeBases->isNotEmpty())
    <div class="kb-selector-wrap">
        <span class="kb-selector-label">KB:</span>
        <select class="kb-selector" id="kbSelector">
            <option value="">All materials</option>
            @foreach($knowledgeBases as $kb)
            <option value="{{ $kb->id }}">{{ Str::limit($kb->title, 30) }}</option>
            @endforeach
        </select>
    </div>
    @endif

    {{-- ── SECTION CARDS ──────────────────────────────────── --}}
    @foreach($sections as $i => $section)
    @php
        $existingContent = $contents[$section->id]->content ?? '';
        $wordCount       = $contents[$section->id]->word_count ?? 0;
    @endphp

    <div class="section-card {{ $i === 0 ? 'active' : '' }}"
         id="section-{{ $section->id }}"
         data-section-id="{{ $section->id }}">

        {{-- Section header --}}
        <div class="section-head">
            <div class="section-title-text">{{ $section->title }}</div>
            <span class="section-num-badge">{{ $i + 1 }}/{{ $sections->count() }}</span>
        </div>

        {{-- Guidance note --}}
        @if($section->guidance_note)
        <div class="guidance-note">{{ $section->guidance_note }}</div>
        @endif

        {{-- Questions accordion --}}
        @if(!empty($section->questions))
        <div class="questions-accordion" id="qa-{{ $section->id }}">
            <button class="questions-toggle"
                    onclick="toggleQuestions({{ $section->id }})">
                <span>{{ count($section->questions) }} question{{ count($section->questions) !== 1 ? 's' : '' }} to answer</span>
                <span class="questions-chevron">▾</span>
            </button>
            <div class="questions-body">
                @foreach($section->questions as $qi => $question)
                <div class="question-item">
                    <span class="question-num">Q{{ $qi + 1 }}</span>
                    <span>{{ $question }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- AI result preview (hidden until AI returns) --}}
        <div class="ai-result-preview" id="ai-preview-{{ $section->id }}">
            <div class="ai-result-label">
                ◈ AI Draft
                <span class="provider-tag" id="ai-provider-{{ $section->id }}"></span>
            </div>
            <div class="ai-result-text" id="ai-text-{{ $section->id }}"></div>
            <div class="ai-result-actions">
                <button class="ai-result-btn accept" onclick="acceptAiResult({{ $section->id }})">Use this →</button>
                <button class="ai-result-btn discard" onclick="discardAiResult({{ $section->id }})">Discard</button>
            </div>
        </div>

        {{-- Editor --}}
        <div class="editor-wrap {{ !$isPremium ? 'is-locked' : '' }}">
            <textarea
                class="section-editor"
                id="editor-{{ $section->id }}"
                data-section="{{ $section->id }}"
                placeholder="{{ $isPremium ? "Write your response here, or use 'Generate with AI' to get a draft based on your study materials..." : "⚠️ Premium Workspace: Upgrade to unlock the editor and AI tools." }}"
                oninput="onEditorInput(this)"
                {{ !$isPremium ? 'readonly' : '' }}
            >{{ $existingContent }}</textarea>
            
            @if(!$isPremium)
                <div class="editor-overlay" onclick="window.location.href='{{ route('payment.plans') }}'">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                    <span>Unlock Workspace</span>
                </div>
            @endif
        </div>

        <div class="editor-footer">
            <span class="word-count {{ $wordCount >= 100 ? 'good' : '' }}" id="wc-{{ $section->id }}">
                {{ $wordCount }} words
            </span>
            <span style="color:var(--ink-500)">Aim for 200–500 words</span>
        </div>

        {{-- AI action buttons --}}
        <div class="ai-actions" style="margin-top:12px">
            {{-- Generate Button --}}
            <button class="ai-btn ai-btn-generate {{ !$isPremium ? 'is-locked' : '' }}"
                    id="gen-btn-{{ $section->id }}"
                    onclick="{{ $isPremium ? "generateSection($section->id)" : "window.location.href='" . route('payment.plans') . "'" }}">
                @if(!$isPremium)
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                @else
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9.5 2A2.5 2.5 0 0 1 12 4.5v15a2.5 2.5 0 0 1-4.96-.44l-1.4-8.4a4 4 0 0 1 3.86-4.66z"/>
                        <path d="M14.5 2A2.5 2.5 0 0 0 12 4.5v15a2.5 2.5 0 0 0 4.96-.44l1.4-8.4a4 4 0 0 0-3.86-4.66z"/>
                    </svg>
                @endif
                Generate with AI
            </button>
        
            {{-- Improve Button --}}
            <button class="ai-btn ai-btn-improve {{ !$isPremium ? 'is-locked' : '' }}"
                    id="imp-btn-{{ $section->id }}"
                    onclick="{{ $isPremium ? "improveSection($section->id)" : "window.location.href='" . route('payment.plans') . "'" }}">
                @if(!$isPremium)
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                @else
                    ✦
                @endif
                Improve
            </button>
        </div>

        {{-- Prev / Next nav --}}
        <div class="section-nav-row">
            @if($i > 0)
            <button class="section-nav-btn"
                    onclick="switchSection({{ $sections[$i-1]->id }}, null)">
                ← {{ Str::limit($sections[$i-1]->title, 14) }}
            </button>
            @else
            <div></div>
            @endif

            @if($i < $sections->count() - 1)
            <button class="section-nav-btn next-btn"
                    onclick="switchSection({{ $sections[$i+1]->id }}, null)">
                {{ Str::limit($sections[$i+1]->title, 14) }} →
            </button>
            @else
            <button class="section-nav-btn next-btn {{ !$isPremium ? 'upgrade-mode' : '' }}" 
                    onclick="{{ $isPremium ? 'markCompleted()' : "window.location.href='" . route('payment.plans') . "'" }}">
                
                @if(!$isPremium)
                    <svg style="width:14px; height:14px; margin-right:5px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                    Upgrade to Access
                @else
                    ✓ Finish
                @endif
            </button>
            @endif
        </div>

    </div>
    @endforeach

</div>

{{-- BOTTOM BAR --}}
<div class="bottom-bar">
    {{-- Save All: Usually we allow saving even for free users, but added the lock icon per your style --}}
    <button class="save-all-btn {{ !$isPremium ? 'is-locked' : '' }}" 
            id="saveAllBtn" 
            onclick="saveAll()">
        @if(!$isPremium)
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-right:4px;">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
            </svg>
        @else
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-right:4px;">
                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                <polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
            </svg>
        @endif
        Save All Sections
    </button>

    {{-- PDF Button: Redirects to plans if not premium --}}
    <a href="{{ $isPremium ? route('assignments.pdf', $userAssignment) : route('payment.plans') }}"
       class="pdf-btn {{ !$isPremium ? 'is-locked' : '' }}" 
       title="{{ $isPremium ? 'Download PDF' : 'Upgrade to Download PDF' }}" 
       @if($isPremium) target="_blank" @endif>
        @if(!$isPremium)
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
            </svg>
        @else
            📄
        @endif
    </a>
</div>

@endsection

@push('scripts')
@if(auth()->user()->is_premium)
<script>
// ─── State ────────────────────────────────────────────────────
const CSRF   = '{{ csrf_token() }}';
const UA_ID  = {{ $userAssignment->id }};
let   saveTimer  = null;
let   activeSec  = {{ $sections->first()->id ?? 0 }};
let   dirtyMap   = {}; // sectionId → true if unsaved changes

// ─── Switch section ───────────────────────────────────────────
function switchSection(sectionId, tabEl) {
    document.querySelectorAll('.section-card').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.nav-tab').forEach(el => el.classList.remove('active'));

    document.getElementById('section-' + sectionId)?.classList.add('active');

    const tab = tabEl || document.querySelector(`.nav-tab[data-section="${sectionId}"]`);
    if (tab) {
        tab.classList.add('active');
        tab.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
    }

    activeSec = sectionId;

    // Auto-save current section before switching if dirty
    if (dirtyMap[sectionId]) {
        debouncedSave(sectionId);
    }
}

// ─── Questions accordion ───────────────────────────────────────
function toggleQuestions(id) {
    document.getElementById('qa-' + id)?.classList.toggle('open');
}

// Auto-open questions accordion for first section
document.querySelector('.questions-accordion')?.classList.add('open');

// ─── Editor input handler ─────────────────────────────────────
function onEditorInput(textarea) {
    const sid   = parseInt(textarea.dataset.section);
    const words = textarea.value.trim() ? textarea.value.trim().split(/\s+/).length : 0;

    const wc = document.getElementById('wc-' + sid);
    if (wc) {
        wc.textContent = words + ' words';
        wc.className   = 'word-count' + (words >= 100 ? ' good' : '');
    }

    dirtyMap[sid] = true;
    clearTimeout(saveTimer);
    // Debounced auto-save: 8 seconds after last keystroke
    saveTimer = setTimeout(() => autoSaveSection(sid), 8000);
    setSaveStatus('idle');
}

// ─── Auto-save single section ─────────────────────────────────
async function autoSaveSection(sectionId) {
    const content = document.getElementById('editor-' + sectionId)?.value;
    if (!content || !dirtyMap[sectionId]) return;

    setSaveStatus('saving');

    try {
        const res = await post('{{ route('assignments.save-content') }}', {
            user_assignment_id: UA_ID,
            section_id: sectionId,
            content: content,
        });

        if (res.success) {
            dirtyMap[sectionId] = false;
            setSaveStatus('saved');
            updateProgress(res.progress);
            markTabDone(sectionId, res.word_count > 10);
        }
    } catch {
        setSaveStatus('error');
    }
}

// ─── Save ALL sections at once ────────────────────────────────
async function saveAll() {
    const btn = document.getElementById('saveAllBtn');
    btn.disabled = true;
    btn.innerHTML = `<span class="btn-spinner"></span> Saving…`;
    setSaveStatus('saving');

    const sections = [];
    document.querySelectorAll('.section-editor').forEach(ta => {
        const sid = parseInt(ta.dataset.section);
        if (ta.value.trim()) {
            sections.push({ section_id: sid, content: ta.value });
        }
    });

    try {
        const res = await post('{{ route('assignments.save-all') }}', {
            user_assignment_id: UA_ID,
            sections: sections,
        });

        if (res.success) {
            dirtyMap = {}; // all clean
            setSaveStatus('saved');
            updateProgress(res.progress);

            sections.forEach(s => markTabDone(s.section_id, true));
        }
    } catch {
        setSaveStatus('error');
    }

    btn.disabled = false;
    btn.innerHTML = `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg> Save All Sections`;
}

// ─── Generate section with AI ─────────────────────────────────
async function generateSection(sectionId) {
    const btn = document.getElementById('gen-btn-' + sectionId);
    setAiBtn(btn, true, 'Generating…');

    const kbId = document.getElementById('kbSelector')?.value || null;

    try {
        const res = await post('{{ route('assignments.generate-section') }}', {
            section_id: sectionId,
            user_assignment_id: UA_ID,
            knowledge_base_id: kbId,
        });

        if (res.success) {
            showAiPreview(sectionId, res.text, res.provider, res.used_kb);
        } else {
            alert('AI generation failed. Please try again.');
        }
    } catch {
        alert('Connection error. Please try again.');
    }

    setAiBtn(btn, false, 'Generate with AI');
}

// ─── Improve existing content ──────────────────────────────────
async function improveSection(sectionId) {
    const content = document.getElementById('editor-' + sectionId)?.value?.trim();
    if (!content || content.length < 20) {
        alert('Please write some content first before using Improve.');
        return;
    }

    const btn = document.getElementById('imp-btn-' + sectionId);
    setAiBtn(btn, true, 'Improving…');

    try {
        const res = await post('{{ route('assignments.improve-content') }}', {
            section_id: sectionId,
            user_assignment_id: UA_ID,
            content: content,
        });

        if (res.success) {
            showAiPreview(sectionId, res.text, res.provider, false);
        } else {
            alert('AI improvement failed. Please try again.');
        }
    } catch {
        alert('Connection error. Please try again.');
    }

    setAiBtn(btn, false, '✦ Improve');
}

// ─── AI preview helpers ───────────────────────────────────────
function showAiPreview(sectionId, text, provider, usedKb) {
    const preview     = document.getElementById('ai-preview-' + sectionId);
    const textEl      = document.getElementById('ai-text-' + sectionId);
    const providerEl  = document.getElementById('ai-provider-' + sectionId);

    textEl.textContent    = text;
    providerEl.textContent = provider;
    providerEl.className   = 'provider-tag ' + provider;

    preview.classList.add('visible');
    preview.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

function acceptAiResult(sectionId) {
    const text    = document.getElementById('ai-text-' + sectionId).textContent;
    const editor  = document.getElementById('editor-' + sectionId);
    editor.value  = text;
    editor.dispatchEvent(new Event('input'));
    discardAiResult(sectionId);
}

function discardAiResult(sectionId) {
    document.getElementById('ai-preview-' + sectionId).classList.remove('visible');
}

// ─── Mark tab as done ─────────────────────────────────────────
function markTabDone(sectionId, done) {
    const tab = document.querySelector(`.nav-tab[data-section="${sectionId}"]`);
    if (!tab) return;

    const existing = tab.querySelector('.tab-done');
    if (done && !existing) {
        const dot = document.createElement('span');
        dot.className = 'tab-done';
        tab.appendChild(dot);
    } else if (!done && existing) {
        existing.remove();
    }
}

// ─── Progress ────────────────────────────────────────────────
function updateProgress(pct) {
    document.getElementById('progressFill').style.width = pct + '%';
    document.getElementById('progressPct').textContent  = pct + '%';
}

// ─── Mark completed ───────────────────────────────────────────
async function markCompleted() {
    await saveAll();
    try {
        await post('{{ route('assignments.complete', $userAssignment) }}', {});
        window.location.href = '{{ route('assignments.index') }}';
    } catch {
        window.location.href = '{{ route('assignments.index') }}';
    }
}

// ─── Save status UI ───────────────────────────────────────────
function setSaveStatus(state) {
    const el   = document.getElementById('saveStatus');
    const text = document.getElementById('saveStatusText');
    el.className = 'save-status ' + state;
    text.textContent = { saving: 'Saving…', saved: 'Saved', error: 'Save failed', idle: 'Auto-save on' }[state] || '';
}

// ─── Button helpers ───────────────────────────────────────────
function setAiBtn(btn, loading, label) {
    if (!btn) return;
    btn.disabled   = loading;
    btn.innerHTML  = loading
        ? `<span class="btn-spinner"></span> ${label}`
        : label;
}

// ─── Generic POST helper ──────────────────────────────────────
async function post(url, data) {
    const res = await fetch(url, {
        method:  'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        body:    JSON.stringify(data),
    });

    if (!res.ok) {
        const err = await res.json().catch(() => ({}));
        throw new Error(err.message || 'Request failed');
    }

    return res.json();
}

// ─── Warn before leaving if dirty ────────────────────────────
window.addEventListener('beforeunload', (e) => {
    const hasDirty = Object.values(dirtyMap).some(v => v);
    if (hasDirty) {
        e.preventDefault();
        e.returnValue = '';
    }
});
</script>
@endif
@endpush
