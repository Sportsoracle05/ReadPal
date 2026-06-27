@extends('layouts.guest')
@section('title', 'About ReadPal')
@section('meta_description', 'Learn about ReadPal — materials, quizzes, alerts, notes, CGPA calculator, and Karls community. Developed by Oracle Tech for AAUA Sociology 300L.')

@section('content')

<style>
  .container-rp  { max-width:1140px; margin:0 auto; padding:0 1.5rem; }
  .rp-section    { padding:5rem 0; position:relative; }
  .rp-section-sm { padding:3rem 0; position:relative; }

  .rp-eyebrow {
    display:inline-flex; align-items:center; gap:.5rem;
    font-family:'Cabinet Grotesk',sans-serif; font-size:.68rem; font-weight:700;
    letter-spacing:.2em; text-transform:uppercase; color:#15803D; margin-bottom:.75rem;
  }
  .rp-eyebrow::before { content:''; width:16px; height:1px; background:#15803D; }

  /* Feature card */
  .feat-card {
    background:linear-gradient(135deg,rgba(26,26,36,.92),rgba(17,17,24,.97));
    border:1px solid rgba(212,136,42,.1); border-radius:14px; padding:1.6rem;
    transition:border-color .2s, transform .2s;
  }
  .feat-card:hover { border-color:rgba(212,136,42,.25); transform:translateY(-2px); }
  .feat-icon {
    width:42px; height:42px; border-radius:11px;
    background:rgba(21,128,61,.1); border:1px solid rgba(21,128,61,.2);
    display:flex; align-items:center; justify-content:center;
    color:#15803D; margin-bottom:.9rem;
  }
  .feat-num { font-family:'Cabinet Grotesk',sans-serif; font-size:.62rem;
    color:rgba(21,128,61,.4); letter-spacing:.12em; margin-bottom:.35rem; }

  /* Tag badge */
  .rp-tag {
    padding:.18rem .58rem; border-radius:999px; font-size:.64rem; font-weight:700;
    background:rgba(21,128,61,.1); border:1px solid rgba(21,128,61,.22); color:#15803D;
  }
  .rp-tag-blue {
    padding:.18rem .58rem; border-radius:999px; font-size:.64rem; font-weight:700;
    background:rgba(96,165,250,.08); border:1px solid rgba(96,165,250,.2); color:#60a5fa;
  }

  /* New pill */
  .new-pill {
    display:inline-flex; align-items:center; padding:.18rem .55rem;
    border-radius:999px; font-size:.62rem; font-weight:700; margin-left:.5rem;
    background:rgba(21,128,61,.1); border:1px solid rgba(21,128,61,.25); color:#15803D;
    vertical-align:middle;
  }

  /* Amber divider */
  .amber-rule { height:1px; background:linear-gradient(90deg,transparent,rgba(212,136,42,.18),transparent); }

  /* Info table */
  .info-table-row {
    display:flex; padding:.58rem 0; border-bottom:1px solid rgba(212,136,42,.08);
  }
  .info-table-row:last-child { border-bottom:none; }
  .info-key { width:42%; font-size:.78rem; color:rgba(247,242,232,.32); }
  .info-val { font-size:.86rem; color:rgba(247,242,232,.75); font-weight:500; }

  /* Timeline */
  .tl-item { position:relative; padding-left:42px; margin-bottom:1.75rem; }
  .tl-line  { position:absolute; left:16px; top:26px; bottom:-1.75rem; width:1px;
    background:linear-gradient(180deg,rgba(21,128,61,.4),rgba(212,136,42,.1)); }
  .tl-dot   { position:absolute; left:10px; top:1rem; width:13px; height:13px;
    border-radius:50%; background:rgba(21,128,61,.15); border:2px solid rgba(21,128,61,.4);
    box-shadow:0 0 8px rgba(21,128,61,.2); }

  /* Value card */
  .val-card {
    padding:1.4rem; border-radius:12px;
    background:linear-gradient(135deg,rgba(26,26,36,.85),rgba(17,17,24,.92));
    border:1px solid rgba(212,136,42,.1);
    border-left:3px solid rgba(21,128,61,.5);
  }

  /* CGPA grade row */
  .grade-row { display:flex; align-items:center; gap:.75rem; padding:.52rem .9rem;
    background:rgba(13,15,20,.6); border-radius:9px; border:1px solid rgba(212,136,42,.08);
    margin-bottom:.4rem; }

  /* Check item */
  .check-item { display:flex; align-items:flex-start; gap:.6rem;
    font-size:.86rem; color:rgba(247,242,232,.42); line-height:1.65; margin-bottom:.55rem; }

  /* Section strips */
  .strip-dark { background:linear-gradient(135deg,rgba(17,17,24,.8),rgba(13,15,20,.95));
    border-top:1px solid rgba(212,136,42,.08); border-bottom:1px solid rgba(212,136,42,.08); }

  @media (max-width:780px) {
    .two-col, .two-col-r { grid-template-columns:1fr !important; }
    .feat-grid { grid-template-columns:1fr !important; }
  }
</style>


{{-- ══════════ HERO ══════════════════════════════════════════════ --}}
<section class="rp-section" style="padding:5rem 0 4rem;text-align:center;position:relative;overflow:hidden;">
  <div style="position:absolute;top:-40%;left:50%;transform:translateX(-50%);
              width:700px;height:500px;pointer-events:none;
              background:radial-gradient(ellipse,rgba(212,136,42,.07),transparent 60%);"></div>
  <div class="container-rp" style="position:relative;z-index:1;">
    <div class="rp-eyebrow fade-up" style="justify-content:center;">About ReadPal</div>
    <h1 class="fade-up delay-1"
        style="font-size:clamp(2.8rem,6vw,4.8rem);font-weight:700;color:#F7F2E8;
               line-height:1.08;letter-spacing:-.025em;margin-bottom:1.1rem;">
      Built for Students,<br>by <em style="color:#F0B050;">Oracle Tech</em>
    </h1>
    <p class="fade-up delay-2"
       style="color:rgba(247,242,232,.45);max-width:540px;margin:0 auto;
              font-size:1.02rem;line-height:1.78;">
      ReadPal started as a materials platform and grew into a complete academic companion —
      six tightly integrated tools covering materials, quizzes, alerts, notes,
      CGPA tracking, and student community.
    </p>
  </div>
</section>

<div class="amber-rule"></div>


{{-- ══════════ OVERVIEW ══════════════════════════════════════════ --}}
<section class="rp-section">
  <div class="container-rp">
    <div class="two-col" style="display:grid;grid-template-columns:1fr 1fr;gap:3.5rem;align-items:center;">

      <div class="fade-up">
        <div class="rp-eyebrow">Our Story</div>
        <h2 style="font-size:clamp(1.9rem,3.5vw,2.7rem);font-weight:700;color:#F7F2E8;margin:.2rem 0 1rem;">
          Why ReadPal Exists
        </h2>
        <p style="font-size:.92rem;color:rgba(247,242,232,.43);line-height:1.85;margin-bottom:.9rem;">
          Sociology 300-level students at Adekunle Ajasin University faced the same
          frustrations every semester: materials scattered across WhatsApp groups,
          no structured way to self-assess, missed lectures, and no real academic
          community within the cohort.
        </p>
        <p style="font-size:.92rem;color:rgba(247,242,232,.43);line-height:1.85;margin-bottom:1.75rem;">
          Oracle Tech listened. ReadPal was designed from scratch around those exact pain
          points — and has since expanded based on direct student feedback to include
          a full CGPA calculator and the Karls community platform.
        </p>
        <div style="display:flex;gap:.65rem;flex-wrap:wrap;">
          <a href="{{ route('signup') }}" class="rp-btn-primary px-6 py-3 rounded-xl text-sm font-body">
            Join ReadPal
          </a>
          <a href="{{ route('feedback') }}" class="rp-btn-ghost px-6 py-3 rounded-xl text-sm font-body">
            Give Feedback
          </a>
        </div>
      </div>

      <div class="fade-up delay-2">
        <div style="background:linear-gradient(135deg,rgba(26,26,36,.95),rgba(17,17,24,.98));
                    border:1px solid rgba(212,136,42,.14);border-radius:16px;padding:1.75rem;">
          <p style="font-size:.62rem;letter-spacing:.16em;text-transform:uppercase;
                    color:rgba(247,242,232,.2);margin-bottom:1.1rem;">App Identity</p>
          <div class="info-table-row"><span class="info-key">App Name</span><span class="info-val">ReadPal</span></div>
          <div class="info-table-row"><span class="info-key">Developer</span><span class="info-val">Oracle Tech</span></div>
          <div class="info-table-row"><span class="info-key">Current Cohort</span><span class="info-val">Sociology 300L – AAUA</span></div>
          <div class="info-table-row"><span class="info-key">Platform</span><span class="info-val">Web Application (Mobile-Optimised)</span></div>
          <div class="info-table-row"><span class="info-key">Core Features</span><span class="info-val">6 Integrated Tools</span></div>
          <div class="info-table-row"><span class="info-key">Status</span><span class="info-val" style="color:#15803D;">Active · Continuously Improved</span></div>
        </div>
      </div>
    </div>
  </div>
</section>

<div class="amber-rule"></div>


{{-- ══════════ ALL 6 FEATURES ════════════════════════════════════ --}}
<section class="rp-section strip-dark">
  <div class="container-rp">
    <div style="text-align:center;margin-bottom:3rem;">
      <div class="rp-eyebrow" style="justify-content:center;">Features</div>
      <h2 style="font-size:clamp(1.9rem,3.5vw,2.8rem);font-weight:700;color:#F7F2E8;margin:.2rem 0 .75rem;">
        What ReadPal Offers
      </h2>
      <p style="color:rgba(247,242,232,.38);max-width:460px;margin:0 auto;font-size:.92rem;line-height:1.7;">
        Six tools — all integrated — designed around how AAUA students actually study.
      </p>
    </div>

    <div class="feat-grid" style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;">

      {{-- A: Materials --}}
      <div class="feat-card fade-up">
        <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:.75rem;">
          <div class="feat-icon" style="margin-bottom:0;">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
              <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m.75 12l3 3m0 0l3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
            </svg>
          </div>
          <div>
            <div class="feat-num">Feature A</div>
            <h3 style="font-size:1rem;font-weight:700;color:#F7F2E8;margin:0;">Lecture Notes &amp; Downloads</h3>
          </div>
        </div>
        <p style="font-size:.86rem;color:rgba(247,242,232,.4);line-height:1.78;margin-bottom:.9rem;">
          A centralized secure platform for lecturers to distribute all course notes.
          Download everything in PDF format for reliable offline reading.
        </p>
        <div style="display:flex;gap:.35rem;flex-wrap:wrap;">
          <span class="rp-tag">PDF Format</span>
          <span class="rp-tag">Offline Reading</span>
          <span class="rp-tag">Centralized</span>
        </div>
      </div>

      {{-- B: Quizzes --}}
      <div class="feat-card fade-up delay-1">
        <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:.75rem;">
          <div class="feat-icon" style="margin-bottom:0;">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"/>
            </svg>
          </div>
          <div>
            <div class="feat-num">Feature B</div>
            <h3 style="font-size:1rem;font-weight:700;color:#F7F2E8;margin:0;">Interactive Self-Assessment</h3>
          </div>
        </div>
        <p style="font-size:.86rem;color:rgba(247,242,232,.4);line-height:1.78;margin-bottom:.9rem;">
          Every lesson includes a mandatory 30-question test for immediate self-assessment
          and identification of knowledge gaps before exams.
        </p>
        <div style="display:flex;gap:.35rem;flex-wrap:wrap;">
          <span class="rp-tag">30 Questions</span>
          <span class="rp-tag">Per Lesson</span>
          <span class="rp-tag">Exam Prep</span>
        </div>
      </div>

      {{-- C: Alerts --}}
      <div class="feat-card fade-up delay-2">
        <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:.75rem;">
          <div class="feat-icon" style="margin-bottom:0;">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
              <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
            </svg>
          </div>
          <div>
            <div class="feat-num">Feature C</div>
            <h3 style="font-size:1rem;font-weight:700;color:#F7F2E8;margin:0;">Live Lecture Alerts</h3>
          </div>
        </div>
        <p style="font-size:.86rem;color:rgba(247,242,232,.4);line-height:1.78;margin-bottom:.9rem;">
          Track your full timetable and receive live push notifications 15 minutes
          before every scheduled lecture. Never miss a class again.
        </p>
        <div style="display:flex;gap:.35rem;flex-wrap:wrap;">
          <span class="rp-tag">Push Notifications</span>
          <span class="rp-tag">Timetable</span>
          <span class="rp-tag">Punctuality</span>
        </div>
      </div>

      {{-- D: Notes --}}
      <div class="feat-card fade-up delay-3">
        <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:.75rem;">
          <div class="feat-icon" style="margin-bottom:0;">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
              <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/>
            </svg>
          </div>
          <div>
            <div class="feat-num">Feature D</div>
            <h3 style="font-size:1rem;font-weight:700;color:#F7F2E8;margin:0;">Custom Note Creation</h3>
          </div>
        </div>
        <p style="font-size:.86rem;color:rgba(247,242,232,.4);line-height:1.78;margin-bottom:.9rem;">
          Create, edit, and store personalized notes directly within the app.
          Jot summaries, key points, or research — all in one organized place.
        </p>
        <div style="display:flex;gap:.35rem;flex-wrap:wrap;">
          <span class="rp-tag">Create &amp; Edit</span>
          <span class="rp-tag">Save to App</span>
          <span class="rp-tag">Personalized</span>
        </div>
      </div>

      {{-- E: CGPA --}}
      <div class="feat-card fade-up" style="border-color:rgba(21,128,61,.2);">
        <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:.75rem;">
          <div class="feat-icon" style="margin-bottom:0;">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/>
            </svg>
          </div>
          <div>
            <div class="feat-num">Feature E</div>
            <h3 style="font-size:1rem;font-weight:700;color:#F7F2E8;margin:0;">
              CGPA Calculator <span class="new-pill">New</span>
            </h3>
          </div>
        </div>
        <p style="font-size:.86rem;color:rgba(247,242,232,.4);line-height:1.78;margin-bottom:.9rem;">
          A full AAUA 5.0-scale CGPA calculator. Log courses per semester, select from
          predefined course codes, and automatically compute your semester GPA and
          cumulative CGPA with official degree classification.
        </p>
        <div style="display:flex;gap:.35rem;flex-wrap:wrap;">
          <span class="rp-tag">AAUA 5.0 Scale</span>
          <span class="rp-tag">A–F Grade Map</span>
          <span class="rp-tag">Auto-Calculation</span>
        </div>
      </div>

      {{-- F: Karls --}}
      <div class="feat-card fade-up delay-1" style="border-color:rgba(96,165,250,.15);">
        <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:.75rem;">
          <div class="feat-icon" style="margin-bottom:0;
               background:rgba(96,165,250,.08);border-color:rgba(96,165,250,.18);color:#60a5fa;">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
              <path stroke-linecap="round" stroke-linejoin="round"
                    d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z"/>
            </svg>
          </div>
          <div>
            <div class="feat-num" style="color:rgba(96,165,250,.4);">Feature F</div>
            <h3 style="font-size:1rem;font-weight:700;color:#F7F2E8;margin:0;">
              Karls Community <span class="new-pill" style="background:rgba(96,165,250,.08);border-color:rgba(96,165,250,.22);color:#60a5fa;">New</span>
            </h3>
          </div>
        </div>
        <p style="font-size:.86rem;color:rgba(247,242,232,.4);line-height:1.78;margin-bottom:.9rem;">
          A thread-based community platform exclusive to ReadPal users. Post karls publicly
          or anonymously, view named posters' profiles, and send private DMs that
          auto-delete within 24 hours of being read.
        </p>
        <div style="display:flex;gap:.35rem;flex-wrap:wrap;">
          <span class="rp-tag-blue">Public Threads</span>
          <span class="rp-tag-blue">Anonymous Mode</span>
          <span class="rp-tag-blue">Private DMs</span>
          <span class="rp-tag-blue">24h Auto-Delete</span>
        </div>
      </div>
    </div>
  </div>
</section>

<div class="amber-rule"></div>


{{-- ══════════ HOW KARLS WORKS ════════════════════════════════════ --}}
<section class="rp-section">
  <div class="container-rp" style="max-width:800px;">
    <div style="text-align:center;margin-bottom:2.75rem;">
      <div class="rp-eyebrow" style="justify-content:center;">Deep Dive</div>
      <h2 style="font-size:clamp(1.9rem,3.5vw,2.8rem);font-weight:700;color:#F7F2E8;margin:.2rem 0 .7rem;">
        How Karls Works
      </h2>
      <p style="color:rgba(247,242,232,.38);max-width:440px;margin:0 auto;font-size:.9rem;">
        Karls is ReadPal's built-in community space. Here's exactly how it operates.
      </p>
    </div>
    <div style="position:relative;">

      <div class="tl-item fade-up">
        <div class="tl-line"></div>
        <div class="tl-dot"></div>
        <h3 style="font-size:1.05rem;color:#F7F2E8;margin-bottom:.4rem;">Public Threads</h3>
        <p style="font-size:.87rem;color:rgba(247,242,232,.4);line-height:1.78;">
          All authenticated ReadPal users share a <strong style="color:rgba(247,242,232,.65);">#general</strong>
          thread by default. Additional threads can be created for specific topics.
          Every message inside a thread is called a <em>karl</em>.
        </p>
      </div>

      <div class="tl-item fade-up delay-1">
        <div class="tl-line"></div>
        <div class="tl-dot"></div>
        <h3 style="font-size:1.05rem;color:#F7F2E8;margin-bottom:.4rem;">Named vs Anonymous</h3>
        <p style="font-size:.87rem;color:rgba(247,242,232,.4);line-height:1.78;">
          When posting, you choose whether to appear as yourself or as
          <strong style="color:rgba(247,242,232,.65);">"Anonymous."</strong>
          Anonymous posters have their identity stored privately for admin moderation
          only — it is never visible to other students.
        </p>
      </div>

      <div class="tl-item fade-up delay-2">
        <div class="tl-line"></div>
        <div class="tl-dot"></div>
        <h3 style="font-size:1.05rem;color:#F7F2E8;margin-bottom:.4rem;">DM from Named Karls</h3>
        <p style="font-size:.87rem;color:rgba(247,242,232,.4);line-height:1.78;">
          If a student posts without anonymity, a DM chip appears beside their name.
          Clicking it opens a private conversation — only visible to the two parties.
        </p>
      </div>

      <div class="tl-item fade-up delay-3">
        <div class="tl-line"></div>
        <div class="tl-dot"></div>
        <h3 style="font-size:1.05rem;color:#F7F2E8;margin-bottom:.4rem;">24-Hour Auto-Delete</h3>
        <p style="font-size:.87rem;color:rgba(247,242,232,.4);line-height:1.78;">
          Private karls (DMs) reset nightly at midnight. Once a message has been read,
          it is marked for deletion and removed in the next reset cycle.
          Unread messages are also purged after 24 hours as a safety net.
        </p>
      </div>

      <div class="tl-item fade-up delay-4" style="margin-bottom:0;">
        <div class="tl-dot"></div>
        <h3 style="font-size:1.05rem;color:#F7F2E8;margin-bottom:.4rem;">Live Without Refresh</h3>
        <p style="font-size:.87rem;color:rgba(247,242,232,.4);line-height:1.78;">
          The thread feed polls every 15 seconds for new karls. Messages appear in
          real time without a page reload.
        </p>
      </div>

    </div>
  </div>
</section>

<div class="amber-rule"></div>


{{-- ══════════ CGPA CALCULATOR DETAILS ═══════════════════════════ --}}
<section class="rp-section strip-dark">
  <div class="container-rp" style="max-width:800px;">
    <div style="text-align:center;margin-bottom:2.75rem;">
      <div class="rp-eyebrow" style="justify-content:center;">Deep Dive</div>
      <h2 style="font-size:clamp(1.9rem,3.5vw,2.8rem);font-weight:700;color:#F7F2E8;margin:.2rem 0 .7rem;">
        How the CGPA Calculator Works
      </h2>
    </div>
    <div class="two-col-r" style="display:grid;grid-template-columns:1fr 1fr;gap:2.5rem;align-items:start;">

      <div class="fade-up">
        <h3 style="font-size:1.05rem;color:#F7F2E8;margin-bottom:.85rem;">AAUA Grading Scale</h3>
        <div class="grade-row">
          <span style="font-family:'Cormorant Garamond',serif;font-size:1.2rem;font-weight:700;width:20px;color:#15803D;">A</span>
          <span style="flex:1;font-size:.82rem;color:rgba(247,242,232,.38);">70–100%</span>
          <span style="font-family:'Cabinet Grotesk',sans-serif;font-size:.82rem;font-weight:700;color:#15803D;">5 pts</span>
        </div>
        <div class="grade-row">
          <span style="font-family:'Cormorant Garamond',serif;font-size:1.2rem;font-weight:700;width:20px;color:#73A67C;">B</span>
          <span style="flex:1;font-size:.82rem;color:rgba(247,242,232,.38);">60–69%</span>
          <span style="font-family:'Cabinet Grotesk',sans-serif;font-size:.82rem;font-weight:700;color:#73A67C;">4 pts</span>
        </div>
        <div class="grade-row">
          <span style="font-family:'Cormorant Garamond',serif;font-size:1.2rem;font-weight:700;width:20px;color:#F0B050;">C</span>
          <span style="flex:1;font-size:.82rem;color:rgba(247,242,232,.38);">50–59%</span>
          <span style="font-family:'Cabinet Grotesk',sans-serif;font-size:.82rem;font-weight:700;color:#F0B050;">3 pts</span>
        </div>
        <div class="grade-row">
          <span style="font-family:'Cormorant Garamond',serif;font-size:1.2rem;font-weight:700;width:20px;color:#f97316;">D</span>
          <span style="flex:1;font-size:.82rem;color:rgba(247,242,232,.38);">45–49%</span>
          <span style="font-family:'Cabinet Grotesk',sans-serif;font-size:.82rem;font-weight:700;color:#f97316;">2 pts</span>
        </div>
        <div class="grade-row">
          <span style="font-family:'Cormorant Garamond',serif;font-size:1.2rem;font-weight:700;width:20px;color:#f87171;">E</span>
          <span style="flex:1;font-size:.82rem;color:rgba(247,242,232,.38);">40–44%</span>
          <span style="font-family:'Cabinet Grotesk',sans-serif;font-size:.82rem;font-weight:700;color:#f87171;">1 pt</span>
        </div>
        <div class="grade-row">
          <span style="font-family:'Cormorant Garamond',serif;font-size:1.2rem;font-weight:700;width:20px;color:rgba(247,242,232,.25);">F</span>
          <span style="flex:1;font-size:.82rem;color:rgba(247,242,232,.38);">0–39%</span>
          <span style="font-family:'Cabinet Grotesk',sans-serif;font-size:.82rem;font-weight:700;color:rgba(247,242,232,.25);">0 pts</span>
        </div>
      </div>

      <div class="fade-up delay-1">
        <h3 style="font-size:1.05rem;color:#F7F2E8;margin-bottom:.85rem;">Calculation Formula</h3>
        <div style="background:rgba(13,15,20,.7);border:1px solid rgba(212,136,42,.1);
                    border-radius:12px;padding:1.1rem;margin-bottom:.85rem;">
          <p style="font-size:.7rem;color:rgba(247,242,232,.22);text-transform:uppercase;letter-spacing:.12em;margin-bottom:.5rem;">Quality Point (per course)</p>
          <p style="font-family:'Cormorant Garamond',serif;font-size:1rem;font-weight:700;color:#15803D;">Unit × Grade Point</p>
          <p style="font-size:.78rem;color:rgba(247,242,232,.32);margin-top:.35rem;">e.g. 3 units × 4 pts (B) = 12 QP</p>
        </div>
        <div style="background:rgba(13,15,20,.7);border:1px solid rgba(212,136,42,.1);
                    border-radius:12px;padding:1.1rem;margin-bottom:1.25rem;">
          <p style="font-size:.7rem;color:rgba(247,242,232,.22);text-transform:uppercase;letter-spacing:.12em;margin-bottom:.5rem;">Semester / Cumulative GPA</p>
          <p style="font-family:'Cormorant Garamond',serif;font-size:1rem;font-weight:700;color:#15803D;">Σ(Quality Points) / Σ(Units)</p>
          <p style="font-size:.78rem;color:rgba(247,242,232,.32);margin-top:.35rem;">Across all registered courses</p>
        </div>
        <p style="font-size:.78rem;font-weight:600;color:rgba(247,242,232,.38);margin-bottom:.65rem;">Degree Classification</p>
        <div style="display:flex;justify-content:space-between;padding:.38rem 0;border-bottom:1px solid rgba(212,136,42,.08);">
          <span style="font-size:.8rem;color:rgba(247,242,232,.4);">First Class</span>
          <span style="font-size:.76rem;color:#15803D;font-family:'Cabinet Grotesk',sans-serif;">4.50–5.00</span>
        </div>
        <div style="display:flex;justify-content:space-between;padding:.38rem 0;border-bottom:1px solid rgba(212,136,42,.08);">
          <span style="font-size:.8rem;color:rgba(247,242,232,.4);">2nd Class Upper</span>
          <span style="font-size:.76rem;color:#73A67C;font-family:'Cabinet Grotesk',sans-serif;">3.50–4.49</span>
        </div>
        <div style="display:flex;justify-content:space-between;padding:.38rem 0;border-bottom:1px solid rgba(212,136,42,.08);">
          <span style="font-size:.8rem;color:rgba(247,242,232,.4);">2nd Class Lower</span>
          <span style="font-size:.76rem;color:#F0B050;font-family:'Cabinet Grotesk',sans-serif;">2.40–3.49</span>
        </div>
        <div style="display:flex;justify-content:space-between;padding:.38rem 0;border-bottom:1px solid rgba(212,136,42,.08);">
          <span style="font-size:.8rem;color:rgba(247,242,232,.4);">Third Class</span>
          <span style="font-size:.76rem;color:#f97316;font-family:'Cabinet Grotesk',sans-serif;">1.50–2.39</span>
        </div>
        <div style="display:flex;justify-content:space-between;padding:.38rem 0;">
          <span style="font-size:.8rem;color:rgba(247,242,232,.4);">Pass</span>
          <span style="font-size:.76rem;color:rgba(247,242,232,.32);font-family:'Cabinet Grotesk',sans-serif;">1.00–1.49</span>
        </div>
      </div>
    </div>
  </div>
</section>

<div class="amber-rule"></div>


{{-- ══════════ VALUES ═════════════════════════════════════════════ --}}
<section class="rp-section">
  <div class="container-rp">
    <div style="text-align:center;margin-bottom:2.75rem;">
      <div class="rp-eyebrow" style="justify-content:center;">Philosophy</div>
      <h2 style="font-size:clamp(1.9rem,3.5vw,2.8rem);font-weight:700;color:#F7F2E8;margin:.2rem 0;">
        What We Stand For
      </h2>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
  {{-- Student-First --}}
  <div class="val-card fade-up">
    <h3 style="font-size:1rem;color:#F7F2E8;margin-bottom:.5rem;">Student-First</h3>
    <p style="font-size:.87rem;color:rgba(247,242,232,.4);line-height:1.78;">
      Every feature in ReadPal was born from a real student problem. If it doesn't make
      academic life measurably easier, it doesn't ship.
    </p>
  </div>

  {{-- Continuous Improvement --}}
  <div class="val-card fade-up delay-1">
    <h3 style="font-size:1rem;color:#F7F2E8;margin-bottom:.5rem;">Continuous Improvement</h3>
    <p style="font-size:.87rem;color:rgba(247,242,232,.4);line-height:1.78;">
      ReadPal is never finished. Oracle Tech actively incorporates student feedback into each
      update — the CGPA calculator and Karls both came from direct student requests.
    </p>
  </div>

  {{-- Privacy by Design --}}
  <div class="val-card fade-up delay-2">
    <h3 style="font-size:1rem;color:#F7F2E8;margin-bottom:.5rem;">Privacy by Design</h3>
    <p style="font-size:.87rem;color:rgba(247,242,232,.4);line-height:1.78;">
      Anonymous karls are stored only for admin moderation. Private DMs auto-delete.
      We never sell data, run ads, or build profiles on students.
    </p>
  </div>
</div>

  </div>
</section>

<div class="amber-rule"></div>


{{-- ══════════ ORACLE TECH ════════════════════════════════════════ --}}
<section class="rp-section-sm strip-dark" style="text-align:center;">
  <div class="container-rp" style="max-width:680px;">
    <div class="rp-eyebrow fade-up" style="justify-content:center;">About the Team</div>
    <h2 class="fade-up delay-1"
        style="font-size:clamp(1.9rem,3.5vw,2.8rem);font-weight:700;color:#F7F2E8;margin:.2rem 0 1rem;">
      Oracle Tech
    </h2>
    <p class="fade-up delay-2"
       style="color:rgba(247,242,232,.42);line-height:1.85;margin-bottom:2rem;font-size:.92rem;">
      Oracle Tech is the development team behind ReadPal. We build practical educational software
      for Nigerian universities, working closely with academic institutions to ensure our tools
      genuinely improve student outcomes. ReadPal is our flagship student-facing product — and
      with every semester, it grows based on what students tell us.
    </p>
    <div class="fade-up delay-3" style="display:flex;justify-content:center;gap:.75rem;flex-wrap:wrap;">
      <a href="{{ route('contact') }}" class="rp-btn-primary px-7 py-3 rounded-xl text-sm font-body">
        Get In Touch
      </a>
      <a href="{{ route('feedback') }}" class="rp-btn-ghost px-7 py-3 rounded-xl text-sm font-body">
        Send Feedback
      </a>
    </div>
  </div>
</section>

@endsection