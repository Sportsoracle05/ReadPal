<style>

/* Dropdown/Mobile Menu Backgrounds in Dark Mode (Optional but recommended) */
.dark #notification-dropdown,
.dark #apps-dropdown,
.dark #user-dropdown,
.dark #mobile-menu {
    background-color: #374151 !important; /* gray-700 */
}

  /* Base dark mode */
  .dark body {
    background-color: #1f2937; /* gray-800 */
    color: #f9fafb; /* gray-50 */
  }

  /* Header / Navbar */
  .dark .bg-white {
    background-color: #1f2937 !important;
  }
  .dark .border-gray-200 {
    border-color: #374151 !important;
  }
  .dark .text-gray-700 {
    color: #e5e7eb !important;
  }
  .dark .dark\:text-white {
    color: #ffffff !important;
  }

  /* Hover states */
  .dark .hover\:bg-gray-100:hover {
    background-color: #374151 !important;
  }
  .dark .hover\:text-blue-600:hover {
    color: #60a5fa !important;
  }

  /* Dropdowns */
  .dark .bg-gray-700 {
    background-color: #374151 !important;
  }
  .dark .divide-gray-100 {
    border-color: #4b5563 !important;
  }
  .dark .divide-gray-600 {
    border-color: #4b5563 !important;
  }
  .dark .text-gray-900 {
    color: #f9fafb !important;
  }
  .dark .text-gray-500 {
    color: #9ca3af !important;
  }
  .dark .text-gray-200 {
    color: #e5e7eb !important;
  }
  .dark .hover\:bg-gray-100:hover {
    background-color: #4b5563 !important;
  }
  .dark .hover\:bg-gray-600:hover {
    background-color: #4b5563 !important;
  }

  /* Buttons & icons */
  .dark .text-gray-500 {
    color: #9ca3af !important;
  }
  .dark .dark\:text-gray-400 {
    color: #9ca3af !important;
  }
  .dark .bg-gray-800 {
    background-color: #1f2937 !important;
  }

  /* Optional: focus states */
  .dark .focus\:ring-blue-500:focus {
    box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.5) !important;
  }
  .dark .dark\:focus\:ring-gray-600:focus {
    box-shadow: 0 0 0 3px rgba(75, 85, 99, 0.5) !important;
  }
 /* Inputs in dark mode */
  .dark input {
    background-color: #374151 !important; /* gray-700 */
    color: #f9fafb !important; /* light text */
    border-color: #4b5563 !important; /* gray-600 */
  }

  /* Placeholder text */
  .dark input::placeholder {
    color: #9ca3af !important; /* gray-400 */
    opacity: 1;
  }

  /* Focus ring for inputs in dark mode */
  .dark input:focus {
    border-color: #60a5fa !important; /* blue-400 */
    box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.5) !important;
  }
  /* Quick Actions Card */
  .dark .bg-white {
    background-color: #1f2937 !important; /* gray-800 */
  }

  /* Quick Actions Link Hover */
  .dark a.hover\:bg-gray-50:hover {
    background-color: #374151 !important; /* gray-700 */
  }

  /* Quick Actions Text */
  .dark a span {
    color: #f9fafb !important; /* light text */
  }

  /* Icon backgrounds */
  .dark a div.bg-blue-100 {
    background-color: #2563eb33 !important; /* translucent blue for dark mode */
  }
  .dark a div.bg-green-100 {
    background-color: #16a34a33 !important;
  }
  .dark a div.bg-purple-100 {
    background-color: #7c3aed33 !important;
  }
  .dark a div.bg-yellow-100 {
    background-color: #f59e0b33 !important;
  }

  /* Icon colors */
  .dark a i.text-blue-600 {
    color: #60a5fa !important;
  }
  .dark a i.text-green-600 {
    color: #34d399 !important;
  }
  .dark a i.text-purple-600 {
    color: #c084fc !important;
  }
  .dark a i.text-yellow-600 {
    color: #fbbf24 !important;
  }
  /* Card background in dark mode */
  .dark .bg-blue-50 {
    background-color: #1e293b !important; /* dark blue-gray */
  }

  /* Icon background */
  .dark .bg-blue-100 {
    background-color: #2563eb33 !important; /* translucent blue for dark mode */
  }

  /* Icon color */
  .dark .text-blue-600 {
    color: #60a5fa !important; /* light blue for dark mode */
  }

  /* Text colors */
  .dark .text-gray-500 {
    color: #e5e7eb !important; /* light gray */
  }

  .dark .font-semibold {
    color: #f9fafb !important; /* white-ish for numbers or headings */

  }
  /* Card backgrounds */
.dark .bg-green-50   { background-color: #14532d !important; } /* dark green */
.dark .bg-purple-50  { background-color: #4c1d95 !important; } /* dark purple */
.dark .bg-yellow-50  { background-color: #78350f !important; } /* dark amber */

/* Icon backgrounds */
.dark .bg-green-100  { background-color: #10b98133 !important; } /* translucent green */
.dark .bg-purple-100 { background-color: #a855f733 !important; } /* translucent purple */
.dark .bg-yellow-100 { background-color: #f59e0b33 !important; } /* translucent yellow */

/* Icon colors */
.dark .text-green-600  { color: #34d399 !important; } /* brighter green */
.dark .text-purple-600 { color: #c084fc !important; } /* brighter purple */
.dark .text-yellow-600 { color: #fbbf24 !important; } /* brighter yellow */

/* Text content */
.dark .text-gray-500    { color: #e5e7eb !important; } /* light gray for labels */
.dark .font-semibold    { color: #f9fafb !important; } /* white-ish for numbers */
/* Table header background */
.dark thead.bg-gray-50 {
    background-color: #1f2937 !important; /* dark gray */
}

/* Table header text */
.dark thead th.text-gray-500 {
    color: #e5e7eb !important; /* light gray */
}

/* Optional: Table border colors */
.dark table {
    border-color: #4b5563 !important; /* dark borders */
}

/* Optional: Table row hover for better contrast */
.dark tbody tr:hover {
    background-color: #374151 !important; /* darker row hover */
}
/* Dark mode for select fields */
.dark select {
    background-color: #374151 !important;  /* dark gray background */
    color: #f9fafb !important;            /* light text */
    border-color: #4b5563 !important;     /* darker border */
}

/* Placeholder / option text */
.dark select option {
    background-color: #374151 !important; /* ensures dropdown options are dark too */
    color: #f9fafb !important;            /* readable text */
}

/* Focus state */
.dark select:focus {
    border-color: #60a5fa !important;     /* blue focus ring */
    box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.5) !important;
}

@media (max-width: 640px) {
  #notification-dropdown,
  #apps-dropdown,
  #user-dropdown {
    right: 1rem !important;
    left: auto !important;
    width: 90% !important;
  }
}

</style>

<header class="antialiased">
  {{-- Fixed Navigation Wrapper --}}
  <div class="fixed top-0 left-0 w-full bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 z-50">
    <nav class="px-4 lg:px-6 py-2.5">
      <div class="flex flex-wrap justify-between items-center mx-auto max-w-screen-xl">
        
        {{-- Logo --}}
        <a href="{{ route('dashboard') }}" class="flex items-center">
          <span class="self-center text-xl font-semibold whitespace-nowrap dark:text-white">{{ config('app.name') }}</span>
        </a>

        <!-- Mobile menu button -->
        <button id="mobile-menu-button" 
          class="lg:hidden text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white focus:outline-none">
          <i data-feather="menu"></i>
        </button>


        {{-- Desktop Links --}}
        <div class="hidden lg:flex space-x-8 items-center">
          <a href="{{ route('dashboard') }}" class="text-gray-700 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400">Dashboard</a>
          <a href="{{ route('resources.index') }}" class="text-gray-700 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400">Resources</a>
          <a href="{{ route('materials.index') }}" class="text-gray-700 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400">Materials</a>
          <a href="{{ route('quiz.index') }}" class="text-gray-700 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400">Quiz</a>
        </div>

        {{-- Right Actions --}}
        <div class="flex items-center space-x-4 lg:order-2">
          {{-- Dark Mode Toggle --}}
          <button id="theme-toggle" type="button"
            class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white focus:outline-none">
            <svg id="theme-toggle-dark-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
              <path
                d="M17.293 13.293A8 8 0 016.707 2.707a8 8 0 1010.586 10.586z" />
            </svg>
            <svg id="theme-toggle-light-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd"
                d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4.22 2.47a1 1 0 011.42 1.42l-.71.71a1 1 0 01-1.42-1.42l.71-.71zM18 9a1 1 0 100 2h1a1 1 0 100-2h-1zM15.78 14.53a1 1 0 00-1.42 1.42l.71.71a1 1 0 101.42-1.42l-.71-.71zM10 16a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM4.22 14.53a1 1 0 00-1.42 1.42l.71.71a1 1 0 001.42-1.42l-.71-.71zM2 9a1 1 0 100 2H1a1 1 0 100-2h1zM4.22 4.22a1 1 0 011.42 0l.71.71a1 1 0 01-1.42 1.42l-.71-.71a1 1 0 010-1.42z"
                clip-rule="evenodd" />
            </svg>
          </button>

          {{-- Notification button --}}
          <button id="notificationButton"
            class="relative text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white">
            <i data-feather="bell"></i>
            <span class="absolute -top-1 -right-1 inline-flex items-center justify-center w-3 h-3 bg-red-500 rounded-full"></span>
          </button>

          {{-- Apps button --}}
          <button id="appsButton"
            class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white">
            <i data-feather="grid"></i>
          </button>

          {{-- User profile button --}}
          <button id="dropdownUserButton"
            class="flex items-center text-sm bg-gray-800 rounded-full focus:ring-2 focus:ring-blue-500 dark:focus:ring-gray-600"
            type="button">
            <span class="sr-only">Open user menu</span>
            <img class="w-8 h-8 rounded-full"
              src="{{ Auth::user()->profile_photo_url ?? asset('/images/profile.png') }}" alt="user photo">
          </button>
        </div>
      </div>
    </nav>
  </div>

  {{-- ============================ --}}
  {{-- DROPDOWNS BELOW FIXED NAVBAR --}}
  {{-- ============================ --}}
  <div class="relative z-40">

    <!-- Mobile menu (hidden by default) -->
        <div id="mobile-menu" 
         class="hidden w-64 bg-white divide-y divide-gray-100 rounded-lg shadow-lg dark:bg-gray-700 dark:divide-gray-600">
          <ul class="py-1 text-sm text-gray-700 dark:text-gray-200">
            <li><a href="{{ route('dashboard') }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600">Dashboard</a></li>
            <li><a href="{{ route('resources.index') }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600">Resources</a></li>
            <li><a href="{{ route('materials.index') }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600">Materials</a></li>
            <li><a href="{{ route('quiz.index') }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600">Quiz</a></li>
          </ul>
        </div>

    {{-- Notifications Dropdown --}}
    <div id="notification-dropdown"
      class="hidden w-64 bg-white divide-y divide-gray-100 rounded-lg shadow-lg dark:bg-gray-700 dark:divide-gray-600">
      <div class="px-4 py-3 text-sm text-gray-900 dark:text-white">
        <span class="font-semibold">Notifications</span>
      </div>
      <ul class="py-1 text-sm text-gray-700 dark:text-gray-200">
        <li><a href="#" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600">This part of app is coming soon</a></li>
      </ul>
    </div>

    {{-- Apps Dropdown --}}
    <div id="apps-dropdown"
      class="hidden w-56 bg-white divide-y divide-gray-100 rounded-lg shadow-lg dark:bg-gray-700 dark:divide-gray-600">
      <div class="px-4 py-3 text-sm text-gray-900 dark:text-white">
        <span class="font-semibold">Apps</span>
      </div>
      <ul class="py-1 text-sm text-gray-700 dark:text-gray-200">
        <li><a href="{{ route('dashboard') }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600">Dashboard</a></li>
        @auth
            @if(auth()->user()->role === 'rep' || auth()->user()->role === 'super')
                <li>
                    <a href="{{ route('admin.dashboard') }}"
                      class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600">
                      Administrate
                    </a>
                </li>
            @endif
        @endauth
        <li><a href="{{ route('quiz.index') }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600">Quiz Test</a></li>
        <li><a href="{{ route('notes.index', ['firstname' => Auth::user()->firstname]) }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600">Notes</a></li>
      </ul>
    </div>

    {{-- User Dropdown --}}
    <div id="user-dropdown"
      class="hidden w-48 bg-white divide-y divide-gray-100 rounded-lg shadow-lg dark:bg-gray-700 dark:divide-gray-600">
      <div class="px-4 py-3 text-sm text-gray-900 dark:text-white">
        <div class="font-medium">{{ Auth::user()->firstname }} {{ Auth::user()->lastname }}</div>
        <div class="truncate text-gray-500 dark:text-gray-400">{{ Auth::user()->email }}</div>
      </div>
      <ul class="py-1 text-sm text-gray-700 dark:text-gray-200">
        <li>
          @auth
            <a href="{{ route('profile.show', Auth::user()) }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600">My Profile</a>
          @endauth
        </li>
        <li><a href="{{ route('settings') }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600">Settings</a></li>
      </ul>
      <div class="py-1">
        <form action="{{ route('logout') }}" method="POST">
          @csrf
          <button type="submit"
            class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600">
            Logout
          </button>
        </form>
      </div>
    </div>
  </div>
</header>

{{-- Add top padding so content isn't hidden behind fixed navbar --}}
<div class="pt-16"></div>


<script>
  // Theme toggle
  const themeToggleBtn = document.getElementById('theme-toggle');
  const themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
  const themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

  // Initialize theme
  const savedTheme = localStorage.getItem('color-theme');

  // 🌙 UPDATED LOGIC: Default to 'dark' if no preference is saved, OR if the saved preference is 'dark'.
  if (savedTheme === 'dark' || savedTheme === null) {
    document.documentElement.classList.add('dark');
    themeToggleLightIcon.classList.remove('hidden');
    
    // Optional: Immediately save 'dark' if it's the first visit and no theme was set
    if (savedTheme === null) {
        localStorage.setItem('color-theme', 'dark');
    }
  } else {
    // This runs only if localStorage.getItem('color-theme') === 'light'
    themeToggleDarkIcon.classList.remove('hidden');
  }

  // --- This part remains the same ---
  themeToggleBtn.addEventListener('click', () => {
    themeToggleDarkIcon.classList.toggle('hidden');
    themeToggleLightIcon.classList.toggle('hidden');

    if (document.documentElement.classList.contains('dark')) {
      document.documentElement.classList.remove('dark');
      localStorage.setItem('color-theme', 'light');
    } else {
      document.documentElement.classList.add('dark');
      localStorage.setItem('color-theme', 'dark');
    }
  });


  document.addEventListener('DOMContentLoaded', () => {
    const nav = document.querySelector('.fixed.top-0'); // Navbar
    const $ = id => document.getElementById(id);

    // Buttons and dropdowns
    const btnDropdownPairs = [
      { btnId: 'notificationButton', dropdownId: 'notification-dropdown' },
      { btnId: 'appsButton',         dropdownId: 'apps-dropdown' },
      { btnId: 'dropdownUserButton', dropdownId: 'user-dropdown' },
      { btnId: 'mobile-menu-button', dropdownId: 'mobile-menu', mobileMenu: true }, // mark mobile menu
    ];

    // Initialize dropdowns
    btnDropdownPairs.forEach(({ dropdownId, mobileMenu }) => {
      const dd = $(dropdownId);
      if (!dd) return;
      dd.style.position = 'fixed';
      dd.style.zIndex = 9999;
      dd.style.transformOrigin = 'top right';
      dd.classList.add('hidden');

      // Mobile menu full width
      if (mobileMenu) {
        dd.style.left = '0';
        dd.style.right = '0';
        dd.style.width = '100%';
      }
    });

    // Position dropdowns
    function positionDropdown(btn, dd, mobileMenu = false) {
      if (!btn || !dd) return;
      const btnRect = btn.getBoundingClientRect();
      const navRect = nav ? nav.getBoundingClientRect() : { bottom: 0 };

      if (mobileMenu) {
        dd.style.top = `${navRect.bottom}px`;
        dd.style.left = '0';
        dd.style.right = '0';
        dd.style.width = '100%';
      } else {
        const top = Math.max(navRect.bottom, btnRect.bottom) + 8;
        const right = Math.max(window.innerWidth - btnRect.right, 8);
        dd.style.top = `${top}px`;
        dd.style.right = `${right}px`;
        dd.style.width = 'auto';
      }
    }

    // Close all dropdowns except one
    function closeAllDropdowns(exceptId = null) {
      btnDropdownPairs.forEach(({ dropdownId }) => {
        if (dropdownId === exceptId) return;
        const dd = $(dropdownId);
        if (dd && !dd.classList.contains('hidden')) dd.classList.add('hidden');
      });
    }

    // Toggle dropdown visibility
    function toggleDropdown(btn, dd, mobileMenu = false) {
      const isHidden = dd.classList.contains('hidden');
      closeAllDropdowns(isHidden ? dd.id : null);
      if (isHidden) {
        positionDropdown(btn, dd, mobileMenu);
        dd.classList.remove('hidden');
      } else {
        dd.classList.add('hidden');
      }
    }

    // Wire buttons
    btnDropdownPairs.forEach(({ btnId, dropdownId, mobileMenu }) => {
      const btn = $(btnId);
      const dd = $(dropdownId);
      if (!btn || !dd) return;
      btn.addEventListener('click', (e) => {
        e.stopPropagation();
        toggleDropdown(btn, dd, mobileMenu);
      });
    });

    // Reposition visible dropdowns on scroll/resize
    function recomputeVisible() {
      btnDropdownPairs.forEach(({ btnId, dropdownId, mobileMenu }) => {
        const btn = $(btnId);
        const dd = $(dropdownId);
        if (btn && dd && !dd.classList.contains('hidden')) {
          positionDropdown(btn, dd, mobileMenu);
        }
      });
    }

    window.addEventListener('scroll', recomputeVisible, { passive: true });
    window.addEventListener('resize', recomputeVisible);

    // Close on outside click
    document.addEventListener('click', (e) => {
      for (const { btnId, dropdownId } of btnDropdownPairs) {
        const btn = $(btnId);
        const dd = $(dropdownId);
        if (!btn || !dd) continue;
        if (btn.contains(e.target) || dd.contains(e.target)) return;
      }
      closeAllDropdowns();
    });

    // Close on escape
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') closeAllDropdowns();
    });

    // Ensure dropdowns are positioned on load
    recomputeVisible();
  });
</script>



