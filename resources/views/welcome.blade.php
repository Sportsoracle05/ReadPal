@extends('layouts.guest')
@section('title', 'ReadPal')
@section('meta_description', 'ReadPal – Your Academic Learning Companion. Access lecture notes, self-assessment quizzes, live lecture alerts, and personal notes. Built for AAUA students by Oracle Tech.')

@section('content')

<style>
    /* ── Hero ──────────────────────────────────────────────────── */
    .hero {
        min-height: calc(100vh - 68px);
        display: flex;
        align-items: center;
        position: relative;
        overflow: hidden;
        padding: 5rem 0 4rem;
    }
    .hero-bg {
        position: absolute; inset: 0;
        background:
            radial-gradient(ellipse 60% 50% at 50% 30%, rgba(22,163,74,.1) 0%, transparent 65%),
            radial-gradient(ellipse 40% 40% at 80% 70%, rgba(22,163,74,.05) 0%, transparent 60%);
        pointer-events: none;
    }
    .hero-grid {
        position: absolute; inset: 0; pointer-events: none;
        background-image:
            linear-gradient(rgba(74,222,128,.03) 1px, transparent 1px),
            linear-gradient(90deg, rgba(74,222,128,.03) 1px, transparent 1px);
        background-size: 52px 52px;
    }
    .hero-content {
        position: relative; z-index: 2;
        max-width: 680px;
    }
    .hero-badge {
        display: inline-flex; align-items: center; gap: .5rem;
        padding: .3rem .9rem;
        background: var(--forest-950);
        border: 1px solid var(--forest-900);
        border-radius: 999px;
        font-size: .72rem;
        font-weight: 600;
        letter-spacing: .1em;
        text-transform: uppercase;
        color: var(--forest-400);
        margin-bottom: 1.5rem;
    }
    .hero-badge span { width: 6px; height: 6px; border-radius: 50%; background: var(--forest-500); }
    .hero h1 {
        font-family: 'Playfair Display', serif;
        font-size: clamp(2.4rem, 6vw, 4rem);
        font-weight: 700;
        color: #fff;
        line-height: 1.1;
        letter-spacing: -.03em;
        margin-bottom: 1.25rem;
    }
    .hero h1 em { font-style: italic; color: var(--forest-400); }
    .hero-sub {
        font-size: 1.05rem;
        color: var(--ink-400);
        line-height: 1.75;
        font-weight: 300;
        max-width: 520px;
        margin-bottom: 2rem;
    }
    .hero-actions { display: flex; gap: .75rem; flex-wrap: wrap; }

    /* App mockup floating card */
    .hero-visual {
        position: absolute;
        right: 0; top: 50%;
        transform: translateY(-50%);
        width: 380px;
        pointer-events: none;
        z-index: 1;
    }
    .mock-card {
        background: var(--ink-900);
        border: 1px solid var(--ink-700);
        border-radius: 20px;
        padding: 1.5rem;
        box-shadow: var(--shadow-lg), 0 0 60px rgba(22,163,74,.08);
    }
    .mock-card-header {
        display: flex; align-items: center; gap: .75rem;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid var(--ink-800);
    }
    .mock-avatar {
        width: 36px; height: 36px; border-radius: 50%;
        background: linear-gradient(135deg, var(--forest-800), var(--forest-950));
        border: 1px solid var(--forest-700);
        display: flex; align-items: center; justify-content: center;
        font-size: .75rem; color: var(--forest-300); font-weight: 700;
    }
    .mock-lesson-row {
        display: flex; align-items: center; justify-content: space-between;
        padding: .6rem .75rem;
        border-radius: 8px;
        margin-bottom: .4rem;
        font-size: .78rem;
    }
    .mock-lesson-row.active { background: rgba(22,163,74,.1); }
    .mock-lesson-row span  { color: var(--ink-400); }
    .mock-lesson-row .dot  { width: 6px; height: 6px; border-radius: 50%; background: var(--forest-500); flex-shrink: 0; }
    .mock-lesson-row .badge-sm {
        padding: .15rem .5rem; border-radius: 999px;
        font-size: .62rem; font-weight: 600;
        background: var(--forest-950); color: var(--forest-400);
        border: 1px solid var(--forest-900);
    }

    /* Stats bar */
    .stats-bar {
        display: flex; gap: 0;
        border: 1px solid var(--ink-800);
        border-radius: 14px;
        overflow: hidden;
        margin: 4rem 0;
    }
    .stat-item {
        flex: 1; padding: 1.5rem 1rem; text-align: center;
        border-right: 1px solid var(--ink-800);
    }
    .stat-item:last-child { border-right: none; }
    .stat-item strong {
        display: block;
        font-family: 'Playfair Display', serif;
        font-size: 2rem; font-weight: 700;
        color: var(--forest-300); line-height: 1;
    }
    .stat-item p { font-size: .78rem; color: var(--ink-500); margin-top: .3rem; }

    /* Features section */
    .feature-number {
        font-family: 'JetBrains Mono', monospace;
        font-size: .68rem; color: var(--forest-700);
        letter-spacing: .08em; margin-bottom: .6rem;
    }

    /* CTA section */
    .cta-section {
        position: relative; overflow: hidden;
        background: var(--ink-900);
        border-top: 1px solid var(--ink-800);
        border-bottom: 1px solid var(--ink-800);
        padding: 5rem 0;
        text-align: center;
    }
    .cta-section::before {
        content: '';
        position: absolute; top: 50%; left: 50%;
        transform: translate(-50%, -50%);
        width: 600px; height: 400px;
        background: radial-gradient(ellipse, rgba(22,163,74,.1) 0%, transparent 65%);
        pointer-events: none;
    }

    @media (max-width: 900px) {
        .hero-visual { display: none; }
        .stats-bar { flex-wrap: wrap; }
        .stat-item { flex: 1 1 45%; border-right: 1px solid var(--ink-800); border-bottom: 1px solid var(--ink-800); }
    }
    @media (max-width: 500px) {
        .stat-item { flex: 1 1 100%; }
    }
</style>

{{-- ════════════════════════════════════════════════ HERO ══════ --}}
<section class="hero">
    <div class="hero-bg"></div>
    <div class="hero-grid"></div>

    <div class="container" style="position:relative;z-index:2;width:100%;">
        <div class="hero-content fade-up">
            <div class="hero-badge">
                <span></span>
                Now Available for AAUA 300L Sociology
            </div>

            <h1>
                Your Academic<br><em>Learning Companion</em><br>Reimagined
            </h1>

            <p class="hero-sub">
                ReadPal brings your lecture notes, self-assessment quizzes, live class
                alerts, and personal notes into one elegant mobile experience —
                built specifically for you.
            </p>

            <div class="hero-actions">
                <a href="{{ route('signup') }}" class="btn btn-primary btn-lg">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    Get Started Free
                </a>
                <a href="{{ route('about') }}" class="btn btn-outline btn-lg">Explore Features</a>
            </div>
        </div>

        {{-- Mock App Card --}}
        <div class="hero-visual fade-up-d2">
            <div class="mock-card">
                <div class="mock-card-header">
                    <div class="mock-avatar">RP</div>
                    <div>
                        <p style="font-size:.82rem;color:#fff;font-weight:500;">Good morning, Scholar 👋</p>
                        <p style="font-size:.72rem;color:var(--ink-500);">300L · Sociology · AAUA</p>
                    </div>
                    <div style="margin-left:auto;width:8px;height:8px;border-radius:50%;
                                background:var(--forest-500);box-shadow:0 0 8px rgba(34,197,94,.6);" class="glow-pulse"></div>
                </div>

                <p style="font-size:.7rem;letter-spacing:.12em;text-transform:uppercase;color:var(--ink-600);margin-bottom:.6rem;">Today's Materials</p>

                <div class="mock-lesson-row active">
                    <div class="dot"></div>
                    <span style="flex:1;padding:0 .6rem;color:var(--ink-200);">SOC 303 – Crime & Delinquency</span>
                    <span class="badge-sm">PDF</span>
                </div>
                <div class="mock-lesson-row">
                    <div style="width:6px;height:6px;border-radius:50%;background:var(--ink-700);flex-shrink:0;"></div>
                    <span style="flex:1;padding:0 .6rem;">SOC 307 – Rural Sociology</span>
                    <span class="badge-sm" style="color:var(--ink-500);border-color:var(--ink-800);background:transparent;">Quiz</span>
                </div>
                <div class="mock-lesson-row">
                    <div style="width:6px;height:6px;border-radius:50%;background:var(--ink-700);flex-shrink:0;"></div>
                    <span style="flex:1;padding:0 .6rem;">SOC 305 – Political Sociology</span>
                    <span class="badge-sm" style="color:var(--ink-500);border-color:var(--ink-800);background:transparent;">Notes</span>
                </div>

                <div style="margin-top:1rem;padding:.75rem;background:rgba(22,163,74,.08);
                            border:1px solid var(--forest-900);border-radius:10px;
                            display:flex;align-items:center;gap:.6rem;">
                    <span style="font-size:1rem;">🔔</span>
                    <div>
                        <p style="font-size:.75rem;color:var(--forest-300);font-weight:500;">Lecture Alert</p>
                        <p style="font-size:.7rem;color:var(--ink-500);">SOC 303 starts in 15 minutes</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ════════════════════════════════════════════════ STATS ═════ --}}
<div class="container">
    <div class="stats-bar fade-up-d3">
        <div class="stat-item"><strong>300L</strong><p>Current Cohort</p></div>
        <div class="stat-item"><strong>30</strong><p>Questions Per Test</p></div>
        <div class="stat-item"><strong>PDF</strong><p>All Materials</p></div>
        <div class="stat-item"><strong>Live</strong><p>Class Alerts</p></div>
    </div>
</div>

{{-- ════════════════════════════════════════════════ FEATURES ══ --}}
<section class="section">
    <div class="container">
        <div style="text-align:center;margin-bottom:3.5rem;">
            <span class="eyebrow">Core Features</span>
            <h2>Everything You Need to Excel</h2>
            <p style="max-width:480px;margin:.75rem auto 0;">
                Four powerful tools designed around the real academic challenges
                faced by university students.
            </p>
        </div>

        <div class="grid-2" style="gap:1.5rem;align-items:start;">

            {{-- Feature A --}}
            <div class="card fade-up" style="grid-row:span 2;">
                <div class="feature-number">01 / 04</div>
                <div class="card-icon" style="width:56px;height:56px;border-radius:14px;">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m.75 12l3 3m0 0l3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                    </svg>
                </div>
                <h3>Lecture Note Repository & Downloads</h3>
                <p style="margin-top:.6rem;line-height:1.8;">
                    A centralized, secure platform where all course notes and reading materials
                    are distributed. Access, view, and download everything in <strong style="color:var(--forest-400);">PDF format</strong>
                    directly to your device for reliable offline reading — anytime, anywhere.
                </p>
                <div style="margin-top:1.25rem;display:flex;gap:.5rem;flex-wrap:wrap;">
                    <span class="badge badge-green">PDF Downloads</span>
                    <span class="badge badge-green">Offline Access</span>
                    <span class="badge badge-green">Secure</span>
                </div>
            </div>

            {{-- Feature B --}}
            <div class="card fade-up-d1">
                <div class="feature-number">02 / 04</div>
                <div class="card-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"/>
                    </svg>
                </div>
                <h3>Interactive Self-Assessment</h3>
                <p style="margin-top:.5rem;font-size:.88rem;line-height:1.7;">
                    Every lesson includes a mandatory <strong style="color:var(--forest-400);">30-question test</strong>
                    so you can immediately check your grasp of the material and identify knowledge gaps before exams.
                </p>
            </div>

            {{-- Feature C --}}
            <div class="card fade-up-d2">
                <div class="feature-number">03 / 04</div>
                <div class="card-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
                    </svg>
                </div>
                <h3>Live Lecture Alerts</h3>
                <p style="margin-top:.5rem;font-size:.88rem;line-height:1.7;">
                    Track your class timetable and receive <strong style="color:var(--forest-400);">live notifications</strong>
                    when a scheduled lecture is about to begin. Never miss a class again.
                </p>
            </div>
        </div>

        {{-- Feature D – full width --}}
        <div class="card fade-up-d3" style="margin-top:1.5rem;display:flex;gap:2rem;align-items:center;flex-wrap:wrap;">
            <div style="flex:1;min-width:240px;">
                <div class="feature-number">04 / 04</div>
                <div class="card-icon" style="width:48px;height:48px;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                    </svg>
                </div>
                <h3>Custom Note Creation</h3>
                <p style="margin-top:.5rem;font-size:.88rem;line-height:1.7;">
                    Create, edit, and store your own personalized notes directly within the app.
                    A convenient digital space for summaries, key points, and supplementary research —
                    all in one place.
                </p>
            </div>
            <div style="flex:1;min-width:240px;background:var(--ink-800);border-radius:12px;padding:1.25rem;border:1px solid var(--ink-700);">
                <p style="font-size:.7rem;letter-spacing:.12em;text-transform:uppercase;color:var(--ink-600);margin-bottom:.75rem;">My Notes</p>
                @foreach(['SOC 303 – Week 4 summary', 'Key theories: Durkheim on crime', 'Rural-urban migration notes', 'Exam prep checklist'] as $i => $note)
                <div style="display:flex;align-items:center;gap:.6rem;padding:.5rem 0;
                            border-bottom:{{ $i < 3 ? '1px solid var(--ink-700)' : 'none' }};">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="var(--forest-700)" stroke-width="2.5">
                        <path d="M9 12.75L11.25 15 15 9.75"/>
                    </svg>
                    <span style="font-size:.8rem;color:var(--ink-300);">{{ $note }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- ════════════════════════════════════════════════ CTA ════════ --}}
<section class="cta-section">
    <div class="container" style="position:relative;z-index:1;">
        <span class="eyebrow">Get Started Today</span>
        <h2 style="margin-bottom:1rem;">Ready to Elevate Your Academic Performance?</h2>
        <p style="max-width:460px;margin:0 auto 2rem;">
            Join your coursemates on ReadPal and take control of your learning experience
            at Adekunle Ajasin University.
        </p>
        <div style="display:flex;gap:.75rem;justify-content:center;flex-wrap:wrap;">
            <a href="{{ route('signup') }}" class="btn btn-primary btn-lg">Create Free Account</a>
            <a href="{{ route('contact') }}" class="btn btn-outline btn-lg">Talk to Oracle Tech</a>
        </div>
    </div>
</section>

@endsection
