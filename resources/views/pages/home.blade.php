@extends('layouts.guest')
@section('title', 'ReadPal – Your Academic Learning Companion')
@section('meta_description', 'ReadPal by Oracle Tech — materials, quizzes, live alerts, notes, CGPA calculator, and Karls community. Built exclusively for AAUA students.')

@section('content')

<style>
  .container-rp    { max-width:1140px; margin:0 auto; padding:0 1.5rem; }
  .rp-section      { padding:5rem 0; position:relative; }

  /* Eyebrow */
  .rp-eyebrow {
    display:inline-flex; align-items:center; gap:.5rem;
    font-family:'Cabinet Grotesk',sans-serif; font-size:.68rem; font-weight:700;
    letter-spacing:.2em; text-transform:uppercase; color:#15803D; margin-bottom:.75rem;
  }
  .rp-eyebrow::before { content:''; width:16px; height:1px; background:#15803D; }

  /* Feature pill */
  .feat-pill {
    display:inline-flex; align-items:center; gap:.3rem;
    padding:.28rem .75rem; border-radius:999px; font-size:.7rem; font-weight:600;
    background:rgba(21,128,61,.07); border:1px solid rgba(21,128,61,.18);
    color:rgba(247,242,232,.45);
  }

  /* Stats bar */
  .stats-bar { display:flex; overflow:hidden; border-radius:14px;
    border:1px solid rgba(212,136,42,.12);
    background:linear-gradient(135deg,rgba(26,26,36,.8),rgba(17,17,24,.9)); }
  .stat-cell { flex:1; padding:1.5rem 1rem; text-align:center; border-right:1px solid rgba(212,136,42,.1); }
  .stat-cell:last-child { border-right:none; }
  .stat-val  { display:block; font-family:'Cormorant Garamond',serif;
    font-size:2rem; font-weight:700; color:#F0B050; line-height:1; }
  .stat-lbl  { font-size:.7rem; color:rgba(247,242,232,.3); margin-top:.3rem; letter-spacing:.06em; }

  /* Feature card */
  .feat-card {
    background:linear-gradient(135deg,rgba(26,26,36,.92),rgba(17,17,24,.97));
    border:1px solid rgba(212,136,42,.1); border-radius:14px; padding:1.75rem;
    transition:border-color .22s, transform .22s, box-shadow .22s;
  }
  .feat-card:hover {
    border-color:rgba(212,136,42,.28); transform:translateY(-3px);
    box-shadow:0 0 40px rgba(212,136,42,.1), 0 8px 30px rgba(0,0,0,.45);
  }
  .feat-icon {
    width:44px; height:44px; border-radius:11px;
    background:rgba(21,128,61,.1); border:1px solid rgba(21,128,61,.2);
    display:flex; align-items:center; justify-content:center;
    color:#15803D; margin-bottom:1rem;
  }
  .feat-num { font-family:'Cabinet Grotesk',sans-serif; font-size:.62rem;
    color:rgba(21,128,61,.45); letter-spacing:.12em; margin-bottom:.4rem; }

  /* Showcase strip (CGPA / Karls sections) */
  .showcase-strip {
    background:linear-gradient(135deg,rgba(17,17,24,.85),rgba(13,15,20,.95));
    border-top:1px solid rgba(212,136,42,.08); border-bottom:1px solid rgba(212,136,42,.08);
  }

  /* CGPA mock */
  .gpa-row  { display:flex; justify-content:space-between; margin-bottom:.2rem; }
  .gpa-bar  { height:5px; border-radius:9px; background:rgba(247,242,232,.06); overflow:hidden; }
  .gpa-fill { height:100%; border-radius:9px;
    background:linear-gradient(90deg,#15803D,#F0B050); }

  /* Karl chat bubbles */
  .karl-row  { display:flex; gap:.5rem; align-items:flex-end; margin-bottom:.65rem; }
  .karl-row.own { flex-direction:row-reverse; }
  .karl-av   { width:23px; height:23px; border-radius:50%; flex-shrink:0;
    display:flex; align-items:center; justify-content:center;
    font-size:.65rem; font-weight:700; font-family:'Cormorant Garamond',serif; }
  .karl-av.kn { background:rgba(21,128,61,.14); border:1px solid rgba(21,128,61,.28); color:#15803D; }
  .karl-av.ka { background:rgba(247,242,232,.05); border:1px solid rgba(247,242,232,.1); color:rgba(247,242,232,.3); }
  .karl-av.ko { background:rgba(212,136,42,.1); border:1px solid rgba(212,136,42,.25); color:#F0B050; }
  .karl-bub  { padding:.42rem .78rem; border-radius:10px; font-size:.75rem; line-height:1.5; max-width:85%; }
  .karl-bub.kt { background:rgba(26,26,36,.9); border:1px solid rgba(247,242,232,.07);
    color:rgba(247,242,232,.6); border-top-left-radius:3px; }
  .karl-bub.km { background:rgba(21,128,61,.1); border:1px solid rgba(21,128,61,.2);
    color:rgba(247,242,232,.75); border-top-right-radius:3px; }

  /* CTA */
  .cta-strip {
    position:relative; overflow:hidden; text-align:center;
    padding:5.5rem 0; border-top:1px solid rgba(212,136,42,.1);
  }
  .cta-strip::before {
    content:''; position:absolute; top:50%; left:50%;
    transform:translate(-50%,-50%); width:700px; height:400px;
    background:radial-gradient(ellipse,rgba(212,136,42,.07),transparent 65%);
    pointer-events:none;
  }
  .cta-strip::after {
    content:''; position:absolute; top:0; left:0; right:0; height:1px;
    background:linear-gradient(90deg,transparent,rgba(212,136,42,.28),transparent);
  }

  /* Mock floating card */
  .hero-mock-wrap {
    position:absolute; right:0; top:50%;
    transform:translateY(-50%); width:330px; z-index:1; pointer-events:none;
  }
  .hero-mock-inner {
    background:linear-gradient(135deg,rgba(26,26,36,.97),rgba(17,17,24,.99));
    border:1px solid rgba(212,136,42,.18); border-radius:18px; padding:1.2rem;
    box-shadow:0 0 60px rgba(212,136,42,.07), 0 20px 60px rgba(0,0,0,.5);
  }
  .mock-row  { display:flex; align-items:center; gap:.55rem; padding:.48rem .62rem;
    border-radius:8px; margin-bottom:.3rem; font-size:.75rem; }
  .mock-row.act { background:rgba(21,128,61,.08); }
  .dot-g { width:5px; height:5px; border-radius:50%; background:#15803D; flex-shrink:0; }
  .dot-d { width:5px; height:5px; border-radius:50%; background:rgba(247,242,232,.1); flex-shrink:0; }
  .rp-badge-xs {
    padding:.16rem .5rem; border-radius:999px; font-size:.6rem; font-weight:700;
    background:rgba(21,128,61,.12); border:1px solid rgba(21,128,61,.24); color:#15803D;
  }

  /* Amber rule divider */
  .amber-rule { height:1px; background:linear-gradient(90deg,transparent,rgba(212,136,42,.18),transparent); }

  /* Check list item */
  .check-item { display:flex; align-items:flex-start; gap:.6rem;
    font-size:.86rem; color:rgba(247,242,232,.42); line-height:1.65; }

  @media (max-width:960px) { .hero-mock-wrap { display:none; } }
  @media (max-width:700px) {
    .stats-bar { flex-wrap:wrap; }
    .stat-cell { flex:1 1 40%; border-bottom:1px solid rgba(212,136,42,.1); }
    .showcase-grid, .karls-grid { grid-template-columns:1fr !important; }
  }


  /* Responsive Grid */
.feat-grid {
    display: grid;
    grid-template-columns: 1fr 1fr; /* 2 columns on desktop */
    gap: 1.25rem;
}

@media (max-width: 768px) {
    .feat-grid {
        grid-template-columns: 1fr; /* 1 column on mobile */
    }
    
    /* Remove the 'tall' effect on mobile so it flows naturally */
    .feat-card[style*="grid-row:span 2"] {
        grid-row: span 1 !important;
    }
}

</style>


{{-- ══════════════ HERO ══════════════════════════════════════════ --}}
<section class="rp-section" style="min-height:calc(100vh - 72px);display:flex;align-items:center;padding:5rem 0 4rem;">
  <div class="container-rp" style="position:relative;width:100%;">

    <div style="max-width:600px;position:relative;z-index:2;">
      <div class="rp-eyebrow fade-up">AAUA Sociology 300L · Now Live</div>

      <h1 class="fade-up delay-1"
          style="font-size:clamp(2.8rem,6.5vw,4.8rem);font-weight:700;color:#F7F2E8;
                 line-height:1.08;letter-spacing:-.025em;margin-bottom:1.25rem;">
        Your Academic<br>
        <em style="color:#F0B050;">All&#8209;In&#8209;One</em><br>
        Companion
      </h1>

      <p class="fade-up delay-2"
         style="font-size:1.02rem;color:rgba(247,242,232,.48);line-height:1.78;
                font-weight:400;max-width:490px;margin-bottom:1.75rem;">
        ReadPal combines lecture notes, self-assessment quizzes, live class alerts,
        personal notes, a CGPA calculator, and a student community —
        all in one focused platform built exclusively for AAUA.
      </p>

      <div class="fade-up delay-2" style="display:flex;flex-wrap:wrap;gap:.4rem;margin-bottom:2rem;">
        <span class="feat-pill">📚 Materials &amp; PDFs</span>
        <span class="feat-pill">🧠 30-Q Quizzes</span>
        <span class="feat-pill">🔔 Live Alerts</span>
        <span class="feat-pill">✏️ My Notes</span>
        <span class="feat-pill">🎓 CGPA Calculator</span>
        <span class="feat-pill">💬 Karls Community</span>
      </div>

      <div class="fade-up delay-3" style="display:flex;gap:.75rem;flex-wrap:wrap;">
        <a href="{{ route('signup') }}"
           class="rp-btn-primary px-7 py-3.5 rounded-xl text-sm font-body inline-flex items-center gap-2">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <path d="M12 4.5v15m7.5-7.5h-15"/>
          </svg>
          Get Started Free
        </a>
        <a href="{{ route('about') }}"
           class="rp-btn-ghost px-7 py-3.5 rounded-xl text-sm font-body inline-flex items-center gap-1.5">
          Explore Features
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <path d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
          </svg>
        </a>
      </div>
    </div>

    {{-- Floating mock card --}}
    <div class="hero-mock-wrap fade-up delay-2">
      <div class="hero-mock-inner">
        <div style="display:flex;align-items:center;gap:.6rem;padding-bottom:.85rem;
                    border-bottom:1px solid rgba(212,136,42,.1);margin-bottom:.85rem;">
          <div style="width:30px;height:30px;border-radius:50%;background:linear-gradient(135deg,rgba(21,128,61,.25),rgba(21,128,61,.08));
                      border:1px solid rgba(21,128,61,.28);display:flex;align-items:center;justify-content:center;
                      font-family:'Cormorant Garamond',serif;font-size:.78rem;font-weight:700;color:#15803D;">R</div>
          <div>
            <p style="font-size:.76rem;color:#F7F2E8;font-weight:600;">Good morning, Scholar 👋</p>
            <p style="font-size:.66rem;color:rgba(247,242,232,.32);">300L · Sociology · AAUA</p>
          </div>
          <div style="margin-left:auto;width:7px;height:7px;border-radius:50%;
                      background:#15803D;box-shadow:0 0 7px rgba(21,128,61,.7);"></div>
        </div>
        <p style="font-size:.6rem;letter-spacing:.14em;text-transform:uppercase;
                  color:rgba(247,242,232,.22);margin-bottom:.5rem;">Today's Materials</p>
        <div class="mock-row act">
          <span class="dot-g"></span>
          <span style="flex:1;color:#F7F2E8;">SOC 303 – Crime &amp; Delinquency</span>
          <span class="rp-badge-xs">PDF</span>
        </div>
        <div class="mock-row">
          <span class="dot-d"></span>
          <span style="flex:1;color:rgba(247,242,232,.38);">SOC 307 – Rural Sociology</span>
          <span style="font-size:.6rem;color:rgba(247,242,232,.22);">Quiz</span>
        </div>
        <div class="mock-row">
          <span class="dot-d"></span>
          <span style="flex:1;color:rgba(247,242,232,.38);">SOC 305 – Political Sociology</span>
          <span style="font-size:.6rem;color:rgba(247,242,232,.22);">Notes</span>
        </div>
        <div style="margin-top:.85rem;padding:.68rem;background:rgba(21,128,61,.07);
                    border:1px solid rgba(21,128,61,.2);border-radius:9px;
                    display:flex;align-items:center;gap:.5rem;">
          <span style="font-size:.88rem;">🔔</span>
          <div>
            <p style="font-size:.7rem;color:#15803D;font-weight:600;">Lecture Alert</p>
            <p style="font-size:.65rem;color:rgba(247,242,232,.32);">SOC 303 starts in 15 minutes</p>
          </div>
        </div>
        <div style="margin-top:.65rem;padding:.62rem .85rem;background:rgba(212,136,42,.05);
                    border:1px solid rgba(212,136,42,.12);border-radius:9px;
                    display:flex;align-items:center;justify-content:space-between;">
          <div>
            <p style="font-size:.6rem;color:rgba(247,242,232,.22);text-transform:uppercase;letter-spacing:.1em;">CGPA</p>
            <p style="font-family:'Cormorant Garamond',serif;font-size:1.3rem;font-weight:700;color:#F0B050;line-height:1.1;">4.25</p>
          </div>
          <div style="text-align:right;">
            <p style="font-size:.63rem;color:rgba(247,242,232,.32);">2nd Class Upper</p>
            <p style="font-size:.6rem;color:rgba(247,242,232,.18);margin-top:.12rem;">300L · 1st Sem</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>


{{-- ══════════════ STATS BAR ═════════════════════════════════════ --}}
<div class="container-rp" style="padding-bottom:1rem;">
  <div class="stats-bar fade-up">
    <div class="stat-cell"><strong class="stat-val">6</strong><p class="stat-lbl">Core Features</p></div>
    <div class="stat-cell"><strong class="stat-val">30</strong><p class="stat-lbl">Questions Per Quiz</p></div>
    <div class="stat-cell"><strong class="stat-val">5.0</strong><p class="stat-lbl">CGPA Scale</p></div>
    <div class="stat-cell"><strong class="stat-val">24h</strong><p class="stat-lbl">DM Auto-Reset</p></div>
    <div class="stat-cell"><strong class="stat-val">Live</strong><p class="stat-lbl">Class Alerts</p></div>
  </div>
</div>

<div class="amber-rule" style="margin:3.5rem 0 0;"></div>


{{-- ══════════════ CORE FEATURES ══════════════════════════════════ --}}
<section class="rp-section">
  <div class="container-rp">
    <div style="text-align:center;margin-bottom:3.5rem;">
      <div class="rp-eyebrow" style="justify-content:center;">What's Inside</div>
      <h2 style="font-size:clamp(2rem,4vw,3rem);font-weight:700;color:#F7F2E8;margin:.2rem 0 .75rem;">
        Everything You Need to Excel
      </h2>
      <p style="color:rgba(247,242,232,.4);max-width:460px;margin:0 auto;font-size:.93rem;line-height:1.7;">
        Six tightly integrated tools — from materials to grades to community —
        covering every dimension of your academic life.
      </p>
    </div>

    <div class="feat-grid">

      {{-- A: Materials – tall --}}
      <div class="feat-card fade-up" style="grid-row:span 2;">
        <div class="feat-num">01 / 06</div>
        <div class="feat-icon" style="width:50px;height:50px;border-radius:13px;">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m.75 12l3 3m0 0l3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
          </svg>
        </div>
        <h3 style="font-size:1.35rem;font-weight:700;color:#F7F2E8;margin-bottom:.65rem;">
          Lecture Notes &amp; PDF Downloads
        </h3>
        <p style="font-size:.89rem;color:rgba(247,242,232,.43);line-height:1.82;margin-bottom:1.25rem;">
          A centralized, secure platform where lecturers distribute all course materials.
          Access, view, and download everything in
          <strong style="color:#15803D;">PDF format</strong>
          directly to your device for reliable offline reading — anytime, anywhere.
        </p>
        <div style="display:flex;gap:.4rem;flex-wrap:wrap;">
          <span style="padding:.2rem .6rem;border-radius:999px;font-size:.65rem;font-weight:700;
                       background:rgba(21,128,61,.1);border:1px solid rgba(21,128,61,.22);color:#15803D;">
            PDF Downloads
          </span>
          <span style="padding:.2rem .6rem;border-radius:999px;font-size:.65rem;font-weight:700;
                       background:rgba(21,128,61,.1);border:1px solid rgba(21,128,61,.22);color:#15803D;">
            Offline Access
          </span>
          <span style="padding:.2rem .6rem;border-radius:999px;font-size:.65rem;font-weight:700;
                       background:rgba(21,128,61,.1);border:1px solid rgba(21,128,61,.22);color:#15803D;">
            Secure
          </span>
        </div>
      </div>

      {{-- B: Quizzes --}}
      <div class="feat-card fade-up delay-1">
        <div class="feat-num">02 / 06</div>
        <div class="feat-icon">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"/>
          </svg>
        </div>
        <h3 style="font-size:1.15rem;font-weight:700;color:#F7F2E8;margin-bottom:.5rem;">
          Interactive Self-Assessment
        </h3>
        <p style="font-size:.87rem;color:rgba(247,242,232,.4);line-height:1.75;">
          Every lesson includes a mandatory <strong style="color:#15803D;">30-question test</strong>
          to immediately verify your understanding and close knowledge gaps before exams.
        </p>
      </div>

      {{-- C: Alerts --}}
      <div class="feat-card fade-up delay-2">
        <div class="feat-num">03 / 06</div>
        <div class="feat-icon">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
          </svg>
        </div>
        <h3 style="font-size:1.15rem;font-weight:700;color:#F7F2E8;margin-bottom:.5rem;">
          Live Lecture Alerts
        </h3>
        <p style="font-size:.87rem;color:rgba(247,242,232,.4);line-height:1.75;">
          Track your class timetable and receive <strong style="color:#15803D;">push notifications</strong>
          15 minutes before every scheduled lecture. Never miss a class again.
        </p>
      </div>
    </div>

    {{-- D: Notes – full-width --}}
    <div class="feat-card fade-up" style="margin-top:1.25rem;display:flex;gap:2rem;align-items:center;flex-wrap:wrap;">
      <div style="flex:1;min-width:220px;">
        <div class="feat-num">04 / 06</div>
        <div class="feat-icon" style="width:44px;height:44px;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/>
          </svg>
        </div>
        <h3 style="font-size:1.15rem;font-weight:700;color:#F7F2E8;margin-bottom:.5rem;">
          Custom Note Creation
        </h3>
        <p style="font-size:.87rem;color:rgba(247,242,232,.4);line-height:1.75;">
          Create, edit, and store personalized notes directly in the app.
          Jot summaries, key points, or research — all in one organised space.
        </p>
      </div>
      <div style="flex:1;min-width:220px;background:rgba(13,15,20,.65);
                  border:1px solid rgba(212,136,42,.1);border-radius:12px;padding:1.1rem;">
        <p style="font-size:.6rem;letter-spacing:.14em;text-transform:uppercase;
                  color:rgba(247,242,232,.2);margin-bottom:.6rem;">My Notes</p>
        <div style="display:flex;align-items:center;gap:.5rem;padding:.42rem 0;border-bottom:1px solid rgba(212,136,42,.08);">
          <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="rgba(21,128,61,.45)" stroke-width="2.5"><path d="M9 12.75L11.25 15 15 9.75"/></svg>
          <span style="font-size:.78rem;color:rgba(247,242,232,.42);">SOC 303 – Week 4 summary</span>
        </div>
        <div style="display:flex;align-items:center;gap:.5rem;padding:.42rem 0;border-bottom:1px solid rgba(212,136,42,.08);">
          <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="rgba(21,128,61,.45)" stroke-width="2.5"><path d="M9 12.75L11.25 15 15 9.75"/></svg>
          <span style="font-size:.78rem;color:rgba(247,242,232,.42);">Key theories: Durkheim on crime</span>
        </div>
        <div style="display:flex;align-items:center;gap:.5rem;padding:.42rem 0;border-bottom:1px solid rgba(212,136,42,.08);">
          <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="rgba(21,128,61,.45)" stroke-width="2.5"><path d="M9 12.75L11.25 15 15 9.75"/></svg>
          <span style="font-size:.78rem;color:rgba(247,242,232,.42);">Rural-urban migration notes</span>
        </div>
        <div style="display:flex;align-items:center;gap:.5rem;padding:.42rem 0;">
          <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="rgba(21,128,61,.45)" stroke-width="2.5"><path d="M9 12.75L11.25 15 15 9.75"/></svg>
          <span style="font-size:.78rem;color:rgba(247,242,232,.42);">Exam prep checklist</span>
        </div>
      </div>
    </div>
  </div>
</section>

<div class="amber-rule"></div>


{{-- ══════════════ CGPA SHOWCASE ══════════════════════════════════ --}}
<section class="rp-section showcase-strip">
  <div class="container-rp">
    <div class="showcase-grid" style="display:grid;grid-template-columns:1fr 1fr;gap:3.5rem;align-items:center;">

      <div class="fade-up">
        <div class="rp-eyebrow">New Feature · 05 / 06</div>
        <h2 style="font-size:clamp(2rem,4vw,3rem);font-weight:700;color:#F7F2E8;margin:.2rem 0 1rem;">
          AAUA CGPA<br>Calculator
        </h2>
        <p style="font-size:.92rem;color:rgba(247,242,232,.43);line-height:1.85;margin-bottom:1.5rem;">
          A full 5.0-scale CGPA tracker built for AAUA's grading system. Log every course
          across all 8 semesters, pick from predefined course codes per level, and watch
          your cumulative GPA compute in real time — with official degree classification.
        </p>
        <div style="display:flex;flex-direction:column;gap:.6rem;margin-bottom:1.75rem;">
          <div class="check-item">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#15803D" stroke-width="2.5" style="flex-shrink:0;margin-top:.15rem;"><path d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Grades A–F auto-mapped to AAUA point values: 5, 4, 3, 2, 1, 0
          </div>
          <div class="check-item">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#15803D" stroke-width="2.5" style="flex-shrink:0;margin-top:.15rem;"><path d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Quality points computed per course — units × grade points
          </div>
          <div class="check-item">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#15803D" stroke-width="2.5" style="flex-shrink:0;margin-top:.15rem;"><path d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Predefined course dropdowns per level &amp; semester — no typing needed
          </div>
          <div class="check-item">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#15803D" stroke-width="2.5" style="flex-shrink:0;margin-top:.15rem;"><path d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            First Class · 2nd Upper · 2nd Lower · Third Class · Pass
          </div>
        </div>
        <a href="{{ route('signup') }}"
           class="rp-btn-primary px-7 py-3 rounded-xl text-sm font-body inline-flex items-center gap-1.5">
          Calculate My CGPA →
        </a>
      </div>

      {{-- CGPA mock panel --}}
      <div class="fade-up delay-2">
        <div style="background:linear-gradient(135deg,rgba(26,26,36,.95),rgba(17,17,24,.98));
                    border:1px solid rgba(212,136,42,.14);border-radius:16px;overflow:hidden;">
          <div style="padding:.9rem 1.25rem;border-bottom:1px solid rgba(212,136,42,.1);
                      display:flex;align-items:center;justify-content:space-between;">
            <p style="font-size:.9rem;font-weight:700;color:#F7F2E8;">CGPA Overview</p>
            <span style="font-size:.66rem;color:#F0B050;letter-spacing:.04em;">2nd Class Upper</span>
          </div>
          <div style="padding:1.1rem 1.25rem;border-bottom:1px solid rgba(212,136,42,.08);
                      display:flex;align-items:flex-end;gap:.75rem;">
            <span style="font-family:'Cormorant Garamond',serif;font-size:3.5rem;font-weight:700;color:#F0B050;line-height:1;">3.87</span>
            <div style="margin-bottom:.3rem;">
              <p style="font-size:.7rem;color:rgba(247,242,232,.22);">/ 5.00</p>
              <p style="font-size:.68rem;color:rgba(247,242,232,.2);margin-top:.15rem;">48 units · 186 QP</p>
            </div>
          </div>
          <div style="padding:1rem 1.25rem;">
            <div style="margin-bottom:.8rem;">
              <div class="gpa-row"><span style="font-size:.73rem;color:rgba(247,242,232,.32);">100L · 1st</span><span style="font-size:.73rem;font-weight:600;color:#F7F2E8;">3.60</span></div>
              <div class="gpa-bar" style="margin-top:.2rem;"><div class="gpa-fill" style="width:72%;"></div></div>
            </div>
            <div style="margin-bottom:.8rem;">
              <div class="gpa-row"><span style="font-size:.73rem;color:rgba(247,242,232,.32);">100L · 2nd</span><span style="font-size:.73rem;font-weight:600;color:#F7F2E8;">3.75</span></div>
              <div class="gpa-bar" style="margin-top:.2rem;"><div class="gpa-fill" style="width:75%;"></div></div>
            </div>
            <div style="margin-bottom:.8rem;">
              <div class="gpa-row"><span style="font-size:.73rem;color:rgba(247,242,232,.32);">200L · 1st</span><span style="font-size:.73rem;font-weight:600;color:#F7F2E8;">3.90</span></div>
              <div class="gpa-bar" style="margin-top:.2rem;"><div class="gpa-fill" style="width:78%;"></div></div>
            </div>
            <div style="margin-bottom:.8rem;">
              <div class="gpa-row"><span style="font-size:.73rem;color:rgba(247,242,232,.32);">200L · 2nd</span><span style="font-size:.73rem;font-weight:600;color:#F7F2E8;">4.10</span></div>
              <div class="gpa-bar" style="margin-top:.2rem;"><div class="gpa-fill" style="width:82%;"></div></div>
            </div>
            <div>
              <div class="gpa-row"><span style="font-size:.73rem;color:rgba(247,242,232,.32);">300L · 1st</span><span style="font-size:.73rem;font-weight:600;color:#F7F2E8;">4.05</span></div>
              <div class="gpa-bar" style="margin-top:.2rem;"><div class="gpa-fill" style="width:81%;"></div></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<div class="amber-rule"></div>


{{-- ══════════════ KARLS SHOWCASE ═════════════════════════════════ --}}
<section class="rp-section">
  <div class="container-rp">
    <div class="karls-grid" style="display:grid;grid-template-columns:1fr 1fr;gap:3.5rem;align-items:center;">

      {{-- Karls mock --}}
      <div class="fade-up" style="order:1;">
        <div style="background:linear-gradient(135deg,rgba(26,26,36,.97),rgba(17,17,24,.99));
                    border:1px solid rgba(212,136,42,.16);border-radius:16px;
                    overflow:hidden;max-width:330px;">
          <div style="padding:.78rem 1rem;border-bottom:1px solid rgba(212,136,42,.1);
                      display:flex;align-items:center;gap:.55rem;">
            <span style="font-size:.95rem;color:#15803D;font-weight:700;font-family:'Cormorant Garamond',serif;">#</span>
            <p style="font-size:.88rem;font-weight:700;color:#F7F2E8;">general</p>
            <div style="margin-left:auto;display:flex;align-items:center;gap:.4rem;">
              <span style="font-size:.6rem;color:#15803D;letter-spacing:.06em;">Live</span>
              <div style="width:6px;height:6px;border-radius:50%;background:#15803D;
                          box-shadow:0 0 6px rgba(21,128,61,.7);"></div>
            </div>
          </div>
          <div style="padding:1rem;">
            <div class="karl-row">
              <div class="karl-av kn">C</div>
              <div>
                <p style="font-size:.6rem;color:#15803D;margin-bottom:.16rem;">Chiamaka</p>
                <div class="karl-bub kt">Has anyone done the SOC 305 assignment? 😅</div>
              </div>
            </div>
            <div class="karl-row">
              <div class="karl-av ka">?</div>
              <div>
                <p style="font-size:.6rem;color:rgba(247,242,232,.22);margin-bottom:.16rem;">Anonymous</p>
                <div class="karl-bub kt">Not yet but the deadline is tomorrow 😭</div>
              </div>
            </div>
            <div class="karl-row own">
              <div class="karl-av ko">Y</div>
              <div style="display:flex;flex-direction:column;align-items:flex-end;">
                <p style="font-size:.6rem;color:#F0B050;margin-bottom:.16rem;">You</p>
                <div class="karl-bub km">I dropped notes in the SOC 305 thread 👆</div>
              </div>
            </div>
          </div>
          <div style="padding:.62rem 1rem;border-top:1px solid rgba(212,136,42,.08);
                      display:flex;align-items:center;gap:.5rem;">
            <div style="flex:1;background:rgba(13,15,20,.65);border:1px solid rgba(212,136,42,.1);
                        border-radius:8px;padding:.38rem .68rem;font-size:.72rem;color:rgba(247,242,232,.22);">
              Drop a karl…
            </div>
            <div style="width:27px;height:27px;border-radius:8px;background:rgba(21,128,61,.14);
                        border:1px solid rgba(21,128,61,.24);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
              <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="#15803D" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/>
              </svg>
            </div>
          </div>
          <div style="padding:.4rem 1rem .6rem;border-top:1px solid rgba(212,136,42,.05);
                      display:flex;align-items:center;gap:.4rem;">
            <div style="width:11px;height:11px;border-radius:3px;border:1px solid rgba(247,242,232,.1);"></div>
            <span style="font-size:.6rem;color:rgba(247,242,232,.22);">Post anonymously</span>
            <span style="margin-left:auto;font-size:.58rem;color:rgba(212,136,42,.38);">DM enabled for named posts</span>
          </div>
        </div>
      </div>

      {{-- Text --}}
      <div class="fade-up delay-1" style="order:2;">
        <div class="rp-eyebrow">Exclusive to ReadPal · 06 / 06</div>
        <h2 style="font-size:clamp(2rem,4vw,3rem);font-weight:700;color:#F7F2E8;margin:.2rem 0 1rem;">
          Karls<span style="color:#F0B050;">·</span>Space<br>Student Community
        </h2>
        <p style="font-size:.92rem;color:rgba(247,242,232,.43);line-height:1.85;margin-bottom:1.5rem;">
          A thread-based community platform built into ReadPal. Drop
          <em>karls</em> in public threads, post anonymously when you want, and
          send private messages that auto-delete within 24 hours of being read.
        </p>
        <div style="display:flex;flex-direction:column;gap:.6rem;margin-bottom:1.75rem;">
          <div class="check-item">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#15803D" stroke-width="2.5" style="flex-shrink:0;margin-top:.15rem;"><path d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Automatic #general thread open to all authenticated users
          </div>
          <div class="check-item">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#15803D" stroke-width="2.5" style="flex-shrink:0;margin-top:.15rem;"><path d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Post under your real name or completely anonymously
          </div>
          <div class="check-item">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#15803D" stroke-width="2.5" style="flex-shrink:0;margin-top:.15rem;"><path d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Click any named poster to view their profile or send a private karl
          </div>
          <div class="check-item">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#15803D" stroke-width="2.5" style="flex-shrink:0;margin-top:.15rem;"><path d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Private DMs auto-delete every 24 hours once read
          </div>
          <div class="check-item">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#15803D" stroke-width="2.5" style="flex-shrink:0;margin-top:.15rem;"><path d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Live 15-second poll — new karls appear without page refresh
          </div>
        </div>
        <a href="{{ route('signup') }}"
           class="rp-btn-primary px-7 py-3 rounded-xl text-sm font-body inline-flex items-center gap-1.5">
          Join the Community →
        </a>
      </div>
    </div>
  </div>
</section>

<div class="amber-rule"></div>


{{-- ══════════════ CTA ════════════════════════════════════════════ --}}
<section class="cta-strip">
  <div class="container-rp" style="position:relative;z-index:1;">
    <div class="rp-eyebrow fade-up" style="justify-content:center;">Get Started Today</div>
    <h2 class="fade-up delay-1"
        style="font-size:clamp(2rem,4.5vw,3.5rem);font-weight:700;color:#F7F2E8;
               margin:.25rem 0 1rem;line-height:1.15;">
      Everything Your Academic Life Needs,<br>In One Place
    </h2>
    <p class="fade-up delay-2"
       style="color:rgba(247,242,232,.4);max-width:450px;margin:0 auto 2.25rem;
              line-height:1.75;font-size:.93rem;">
      Free, focused, and built exclusively for AAUA Sociology 300L students by Oracle Tech.
    </p>
    <div class="fade-up delay-3" style="display:flex;gap:.75rem;justify-content:center;flex-wrap:wrap;">
      <a href="{{ route('signup') }}"
         class="rp-btn-primary px-8 py-3.5 rounded-xl text-sm font-body">
        Create Free Account
      </a>
      <a href="{{ route('contact') }}"
         class="rp-btn-ghost px-8 py-3.5 rounded-xl text-sm font-body">
        Talk to Oracle Tech
      </a>
    </div>
  </div>
</section>

@endsection