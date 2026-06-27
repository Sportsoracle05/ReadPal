<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="referrer" content="no-referrer-when-downgrade" />
    <meta name="a42e55b9917c097fba5f86d7139158d9dda00822" content="a42e55b9917c097fba5f86d7139158d9dda00822" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ReadPal Online')</title>
    <meta name="description" content="@yield('meta_description', 'ReadPal by Oracle Tech — The dedicated academic companion for university students.')">
<meta property="og:site_name" content="ReadPal Online" />
       <meta property="og:title" content="@yield('title', 'ReadPal Online')" />
<meta property="og:type" content="article" /> 
<meta property="og:url" content="https://readpal.online" />
<meta property="og:image" content="https://readpal.online/ReadPalMain.png" />
<meta property="og:description" content="ReadPal is a dedicated mobile learning companion designed to streamline access to academic materials" />

<meta property="og:site_name" content="ReadPal Online" />
<meta property="og:locale" content="en_US" />

<meta name="twitter:card" content="summary_large_image" /> 
<meta name="twitter:site" content="" />
<meta name="twitter:creator" content="" />
<meta name="twitter:title" content="ReadPal Online" />
<meta name="twitter:description" content="ReadPal is a dedicated mobile learning companion designed to streamline access to academic materials"/>
<meta name="twitter:image" content="https://readpal.online/ReadPalMain.png" />


<meta name="google-adsense-account" content="ca-pub-4261871524010781">

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400;1,600&family=Cabinet+Grotesk:wght@300;400;500;700;800&display=swap" rel="stylesheet">

    {{-- Tailwind CDN (dev only — replace with Vite build in production) --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        display: ['"Cormorant Garamond"', 'Georgia', 'serif'],
                        body: ['"Cabinet Grotesk"', 'sans-serif'],
                    },
                    colors: {
                        ink:    { DEFAULT: '#0D0F14', 50: '#f5f5f7', 100: '#e8e8ed', 200: '#c4c4cf', 300: '#9191a1', 400: '#5e5e72', 500: '#3a3a4a', 600: '#262633', 700: '#1a1a24', 800: '#111118', 900: '#0D0F14' },
                        amber:  { DEFAULT: '#15803D', light: '#F0B050', dark: '#A06015', glow: 'rgba(212,136,42,0.18)' },
                        parch:  { DEFAULT: '#F7F2E8', 50: '#FDFAF4', 100: '#F7F2E8', 200: '#EDE3CC' },
                        sage:   { DEFAULT: '#4B6E52', light: '#73A67C', dark: '#2E4533' },
                    },
                    boxShadow: {
                        'amber-glow': '0 0 40px rgba(212,136,42,0.15), 0 4px 24px rgba(0,0,0,0.4)',
                        'card':       '0 2px 16px rgba(0,0,0,0.35)',
                    }
                }
            }
        }
    </script>

    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Cabinet Grotesk', sans-serif; }
        h1,h2,h3,.font-display { font-family: 'Cormorant Garamond', Georgia, serif; }

        /* Noise texture overlay */
        body::before {
            content: '';
            position: fixed; inset: 0; z-index: 0; pointer-events: none;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.04'/%3E%3C/svg%3E");
            opacity: 0.6;
        }

        /* Geometric bg pattern */
        .geo-bg {
            background-color: #0D0F14;
            background-image:
                radial-gradient(ellipse 80% 60% at 20% -10%, rgba(212,136,42,0.12) 0%, transparent 60%),
                radial-gradient(ellipse 60% 80% at 80% 110%, rgba(75,110,82,0.08) 0%, transparent 55%),
                linear-gradient(180deg, #0D0F14 0%, #111118 100%);
        }

        /* Grid lines */
        .grid-lines {
            background-image:
                linear-gradient(rgba(212,136,42,0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(212,136,42,0.04) 1px, transparent 1px);
            background-size: 64px 64px;
        }

        /* Auth card */
        .auth-card {
            background: linear-gradient(135deg, rgba(26,26,36,0.95) 0%, rgba(17,17,24,0.98) 100%);
            border: 1px solid rgba(212,136,42,0.15);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }

        /* Input styles */
        .rp-input {
            background: rgba(13,15,20,0.7);
            border: 1px solid rgba(212,136,42,0.2);
            color: #F7F2E8;
            transition: border-color 0.2s, box-shadow 0.2s;
            font-family: 'Cabinet Grotesk', sans-serif;
        }
        .rp-input::placeholder { color: rgba(247,242,232,0.35); }
        .rp-input:focus {
            outline: none;
            border-color: rgba(212,136,42,0.6);
            box-shadow: 0 0 0 3px rgba(212,136,42,0.08), inset 0 1px 0 rgba(255,255,255,0.03);
        }

        /* Primary button */
        .rp-btn-primary {
            background: linear-gradient(135deg, #15803D 0%, #A06015 100%);
            color: #0D0F14;
            font-weight: 700;
            letter-spacing: 0.03em;
            transition: all 0.2s;
            position: relative; overflow: hidden;
        }
        .rp-btn-primary::after {
            content: '';
            position: absolute; inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.15) 0%, transparent 60%);
            opacity: 0; transition: opacity 0.2s;
        }
        .rp-btn-primary:hover::after { opacity: 1; }
        .rp-btn-primary:hover { transform: translateY(-1px); box-shadow: 0 8px 24px rgba(212,136,42,0.35); }
        .rp-btn-primary:active { transform: translateY(0); }

        /* Ghost button */
        .rp-btn-ghost {
            border: 1px solid rgba(212,136,42,0.3);
            color: #15803D;
            transition: all 0.2s;
        }
        .rp-btn-ghost:hover {
            background: rgba(212,136,42,0.08);
            border-color: rgba(212,136,42,0.6);
        }

        /* Divider */
        .rp-divider {
            border-color: rgba(212,136,42,0.12);
        }

        /* Animated entrance */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .fade-up { animation: fadeUp 0.55s cubic-bezier(0.22, 1, 0.36, 1) both; }
        .delay-1 { animation-delay: 0.08s; }
        .delay-2 { animation-delay: 0.16s; }
        .delay-3 { animation-delay: 0.24s; }
        .delay-4 { animation-delay: 0.32s; }
        .delay-5 { animation-delay: 0.40s; }

        /* Alert */
        .rp-alert-error { background: rgba(185,28,28,0.15); border: 1px solid rgba(185,28,28,0.3); color: #fca5a5; }
        .rp-alert-success { background: rgba(75,110,82,0.15); border: 1px solid rgba(75,110,82,0.35); color: #86efac; }

        /* Nav link */
        .nav-link { color: rgba(247,242,232,0.6); transition: color 0.2s; font-size: 0.875rem; font-weight: 500; }
        .nav-link:hover { color: #15803D; }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #0D0F14; }
        ::-webkit-scrollbar-thumb { background: rgba(212,136,42,0.3); border-radius: 3px; }
    </style>

    @stack('styles')
</head>
<body class="geo-bg grid-lines min-h-screen text-parch-100 relative">

    {{-- ── Top navigation bar ──────────────────────────────────────── --}}
    @include('partials.guest-nav')

    {{-- ── Main content ────────────────────────────────────────────── --}}
    <main class="relative z-10">
        @yield('content')
    </main>

    {{-- ── Footer ──────────────────────────────────────────────────── --}}
    @include('partials.footer')

    @stack('scripts')
    <script>
        // Ping every 5 minutes to keep the session active
    setInterval(() => {
        fetch("{{ route('heartbeat') }}")
            .then(response => {
                if (!response.ok && response.status === 419) {
                    // If the ping itself fails with 419, the session is already dead
                    window.location.reload();
                }
            })
            .catch(error => console.error('Heartbeat failed:', error));
    }, 5 * 60 * 1000);
    </script>
    
</body>
</html>
