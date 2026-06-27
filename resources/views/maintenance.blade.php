<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<title>Under Maintenance</title>
	<link rel="preconnect" href="https://googleapis.com" />
	<link rel="preconnect" href="https://gstatic.com" crossorigin />
	<link href="https://googleapis.com/css2?family=Playfair+Display:wght@700&family=DM+Sans:wght@400;500&family=Crimson+Pro:ital,wght@1,300&display=swap" rel="stylesheet" />
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
		:root {
			--ink-950: #020617;
			--ink-900: #0f172a;
			--ink-700: #334155;
			--forest-700: #15803d;
			--forest-400: #4ade80;
		}

		.maintenance-shell {
			min-height: 100vh;
			background: var(--ink-950);
			display: flex;
			align-items: center;
			justify-content: center;
			padding: 2rem;
			position: relative;
			overflow: hidden;
		}

		/* Radial Glow (matching your login panel) */
		.maintenance-shell::before {
			content: '';
			position: absolute;
			width: 600px; height: 600px;
			background: radial-gradient(circle, rgba(22,163,74,0.12) 0%, transparent 70%);
			pointer-events: none;
		}

		.maintenance-card {
			width: 100%;
			max-width: 500px;
			background: var(--ink-900);
			border: 1px solid var(--ink-700);
			border-radius: 24px;
			padding: 3.5rem 2rem;
			text-align: center;
			position: relative;
			z-index: 1;
			box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
		}

		.crest-ring {
			width: 90px; height: 90px;
			border-radius: 50%;
			border: 1.5px solid var(--forest-700);
			display: flex; align-items: center; justify-content: center;
			margin: 0 auto 2rem;
			box-shadow: 0 0 30px rgba(22,163,74,0.15);
		}

		h1 {
			font-family: 'Playfair Display', serif;
			font-size: 2.5rem;
			color: #fff;
			margin-bottom: 1rem;
		}

		h1 span { color: var(--forest-400); }

		.status-badge {
			display: inline-block;
			padding: 0.4rem 1rem;
			background: rgba(22, 163, 74, 0.1);
			border: 1px solid rgba(22, 163, 74, 0.2);
			color: var(--forest-400);
			border-radius: 99px;
			font-size: 0.75rem;
			font-weight: 600;
			letter-spacing: 0.1em;
			text-transform: uppercase;
			margin-bottom: 1.5rem;
		}

		p {
			color: #94a3b8;
			font-family: 'DM Sans', sans-serif;
			line-height: 1.6;
			margin-bottom: 2rem;
		}

		.motto {
			font-family: 'Crimson Pro', serif;
			font-style: italic;
			color: #64748b;
			border-top: 1px solid var(--ink-700);
			padding-top: 1.5rem;
			margin-top: 2rem;
		}
		
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
</head>
<body>

<div class="maintenance-shell">
    <div class="maintenance-card fade-up">
        <div class="crest-ring">
            <svg class="w-10 h-10 text-forest-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z" />
            </svg>
        </div>

        <div class="status-badge">System Improving</div>
        
        <h1>Read<span>Pal</span></h1>
        
        <p>
            We're currently expanding our archives and refining the library. 
            ReadPal will be back online shortly with enhanced learning resources.
        </p>

        <div class="motto">
            "Refining the path to knowledge."
        </div>
    </div>
</div>
</body>
</html>