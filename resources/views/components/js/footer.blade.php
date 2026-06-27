    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 pt-16 pb-8">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-center mb-12">
          <div class="flex items-center gap-2 mb-4 md:mb-0">
            <i data-lucide="graduation-cap" class="w-6 h-6 text-slate-400"></i>
            <span class="text-lg font-bold text-slate-700">ReadPal</span>
          </div>
          <div class="flex gap-6">
            <a href="#" class="text-slate-400 hover:text-indigo-600 transition-colors">Privacy</a>
            <a href="#" class="text-slate-400 hover:text-indigo-600 transition-colors">Terms</a>
            <a href="https://emsetech.page.gd/#contact" class="text-slate-400 hover:text-indigo-600 transition-colors">Contact</a>
          </div>
        </div>
        <div class="text-center text-sm text-slate-400">
          <p>&copy; {{ now()->year }} Oracle Tech. All rights reserved.</p>
          <p class="mt-2">Built for Students.</p>
        </div>
      </div>
    </footer>

    <!-- Initialize Lucide Icons & Mobile Menu Script -->
    <script>
      // Initialize icons
      lucide.createIcons();

      // Mobile menu toggle
      const btn = document.getElementById('mobile-menu-btn');
      const menu = document.getElementById('mobile-menu');

      btn.addEventListener('click', () => {
        menu.classList.toggle('hidden');
      });
    </script>
  <script type="module" src="{{ asset("index.tsx") }}"></script>