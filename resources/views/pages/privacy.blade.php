@extends('layouts.guest')
@section('title', 'Privacy Policy – ReadPal')
@section('meta_description', 'ReadPal Privacy Policy – how Oracle Tech collects, uses, and protects your data.')

@section('content')

<style>
    /* 1. Ensure the main container has healthy side margins on mobile */
    @media (max-width: 1023px) {
        .container { 
            padding-left: 1.5rem !important; 
            padding-right: 1.5rem !important; 
            width: 100%;
        }
        
        /* Make the sticky nav a bit smaller and centered */
        .policy-nav {
            margin: 1rem 0;
            padding: 0.5rem 0;
            background: transparent;
            border: none;
            border-bottom: 1px solid var(--ink-800);
            border-radius: 0;
        }
    }

    /* 2. Navigation Styling */
    .policy-nav {
        display: flex;
        gap: 0.6rem;
        overflow-x: auto;
        white-space: nowrap;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none; /* Hide scrollbar Firefox */
    }
    .policy-nav::-webkit-scrollbar { display: none; } /* Hide scrollbar Chrome/Safari */

    .policy-nav a {
        font-size: 0.75rem;
        color: var(--ink-400);
        padding: 0.5rem 0.9rem;
        background: var(--ink-800);
        border: 1px solid var(--ink-700);
        border-radius: 99px;
        text-decoration: none;
    }

    /* 3. Section Spacing */
    .policy-section {
        padding: 2.5rem 0;
        border-bottom: 1px solid var(--ink-800);
    }
    
    /* 4. Desktop Layout (Restore the sidebar look) */
    @media (min-width: 1024px) {
        .policy-container {
            display: grid;
            grid-template-columns: 240px 1fr;
            gap: 4rem;
            align-items: start;
        }
        .policy-nav {
            position: sticky; top: 100px;
            flex-direction: column;
            background: var(--ink-900);
            border: 1px solid var(--ink-800);
            border-radius: 16px;
            padding: 1.5rem;
            overflow-x: visible;
        }
        .policy-nav a {
            background: transparent;
            border: none;
            border-radius: 8px;
            padding: 0.6rem 0.8rem;
        }
    }

    /* Prevent text blowout */
    .policy-section p, .policy-section li {
        word-wrap: break-word;
        overflow-wrap: break-word;
    }
</style>



{{-- Hero --}}
<section class="page-hero" style="padding:4rem 0 3rem;">
    <div class="container">
        <span class="eyebrow fade-up">Legal</span>
        <h1 class="fade-up-d1" style="font-size:clamp(1.8rem,4vw,2.8rem);">Privacy &amp; Policy</h1>
        <p class="fade-up-d2" style="font-size:.9rem;">
            Effective Date: <strong style="color:var(--ink-200);">{{ date('F d, Y') }}</strong>
            &nbsp;·&nbsp; Maintained by Oracle Tech
        </p>
    </div>
</section>

<section class="section" style="padding-top:1rem;">
    <div class="container">
        <div class="policy-container">

            {{-- Navigation: Becomes a top-scroller on mobile --}}
            <div class="policy-nav fade-up">
                {{-- Hidden label on mobile, visible on desktop --}}
                <p class="hidden lg:block text-[0.6rem] uppercase tracking-widest text-ink-600 mb-3">Contents</p>
                
                @foreach([
                    ['#overview',      'Overview'],
                    ['#information',   'Data Collection'],
                    ['#usage',         'Usage'],
                    ['#sharing',       'Sharing'],
                    ['#security',      'Security'],
                    ['#rights',        'Your Rights'],
                    ['#children',      'Children'],
                    ['#changes',       'Changes'],
                    ['#contact',       'Contact'],
                ] as [$href, $label])
                <a href="{{ $href }}">{{ $label }}</a>
                @endforeach
            </div>

            {{-- Policy Content --}}
            <div class="fade-up-d1 min-w-0"> {{-- min-w-0 prevents flex/grid blowout --}}
                {{-- Content sections remain the same, just ensure images/tables have max-w-full --}}
                <div id="overview" class="policy-section">
                    <h2>Overview <span>01</span></h2>
                    <div class="highlight-box">
                        ReadPal ("the App") is developed and maintained by <strong>Oracle Tech</strong>.
                        This Privacy Policy explains how we collect, use, and protect the information of users —
                        currently Sociology 300-level students at Adekunle Ajasin University, Akungba (AAUA).
                    </div>
                    <p>
                        By using ReadPal, you agree to the collection and use of information in accordance with this
                        policy. We are committed to transparency and protecting the privacy of our student community.
                    </p>
                </div>

                <div id="information" class="policy-section">
                    <h2>Information We Collect <span>02</span></h2>
                    <p>We collect two categories of information:</p>
                    <p style="color:var(--ink-300);font-weight:500;margin-bottom:.4rem;">Information you provide directly:</p>
                    <ul>
                        <li>Name and email address used during account registration</li>
                        <li>Personal notes created within the app</li>
                        <li>Feedback and messages submitted through our forms</li>
                        <li>Quiz responses and self-assessment results</li>
                    </ul>
                    <p style="color:var(--ink-300);font-weight:500;margin-bottom:.4rem;">Information collected automatically:</p>
                    <ul>
                        <li>Device type and operating system for compatibility purposes</li>
                        <li>App usage patterns to improve features and performance</li>
                        <li>Crash reports and error logs to maintain stability</li>
                        <li>Lecture alert interaction data (notification open/dismiss)</li>
                    </ul>
                </div>

                <div id="usage" class="policy-section">
                    <h2>How We Use Your Information <span>03</span></h2>
                    <p>Oracle Tech uses collected information exclusively to:</p>
                    <ul>
                        <li>Provide and maintain the ReadPal application and its features</li>
                        <li>Deliver timely lecture notifications and academic alerts</li>
                        <li>Personalize your learning experience within the app</li>
                        <li>Analyse usage patterns to improve future versions of ReadPal</li>
                        <li>Respond to feedback, support requests, and contact form submissions</li>
                        <li>Ensure the security and integrity of the platform</li>
                    </ul>
                    <div class="highlight-box">
                        We do <strong>not</strong> use your data for advertising, sell it to third parties,
                        or use it for any purpose beyond delivering and improving the ReadPal experience.
                    </div>
                </div>

                <div id="sharing" class="policy-section">
                    <h2>Data Sharing &amp; Disclosure <span>04</span></h2>
                    <p>
                        Oracle Tech does not sell, trade, or rent your personal information to third parties.
                        We may share data in the following limited circumstances:
                    </p>
                    <ul>
                        <li><strong style="color:var(--ink-300);">With Lecturers/Administrators:</strong> Material access logs may be shared with authorised academic staff for content distribution purposes only.</li>
                        <li><strong style="color:var(--ink-300);">Legal Requirements:</strong> If required by applicable law, court order, or governmental authority.</li>
                        <li><strong style="color:var(--ink-300);">Service Providers:</strong> With trusted technical partners (e.g., hosting) under strict confidentiality agreements.</li>
                    </ul>
                </div>

                <div id="security" class="policy-section">
                    <h2>Data Security <span>05</span></h2>
                    <p>
                        We take the security of your data seriously. ReadPal implements industry-standard
                        security measures including encrypted data transmission (HTTPS), secure server storage,
                        and regular security reviews.
                    </p>
                    <p>
                        However, no method of transmission over the internet or electronic storage is 100%
                        secure. While we strive to protect your data, we cannot guarantee absolute security.
                        We encourage you to use a strong, unique password for your ReadPal account.
                    </p>
                </div>

                <div id="rights" class="policy-section">
                    <h2>Your Rights <span>06</span></h2>
                    <p>As a ReadPal user, you have the right to:</p>
                    <ul>
                        <li>Access the personal information we hold about you</li>
                        <li>Request correction of inaccurate or incomplete data</li>
                        <li>Request deletion of your account and associated data</li>
                        <li>Withdraw consent for data processing where applicable</li>
                        <li>Lodge a complaint with relevant data protection authorities</li>
                    </ul>
                    <p>
                        To exercise any of these rights, please contact us via the
                        <a href="{{ route('contact') }}" style="color:var(--forest-400);">Contact page</a>.
                    </p>
                </div>

                <div id="children" class="policy-section">
                    <h2>Children's Privacy <span>07</span></h2>
                    <p>
                        ReadPal is designed for university-level students (18 years and above). We do not
                        knowingly collect personal information from anyone under the age of 16. If you believe
                        a minor has provided us with personal information, please contact us immediately.
                    </p>
                </div>

                <div id="changes" class="policy-section">
                    <h2>Policy Changes <span>08</span></h2>
                    <p>
                        Oracle Tech reserves the right to update this Privacy Policy as ReadPal evolves.
                        We will notify users of significant changes through the app or via email.
                        Continued use of ReadPal after changes constitutes acceptance of the updated policy.
                    </p>
                    <p>
                        The effective date at the top of this page reflects the most recent revision.
                        We encourage you to review this policy periodically.
                    </p>
                </div>

                <div id="contact" class="policy-section" style="border-bottom:none;">
                    <h2>Contact Us <span>09</span></h2>
                    <p>
                        If you have any questions, concerns, or requests regarding this Privacy Policy
                        or how Oracle Tech handles your data, please reach out:
                    </p>
                    <div style="background:var(--ink-900);border:1px solid var(--ink-700);border-radius:12px;padding:1.25rem;margin-top:.75rem;">
                        <p style="font-size:.88rem;color:var(--ink-200);font-weight:500;margin-bottom:.25rem;">Oracle Tech – ReadPal Privacy Team</p>
                        <p style="font-size:.85rem;">Website: <a href="{{ route('contact') }}" style="color:var(--forest-400);">readpal.online/contact</a></p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
// Highlight active section in ToC on scroll
const sections = document.querySelectorAll('.policy-section');
const navLinks  = document.querySelectorAll('.policy-nav a');
window.addEventListener('scroll', () => {
    let current = '';
    sections.forEach(s => {
        if (window.scrollY >= s.offsetTop - 120) current = '#' + s.id;
    });
    navLinks.forEach(a => {
        const active = a.getAttribute('href') === current;
        a.style.color = active ? 'var(--forest-300)' : '';
        a.style.borderLeftColor = active ? 'var(--forest-600)' : 'transparent';
        a.style.background = active ? 'rgba(22,163,74,.08)' : '';
        a.style.paddingLeft = active ? '.9rem' : '';
    });
});
</script>
@endpush

@endsection