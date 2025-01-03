<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">

<head>
    <meta charset="utf-8" />
    <meta name="application-name" content="{{ config('app.name') }}" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

    <!-- Add favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('img/CBIpreview.png') }}">
    <!-- Alternative formats if needed -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('img/CBIpreview.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/CBIpreview.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('img/CBIpreview.png') }}">

    <title>@yield('title', 'IoT SRS SSMS Portal')</title>

    @filamentStyles
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/css/navbar.css'])
    <script>
        // Check for saved theme preference or use default (light)
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
</head>

<body class="antialiased">
    <!-- Add this loading screen div -->
    <div id="loading-screen" class="fixed inset-0 z-50 flex items-center justify-center bg-white bg-opacity-90 h-screen w-screen overflow-hidden">
        <div class="text-center">
            <div id="lottie-container" class="w-64 h-64">
                <div class="nav-icon lottie-animation" data-animation-path="{{ asset('loading/waterdrop.json') }}"></div>
            </div>
        </div>
    </div>

    @auth
    <div class="flex flex-col md:flex-row min-h-screen overflow-x-hidden bg-gradient-to-b from-sky-200 to-sky-100 dark:from-gray-800 dark:to-gray-900">
        <!-- Mobile menu button -->
        <button id="mobile-menu-button" class="md:hidden fixed top-4 left-4 z-50 p-2 mt-10 bg-white rounded-md shadow-md">
            <i class="fas fa-bars"></i>
        </button>

        <!-- Navbar -->
        <nav id="navbar" class="navbar md:block">
            <div class="navbar__brand">
                <img src="{{ asset('/img/CBIpreview.png') }}" alt="Company Logo" class="navbar__logo">
                <span class="navbar__company-name">SSMS</span>
            </div>
            <ul class="navbar__menu">
                <li class="navbar__item">
                    <a href="{{ route('dashboard') }}" class="navbar__link"><i class="fas fa-home"></i><span>Dashboard</span></a>
                </li>
                <li class="navbar__item">
                    <a href="{{ route('waterlevel') }}" class="navbar__link"><i class="fa fa-water fa-2x"></i><span>Water Level</span></a>
                </li>
                <li class="navbar__item">
                    <a href="{{ route('dashboardaws') }}" class="navbar__link"><i class="fa-solid fa-cloud"></i><span>Aws</span></a>
                </li>
                <li class="navbar__item">
                    <a href="#" onclick="handleLogout(); return false;" class="navbar__link">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </a>
                </li>
            </ul>
            <button id="sidebar-toggle" aria-label="Toggle Sidebar" class="dark:text-black absolute top-1/2 -right-4 transform -translate-y-1/2">
                <i class="fas fa-chevron-left"></i>
            </button>
        </nav>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
            @csrf
        </form>

        <!-- Overlay for closing sidebar on mobile -->
        <div id="sidebar-overlay" class="fixed inset-0 bg-black opacity-50 z-40 hidden md:hidden"></div>

        <!-- Main Content -->
        <div class="flex-1">
            <!-- Scrolling text at the top -->
            <div class="fixed top-0 w-full bg-gray-100 p-4 z-10">
                <div class="scrolling-text-container">
                    <p class="scrolling-text text-sm text-gray-700">
                        Selamat datang di Sistem Pemantauan IoT kami: Pengumpulan dan analisis data lingkungan secara real-time melalui jaringan terpadu Stasiun Cuaca dan pemantauan Tinggi Muka Air kami. Memberikan pengukuran presisi untuk pengambilan keputusan yang tepat.
                    </p>
                </div>
            </div>

            <!-- Main content area -->
            <main class="w-full pt-16"> <!-- Added pt-16 to account for fixed header -->
                {{ $slot }}
            </main>
        </div>
    </div>
    @else
    <!-- Full-screen layout for non-authenticated users (like login page) -->
    <div class="min-h-screen bg-gradient-to-b from-sky-200 to-sky-100 dark:from-gray-800 dark:to-gray-900 flex items-center justify-center">
        {{ $slot }}
    </div>
    @endauth

    <!-- Theme toggle button -->
    <button id="theme-toggle" class="fixed bottom-4 right-4 p-2 rounded-full bg-gray-200 dark:bg-gray-700 z-50">
        <i class="fas fa-moon dark:hidden"></i>
        <i class="fas fa-sun hidden dark:inline"></i>
    </button>

    @livewire('notifications')

    @filamentScripts

    <script type="module">
        function handleLogout() {
            // Show loading screen
            document.getElementById('loading-screen').style.display = 'flex';

            // Fade out the current page
            document.body.style.opacity = '0';

            // Submit the logout form after a brief delay
            setTimeout(() => {
                document.getElementById('logout-form').submit();
            }, 300);
        }
    </script>

</body>

</html>