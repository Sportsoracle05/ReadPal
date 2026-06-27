<!-- Navigation -->
    <nav class="fixed w-full bg-white/80 backdrop-blur-md border-b border-slate-100 z-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
          <div class="flex items-center gap-2">
            <i data-lucide="graduation-cap" class="w-8 h-8 text-indigo-600"></i>
            <span class="text-xl font-bold tracking-tight text-slate-900">ReadPal</span>
          </div>
          <div class="hidden md:flex items-center space-x-8">
            @auth
            <a href="{{ route("dashboard") }}" class="text-sm font-medium text-slate-600 hover:text-indigo-600 transition-colors">
              Dashboard
            </a>
            @endauth
            <a href="{{ route("home") }}" class="text-sm font-medium text-slate-600 hover:text-indigo-600 transition-colors">Overview</a>
            <a href="#features" class="text-sm font-medium text-slate-600 hover:text-indigo-600 transition-colors">Features</a>
            @guest
            <a href="{{ route("login") }}" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-full transition-colors">
              Get Started
            </a>
            @endguest
           
          </div>
          <!-- Mobile menu button -->
          <div class="md:hidden">
            <button id="mobile-menu-btn" class="p-2 text-slate-600 hover:bg-slate-50 rounded-lg">
              <i data-lucide="menu" class="w-6 h-6"></i>
            </button>
          </div>
        </div>
      </div>
      <!-- Mobile Menu -->
      <div id="mobile-menu" class="hidden md:hidden bg-white border-b border-slate-100 p-4 space-y-4">
      @auth
      <a href="{{ route("dashboard") }}" class="block text-sm font-medium text-slate-600 hover:text-indigo-600">Dashboard</a>
      @endauth
        <a href="{{ route("home") }}" class="block text-sm font-medium text-slate-600 hover:text-indigo-600">Overview</a>
        <a href="#features" class="block text-sm font-medium text-slate-600 hover:text-indigo-600">Features</a>
        @guest
        <a href="{{ route("login") }}" class="block text-sm font-medium text-indigo-600 font-bold">Get Started</a>
        @endguest
      </div>
    </nav>
    
    