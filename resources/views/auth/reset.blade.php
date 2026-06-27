@extends('layouts.guest')
@section('title', 'Reset Password')

@section('content')

{{-- ── Google Fonts ──────────────────────────────────────────────── --}}
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,600&family=DM+Sans:wght@300;400;500&family=Crimson+Pro:ital,wght@0,300;1,300&display=swap" rel="stylesheet" />

<style>
    html, body {
    max-width: 100%;
    overflow-x: hidden;
}


    :root {
        --ink-950: #020617;
        --ink-900: #0f172a;
        --ink-800: #1e293b;
        --ink-700: #334155;
        --ink-600: #475569;
        --ink-400: #94a3b8;
        --ink-300: #cbd5e1;
        --ink-100: #f1f5f9;
        --forest-900: #14532d;
        --forest-800: #166534;
        --forest-700: #15803d;
        --forest-600: #16a34a;
        --forest-500: #22c55e;
        --forest-400: #4ade80;
        --forest-300: #86efac;
        --gold:    #d4a853;
        --gold-dim: #a07830;
    }

    
   
    /* ── Page Shell ─────────────────────────────────────────────── */
    .login-shell {
        flex: 1;
        display: grid;
        grid-template-columns: 1fr 1fr;
        min-height: 100vh;
    }

    /* ── LEFT PANEL ─────────────────────────────────────────────── */
    .panel-left {
        position: relative;
        overflow: hidden;
        background: var(--ink-900);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 3rem;
    }

    /* Radial glow behind crest */
    .panel-left::before {
        content: '';
        position: absolute;
        top: 50%; left: 50%;
        width: 520px; height: 520px;
        transform: translate(-50%, -50%);
        background: radial-gradient(ellipse at center,
            rgba(22,163,74,.18) 0%,
            rgba(21,128,61,.07) 45%,
            transparent 70%);
        pointer-events: none;
        z-index: 0;
    }

    /* Grid texture */
    .panel-left::after {
        content: '';
        position: absolute; inset: 0;
        background-image:
            linear-gradient(rgba(74,222,128,.04) 1px, transparent 1px),
            linear-gradient(90deg, rgba(74,222,128,.04) 1px, transparent 1px);
        background-size: 48px 48px;
        pointer-events: none;
        z-index: 0;
    }

    /* Top-left corner bracket */
    .corner-bracket {
        position: absolute;
        top: 2rem; left: 2rem;
        width: 40px; height: 40px;
        border-top: 2px solid var(--forest-700);
        border-left: 2px solid var(--forest-700);
        opacity: .6;
    }
    .corner-bracket-br {
        position: absolute;
        bottom: 2rem; right: 2rem;
        width: 40px; height: 40px;
        border-bottom: 2px solid var(--forest-700);
        border-right: 2px solid var(--forest-700);
        opacity: .6;
    }

    /* Floating academic glyphs */
    .glyphs-canvas {
        position: absolute; inset: 0;
        pointer-events: none;
        z-index: 1;
    }
    .glyph {
        position: absolute;
        font-family: 'Playfair Display', serif;
        color: var(--forest-800);
        font-size: 1rem;
        opacity: 0;
        animation: floatUp var(--dur, 12s) ease-in-out var(--delay, 0s) infinite;
        user-select: none;
    }
    @keyframes floatUp {
        0%   { opacity: 0;    transform: translateY(0)     rotate(var(--rot, 0deg)); }
        15%  { opacity: .55; }
        85%  { opacity: .55; }
        100% { opacity: 0;    transform: translateY(-120px) rotate(calc(var(--rot, 0deg) + 6deg)); }
    }

    /* ── Crest ──────────────────────────────────────────────────── */
    .crest-wrap {
        position: relative; z-index: 2;
        display: flex; flex-direction: column;
        align-items: center; gap: 1.5rem;
        animation: fadeSlideUp .9s ease both;
    }
    @keyframes fadeSlideUp {
        from { opacity: 0; transform: translateY(24px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .crest-ring {
        width: 110px; height: 110px;
        border-radius: 50%;
        border: 1.5px solid var(--forest-700);
        display: flex; align-items: center; justify-content: center;
        position: relative;
        box-shadow: 0 0 40px rgba(22,163,74,.15), inset 0 0 30px rgba(22,163,74,.06);
    }
    .crest-ring::before {
        content: '';
        position: absolute; inset: 6px;
        border-radius: 50%;
        border: 1px solid rgba(74,222,128,.15);
    }
    .crest-icon {
        width: 48px; height: 48px;
        color: var(--forest-400);
        filter: drop-shadow(0 0 8px rgba(74,222,128,.4));
    }

    .crest-title {
        text-align: center;
    }
    .crest-title h2 {
        font-family: 'Playfair Display', serif;
        font-size: 1.75rem;
        font-weight: 700;
        color: #fff;
        letter-spacing: -.02em;
        line-height: 1.15;
    }
    .crest-title h2 span { color: var(--forest-400); }
    .crest-title p {
        font-family: 'Crimson Pro', serif;
        font-style: italic;
        font-size: .95rem;
        color: var(--ink-400);
        margin-top: .4rem;
        letter-spacing: .04em;
    }

    .crest-divider {
        width: 100px;
        height: 1px;
        background: linear-gradient(90deg, transparent, var(--forest-700), transparent);
        margin: .5rem auto 0;
    }

    /* Stat chips at bottom of left panel */
    .left-stats {
        position: absolute; bottom: 2.5rem;
        display: flex; gap: 1.5rem;
        z-index: 2;
        animation: fadeSlideUp 1s ease .4s both;
    }
    .stat-chip {
        text-align: center;
    }
    .stat-chip strong {
        display: block;
        font-family: 'Playfair Display', serif;
        font-size: 1.4rem;
        color: var(--forest-300);
        line-height: 1;
    }
    .stat-chip span {
        font-size: .65rem;
        text-transform: uppercase;
        letter-spacing: .12em;
        color: var(--ink-500, #64748b);
    }
    .stat-sep {
        width: 1px;
        height: 36px;
        background: var(--ink-700);
        align-self: center;
    }

    /* ── RIGHT PANEL ────────────────────────────────────────────── */
    .panel-right {
        background: var(--ink-950);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 3rem 2.5rem;
        position: relative;
    }

    /* Subtle top-right glow */
    .panel-right::before {
        content: '';
        position: absolute;
        top: -80px; right: -80px;
        width: 300px; height: 300px;
        background: radial-gradient(circle, rgba(22,163,74,.06), transparent 60%);
        pointer-events: none;
    }

    .form-card {
        width: 100%;
        max-width: 400px;
        position: relative;
        z-index: 1;
        animation: fadeSlideUp .8s ease .15s both;
    }

    /* ── Form Header ───────────────────────────────────────────── */
    .form-header {
        margin-bottom: 2rem;
    }
    .form-header .eyebrow {
        font-size: .68rem;
        font-weight: 500;
        letter-spacing: .18em;
        text-transform: uppercase;
        color: var(--forest-500);
        margin-bottom: .5rem;
    }
    .form-header h1 {
        font-family: 'Playfair Display', serif;
        font-size: 2.1rem;
        font-weight: 700;
        color: #fff;
        line-height: 1.1;
        letter-spacing: -.02em;
    }
    .form-header p {
        font-size: .85rem;
        color: var(--ink-400);
        margin-top: .5rem;
        font-weight: 300;
    }

    /* ── Input Styles (override Breeze/default) ─────────────────── */
    .form-card input[type="email"],
    .form-card input[type="password"],
    .form-card input[type="text"] {
        width: 100%;
        background: var(--ink-900) !important;
        border: 1px solid var(--ink-700) !important;
        border-radius: 10px !important;
        padding: .75rem 1rem !important;
        font-family: 'DM Sans', sans-serif !important;
        font-size: .875rem !important;
        color: var(--ink-100) !important;
        transition: border-color .2s, box-shadow .2s !important;
        outline: none !important;
    }
    .form-card input[type="email"]:focus,
    .form-card input[type="password"]:focus,
    .form-card input[type="text"]:focus {
        border-color: var(--forest-700) !important;
        box-shadow: 0 0 0 3px rgba(22,163,74,.12) !important;
    }
    .form-card input::placeholder { color: var(--ink-600) !important; }

    .form-card label {
        font-size: .75rem !important;
        font-weight: 500 !important;
        letter-spacing: .06em !important;
        text-transform: uppercase !important;
        color: var(--ink-400) !important;
        margin-bottom: .4rem !important;
        display: block !important;
    }

    /* Remember me checkbox */
    .form-card input[type="checkbox"] {
        accent-color: var(--forest-600);
        width: 15px; height: 15px;
    }

    /* Submit button */
    .btn-login {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: .5rem;
        width: 100%;
        padding: .8rem 1.5rem;
        background: var(--forest-700);
        border: 1px solid var(--forest-600);
        border-radius: 10px;
        color: #fff;
        font-family: 'DM Sans', sans-serif;
        font-size: .875rem;
        font-weight: 600;
        cursor: pointer;
        transition: background .2s, transform .15s, box-shadow .2s;
        position: relative;
        overflow: hidden;
        letter-spacing: .02em;
    }
    .btn-login::before {
        content: '';
        position: absolute; inset: 0;
        background: linear-gradient(135deg, rgba(255,255,255,.06) 0%, transparent 60%);
    }
    .btn-login:hover {
        background: var(--forest-600);
        box-shadow: 0 4px 20px rgba(22,163,74,.25);
        transform: translateY(-1px);
    }
    .btn-login:active { transform: translateY(0); }

    /* Override any existing submit buttons inside x-login */
    .form-card button[type="submit"] {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 100% !important;
        padding: .8rem 1.5rem !important;
        background: var(--forest-700) !important;
        border: 1px solid var(--forest-600) !important;
        border-radius: 10px !important;
        color: #fff !important;
        font-family: 'DM Sans', sans-serif !important;
        font-size: .875rem !important;
        font-weight: 600 !important;
        cursor: pointer !important;
        transition: background .2s, transform .15s, box-shadow .2s !important;
        letter-spacing: .02em !important;
    }
    .form-card button[type="submit"]:hover {
        background: var(--forest-600) !important;
        box-shadow: 0 4px 20px rgba(22,163,74,.25) !important;
        transform: translateY(-1px) !important;
    }

    /* Error messages */
    .form-card .text-red-600,
    .form-card .text-red-500 {
        color: #f87171 !important;
        font-size: .78rem !important;
    }

    /* ── Divider ────────────────────────────────────────────────── */
    .or-divider {
        display: flex; align-items: center; gap: .75rem;
        margin: 1.5rem 0;
    }
    .or-divider::before, .or-divider::after {
        content: '';
        flex: 1;
        height: 1px;
        background: var(--ink-800);
    }
    .or-divider span {
        font-size: .72rem;
        color: var(--ink-600);
        letter-spacing: .08em;
        text-transform: uppercase;
        white-space: nowrap;
    }

    /* ── OAuth Buttons ──────────────────────────────────────────── */
    .oauth-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: .75rem;
    }
    .oauth-btn {
        display: flex; align-items: center; justify-content: center; gap: .5rem;
        padding: .65rem 1rem;
        background: var(--ink-900);
        border: 1px solid var(--ink-700);
        border-radius: 10px;
        color: var(--ink-300);
        font-size: .8rem;
        font-weight: 500;
        text-decoration: none;
        transition: all .2s;
    }
    .oauth-btn:hover {
        border-color: var(--ink-600);
        background: var(--ink-800);
        color: #fff;
    }
    .oauth-btn svg { width: 16px; height: 16px; flex-shrink: 0; }

    /* ── Sign-up link ───────────────────────────────────────────── */
    .signup-row {
        margin-top: 1.75rem;
        text-align: center;
        font-size: .82rem;
        color: var(--ink-500, #64748b);
    }
    .signup-row a {
        color: var(--forest-400);
        font-weight: 500;
        text-decoration: none;
        transition: color .15s;
    }
    .signup-row a:hover { color: var(--forest-300); }

    /* ── Vertical rule between panels ──────────────────────────── */
    .panel-rule {
        position: absolute;
        left: 0; top: 10%; bottom: 10%;
        width: 1px;
        background: linear-gradient(180deg, transparent, var(--ink-700) 30%, var(--ink-700) 70%, transparent);
    }

    /* ── Mobile: stack vertically ───────────────────────────────── */
    @media (max-width: 768px) {
        .login-shell {
            grid-template-columns: 1fr;
        }
        .panel-left {
            min-height: 220px;
            padding: 2rem;
        }
        .left-stats { display: none; }
        .crest-title h2 { font-size: 1.4rem; }
        .panel-right { padding: 2rem 1.25rem; }
        .panel-rule { display: none; }
    }
</style>

<div class="login-shell">

    {{-- ══════════════════════════════════════ LEFT PANEL ══════ --}}
    <div class="!hidden md:!block p-8 panel-left">
        <div class="corner-bracket"></div>
        <div class="corner-bracket-br"></div>

        {{-- Floating academic glyphs --}}
        <div class="glyphs-canvas" aria-hidden="true">
            <span class="glyph" style="left:8%;  bottom:15%; --dur:14s; --delay:0s;   --rot:-8deg;  font-size:1.1rem;">∑</span>
            <span class="glyph" style="left:18%; bottom:10%; --dur:11s; --delay:2s;   --rot:5deg;   font-size:.85rem;">GPA</span>
            <span class="glyph" style="left:75%; bottom:18%; --dur:13s; --delay:.5s;  --rot:10deg;  font-size:1rem;">∂</span>
            <span class="glyph" style="left:85%; bottom:8%;  --dur:16s; --delay:3.5s; --rot:-5deg;  font-size:.8rem;">4.50</span>
            <span class="glyph" style="left:55%; bottom:12%; --dur:10s; --delay:1.2s; --rot:3deg;   font-size:1.2rem;">α</span>
            <span class="glyph" style="left:35%; bottom:20%; --dur:15s; --delay:4s;   --rot:-12deg; font-size:.9rem;">β</span>
            <span class="glyph" style="left:65%; bottom:22%; --dur:12s; --delay:2.8s; --rot:7deg;   font-size:.75rem;">σ²</span>
            <span class="glyph" style="left:25%; bottom:5%;  --dur:17s; --delay:1.7s; --rot:-3deg;  font-size:.85rem;">π</span>
            <span class="glyph" style="left:92%; bottom:30%; --dur:9s;  --delay:5s;   --rot:14deg;  font-size:1rem;">μ</span>
            <span class="glyph" style="left:5%;  bottom:40%; --dur:13s; --delay:3s;   --rot:-10deg; font-size:.8rem;">∞</span>
        </div>

        {{-- Institution Crest --}}
        <div class="crest-wrap">
            <div class="crest-ring">
                <svg class="w-12 h-12 text-forest-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
            </div>
            <div class="crest-title">
                <h2>Read<span>Pal</span></h2>
                <p>RECOVER YOUR ACCESS</p>
            </div>
        </div>

        {{-- Bottom stat chips --}}
        <div class="left-stats">
            <div class="stat-chip">
                <strong>5.0</strong>
                <span>Max CGPA</span>
            </div>
            <div class="stat-sep"></div>
            <div class="stat-chip">
                <strong>8</strong>
                <span>Semesters</span>
            </div>
            <div class="stat-sep"></div>
            <div class="stat-chip">
                <strong>A–F</strong>
                <span>Grade Scale</span>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════ RIGHT PANEL ═════ --}}
    <div class="panel-right">
        <div class="form-card">
            <div class="form-header">
                <div class="eyebrow">Security Check</div>
                <h1>Forgot Password?</h1>
                <p>Input your email again to verify and then, choose a new password.</p>
            </div>


        <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            {{-- Email --}}
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Email</label>
                <input 
                    type="email" 
                    name="email" 
                    required
                    class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 px-3 py-2"
                >
            </div>

            {{-- New Password --}}
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">New Password</label>
                <input 
                    type="password" 
                    name="password" 
                    required
                    class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 px-3 py-2"
                >
            </div>

            {{-- Confirm Password --}}
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Confirm Password</label>
                <input 
                    type="password" 
                    name="password_confirmation" 
                    required
                    class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 px-3 py-2"
                >
            </div>

            {{-- Submit --}}
            <button 
                type="submit"
                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2 rounded-lg font-semibold transition"
            >
                Reset Password
            </button>
        </form>
    </div>
    </div>
</div>
    </div>

</div>

@endsection


