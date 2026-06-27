@extends('layouts.admin')

@section('head')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=JetBrains+Mono:wght@300;400;500;600&family=Geist:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
/* ═══════════════════════════════════════════════════════════════
   AI MONITOR DASHBOARD
   Forest Green × Ink — Terminal / DevOps SaaS aesthetic
   The one thing users remember: the real-time provider status
   cards that pulse green/red like a network health panel.
═══════════════════════════════════════════════════════════════ */
:root {
    --ink-950: #030712; --ink-900: #0c1120; --ink-800: #111827;
    --ink-700: #1c2639; --ink-600: #243044; --ink-500: #374151;
    --ink-400: #4b5563; --ink-300: #6b7280; --ink-200: #9ca3af;
    --ink-100: #d1d5db;
    --forest-950: #052e16; --forest-900: #14532d; --forest-800: #166534;
    --forest-700: #15803d; --forest-600: #16a34a; --forest-500: #22c55e;
    --forest-400: #4ade80; --forest-300: #86efac;
    --red-500: #ef4444; --red-400: #f87171; --red-900: #450a0a;
    --amber-500: #f59e0b; --amber-400: #fbbf24; --amber-900: #451a03;
    --blue-500: #3b82f6; --blue-400: #60a5fa; --blue-900: #1e3a5f;
    --violet-500: #8b5cf6; --violet-400: #a78bfa;
    --bg: var(--ink-950); --surface: var(--ink-900); --surface-2: var(--ink-800);
    --surface-3: var(--ink-700); --border: var(--ink-600); --border-sub: var(--ink-700);
    --text: #f0f4f8; --text-m: var(--ink-200); --text-d: var(--ink-300);
    --accent: var(--forest-500);
    --accent-dim: rgba(34, 197, 94, .10); --accent-border: rgba(34, 197, 94, .22);
    --font-d: 'Instrument Serif', Georgia, serif;
    --font-b: 'Geist', system-ui, sans-serif;
    --font-m: 'JetBrains Mono', monospace;
    --r: 8px; --r-sm: 5px; --r-lg: 12px;
    --shadow: 0 8px 32px rgba(0,0,0,.5);
}
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body { background: var(--bg); color: var(--text); font-family: var(--font-b); min-height: 100vh; }

/* ── Scanline texture overlay (terminal feel) ─────────────── */
body::before {
    content: '';
    position: fixed; inset: 0; pointer-events: none; z-index: 0;
    background: repeating-linear-gradient(
        0deg, transparent, transparent 2px,
        rgba(0,0,0,.03) 2px, rgba(0,0,0,.03) 4px
    );
}

/* ── Layout ──────────────────────────────────────────────────── */
.monitor-wrap {
    position: relative; z-index: 1;
    max-width: 1200px; margin: 0 auto; padding: 24px 16px 60px;
}

/* ── Page header ─────────────────────────────────────────────── */
.page-head {
    display: flex; align-items: flex-start; justify-content: space-between;
    gap: 16px; margin-bottom: 28px; flex-wrap: wrap;
}
.head-left h1 {
    font-family: var(--font-d); font-size: 28px;
    letter-spacing: -.02em; margin-bottom: 5px;
}
.head-left p { font-size: 13px; color: var(--text-m); }

.head-right { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }

/* Live clock */
.live-clock {
    font-family: var(--font-m); font-size: 12px; color: var(--forest-600);
    background: var(--accent-dim); border: 1px solid var(--accent-border);
    border-radius: var(--r-sm); padding: 5px 11px;
    display: flex; align-items: center; gap: 6px;
}
.live-dot {
    width: 6px; height: 6px; border-radius: 50%; background: var(--forest-500);
    animation: livePulse 2s ease-in-out infinite;
}
@keyframes livePulse { 0%,100%{opacity:1;} 50%{opacity:.3;} }

.btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 7px 14px; border-radius: var(--r-sm);
    font-family: var(--font-b); font-size: 12.5px; font-weight: 500;
    cursor: pointer; border: 1px solid; text-decoration: none; transition: all .14s;
    white-space: nowrap;
}
.btn-ghost { background: none; border-color: var(--border); color: var(--text-m); }
.btn-ghost:hover { background: var(--surface-2); color: var(--text); }
.btn-green { background: var(--forest-900); border-color: var(--forest-800); color: var(--forest-300); }
.btn-green:hover { background: var(--forest-800); color: var(--text); }
.btn-sm { padding: 5px 10px; font-size: 11px; }

/* ── Section labels ──────────────────────────────────────────── */
.section-label {
    font-family: var(--font-m); font-size: 10px; font-weight: 600;
    letter-spacing: .12em; text-transform: uppercase; color: var(--ink-400);
    margin-bottom: 12px; display: flex; align-items: center; gap: 8px;
}
.section-label::after {
    content: ''; flex: 1; height: 1px; background: var(--border-sub);
}

/* ── Summary tiles ───────────────────────────────────────────── */
.summary-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px; margin-bottom: 28px;
}
@media(max-width: 860px) { .summary-grid { grid-template-columns: repeat(2, 1fr); } }
@media(max-width: 500px) { .summary-grid { grid-template-columns: 1fr 1fr; } }

.summary-tile {
    background: var(--surface); border: 1px solid var(--border);
    border-radius: var(--r); padding: 16px 18px;
    position: relative; overflow: hidden;
    animation: tileIn .35s ease both;
}
.summary-tile::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 1px;
    background: linear-gradient(90deg, var(--forest-900), var(--forest-600), transparent);
}
@keyframes tileIn { from{opacity:0;transform:translateY(8px)} to{opacity:1;transform:translateY(0)} }

.tile-label {
    font-family: var(--font-m); font-size: 9.5px; font-weight: 600;
    letter-spacing: .1em; text-transform: uppercase; color: var(--ink-400);
    margin-bottom: 8px;
}
.tile-value {
    font-family: var(--font-m); font-size: 26px; font-weight: 600;
    color: var(--forest-400); line-height: 1; margin-bottom: 4px;
}
.tile-value.red { color: var(--red-400); }
.tile-value.amber { color: var(--amber-400); }
.tile-value.blue { color: var(--blue-400); }

.tile-sub {
    font-family: var(--font-m); font-size: 10px; color: var(--ink-400);
}

/* ── Provider health cards ───────────────────────────────────── */
.providers-grid {
    display: grid; grid-template-columns: repeat(2, 1fr);
    gap: 12px; margin-bottom: 28px;
}
@media(max-width: 640px) { .providers-grid { grid-template-columns: 1fr; } }

.provider-card {
    background: var(--surface); border: 1px solid var(--border);
    border-radius: var(--r-lg); padding: 18px;
    position: relative; overflow: hidden;
    transition: border-color .2s;
    animation: tileIn .35s ease both;
}
.provider-card.online  { border-left: 3px solid var(--forest-600); }
.provider-card.offline { border-left: 3px solid var(--red-500); }
.provider-card.unconfigured { border-left: 3px solid var(--ink-500); }

/* Glow effect for online cards */
.provider-card.online::after {
    content: ''; position: absolute; top: 0; left: 0; bottom: 0; width: 3px;
    background: var(--forest-500); box-shadow: 0 0 12px var(--forest-600);
}

.prov-head {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 14px;
}
.prov-identity { display: flex; align-items: center; gap: 10px; }

/* Status LED */
.status-led {
    width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0;
    position: relative;
}
.status-led.online {
    background: var(--forest-500);
    box-shadow: 0 0 6px var(--forest-500);
    animation: ledPulse 2.5s ease-in-out infinite;
}
.status-led.offline { background: var(--red-500); }
.status-led.unconfigured { background: var(--ink-500); }

@keyframes ledPulse {
    0%,100%{ box-shadow: 0 0 4px var(--forest-600); }
    50%    { box-shadow: 0 0 12px var(--forest-400); }
}

.prov-name {
    font-family: var(--font-m); font-size: 13px; font-weight: 600; color: var(--text);
}
.prov-model {
    font-family: var(--font-m); font-size: 10px; color: var(--ink-400); margin-top: 1px;
}

.status-badge {
    font-family: var(--font-m); font-size: 10px; font-weight: 600;
    padding: 3px 8px; border-radius: 3px; border: 1px solid; letter-spacing: .05em;
}
.status-badge.online { background: rgba(34,197,94,.08); border-color: rgba(34,197,94,.2); color: var(--forest-400); }
.status-badge.offline { background: rgba(239,68,68,.08); border-color: rgba(239,68,68,.2); color: var(--red-400); }
.status-badge.unconfigured { background: rgba(107,114,128,.08); border-color: rgba(107,114,128,.2); color: var(--ink-300); }

/* Provider metrics grid */
.prov-metrics {
    display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px;
    margin-bottom: 12px;
}
.prov-metric-box {
    background: rgba(255,255,255,.02); border: 1px solid var(--border-sub);
    border-radius: var(--r-sm); padding: 8px 10px;
}
.prov-metric-val {
    font-family: var(--font-m); font-size: 15px; font-weight: 600;
    color: var(--text); line-height: 1; margin-bottom: 3px;
}
.prov-metric-lbl {
    font-family: var(--font-m); font-size: 9px; letter-spacing: .08em;
    text-transform: uppercase; color: var(--ink-400);
}

/* Success rate bar */
.rate-bar-wrap { margin-bottom: 12px; }
.rate-bar-head {
    display: flex; justify-content: space-between; align-items: center;
    font-family: var(--font-m); font-size: 10px; color: var(--text-d);
    margin-bottom: 5px;
}
.rate-pct { color: var(--forest-400); font-weight: 600; }
.rate-track { height: 5px; background: var(--surface-3); border-radius: 3px; overflow: hidden; }
.rate-fill {
    height: 100%; border-radius: 3px;
    background: linear-gradient(90deg, var(--forest-800), var(--forest-500));
    transition: width .6s ease;
}
.rate-fill.low { background: linear-gradient(90deg, var(--red-900), var(--red-500)); }

/* Error message */
.prov-error {
    font-family: var(--font-m); font-size: 10.5px; color: var(--red-400);
    background: rgba(239,68,68,.06); border: 1px solid rgba(239,68,68,.15);
    border-radius: var(--r-sm); padding: 7px 10px;
    margin-bottom: 10px; word-break: break-word; line-height: 1.5;
}

.prov-footer {
    display: flex; align-items: center; justify-content: space-between;
}
.prov-last-test {
    font-family: var(--font-m); font-size: 9.5px; color: var(--ink-500);
}

/* ── Chart area ──────────────────────────────────────────────── */
.charts-row {
    display: grid; grid-template-columns: 1fr 1fr;
    gap: 14px; margin-bottom: 28px;
}
@media(max-width: 700px) { .charts-row { grid-template-columns: 1fr; } }

.chart-card {
    background: var(--surface); border: 1px solid var(--border);
    border-radius: var(--r); padding: 18px;
}
.chart-title {
    font-family: var(--font-m); font-size: 11px; font-weight: 600;
    letter-spacing: .06em; text-transform: uppercase; color: var(--text-d);
    margin-bottom: 16px;
}

/* SVG bar chart */
.bar-chart { width: 100%; overflow: visible; }
.bar-chart-wrap { overflow-x: auto; }

/* Hourly sparkline */
.sparkline-wrap { position: relative; height: 60px; margin-bottom: 8px; }
.sparkline {
    width: 100%; height: 100%;
    display: flex; align-items: flex-end; gap: 2px;
}
.spark-bar {
    flex: 1; min-width: 2px; border-radius: 2px 2px 0 0;
    background: var(--forest-800); transition: background .2s;
    position: relative;
}
.spark-bar:hover { background: var(--forest-500); }
.spark-bar.has-data { background: var(--forest-700); }

/* Provider share donut (CSS-only) */
.donut-wrap {
    display: flex; align-items: center; gap: 16px; flex-wrap: wrap;
}
.donut { position: relative; width: 80px; height: 80px; flex-shrink: 0; }
.donut svg { transform: rotate(-90deg); }
.donut-center {
    position: absolute; inset: 0; display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    font-family: var(--font-m); font-size: 10px; color: var(--text-d);
    pointer-events: none;
}
.donut-center strong { font-size: 13px; color: var(--forest-400); }

.donut-legend { flex: 1; }
.legend-item {
    display: flex; align-items: center; gap: 7px;
    font-family: var(--font-m); font-size: 11px; color: var(--text-m);
    margin-bottom: 5px;
}
.legend-dot { width: 8px; height: 8px; border-radius: 2px; flex-shrink: 0; }

/* Context split bar */
.context-bar {
    height: 20px; border-radius: 4px; overflow: hidden;
    display: flex; margin-bottom: 8px;
}
.context-segment { height: 100%; transition: width .5s ease; }

/* ── Error log table ─────────────────────────────────────────── */
.log-card {
    background: var(--surface); border: 1px solid var(--border);
    border-radius: var(--r); padding: 18px; margin-bottom: 28px;
}
.log-filters {
    display: flex; gap: 8px; margin-bottom: 14px; flex-wrap: wrap;
}
.log-filter-select {
    background: var(--surface-2); border: 1px solid var(--border);
    border-radius: var(--r-sm); padding: 6px 24px 6px 10px;
    font-family: var(--font-m); font-size: 11px; color: var(--text-m);
    cursor: pointer; appearance: none; outline: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
    background-repeat: no-repeat; background-position: right 7px center;
    transition: border-color .14s;
}
.log-filter-select:focus { border-color: var(--accent-border); }

/* Log rows */
.log-table { width: 100%; border-collapse: collapse; font-size: 12px; }
.log-table th {
    padding: 8px 10px; text-align: left;
    font-family: var(--font-m); font-size: 9.5px; font-weight: 600;
    letter-spacing: .1em; text-transform: uppercase; color: var(--ink-400);
    border-bottom: 1px solid var(--border); background: var(--surface-2);
}
.log-table td {
    padding: 9px 10px; border-bottom: 1px solid var(--border-sub);
    vertical-align: middle;
}
.log-table tr:last-child td { border-bottom: none; }
.log-table tr:hover td { background: rgba(255,255,255,.015); }

.log-provider { font-family: var(--font-m); font-size: 11px; font-weight: 600; }
.log-success { color: var(--forest-500); font-size: 12px; }
.log-failure { color: var(--red-400); font-size: 12px; }
.log-time { font-family: var(--font-m); font-size: 10px; color: var(--ink-400); }
.log-error-msg { font-family: var(--font-m); font-size: 10.5px; color: var(--red-400); max-width: 260px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.log-ms { font-family: var(--font-m); font-size: 11px; color: var(--text-d); }

/* Mobile log (card layout) */
@media(max-width: 640px) {
    .log-table, .log-table thead, .log-table tbody, .log-table tr, .log-table th, .log-table td { display: block; }
    .log-table thead { display: none; }
    .log-table tr { background: var(--surface-2); border: 1px solid var(--border-sub); border-radius: var(--r-sm); margin-bottom: 6px; padding: 10px 12px; }
    .log-table td { padding: 3px 0; border: none; font-size: 11px; }
    .log-table td::before { content: attr(data-label); display: inline-block; font-family: var(--font-m); font-size: 9px; color: var(--ink-400); text-transform: uppercase; letter-spacing: .07em; margin-right: 8px; }
}

/* ── Pagination ──────────────────────────────────────────────── */
.pagination-row { display: flex; justify-content: center; gap: 5px; margin-top: 14px; flex-wrap: wrap; }
.page-btn {
    min-width: 32px; height: 32px; padding: 0 8px;
    background: var(--surface-2); border: 1px solid var(--border);
    border-radius: var(--r-sm); font-family: var(--font-m); font-size: 11px;
    color: var(--text-m); cursor: pointer; display: inline-flex;
    align-items: center; justify-content: center; transition: all .13s;
}
.page-btn:hover { background: var(--surface-3); color: var(--text); }
.page-btn.active { background: var(--accent-dim); border-color: var(--accent-border); color: var(--forest-400); }
.page-btn.disabled { opacity: .35; pointer-events: none; }

/* ── Loading overlay ─────────────────────────────────────────── */
.loading-overlay {
    position: fixed; inset: 0; background: rgba(3,7,18,.7);
    backdrop-filter: blur(4px); z-index: 200;
    display: none; place-items: center;
}
.loading-overlay.active { display: grid; }
.loading-spinner {
    width: 40px; height: 40px;
    border: 3px solid var(--border); border-top-color: var(--forest-500);
    border-radius: 50%; animation: spin .8s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* ── Toast notification ──────────────────────────────────────── */
.toast-container {
    position: fixed; bottom: 20px; right: 16px; z-index: 300;
    display: flex; flex-direction: column; gap: 8px; align-items: flex-end;
}
.toast {
    background: var(--surface); border: 1px solid var(--border);
    border-radius: var(--r-sm); padding: 10px 16px;
    font-family: var(--font-m); font-size: 12px; color: var(--text);
    box-shadow: var(--shadow); animation: toastIn .2s ease;
    max-width: 300px;
}
.toast.success { border-left: 3px solid var(--forest-500); }
.toast.error   { border-left: 3px solid var(--red-500); }
@keyframes toastIn { from{opacity:0;transform:translateX(20px)} to{opacity:1;transform:translateX(0)} }
</style>
@endsection

@section('content')
<div class="monitor-wrap">

{{-- ═══ PAGE HEADER ════════════════════════════════════════════ --}}
<div class="page-head">
    <div class="head-left">
        <h1>AI Monitor</h1>
        <p>Token usage, provider health, and error tracking across all AI services.</p>
    </div>
    <div class="head-right">
        <div class="live-clock">
            <span class="live-dot"></span>
            <span id="liveClock">--:--:--</span>
        </div>
        <button class="btn btn-ghost" onclick="refreshStats()">↻ Refresh</button>
        <button class="btn btn-green" onclick="testAllProviders()">
            ⚡ Test All Providers
        </button>
    </div>
</div>

{{-- ═══ SUMMARY TILES ══════════════════════════════════════════ --}}
<div class="section-label">Overview</div>

<div class="summary-grid" id="summaryGrid">
    @php $s = $stats['summary']; @endphp

    <div class="summary-tile" style="animation-delay:.00s">
        <div class="tile-label">Total API Calls</div>
        <div class="tile-value">{{ number_format($s['total_calls']) }}</div>
        <div class="tile-sub">Last 24h: {{ number_format($s['last_24h_calls']) }}</div>
    </div>

    <div class="summary-tile" style="animation-delay:.05s">
        <div class="tile-label">Tokens Used</div>
        <div class="tile-value">{{ number_format($s['total_tokens']) }}</div>
        <div class="tile-sub">Last 24h: {{ number_format($s['last_24h_tokens']) }}</div>
    </div>

    <div class="summary-tile" style="animation-delay:.10s">
        <div class="tile-label">Success Rate</div>
        <div class="tile-value {{ $s['success_rate'] < 70 ? 'amber' : '' }}">
            {{ $s['success_rate'] }}%
        </div>
        <div class="tile-sub">{{ number_format($s['total_success']) }} / {{ number_format($s['total_calls']) }} calls</div>
    </div>

    <div class="summary-tile" style="animation-delay:.15s">
        <div class="tile-label">Avg Response</div>
        <div class="tile-value blue">{{ $s['avg_response_ms'] }}<span style="font-size:14px">ms</span></div>
        <div class="tile-sub">{{ number_format($s['total_failures']) }} total failures</div>
    </div>
</div>

{{-- ═══ PROVIDER HEALTH CARDS ══════════════════════════════════ --}}
<div class="section-label" style="margin-top:4px;">Provider Status</div>

@php
    $provColors = [
        'gemini'      => ['#3b82f6', '#60a5fa', 'Gemini'],
        'groq'        => ['#f59e0b', '#fbbf24', 'Groq'],
        'openrouter'  => ['#8b5cf6', '#a78bfa', 'OpenRouter'],
        'huggingface' => ['#f59e0b', '#fcd34d', 'HuggingFace'],
    ];
@endphp

<div class="providers-grid" id="providersGrid">
    @foreach(['gemini', 'groq', 'openrouter', 'huggingface'] as $i => $pname)
    @php
        $h   = $health[$pname] ?? [];
        $st  = $h['online'] ?? false ? 'online' : ($h['configured'] ?? false ? 'offline' : 'unconfigured');
        $log = $stats['by_provider'][$pname] ?? [];
        $col = $provColors[$pname];
    @endphp

    <div class="provider-card {{ $st }}" id="pcard-{{ $pname }}"
         style="animation-delay:{{ $i * 0.07 }}s">

        <div class="prov-head">
            <div class="prov-identity">
                <span class="status-led {{ $st }}"></span>
                <div>
                    <div class="prov-name">{{ $col[2] }}</div>
                    <div class="prov-model">{{ $h['model'] ?? config("services.{$pname}.model", 'N/A') }}</div>
                </div>
            </div>
            <span class="status-badge {{ $st }}" id="pbadge-{{ $pname }}">
                {{ $st === 'online' ? '● Online' : ($st === 'offline' ? '● Offline' : '○ Not Set') }}
            </span>
        </div>

        @if($st === 'offline' && !empty($h['error']))
        <div class="prov-error" id="perror-{{ $pname }}">⚠ {{ $h['error'] }}</div>
        @elseif($st === 'unconfigured')
        <div class="prov-error" id="perror-{{ $pname }}" style="color:var(--ink-300);border-color:var(--border);background:transparent;">
            No API key configured. Add {{ strtoupper($pname) }}_API_KEY to .env
        </div>
        @else
        <div id="perror-{{ $pname }}" style="display:none"></div>
        @endif

        <div class="prov-metrics">
            <div class="prov-metric-box">
                <div class="prov-metric-val">{{ number_format($log['total_calls'] ?? 0) }}</div>
                <div class="prov-metric-lbl">Total Calls</div>
            </div>
            <div class="prov-metric-box">
                <div class="prov-metric-val">{{ number_format($log['total_tokens'] ?? 0) }}</div>
                <div class="prov-metric-lbl">Tokens</div>
            </div>
            <div class="prov-metric-box">
                <div class="prov-metric-val" id="pms-{{ $pname }}">
                    {{ $h['response_time_ms'] ?? ($log['avg_ms'] ?? 0) }}<span style="font-size:10px">ms</span>
                </div>
                <div class="prov-metric-lbl">Resp Time</div>
            </div>
        </div>

        <div class="rate-bar-wrap">
            <div class="rate-bar-head">
                <span>Success Rate</span>
                <span class="rate-pct" id="prate-{{ $pname }}">{{ $log['success_rate'] ?? 0 }}%</span>
            </div>
            <div class="rate-track">
                <div class="rate-fill {{ ($log['success_rate'] ?? 0) < 50 ? 'low' : '' }}"
                     id="pratefill-{{ $pname }}"
                     style="width:{{ $log['success_rate'] ?? 0 }}%"></div>
            </div>
        </div>

        <div class="prov-footer">
            <span class="prov-last-test" id="ptested-{{ $pname }}">
                Tested: {{ isset($h['tested_at']) ? \Carbon\Carbon::parse($h['tested_at'])->diffForHumans() : 'Never' }}
            </span>
            <button class="btn btn-ghost btn-sm" onclick="testProvider('{{ $pname }}')"
                    id="ptest-btn-{{ $pname }}">
                ↺ Test
            </button>
        </div>

    </div>
    @endforeach
</div>

{{-- ═══ CHARTS ROW ══════════════════════════════════════════════ --}}
<div class="section-label">Usage Analytics</div>

<div class="charts-row">

    {{-- Daily Token Chart --}}
    <div class="chart-card">
        <div class="chart-title">Daily Tokens — Last 14 Days</div>
        @php
            $dailyTokens = $stats['daily_tokens'];
            $maxTokens   = max(array_column($dailyTokens, 'total') ?: [1]);
        @endphp
        <div class="bar-chart-wrap">
            <svg class="bar-chart" viewBox="0 0 {{ count($dailyTokens) * 18 }} 80"
                 style="height:80px;min-width:100%">
                @foreach($dailyTokens as $di => $day)
                @php
                    $barH = $maxTokens > 0 ? round($day['total'] / $maxTokens * 68) : 2;
                    $x    = $di * 18 + 2;
                @endphp
                <rect x="{{ $x }}" y="{{ 80 - $barH }}" width="14" height="{{ max($barH, 2) }}"
                      rx="2" fill="{{ $day['total'] > 0 ? '#166534' : '#1c2639' }}"
                      class="chart-bar">
                    <title>{{ \Carbon\Carbon::parse($day['date'])->format('M d') }}: {{ number_format($day['total']) }} tokens</title>
                </rect>
                @endforeach
            </svg>
        </div>
        <div style="display:flex;justify-content:space-between;font-family:var(--font-m);font-size:9px;color:var(--ink-500);margin-top:4px;">
            <span>{{ \Carbon\Carbon::parse($dailyTokens[0]['date'])->format('M d') }}</span>
            <span>{{ \Carbon\Carbon::parse(end($dailyTokens)['date'])->format('M d') }}</span>
        </div>
    </div>

    {{-- Provider Token Share --}}
    <div class="chart-card">
        <div class="chart-title">Token Share by Provider</div>
        @php
            $byProvider  = $stats['by_provider'];
            $totalTokens = array_sum(array_column($byProvider, 'total_tokens')) ?: 1;
            $provChartColors = ['gemini'=>'#3b82f6','groq'=>'#f59e0b','openrouter'=>'#8b5cf6','huggingface'=>'#fcd34d'];

            // Build SVG donut segments
            $cx = 40; $cy = 40; $r = 32; $stroke = 12;
            $circumference = 2 * M_PI * $r;
            $offset = 0;
            $segments = [];
            foreach ($byProvider as $pn => $pd) {
                $pct = $pd['total_tokens'] / $totalTokens;
                $dash = $circumference * $pct;
                $gap  = $circumference - $dash;
                $segments[] = ['name' => $pn, 'pct' => $pct, 'dash' => $dash, 'gap' => $gap, 'offset' => $offset, 'color' => $provChartColors[$pn] ?? '#4b5563'];
                $offset += $dash;
            }
        @endphp

        <div class="donut-wrap">
            <div class="donut">
                <svg viewBox="0 0 80 80" width="80" height="80">
                    <circle cx="{{ $cx }}" cy="{{ $cy }}" r="{{ $r }}"
                            fill="none" stroke="#1c2639" stroke-width="{{ $stroke }}"/>
                    @foreach($segments as $seg)
                    @if($seg['pct'] > 0)
                    <circle cx="{{ $cx }}" cy="{{ $cy }}" r="{{ $r }}"
                            fill="none"
                            stroke="{{ $seg['color'] }}"
                            stroke-width="{{ $stroke }}"
                            stroke-dasharray="{{ round($seg['dash'], 2) }} {{ round($seg['gap'], 2) }}"
                            stroke-dashoffset="{{ -round($seg['offset'], 2) }}"
                            opacity=".85"/>
                    @endif
                    @endforeach
                </svg>
                <div class="donut-center">
                    <strong>{{ number_format($totalTokens) }}</strong>
                    <span>tokens</span>
                </div>
            </div>

            <div class="donut-legend">
                @foreach($byProvider as $pn => $pd)
                <div class="legend-item">
                    <span class="legend-dot" style="background:{{ $provChartColors[$pn] ?? '#4b5563' }}"></span>
                    <span style="flex:1">{{ ucfirst($pn) }}</span>
                    <span style="font-family:var(--font-m);font-size:11px;color:var(--forest-400)">
                        {{ number_format($pd['total_tokens']) }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

</div>

{{-- Hourly Today + Context Split --}}
<div class="charts-row">

    <div class="chart-card">
        <div class="chart-title">Hourly Activity — Today</div>
        @php
            $hourly   = $stats['hourly_today'];
            $maxCalls = max(array_column($hourly, 'calls') ?: [1]);
        @endphp
        <div class="sparkline-wrap">
            <div class="sparkline">
                @foreach($hourly as $h)
                @php $barPct = $maxCalls > 0 ? ($h['calls'] / $maxCalls * 100) : 0; @endphp
                <div class="spark-bar {{ $h['calls'] > 0 ? 'has-data' : '' }}"
                     style="height:{{ max($barPct, 3) }}%"
                     title="{{ $h['hour'] }}: {{ $h['calls'] }} calls, {{ $h['tokens'] }} tokens">
                </div>
                @endforeach
            </div>
        </div>
        <div style="display:flex;justify-content:space-between;font-family:var(--font-m);font-size:9px;color:var(--ink-500);">
            <span>00:00</span><span>12:00</span><span>23:00</span>
        </div>
    </div>

    <div class="chart-card">
        <div class="chart-title">Usage by Context</div>
        @php
            $ctxSplit   = $stats['context_split'];
            $ctxTotal   = array_sum(array_column($ctxSplit, 'calls')) ?: 1;
            $chatCalls  = $ctxSplit['chat']       ?? null;
            $asnCalls   = $ctxSplit['assignment'] ?? null;
            $chatPct    = $chatCalls ? round($chatCalls->calls / $ctxTotal * 100) : 0;
            $asnPct     = $asnCalls  ? round($asnCalls->calls  / $ctxTotal * 100) : 0;
        @endphp

        <div class="context-bar" style="margin-bottom:10px;">
            <div class="context-segment" style="width:{{ $chatPct }}%;background:var(--forest-800);"></div>
            <div class="context-segment" style="width:{{ $asnPct }}%;background:var(--violet-500);opacity:.6;"></div>
            <div class="context-segment" style="flex:1;background:var(--surface-3);"></div>
        </div>

        @foreach([['chat', 'var(--forest-600)', 'Chat AI'], ['assignment', 'var(--violet-500)', 'Assignments']] as [$ctx, $color, $label])
        @php $row = $ctxSplit[$ctx] ?? null; @endphp
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px;">
            <span style="width:10px;height:10px;border-radius:2px;background:{{ $color }};flex-shrink:0;display:inline-block;"></span>
            <span style="flex:1;font-size:12px;color:var(--text-m);">{{ $label }}</span>
            <span style="font-family:var(--font-m);font-size:12px;color:var(--forest-400)">{{ $row ? number_format($row->calls) : 0 }} calls</span>
            <span style="font-family:var(--font-m);font-size:11px;color:var(--ink-400)">{{ $row ? number_format($row->tokens) : 0 }} tok</span>
        </div>
        @endforeach

        <div style="margin-top:12px;padding-top:10px;border-top:1px solid var(--border-sub)">
            <div class="section-label" style="margin-bottom:8px;">Top Users by Token Usage</div>
            @forelse($stats['top_users'] as $tu)
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:5px;font-size:11px;">
                <span style="font-family:var(--font-m);color:var(--ink-400);width:24px">U{{ $tu->user_id }}</span>
                <div style="flex:1;height:4px;background:var(--surface-3);border-radius:2px;overflow:hidden;">
                    <div style="height:100%;background:var(--forest-700);width:{{ min(100, round($tu->tokens / max(array_column($stats['top_users'], 'tokens')) * 100)) }}%;border-radius:2px;"></div>
                </div>
                <span style="font-family:var(--font-m);color:var(--forest-400);min-width:60px;text-align:right">{{ number_format($tu->tokens) }}</span>
            </div>
            @empty
            <div style="font-size:12px;color:var(--ink-400)">No user data yet.</div>
            @endforelse
        </div>
    </div>

</div>

{{-- ═══ ERROR LOG ═══════════════════════════════════════════════ --}}
<div class="section-label">Activity Log</div>

<div class="log-card">
    <div class="log-filters">
        <select class="log-filter-select" id="filterProvider" onchange="loadLogs(1)">
            <option value="">All Providers</option>
            <option value="gemini">Gemini</option>
            <option value="groq">Groq</option>
            <option value="openrouter">OpenRouter</option>
            <option value="huggingface">HuggingFace</option>
        </select>
        <select class="log-filter-select" id="filterStatus" onchange="loadLogs(1)">
            <option value="">All Status</option>
            <option value="success">Success only</option>
            <option value="fail">Failures only</option>
        </select>
        <select class="log-filter-select" id="filterContext" onchange="loadLogs(1)">
            <option value="">All Contexts</option>
            <option value="chat">Chat</option>
            <option value="assignment">Assignment</option>
        </select>
        <button class="btn btn-ghost btn-sm" onclick="loadLogs(1)" style="margin-left:auto">↻ Refresh</button>
        <button class="btn btn-ghost btn-sm" onclick="confirmPrune()" style="color:var(--red-400)">🗑 Prune Old</button>
    </div>

    <div id="logTableWrap">
        <table class="log-table">
            <thead>
                <tr>
                    <th>Provider</th>
                    <th>Status</th>
                    <th>Tokens</th>
                    <th>Response</th>
                    <th>Context</th>
                    <th>Error</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody id="logTableBody">
                @foreach($stats['recent_errors'] as $err)
                <tr>
                    <td data-label="Provider"><span class="log-provider" style="color:{{ ['gemini'=>'#60a5fa','groq'=>'#fbbf24','openrouter'=>'#a78bfa','huggingface'=>'#fcd34d'][$err->provider] ?? '#9ca3af' }}">{{ ucfirst($err->provider) }}</span></td>
                    <td data-label="Status"><span class="log-failure">✗ Fail</span></td>
                    <td data-label="Tokens"><span class="log-ms">—</span></td>
                    <td data-label="Response"><span class="log-ms">{{ $err->response_time_ms }}ms</span></td>
                    <td data-label="Context"><span style="font-family:var(--font-m);font-size:10px;color:var(--ink-400)">{{ $err->context }}</span></td>
                    <td data-label="Error"><span class="log-error-msg">{{ $err->error_message }}</span></td>
                    <td data-label="Time"><span class="log-time">{{ \Carbon\Carbon::parse($err->created_at)->diffForHumans() }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="pagination-row" id="logPagination"></div>
    </div>
</div>

{{-- Danger zone --}}
<div style="display:flex;justify-content:flex-end;">
    <div style="font-family:var(--font-m);font-size:11px;color:var(--ink-500);text-align:right;line-height:1.7">
        Stats cached for 10 min · Last generated: {{ \Carbon\Carbon::parse($stats['generated_at'])->format('H:i:s') }}
        <br>Run <code style="color:var(--forest-600)">php artisan ai:prune-logs</code> to prune old records
    </div>
</div>

</div><!-- /monitor-wrap -->

{{-- Loading + Toast --}}
<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-spinner"></div>
</div>
<div class="toast-container" id="toastContainer"></div>

@endsection

@push('scripts')
<script>
const CSRF = '{{ csrf_token() }}';

// ── Live clock ─────────────────────────────────────────────────
function tick() {
    const el = document.getElementById('liveClock');
    if (el) el.textContent = new Date().toLocaleTimeString();
}
tick();
setInterval(tick, 1000);

// ── Auto-refresh stats every 5 minutes ────────────────────────
setInterval(refreshStats, 300000);

// ── Refresh stats (AJAX) ───────────────────────────────────────
async function refreshStats() {
    try {
        const res = await fetch('{{ route('admin.ai-monitor.stats') }}', {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
        });
        const data = await res.json();

        // Update summary tiles
        const s = data.summary;
        document.querySelector('#summaryGrid .summary-tile:nth-child(1) .tile-value').textContent = fmt(s.total_calls);
        document.querySelector('#summaryGrid .summary-tile:nth-child(2) .tile-value').textContent = fmt(s.total_tokens);
        document.querySelector('#summaryGrid .summary-tile:nth-child(3) .tile-value').textContent = s.success_rate + '%';
        document.querySelector('#summaryGrid .summary-tile:nth-child(4) .tile-value').innerHTML   = s.avg_response_ms + '<span style="font-size:14px">ms</span>';

        toast('Stats refreshed', 'success');
    } catch {
        toast('Failed to refresh stats', 'error');
    }
}

// ── Test single provider ───────────────────────────────────────
async function testProvider(provider) {
    const btn = document.getElementById('ptest-btn-' + provider);
    btn.disabled = true;
    btn.textContent = '…';

    showLoading(true);

    try {
        const res  = await fetch('{{ route('admin.ai-monitor.test') }}', {
            method:  'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body:    JSON.stringify({ provider }),
        });

        const data = await res.json();
        const r    = data.result;

        const card   = document.getElementById('pcard-' + provider);
        const badge  = document.getElementById('pbadge-' + provider);
        const led    = card.querySelector('.status-led');
        const errDiv = document.getElementById('perror-' + provider);
        const msEl   = document.getElementById('pms-' + provider);
        const tested = document.getElementById('ptested-' + provider);

        const status = r.online ? 'online' : (r.configured ? 'offline' : 'unconfigured');

        // Update classes
        card.className  = 'provider-card ' + status;
        led.className   = 'status-led ' + status;
        badge.className = 'status-badge ' + status;
        badge.textContent = status === 'online' ? '● Online' : (status === 'offline' ? '● Offline' : '○ Not Set');

        if (msEl) msEl.innerHTML = r.response_time_ms + '<span style="font-size:10px">ms</span>';
        tested.textContent = 'Tested: just now';

        if (r.error) {
            errDiv.textContent = '⚠ ' + r.error;
            errDiv.style.display = 'block';
        } else {
            errDiv.style.display = 'none';
        }

        toast(
            provider + ' is ' + (r.online ? 'online (' + r.response_time_ms + 'ms)' : 'offline'),
            r.online ? 'success' : 'error'
        );

    } catch {
        toast('Test failed for ' + provider, 'error');
    }

    showLoading(false);
    btn.disabled    = false;
    btn.textContent = '↺ Test';
}

// ── Test all providers ─────────────────────────────────────────
async function testAllProviders() {
    showLoading(true);

    try {
        const res  = await fetch('{{ route('admin.ai-monitor.test-all') }}', {
            method:  'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body:    '{}',
        });

        const data = await res.json();

        let online = 0;
        for (const [prov, r] of Object.entries(data.results)) {
            if (r.online) online++;
            await updateProviderCard(prov, r);
        }

        toast(`${online}/4 providers online`, online > 0 ? 'success' : 'error');

    } catch {
        toast('Test all failed', 'error');
    }

    showLoading(false);
}

async function updateProviderCard(provider, r) {
    const card   = document.getElementById('pcard-' + provider);
    const badge  = document.getElementById('pbadge-' + provider);
    const led    = card?.querySelector('.status-led');
    const errDiv = document.getElementById('perror-' + provider);
    const msEl   = document.getElementById('pms-' + provider);
    const tested = document.getElementById('ptested-' + provider);

    if (!card) return;

    const status = r.online ? 'online' : (r.configured ? 'offline' : 'unconfigured');

    card.className  = 'provider-card ' + status;
    if (led) led.className = 'status-led ' + status;
    if (badge) {
        badge.className   = 'status-badge ' + status;
        badge.textContent = status === 'online' ? '● Online' : (status === 'offline' ? '● Offline' : '○ Not Set');
    }

    if (msEl) msEl.innerHTML = r.response_time_ms + '<span style="font-size:10px">ms</span>';
    if (tested) tested.textContent = 'Tested: just now';

    if (errDiv) {
        if (r.error) { errDiv.textContent = '⚠ ' + r.error; errDiv.style.display = 'block'; }
        else { errDiv.style.display = 'none'; }
    }
}

// ── Load activity log ──────────────────────────────────────────
let currentLogPage = 1;

async function loadLogs(page = 1) {
    currentLogPage = page;

    const provider = document.getElementById('filterProvider').value;
    const status   = document.getElementById('filterStatus').value;
    const context  = document.getElementById('filterContext').value;

    const params = new URLSearchParams({ page, provider, status, context });

    try {
        const res  = await fetch(`{{ route('admin.ai-monitor.logs') }}?${params}`, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
        });

        const data = await res.json();

        const tbody = document.getElementById('logTableBody');
        const provColors = { gemini:'#60a5fa', groq:'#fbbf24', openrouter:'#a78bfa', huggingface:'#fcd34d' };

        if (!data.logs.length) {
            tbody.innerHTML = `<tr><td colspan="7" style="text-align:center;padding:24px;font-size:12px;color:var(--ink-400);">No logs match your filters.</td></tr>`;
        } else {
            tbody.innerHTML = data.logs.map(l => `
                <tr>
                    <td data-label="Provider"><span class="log-provider" style="color:${provColors[l.provider] ?? '#9ca3af'}">${cap(l.provider)}</span><br>
                    <span style="font-family:var(--font-m);font-size:9.5px;color:var(--ink-500)">${l.model ?? ''}</span></td>
                    <td data-label="Status">${l.success ? '<span class="log-success">✓ OK</span>' : '<span class="log-failure">✗ Fail</span>'}</td>
                    <td data-label="Tokens"><span class="log-ms">${fmt(l.tokens_used)}</span></td>
                    <td data-label="Response"><span class="log-ms">${l.response_time_ms}ms</span></td>
                    <td data-label="Context"><span style="font-family:var(--font-m);font-size:10px;color:var(--ink-400)">${l.context}</span></td>
                    <td data-label="Error">${l.error_message ? `<span class="log-error-msg" title="${esc(l.error_message)}">${esc(l.error_message)}</span>` : '<span style="color:var(--ink-600)">—</span>'}</td>
                    <td data-label="Time"><span class="log-time">${timeAgo(l.created_at)}</span></td>
                </tr>
            `).join('');
        }

        // Pagination
        buildPagination(data.page, data.total_pages);

    } catch (e) {
        toast('Failed to load logs', 'error');
    }
}

function buildPagination(current, total) {
    const el = document.getElementById('logPagination');
    if (total <= 1) { el.innerHTML = ''; return; }

    let html = `<button class="page-btn ${current === 1 ? 'disabled':''}" onclick="loadLogs(${current-1})">←</button>`;

    for (let p = Math.max(1, current-2); p <= Math.min(total, current+2); p++) {
        html += `<button class="page-btn ${p===current?'active':''}" onclick="loadLogs(${p})">${p}</button>`;
    }

    html += `<button class="page-btn ${current===total?'disabled':''}" onclick="loadLogs(${current+1})">→</button>`;
    el.innerHTML = html;
}

// ── Prune logs ─────────────────────────────────────────────────
function confirmPrune() {
    const days = prompt('Delete logs older than how many days? (default: 90)', '90');
    if (!days || isNaN(days)) return;

    fetch('{{ route('admin.ai-monitor.prune') }}', {
        method:  'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body:    JSON.stringify({ days: parseInt(days) }),
    })
    .then(r => r.json())
    .then(d => {
        toast(d.message, 'success');
        loadLogs(1);
    })
    .catch(() => toast('Prune failed', 'error'));
}

// ── Helpers ────────────────────────────────────────────────────
function fmt(n) { return Number(n || 0).toLocaleString(); }
function cap(s) { return s ? s.charAt(0).toUpperCase() + s.slice(1) : ''; }
function esc(s) { const d = document.createElement('div'); d.appendChild(document.createTextNode(s)); return d.innerHTML; }

function timeAgo(iso) {
    const diff = Math.floor((Date.now() - new Date(iso)) / 1000);
    if (diff < 60) return diff + 's ago';
    if (diff < 3600) return Math.floor(diff/60) + 'm ago';
    if (diff < 86400) return Math.floor(diff/3600) + 'h ago';
    return Math.floor(diff/86400) + 'd ago';
}

function showLoading(show) {
    document.getElementById('loadingOverlay').classList.toggle('active', show);
}

function toast(msg, type = 'success') {
    const c   = document.getElementById('toastContainer');
    const div = document.createElement('div');
    div.className = 'toast ' + type;
    div.textContent = msg;
    c.appendChild(div);
    setTimeout(() => div.remove(), 3500);
}

// Load log on page open
loadLogs(1);
</script>
@endpush
