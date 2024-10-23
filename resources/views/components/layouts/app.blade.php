<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta name="application-name" content="{{ config('app.name') }}" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title>{{ config('app.name') }} - @yield('title', 'Welcome')</title>

    @filamentStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.7.14/lottie.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/lottie-web@latest"></script>


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
    <div class="flex min-h-screen">
        <!-- Updated Navbar -->
        <nav class="navbar">
            <!-- Add logo and company name -->
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
                <!-- <li class="navbar__item">
                    <a href="#" class="navbar__link"><i class="fas fa-users"></i><span>Customers</span></a>
                </li>
                <li class="navbar__item">
                    <a href="#" class="navbar__link"><i class="fas fa-folder"></i><span>Projects</span></a>
                </li>
                <li class="navbar__item">
                    <a href="#" class="navbar__link"><i class="fas fa-archive"></i><span>Resources</span></a>
                </li>
                <li class="navbar__item">
                    <a href="#" class="navbar__link"><i class="fas fa-question-circle"></i><span>Help</span></a>
                </li>
                <li class="navbar__item">
                    <a href="#" class="navbar__link"><i class="fas fa-cog"></i><span>Settings</span></a>
                </li> -->
                <li class="navbar__item">
                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="navbar__link">
                        <i class="fas fa-sign-out-alt"></i><span>Logout</span>
                    </a>
                </li>
            </ul>
        </nav>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>

        <!-- Main Content -->
        <div class="flex-1">
            <main>
                <div class="min-h-screen">
                    <div class="container mx-auto px-4 py-8">
                        {{ $slot }}
                    </div>
                </div>
            </main>
        </div>
    </div>
    @else
    <div class="min-h-screen flex items-center justify-center">
        {{ $slot }}
    </div>
    @endauth

    @livewire('notifications')

    @filamentScripts

</body>

</html>