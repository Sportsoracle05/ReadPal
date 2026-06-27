{{-- Site footer --}}
<footer class="relative z-10 mt-16 border-t border-amber/10">
    <div class="max-w-6xl mx-auto px-6 md:px-10 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-10">

            {{-- Brand --}}
            <div class="md:col-span-2">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                         style="background: linear-gradient(135deg, #15803D, #A06015);">
                        <svg class="w-4 h-4 text-ink-900" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                        </svg>
                    </div>
                    <span class="font-display text-lg font-semibold text-parch-100">ReadPal</span>
                </div>
                <p class="text-parch-100/50 text-sm leading-relaxed max-w-xs">
                    Your dedicated academic learning companion. Designed for university students, built by Oracle Tech.
                </p>
                <p class="mt-4 text-xs text-amber/50 font-medium">Serving Sociology 300-level students · AAUA</p>
            </div>

            {{-- Quick links --}}
            <div>
                <h4 class="font-body font-700 text-parch-100/80 text-xs uppercase tracking-widest mb-4">Platform</h4>
                <ul class="space-y-2.5">
                    @foreach([['Login','route','login'],['Sign Up','route','signup'],['Forgot Password','route','password.request']] as $link)
                    <li>
                        <a href="{{ $link[1] === 'route' ? route($link[2]) : url($link[2]) }}"
                           class="text-parch-100/45 hover:text-amber text-sm transition-colors">{{ $link[0] }}</a>
                    </li>
                    @endforeach
                </ul>
            </div>

            {{-- Company --}}
            <div>
                <h4 class="font-body font-700 text-parch-100/80 text-xs uppercase tracking-widest mb-4">Company</h4>
                <ul class="space-y-2.5">
                    @foreach([['About Us','/about'],['Contact','/contact'],['Feedback','/feedback'],['Privacy Policy','/privacy']] as $link)
                    <li>
                        <a href="{{ url($link[1]) }}"
                           class="text-parch-100/45 hover:text-amber text-sm transition-colors">{{ $link[0] }}</a>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="mt-10 pt-6 border-t border-amber/8 flex flex-col md:flex-row items-center justify-between gap-3">
            <p class="text-parch-100/30 text-xs">
                &copy; {{ date('Y') }} ReadPal · Developed & Maintained by <span class="text-amber/60">Oracle Tech</span>
            </p>
            <p class="text-parch-100/25 text-xs">Built with ❤ for academic excellence</p>
        </div>
    </div>
</footer>
