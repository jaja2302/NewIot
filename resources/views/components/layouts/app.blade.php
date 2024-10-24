<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">

<head>
    <meta charset="utf-8" />
    <meta name="application-name" content="{{ config('app.name') }}" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

    <title>{{ config('app.name') }} - @yield('title', 'Welcome')</title>

    @filamentStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.7.14/lottie.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/lottie-web@latest"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script>
        // Check for saved theme preference or use default (light)
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>
</head>

<body class="antialiased">
    <!-- Add this loading screen div -->
    <div id="loading-screen" class="fixed inset-0 z-50 flex items-center justify-center bg-white bg-opacity-90 hidden">
        <div class="text-center">
            <div class="nav-icon lottie-animation"
                data-animation-path="{{ asset('loading/loadingsanimate.json') }}"
                id="lottie-container"
                style="width: 300px; height: 300px;">
            </div>
        </div>
    </div>

    @auth
    <div class="flex flex-col md:flex-row min-h-screen overflow-x-hidden">
        <!-- Mobile menu button -->
        <button id="mobile-menu-button" class="md:hidden fixed top-4 left-4 z-50 p-2 mt-10 bg-white rounded-md shadow-md">
            <i class="fas fa-bars"></i>
        </button>

        <!-- Navbar -->
        <nav id="navbar" class="navbar md:block hidden">
            <button id="sidebar-toggle" aria-label="Toggle Sidebar">
                <i class="fas fa-chevron-left"></i>
            </button>
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
                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="navbar__link">
                        <i class="fas fa-sign-out-alt"></i><span>Logout</span>
                    </a>
                </li>
            </ul>
        </nav>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
            @csrf
        </form>

        <!-- Overlay for closing sidebar on mobile -->
        <div id="sidebar-overlay" class="fixed inset-0 bg-black opacity-50 z-40 hidden md:hidden"></div>

        <!-- Main Content -->
        <div class="flex-1 w-full overflow-x-hidden">
            <!-- Add the scrolling information section here -->
            <div class="bg-gray-100 p-4 mb-4 overflow-hidden w-full">
                <div class="scrolling-text-container">
                    <p class="scrolling-text text-sm text-gray-700">
                        IoT Weather Station dan Water Level: Sistem kami menyediakan data real-time dari sensor, menampilkan laporan komprehensif di dashboard, termasuk visualisasi grafik harian, mingguan, dan bulanan. Data tersedia dalam format Excel untuk kemudahan analisis.
                    </p>
                </div>
            </div>

            <main class="p-4 w-full">
                <div class="min-h-screen">
                    <div class="container mx-auto px-4 py-8">
                        {{ $slot }}
                    </div>
                </div>
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

</body>

</html>