@extends('layouts.guest')
@section('title', 'Terms & Conditions – ReadPal')
@section('meta_description', 'ReadPal Terms and Conditions — the agreement between you and Oracle Tech governing your use of the ReadPal platform.')

@section('content')

<style>
  .container-rp { max-width:1140px; margin:0 auto; padding:0 1.5rem; }
  .rp-section   { padding:5rem 0; position:relative; }

  .rp-eyebrow {
    display:inline-flex; align-items:center; gap:.5rem;
    font-family:'Cabinet Grotesk',sans-serif; font-size:.68rem; font-weight:700;
    letter-spacing:.2em; text-transform:uppercase; color:#15803D; margin-bottom:.75rem;
  }
  .rp-eyebrow::before { content:''; width:16px; height:1px; background:#15803D; }

  /* ToC sidebar */
  .toc-nav {
    position:sticky; top:84px;
    background:linear-gradient(135deg,rgba(26,26,36,.95),rgba(17,17,24,.98));
    border:1px solid rgba(212,136,42,.12); border-radius:14px; padding:1.2rem;
  }
  .toc-link {
    display:block; font-size:.8rem; color:rgba(247,242,232,.35);
    text-decoration:none; padding:.38rem .55rem; border-radius:7px;
    transition:all .18s; border-left:2px solid transparent; margin-bottom:.08rem;
  }
  .toc-link:hover {
    color:#15803D; border-left-color:rgba(21,128,61,.5);
    background:rgba(21,128,61,.06); padding-left:.9rem;
  }

  /* Section content */
  .terms-sec { padding:2.25rem 0; border-bottom:1px solid rgba(212,136,42,.08); }
  .terms-sec:last-child { border-bottom:none; }
  .terms-sec h2 {
    font-size:1.2rem; font-weight:700; color:#F7F2E8; margin-bottom:1rem;
    display:flex; align-items:center; gap:.65rem;
  }
  .sec-n {
    font-family:'Cabinet Grotesk',sans-serif; font-size:.6rem;
    color:rgba(21,128,61,.4); letter-spacing:.12em; font-weight:400;
  }
  .terms-sec p { font-size:.9rem; line-height:1.85; margin-bottom:.9rem; color:rgba(247,242,232,.42); }
  .terms-sec h3 { font-size:.95rem; color:rgba(247,242,232,.65); margin:1.2rem 0 .5rem;
    font-family:'Cabinet Grotesk',sans-serif; font-weight:600; }

  /* List items */
  .t-list { list-style:none; padding:0; display:flex; flex-direction:column; gap:.5rem; margin-bottom:.9rem; }
  .t-list li { display:flex; gap:.6rem; align-items:flex-start;
    font-size:.88rem; color:rgba(247,242,232,.4); line-height:1.72; }
  .t-list li::before { content:'▸'; color:rgba(21,128,61,.5); flex-shrink:0; margin-top:.1rem; }

  /* Highlight boxes */
  .hl-green {
    background:rgba(21,128,61,.07); border:1px solid rgba(21,128,61,.18);
    border-radius:10px; padding:.95rem 1.2rem;
    font-size:.87rem; color:rgba(21,128,61,.85); line-height:1.75; margin:1rem 0;
  }
  .hl-amber {
    background:rgba(212,136,42,.06); border:1px solid rgba(212,136,42,.18);
    border-radius:10px; padding:.95rem 1.2rem;
    font-size:.87rem; color:rgba(240,176,80,.8); line-height:1.75; margin:1rem 0;
  }

  .amber-rule { height:1px; background:linear-gradient(90deg,transparent,rgba(212,136,42,.18),transparent); }

  @media (max-width:900px) { .toc-nav { display:none; } }
</style>


{{-- ══════════ HERO ══════════════════════════════════════════════ --}}
<section class="rp-section" style="padding:4.5rem 0 3.5rem;text-align:center;overflow:hidden;">
  <div style="position:absolute;top:-40%;left:50%;transform:translateX(-50%);width:600px;height:400px;
              pointer-events:none;background:radial-gradient(ellipse,rgba(212,136,42,.06),transparent 60%);"></div>
  <div class="container-rp" style="position:relative;z-index:1;">
    <div class="rp-eyebrow fade-up" style="justify-content:center;">Legal</div>
    <h1 class="fade-up delay-1"
        style="font-size:clamp(2.4rem,5vw,3.8rem);font-weight:700;color:#F7F2E8;
               line-height:1.1;letter-spacing:-.02em;margin-bottom:.75rem;">
      Terms &amp; Conditions
    </h1>
    <p class="fade-up delay-2" style="font-size:.88rem;color:rgba(247,242,232,.35);">
      Effective Date: <strong style="color:rgba(247,242,232,.6);">{{ date('F d, Y') }}</strong>
      &nbsp;·&nbsp; Oracle Tech &amp; ReadPal
    </p>
  </div>
</section>

<div class="amber-rule"></div>


{{-- ══════════ MAIN CONTENT ══════════════════════════════════════ --}}
<section class="rp-section" style="padding-top:2.5rem;">
  <div class="container-rp">
    <div style="display:grid;grid-template-columns:210px 1fr;gap:3rem;align-items:start;">

      {{-- ToC --}}
      <div class="toc-nav fade-up">
        <p style="font-size:.6rem;letter-spacing:.18em;text-transform:uppercase;
                  color:rgba(247,242,232,.18);margin-bottom:.75rem;">Contents</p>
        <a href="#acceptance"  class="toc-link" id="nav-acceptance">Acceptance</a>
        <a href="#eligibility" class="toc-link" id="nav-eligibility">Eligibility</a>
        <a href="#account"     class="toc-link" id="nav-account">Your Account</a>
        <a href="#platform"    class="toc-link" id="nav-platform">Platform Use</a>
        <a href="#materials"   class="toc-link" id="nav-materials">Materials</a>
        <a href="#cgpa"        class="toc-link" id="nav-cgpa">CGPA Calculator</a>
        <a href="#karls"       class="toc-link" id="nav-karls">Karls Community</a>
        <a href="#privacy"     class="toc-link" id="nav-privacy">Privacy</a>
        <a href="#ip"          class="toc-link" id="nav-ip">Intellectual Property</a>
        <a href="#conduct"     class="toc-link" id="nav-conduct">Conduct</a>
        <a href="#disclaimer"  class="toc-link" id="nav-disclaimer">Disclaimers</a>
        <a href="#liability"   class="toc-link" id="nav-liability">Liability</a>
        <a href="#termination" class="toc-link" id="nav-termination">Termination</a>
        <a href="#changes"     class="toc-link" id="nav-changes">Changes</a>
        <a href="#contact"     class="toc-link" id="nav-contact">Contact</a>
      </div>

      {{-- Content --}}
      <div class="fade-up delay-1">

        {{-- Preamble --}}
        <div class="hl-green" style="margin-bottom:1.5rem;">
          These Terms &amp; Conditions ("Terms") form a legally binding agreement between you
          ("User," "Student," or "you") and <strong>Oracle Tech</strong> ("we," "our," or "the Developer"),
          governing your use of the <strong>ReadPal</strong> platform and all its features.
          By creating an account or using ReadPal, you confirm that you have read, understood,
          and agree to be bound by these Terms.
        </div>


        {{-- 01 Acceptance --}}
        <div id="acceptance" class="terms-sec">
          <h2>Acceptance of Terms <span class="sec-n">01</span></h2>
          <p>
            Your access to and use of ReadPal — including all modules, features, tools, and
            community spaces — is conditioned on your acceptance of and compliance with these Terms.
            These Terms apply to all users, including students, class representatives, and administrators.
          </p>
          <p>
            If you do not agree with any part of these Terms, you must immediately discontinue
            use of the platform and contact Oracle Tech to have your account closed.
          </p>
        </div>


        {{-- 02 Eligibility --}}
        <div id="eligibility" class="terms-sec">
          <h2>Eligibility <span class="sec-n">02</span></h2>
          <p>
            ReadPal is currently deployed exclusively for eligible students of
            <strong style="color:rgba(247,242,232,.7);">Adekunle Ajasin University, Akungba-Akoko (AAUA)</strong>.
            The current deployment targets Sociology Department 300-level students.
            Access is subject to institutional verification at the discretion of Oracle Tech.
          </p>
          <ul class="t-list">
            <li>You must be enrolled at AAUA and belong to the target cohort.</li>
            <li>You must be at least 16 years of age to use ReadPal.</li>
            <li>You must provide accurate information during registration, including your matric number where requested.</li>
            <li>You may not create an account on behalf of another person.</li>
          </ul>
        </div>


        {{-- 03 Account --}}
        <div id="account" class="terms-sec">
          <h2>Your Account <span class="sec-n">03</span></h2>
          <p>
            You are responsible for maintaining the confidentiality of your login credentials
            and are solely liable for all activity that occurs under your account.
          </p>
          <ul class="t-list">
            <li>Use a strong, unique password for your ReadPal account.</li>
            <li>Notify Oracle Tech immediately if you suspect unauthorized access.</li>
            <li>Do not share your account credentials with any other person.</li>
            <li>Oracle Tech reserves the right to suspend or terminate accounts that violate these Terms.</li>
          </ul>
        </div>


        {{-- 04 Platform Use --}}
        <div id="platform" class="terms-sec">
          <h2>Platform Use <span class="sec-n">04</span></h2>
          <p>
            ReadPal is provided strictly for academic and educational purposes. You agree to
            use the platform in a lawful, respectful, and academically responsible manner.
          </p>
          <h3>Permitted Uses</h3>
          <ul class="t-list">
            <li>Accessing, reading, and downloading course materials distributed through ReadPal.</li>
            <li>Completing self-assessment quizzes for educational benefit.</li>
            <li>Creating and managing personal academic notes.</li>
            <li>Tracking lecture schedules and receiving class alerts.</li>
            <li>Using the CGPA calculator to track your academic performance.</li>
            <li>Participating in the Karls community in good faith.</li>
          </ul>
          <h3>Prohibited Uses</h3>
          <ul class="t-list">
            <li>Attempting to hack, disrupt, or gain unauthorized access to the platform or its database.</li>
            <li>Scraping, reproducing, or redistributing ReadPal content without express written permission.</li>
            <li>Uploading malicious code, viruses, or harmful content through any part of the platform.</li>
            <li>Using ReadPal for any commercial purpose or personal financial gain.</li>
            <li>Impersonating lecturers, administrators, or other students.</li>
          </ul>
        </div>


        {{-- 05 Materials --}}
        <div id="materials" class="terms-sec">
          <h2>Materials &amp; Academic Content <span class="sec-n">05</span></h2>
          <p>
            Lecture notes and resources distributed through ReadPal are provided solely
            for the educational use of enrolled students.
          </p>
          <ul class="t-list">
            <li>Materials remain the intellectual property of the contributing lecturers and AAUA.</li>
            <li>You may download materials in PDF format for personal offline study use only.</li>
            <li>You may not redistribute, sell, or share downloaded materials on external platforms.</li>
            <li>Materials are not to be submitted as your own original work in any academic assessment.</li>
          </ul>
          <div class="hl-amber">
            Unauthorized distribution of ReadPal course materials outside the platform constitutes
            academic misconduct and may breach copyright law. Oracle Tech may report violations
            to the relevant academic authorities.
          </div>
        </div>


        {{-- 06 CGPA --}}
        <div id="cgpa" class="terms-sec">
          <h2>CGPA Calculator <span class="sec-n">06</span></h2>
          <p>
            The CGPA Calculator is provided as a self-management tool to help students
            estimate their academic performance using the AAUA 5.0 grading scale.
          </p>
          <ul class="t-list">
            <li>CGPA results are <strong style="color:rgba(247,242,232,.65);">estimates only</strong> and do not constitute official academic transcripts.</li>
            <li>Oracle Tech does not guarantee accuracy if incorrect course data is entered.</li>
            <li>Your CGPA data is private and accessible only to your account.</li>
            <li>Always consult your official faculty records for your verified academic standing.</li>
          </ul>
          <div class="hl-green">
            ReadPal uses the official AAUA grade-point mapping: A = 5, B = 4, C = 3, D = 2, E = 1, F = 0.
            Results are computed using: Σ(Unit × Grade Point) / Σ(Units).
          </div>
        </div>


        {{-- 07 Karls --}}
        <div id="karls" class="terms-sec">
          <h2>Karls Community <span class="sec-n">07</span></h2>
          <p>
            The Karls feature is a community platform for ReadPal users. By participating,
            you agree to the rules below.
          </p>
          <h3>Public Threads</h3>
          <ul class="t-list">
            <li>All authenticated users may post in public threads.</li>
            <li>Anonymous posting still stores your user identity server-side for moderation. It is only hidden from other users.</li>
            <li>Anonymous posting does not grant immunity from enforcement of these Terms.</li>
          </ul>
          <h3>Private Karls (Direct Messages)</h3>
          <ul class="t-list">
            <li>Private karls may only be sent to non-anonymous users who have posted publicly.</li>
            <li>Private messages are automatically deleted every 24 hours — read messages in the nightly reset, unread messages after 24 hours regardless.</li>
            <li>Do not use private karls to send unsolicited, unwanted, or harmful communications.</li>
          </ul>
          <h3>Community Standards</h3>
          <ul class="t-list">
            <li>No harassment, bullying, threats, or hate speech directed at any person.</li>
            <li>No explicit, sexually suggestive, violent, or disturbing content.</li>
            <li>No sharing of another student's personal information without their consent.</li>
            <li>No spam, repetitive content, or promotional material.</li>
          </ul>
          <div class="hl-amber">
            Violations of Karls community standards — including misuse of anonymous posting —
            will result in immediate account suspension and, where applicable,
            referral to academic disciplinary processes.
          </div>
        </div>


        {{-- 08 Privacy --}}
        <div id="privacy" class="terms-sec">
          <h2>Privacy <span class="sec-n">08</span></h2>
          <p>
            Your use of ReadPal is also governed by our
            <a href="{{ route('privacy') }}"
               style="color:#15803D;text-decoration:none;border-bottom:1px solid rgba(21,128,61,.3);">
              Privacy Policy
            </a>,
            incorporated into these Terms by reference.
          </p>
          <ul class="t-list">
            <li>We collect only the data necessary to operate ReadPal: name, email, matric number, usage data.</li>
            <li>We never sell, rent, or trade your personal data to third parties.</li>
            <li>Anonymous karls store a hidden user reference for admin moderation only.</li>
            <li>You may request deletion of your account and all associated data at any time.</li>
          </ul>
        </div>


        {{-- 09 IP --}}
        <div id="ip" class="terms-sec">
          <h2>Intellectual Property <span class="sec-n">09</span></h2>
          <p>
            All original code, design, interfaces, and branding of ReadPal — including
            the names "ReadPal," "Karls," and all Oracle Tech trademarks — are the
            exclusive intellectual property of Oracle Tech.
          </p>
          <ul class="t-list">
            <li>You may not copy, adapt, or reverse-engineer any part of ReadPal's software.</li>
            <li>You may not use the ReadPal or Oracle Tech name or logo without express written permission.</li>
            <li>Course materials remain the property of their respective authors and AAUA.</li>
          </ul>
        </div>


        {{-- 10 Conduct --}}
        <div id="conduct" class="terms-sec">
          <h2>Acceptable Conduct <span class="sec-n">10</span></h2>
          <p>
            ReadPal relies on mutual respect. By using the platform, you commit to:
          </p>
          <ul class="t-list">
            <li>Treating all other users with courtesy and respect, whether in public threads or private messages.</li>
            <li>Reporting content or behaviour that violates these Terms through feedback or contact channels.</li>
            <li>Not attempting to identify anonymous posters through any technical or social means.</li>
            <li>Not posting content that could damage the reputation of AAUA, Oracle Tech, or any individual.</li>
          </ul>
        </div>


        {{-- 11 Disclaimer --}}
        <div id="disclaimer" class="terms-sec">
          <h2>Disclaimers <span class="sec-n">11</span></h2>
          <p>
            ReadPal is provided on an "as is" and "as available" basis.
            Oracle Tech makes no warranties, express or implied, including:
          </p>
          <ul class="t-list">
            <li>The platform will be available at all times without interruption.</li>
            <li>All information on the platform is accurate, complete, or current.</li>
            <li>CGPA calculations reflect official academic records.</li>
            <li>Materials distributed through ReadPal are error-free or complete.</li>
          </ul>
        </div>


        {{-- 12 Liability --}}
        <div id="liability" class="terms-sec">
          <h2>Limitation of Liability <span class="sec-n">12</span></h2>
          <p>
            To the fullest extent permitted by applicable law, Oracle Tech shall not be liable for:
          </p>
          <ul class="t-list">
            <li>Any indirect, incidental, or consequential damages arising from your use of ReadPal.</li>
            <li>Loss of data, including private karls deleted under the 24-hour reset policy.</li>
            <li>Academic decisions made based on CGPA estimates generated by ReadPal.</li>
            <li>Content posted by other users in the Karls community.</li>
            <li>Disruption of service caused by factors outside Oracle Tech's reasonable control.</li>
          </ul>
        </div>


        {{-- 13 Termination --}}
        <div id="termination" class="terms-sec">
          <h2>Termination <span class="sec-n">13</span></h2>
          <p>
            Oracle Tech reserves the right to suspend or permanently terminate your access to
            ReadPal at any time, without prior notice, for any breach of these Terms or for
            conduct deemed harmful to the platform, its users, or Oracle Tech.
          </p>
          <p>
            You may terminate your own account at any time by contacting Oracle Tech through the
            <a href="{{ route('contact') }}"
               style="color:#15803D;text-decoration:none;border-bottom:1px solid rgba(21,128,61,.3);">
              Contact page
            </a>.
            Upon termination, your personal data will be handled in accordance with our Privacy Policy.
          </p>
        </div>


        {{-- 14 Changes --}}
        <div id="changes" class="terms-sec">
          <h2>Changes to These Terms <span class="sec-n">14</span></h2>
          <p>
            Oracle Tech reserves the right to modify these Terms at any time to reflect
            changes in the platform, applicable law, or operational practices. Significant changes
            will be communicated via the app or email. Continued use of ReadPal after any revision
            constitutes acceptance of the updated Terms.
          </p>
          <p>
            The effective date shown at the top of this page reflects the most recent revision.
          </p>
        </div>


        {{-- 15 Contact --}}
        <div id="contact" class="terms-sec" style="border-bottom:none;">
          <h2>Contact Us <span class="sec-n">15</span></h2>
          <p>
            If you have questions, concerns, or requests relating to these Terms,
            please contact Oracle Tech directly:
          </p>
          <div style="background:linear-gradient(135deg,rgba(26,26,36,.92),rgba(17,17,24,.97));
                      border:1px solid rgba(212,136,42,.14);border-radius:12px;padding:1.25rem;margin-top:.75rem;">
            <p style="font-size:.88rem;color:rgba(247,242,232,.7);font-weight:600;margin-bottom:.3rem;">
              Oracle Tech — ReadPal Legal Team
            </p>
            <p style="font-size:.85rem;color:rgba(247,242,232,.38);">
              Website:
              <a href="{{ route('contact') }}"
                 style="color:#15803D;text-decoration:none;border-bottom:1px solid rgba(21,128,61,.25);">
                readpal.online/contact
              </a>
            </p>
            <p style="font-size:.85rem;color:rgba(247,242,232,.38);margin-top:.2rem;">
              Feedback:
              <a href="{{ route('feedback') }}"
                 style="color:#15803D;text-decoration:none;border-bottom:1px solid rgba(21,128,61,.25);">
                readpal.online/feedback
              </a>
            </p>
          </div>
        </div>

      </div>
    </div>
  </div>
</section>

@push('scripts')
<script>
const sections = document.querySelectorAll('.terms-sec');
const navLinks  = document.querySelectorAll('.toc-link');
window.addEventListener('scroll', () => {
  let cur = '';
  sections.forEach(s => { if (window.scrollY >= s.offsetTop - 110) cur = '#' + s.id; });
  navLinks.forEach(a => {
    const active = a.getAttribute('href') === cur;
    a.style.color           = active ? '#15803D' : '';
    a.style.borderLeftColor = active ? 'rgba(21,128,61,.55)' : 'transparent';
    a.style.background      = active ? 'rgba(21,128,61,.07)' : '';
    a.style.paddingLeft     = active ? '.9rem' : '';
  });
});
</script>
@endpush

@endsection