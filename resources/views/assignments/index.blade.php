{{--
    resources/views/assignments/index.blade.php
    User-facing: browse and start assignments
--}}
@extends('layouts.app')

@section('head')
<link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=JetBrains+Mono:wght@400;500&family=Geist:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
:root{
    --ink-950:#030712;--ink-900:#0c1120;--ink-800:#111827;--ink-700:#1c2639;
    --ink-600:#243044;--ink-400:#4b5563;--ink-300:#6b7280;--ink-200:#9ca3af;
    --forest-950:#052e16;--forest-900:#14532d;--forest-800:#166534;
    --forest-700:#15803d;--forest-600:#16a34a;--forest-500:#22c55e;--forest-400:#4ade80;
    --bg:var(--ink-950);--surface:var(--ink-900);--surface-2:var(--ink-800);
    --border:var(--ink-600);--border-sub:var(--ink-700);
    --text:#f0f4f8;--text-m:var(--ink-200);--text-d:var(--ink-300);
    --accent:var(--forest-500);--accent-dim:rgba(34,197,94,.10);
    --accent-border:rgba(34,197,94,.22);
    --font-d:'Instrument Serif',Georgia,serif;
    --font-b:'Geist',system-ui,sans-serif;
    --font-m:'JetBrains Mono',monospace;
    --r:10px;--r-sm:6px
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{background:var(--bg);color:var(--text);font-family:var(--font-b);min-height:100vh}

.page-wrap{max-width:680px;margin:0 auto;padding:24px 16px 48px}

/* Header */
.page-head{margin-bottom:26px}
.page-head h1{font-family:var(--font-d);font-size:26px;letter-spacing:-.02em;margin-bottom:5px}
.page-head p{font-size:13px;color:var(--text-m);line-height:1.65}

/* Assignment cards */
.assignment-list{display:flex;flex-direction:column;gap:12px}

.asgn-card{
    background:var(--surface);border:1px solid var(--border);
    border-radius:var(--r);padding:18px;
    text-decoration:none;color:inherit;
    display:block;
    transition:border-color .15s,transform .15s,box-shadow .15s;
    position:relative;overflow:hidden;
    animation:cardIn .3s ease both
}

@keyframes cardIn{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:translateY(0)}}

.asgn-card::before{
    content:'';position:absolute;top:0;left:0;right:0;height:1px;
    background:linear-gradient(90deg,var(--forest-900),var(--forest-600),transparent);
    opacity:0;transition:opacity .2s
}

.asgn-card:hover{
    border-color:rgba(34,197,94,.2);
    transform:translateY(-2px);
    box-shadow:0 8px 28px rgba(0,0,0,.4),0 0 16px rgba(34,197,94,.05)
}
.asgn-card:hover::before{opacity:1}

.card-top{display:flex;align-items:flex-start;justify-content:space-between;gap:12px;margin-bottom:12px}
.card-course{
    font-family:var(--font-m);font-size:10px;font-weight:600;
    letter-spacing:.1em;text-transform:uppercase;
    color:var(--forest-600);
    background:var(--accent-dim);border:1px solid var(--accent-border);
    border-radius:3px;padding:2px 7px;flex-shrink:0
}
.card-status{
    font-family:var(--font-m);font-size:10px;
    padding:2px 7px;border-radius:3px;border:1px solid;flex-shrink:0
}
.card-status.draft{background:rgba(107,114,128,.08);border-color:rgba(107,114,128,.2);color:var(--ink-300)}
.card-status.completed{background:rgba(34,197,94,.08);border-color:rgba(34,197,94,.2);color:var(--forest-400)}
.card-status.new{background:rgba(59,130,246,.08);border-color:rgba(59,130,246,.2);color:#60a5fa}

.card-title{
    font-family:var(--font-d);font-size:18px;letter-spacing:-.01em;
    color:var(--text);margin-bottom:5px;line-height:1.3
}

.card-desc{
    font-size:12.5px;color:var(--text-m);line-height:1.65;
    display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;
    margin-bottom:14px
}

/* Progress bar */
.progress-row{display:flex;align-items:center;gap:10px;margin-bottom:14px}
.progress-track{flex:1;height:4px;background:var(--surface-2);border-radius:2px;overflow:hidden}
.progress-fill{height:100%;background:var(--forest-700);border-radius:2px;transition:width .4s ease}
.progress-fill.done{background:var(--forest-500)}
.progress-label{font-family:var(--font-m);font-size:10px;color:var(--text-d);flex-shrink:0;min-width:35px;text-align:right}

/* Card footer */
.card-footer{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px}
.card-meta{font-family:var(--font-m);font-size:11px;color:var(--text-d);display:flex;align-items:center;gap:10px}
.card-cta{
    display:inline-flex;align-items:center;gap:6px;
    padding:7px 14px;background:var(--forest-800);border:1px solid var(--forest-700);
    border-radius:var(--r-sm);color:var(--forest-300);font-size:12px;font-weight:500;
    transition:all .14s
}
.card-cta:hover{background:var(--forest-700);color:var(--text)}
.card-cta.resume{background:var(--accent-dim);border-color:var(--accent-border);color:var(--forest-400)}

/* Empty */
.empty-state{display:flex;flex-direction:column;align-items:center;padding:72px 20px;text-align:center}
.empty-icon{font-size:40px;opacity:.4;margin-bottom:14px}
.empty-title{font-family:var(--font-d);font-size:21px;margin-bottom:6px}
.empty-text{font-size:13px;color:var(--text-m);max-width:280px;line-height:1.65}

/* CSS card */
.asgn-card.is-locked {
    border-color: var(--ai-border-subtle);
    background: rgba(22, 27, 34, 0.6); /* Slightly dimmer background */
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
</style>
@endsection

@section('content')
<div class="page-wrap">

    {{-- 1. Header Section --}}
    <div class="page-head">
        <h1>Assignments</h1>
        <p>AI-guided writing workspace. Complete each section with help from your study materials.</p>
    </div>

    {{-- 2. Empty State Check --}}
    @if($assignments->isEmpty())
        <div class="empty-state">
            <div class="empty-icon">📝</div>
            <h2 class="empty-title">No assignments yet</h2>
            <p class="empty-text">Your lecturer hasn't published any assignments yet. Check back soon.</p>
        </div>
    @else

        {{-- 3. Assignment Grid --}}
        <div class="assignment-list">
            @foreach($assignments as $i => $a)
                @php
                    // Check user status
                    $isPremium = auth()->user()->hasActivePremium();
                    
                    // Determine where the card clicks go
                    $targetUrl = $isPremium ? route('assignments.workspace', $a) : route('payment.plans');
                    
                    // Progress and Status Logic
                    $ua      = $a->userAssignments->first();
                    $percent = 0;
                    $status  = 'new';
                    if ($ua) {
                        $status  = $ua->status;
                        $percent = $ua->total_sections > 0
                            ? (int) round($ua->sections_filled / $ua->total_sections * 100)
                            : 0;
                    }
                @endphp

                <a href="{{ $targetUrl }}" 
                   class="asgn-card {{ !$isPremium ? 'is-locked' : '' }}"
                   style="animation-delay:{{ $i * 0.05 }}s">

                    {{-- Card Top: Course Code & Status --}}
                    <div class="card-top">
                        @if($a->course)
                            <span class="card-course">{{ $a->course }}</span>
                        @endif
                        <span class="card-status {{ $status }}">
                            {{ $status === 'new' ? 'Not started' : ucfirst($status) }}
                        </span>
                    </div>

                    {{-- Title with Padlock Alignment --}}
                    <div class="card-title" style="display: flex; align-items: center; gap: 8px;">
                        @if(!$isPremium) 
                            <svg style="width:15px; height:15px; flex-shrink:0; color:var(--ai-accent);" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                        @endif
                        <span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            {{ $a->title }}
                        </span>
                    </div>

                    {{-- Description --}}
                    @if($a->description)
                        <div class="card-desc">{{ $a->description }}</div>
                    @endif

                    {{-- Progress Bar (Premium Only) --}}
                    @if($ua && $isPremium)
                        <div class="progress-row">
                            <div class="progress-track">
                                <div class="progress-fill {{ $percent === 100 ? 'done' : '' }}"
                                     style="width:{{ $percent }}%"></div>
                            </div>
                            <span class="progress-label">{{ $percent }}%</span>
                        </div>
                    @endif

                    {{-- Footer Metadata --}}
                    <div class="card-footer">
                        <div class="card-meta">
                            <span>{{ $a->sections_count }} section{{ $a->sections_count !== 1 ? 's' : '' }}</span>
                            @if($ua && $isPremium)
                                <span>·</span>
                                <span>{{ $ua->sections_filled }}/{{ $ua->total_sections }} done</span>
                            @endif
                        </div>

                        {{-- CTA Button --}}
                        <span class="card-cta {{ $ua && $isPremium ? 'resume' : '' }} {{ !$isPremium ? 'upgrade-btn' : '' }}">
                            @if(!$isPremium)
                                Upgrade to Access
                            @else
                                {{ $ua ? 'Continue →' : 'Start →' }}
                            @endif
                        </span>
                    </div>
                </a>
            @endforeach
        </div> {{-- End .assignment-list --}}
    @endif {{-- End .isEmpty check --}}

</div> {{-- End .page-wrap --}}

@endsection
