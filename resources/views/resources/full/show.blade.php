<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $resource->course_title }} &mdash; ReadPal Reader</title>
    
{{-- Open Graph --}}
<meta property="og:title" content="{{ $resource->course_title }} &mdash; ReadPal Reader">
<meta property="og:type" content="article">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:image" content="{{ asset('ReadPalMain.png') }}">
<meta property="og:description" content="ReadPal is a dedicated mobile learning companion designed to streamline access to academic materials.">
<meta property="og:site_name" content="ReadPal Online">
<meta property="og:locale" content="en_US">

{{-- Twitter --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $resource->course_title }}">
<meta name="twitter:description" content="ReadPal is a dedicated mobile learning companion designed to streamline access to academic materials.">
<meta name="twitter:image" content="{{ asset('ReadPalMain.png') }}">

  {{-- Favicon --}}
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
        <!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-6CQM0F1CWB"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-6CQM0F1CWB');
</script>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Smooth fade transition between lessons */
        .lesson-panel {
            display: none;
            opacity: 0;
            transition: opacity 0.4s ease;
        }

        .lesson-panel.active {
            display: flex;
            opacity: 1;
        }

        /* Hide scrollbar visually but keep it functional */
        .reading-area::-webkit-scrollbar {
            width: 6px;
        }

        .reading-area::-webkit-scrollbar-track {
            background: transparent;
        }

        .reading-area::-webkit-scrollbar-thumb {
            background-color: #d1d5db;
            border-radius: 999px;
        }

        /* Prose typography */
        .prose-content p   { margin-bottom: 1.25rem; line-height: 1.9; }
        .prose-content h1,
        .prose-content h2,
        .prose-content h3  { font-weight: 700; margin: 1.5rem 0 0.75rem; }
        .prose-content ul  { list-style: disc; padding-left: 1.5rem; margin-bottom: 1rem; }
        .prose-content ol  { list-style: decimal; padding-left: 1.5rem; margin-bottom: 1rem; }
        .prose-content li  { margin-bottom: 0.4rem; line-height: 1.8; }
        .prose-content pre { background: #f3f4f6; padding: 1rem; border-radius: 0.5rem; overflow-x: auto; }
        .prose-content code { font-family: monospace; font-size: 0.9em; }
        .prose-content blockquote {
            border-left: 4px solid #e5e7eb;
            padding-left: 1rem;
            color: #6b7280;
            font-style: italic;
            margin: 1rem 0;
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 antialiased">

    {{-- ===================================================
         TOP HEADER BAR — fixed, always visible
    =================================================== --}}
    <header class="fixed top-0 left-0 right-0 z-30 bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-3xl mx-auto px-6 py-3 flex items-center justify-between gap-4">

            {{-- Course info --}}
            <div class="min-w-0">
                <p class="text-xs font-semibold uppercase tracking-widest text-indigo-500 truncate">
                    {{ $resource->course_code }}
                </p>
                <h1 class="text-sm font-bold text-gray-900 leading-tight truncate">
                    {{ $resource->course_title }}
                </h1>
                <p class="text-xs text-gray-400 truncate">{{ $resource->lecturer }}</p>
            </div>

            {{-- Lesson progress indicator --}}
            <div class="flex-shrink-0 text-right">
                <p class="text-xs text-gray-400">Lesson</p>
                <p class="text-sm font-bold text-gray-700">
                    <span id="current-lesson-number">1</span>
                    <span class="font-normal text-gray-400">/ {{ $materials->count() }}</span>
                </p>
            </div>
        </div>

        {{-- Thin progress bar --}}
        <div class="h-0.5 bg-gray-100">
            <div
                id="progress-bar"
                class="h-full bg-indigo-500 transition-all duration-500 ease-out"
                style="width: {{ $materials->count() > 0 ? round(100 / $materials->count()) : 100 }}%"
            ></div>
        </div>
    </header>

    {{-- ===================================================
         MAIN CONTENT AREA — each lesson is a full panel
    =================================================== --}}
    <main class="pt-[72px] pb-[80px]">

        @forelse ($materials as $index => $material)

            <section
                class="lesson-panel {{ $index === 0 ? 'active' : '' }} min-h-[calc(100vh-152px)] flex-col"
                data-lesson-index="{{ $index }}"
                id="lesson-{{ $index }}"
                aria-label="Lesson {{ $index + 1 }}: {{ $material->title }}"
            >
                {{-- Scrollable reading area --}}
                <div class="reading-area flex-1 overflow-y-auto">
                    <div class="max-w-3xl mx-auto px-6 py-10">

                        {{-- Lesson header --}}
                        <div class="mb-8">
                            <p class="text-xs font-semibold uppercase tracking-widest text-indigo-400 mb-1">
                                Lesson {{ $index + 1 }}
                            </p>
                            <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 leading-snug">
                                {{ $material->title }}
                            </h2>
                            @if ($material->type)
                                <span class="inline-block mt-2 text-xs font-medium bg-indigo-50 text-indigo-600 px-3 py-1 rounded-full">
                                    {{ ucfirst($material->type) }}
                                </span>
                            @endif
                        </div>

                        {{-- Divider --}}
                        <hr class="border-gray-200 mb-8">

                        {{-- Main note content --}}
                        @if ($material->note_text)
                            <div class="prose-content text-gray-700 text-base sm:text-[17px] leading-relaxed">
                                {!! nl2br($material->note_text) !!}
                            </div>
                        @else
                            <p class="text-gray-400 italic">No notes available for this lesson.</p>
                        @endif

                        {{-- Optional: PDF attachment notice --}}
                        @if ($material->pdf_path)
                            <div class="mt-10 p-4 bg-amber-50 border border-amber-200 rounded-xl flex items-center gap-3">
                                <svg class="w-5 h-5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M7 21h10a2 2 0 002-2V9l-5-5H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                                <div>
                                    <p class="text-sm font-semibold text-amber-800">PDF Attachment Available</p>
                                    <a
                                        href="{{ asset($material->pdf_path) }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="text-xs text-amber-600 underline hover:text-amber-800"
                                    >
                                        Open PDF &rarr;
                                    </a>
                                </div>
                            </div>
                        @endif

                        {{-- Bottom spacer so content doesn't hide behind nav --}}
                        <div class="h-16"></div>

                    </div>
                </div>
            </section>

        @empty

            <div class="flex items-center justify-center min-h-[calc(100vh-152px)]">
                <div class="text-center text-gray-400">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <p class="font-medium">No lessons found for this resource.</p>
                </div>
            </div>

        @endforelse

    </main>

    {{-- ===================================================
         BOTTOM NAVIGATION BAR — fixed
    =================================================== --}}
    @if ($materials->count() > 0)
        <nav class="fixed bottom-0 left-0 right-0 z-30 bg-white border-t border-gray-200 shadow-[0_-4px_20px_rgba(0,0,0,0.06)]">
            <div class="max-w-3xl mx-auto px-6 py-3 flex items-center justify-between gap-4">

                {{-- Previous button --}}
                <button
                    id="prev-btn"
                    onclick="navigateLesson(-1)"
                    class="flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-gray-900
                           transition-colors duration-200 disabled:opacity-30 disabled:pointer-events-none"
                    disabled
                    aria-label="Previous lesson"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Previous
                </button>

                {{-- Lesson dot indicators (up to 10; beyond that hide) --}}
                @if ($materials->count() <= 10)
                    <div class="flex items-center gap-1.5" role="tablist" aria-label="Lessons">
                        @foreach ($materials as $i => $mat)
                            <button
                                onclick="goToLesson({{ $i }})"
                                class="lesson-dot w-2 h-2 rounded-full bg-gray-300 transition-all duration-300 hover:bg-indigo-400 focus:outline-none"
                                data-dot-index="{{ $i }}"
                                role="tab"
                                aria-label="Go to lesson {{ $i + 1 }}"
                                aria-selected="{{ $i === 0 ? 'true' : 'false' }}"
                            ></button>
                        @endforeach
                    </div>
                @else
                    {{-- For large lesson sets: just show counter --}}
                    <p class="text-xs text-gray-400">
                        <span id="nav-current">1</span> / {{ $materials->count() }}
                    </p>
                @endif

                {{-- Next button --}}
                <button
                    id="next-btn"
                    onclick="navigateLesson(1)"
                    class="flex items-center gap-2 text-sm font-semibold text-white bg-indigo-600
                           hover:bg-indigo-700 active:bg-indigo-800 px-5 py-2 rounded-xl
                           transition-colors duration-200 disabled:opacity-30 disabled:pointer-events-none
                           shadow-md shadow-indigo-200"
                    aria-label="Next lesson"
                >
                    Next Lesson
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>

            </div>
        </nav>
    @endif

    {{-- ===================================================
         JAVASCRIPT — Pure JS, no frameworks, no AJAX
         All lesson data already in the DOM.
    =================================================== --}}
    <script>
        (function () {
            'use strict';

            const TOTAL = {{ $materials->count() }};
            let current  = 0;
            let isAnimating = false;

            // Cache DOM references
            const panels   = document.querySelectorAll('.lesson-panel');
            const dots     = document.querySelectorAll('.lesson-dot');
            const prevBtn  = document.getElementById('prev-btn');
            const nextBtn  = document.getElementById('next-btn');
            const progressBar      = document.getElementById('progress-bar');
            const currentNumEl     = document.getElementById('current-lesson-number');
            const navCurrentEl     = document.getElementById('nav-current');

            /**
             * Navigate to a specific lesson by index.
             * Handles fade-out → swap → fade-in without page reload.
             */
            function goToLesson(targetIndex) {
                if (isAnimating || targetIndex === current) return;
                if (targetIndex < 0 || targetIndex >= TOTAL) return;

                isAnimating = true;

                const outgoing = panels[current];
                const incoming = panels[targetIndex];

                // Fade out current panel
                outgoing.style.opacity = '0';

                setTimeout(function () {
                    // Hide outgoing, show incoming
                    outgoing.classList.remove('active');
                    outgoing.style.opacity = '';

                    incoming.classList.add('active');

                    // Scroll reading area to top on lesson change
                    const readingArea = incoming.querySelector('.reading-area');
                    if (readingArea) readingArea.scrollTop = 0;

                    // Force reflow so transition fires correctly
                    void incoming.offsetHeight;

                    // Fade in
                    incoming.style.opacity = '0';
                    requestAnimationFrame(function () {
                        incoming.style.transition = 'opacity 0.35s ease';
                        incoming.style.opacity    = '1';
                    });

                    current = targetIndex;
                    updateUI();

                    setTimeout(function () {
                        incoming.style.transition = '';
                        incoming.style.opacity    = '';
                        isAnimating = false;
                    }, 380);

                }, 250); // Wait for fade-out
            }

            /** Step forward or backward by `delta` (±1) */
            function navigateLesson(delta) {
                goToLesson(current + delta);
            }

            /** Sync all UI chrome to match `current` */
            function updateUI() {
                const humanIndex = current + 1;

                // Counter labels
                if (currentNumEl) currentNumEl.textContent = humanIndex;
                if (navCurrentEl) navCurrentEl.textContent = humanIndex;

                // Progress bar
                if (progressBar) {
                    progressBar.style.width = ((humanIndex / TOTAL) * 100) + '%';
                }

                // Dot indicators
                dots.forEach(function (dot, i) {
                    const isActive = i === current;
                    dot.classList.toggle('bg-indigo-600', isActive);
                    dot.classList.toggle('w-3', isActive);
                    dot.classList.toggle('h-3', isActive);
                    dot.classList.toggle('bg-gray-300', !isActive);
                    dot.classList.toggle('w-2', !isActive);
                    dot.classList.toggle('h-2', !isActive);
                    dot.setAttribute('aria-selected', isActive ? 'true' : 'false');
                });

                // Button states
                if (prevBtn) prevBtn.disabled = (current === 0);
                if (nextBtn) {
                    const isLast = current === TOTAL - 1;
                    nextBtn.disabled = isLast;
                    nextBtn.textContent = '';

                    // Rebuild next button content (icon + label)
                    const label = document.createTextNode(isLast ? 'Completed' : 'Next Lesson');
                    const svg   = buildArrowSvg();
                    nextBtn.appendChild(label);
                    if (!isLast) nextBtn.appendChild(svg);
                }
            }

            /** Build the arrow SVG icon for the Next button */
            function buildArrowSvg() {
                const svgNS = 'http://www.w3.org/2000/svg';
                const svg   = document.createElementNS(svgNS, 'svg');
                svg.setAttribute('class', 'w-4 h-4');
                svg.setAttribute('fill', 'none');
                svg.setAttribute('stroke', 'currentColor');
                svg.setAttribute('viewBox', '0 0 24 24');
                const path = document.createElementNS(svgNS, 'path');
                path.setAttribute('stroke-linecap', 'round');
                path.setAttribute('stroke-linejoin', 'round');
                path.setAttribute('stroke-width', '2');
                path.setAttribute('d', 'M9 5l7 7-7 7');
                svg.appendChild(path);
                return svg;
            }

            /** Keyboard navigation: ArrowRight / ArrowLeft */
            document.addEventListener('keydown', function (e) {
                if (e.key === 'ArrowRight' || e.key === 'ArrowDown') navigateLesson(1);
                if (e.key === 'ArrowLeft'  || e.key === 'ArrowUp')   navigateLesson(-1);
            });

            // Expose navigateLesson and goToLesson to global scope for onclick handlers
            window.navigateLesson = navigateLesson;
            window.goToLesson     = goToLesson;

            // Boot: set initial UI state
            updateUI();

        })();
    </script>

</body>
</html>
