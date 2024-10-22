<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta name="application-name" content="{{ config('app.name') }}" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title>{{ config('app.name') }} - @yield('title', 'Welcome')</title>

    <style>
        [x-cloak] {
            display: none !important;
        }

        :root {
            --primary-color: #3B82F6;
            --bg-color: #F0F9FF;
            --text-color: #333;
            --hover-color: #E6F3FF;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            font-family: 'Arial', sans-serif;
        }

        /* New Navbar Styles */
        @import url('https://fonts.googleapis.com/css2?family=Open+Sans:wght@600&display=swap');

        body {
            background: #eaeef6;
            font-family: 'Open Sans', sans-serif;
        }

        .navbar {
            position: fixed;
            top: 50%;
            left: 1rem;
            transform: translateY(-50%);
            background: #fff;
            border-radius: 10px;
            padding: 1rem 0;
            box-shadow: 0 0 40px rgba(0, 0, 0, 0.03);
            display: flex;
            flex-direction: column;
        }

        .navbar__menu {
            display: flex;
            flex-direction: column;
        }

        .navbar__item {
            position: relative;
        }

        .navbar__link {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 3.5rem;
            width: 5.5rem;
            color: #6a778e;
            transition: 250ms ease all;
        }

        .navbar__link i {
            font-size: 1.2rem;
        }

        .navbar__link:hover {
            color: #fff;
        }

        .navbar__link span {
            position: absolute;
            left: 100%;
            transform: translate(-3rem);
            margin-left: 1rem;
            opacity: 0;
            pointer-events: none;
            color: #406ff3;
            background: #fff;
            padding: 0.75rem;
            transition: 250ms ease all;
            border-radius: 17.5px;
        }

        .navbar__link:hover span {
            opacity: 1;
            transform: translate(0);
        }

        .navbar__menu {
            position: relative;
        }

        .navbar__item {
            position: relative;
        }

        .navbar__item:before {
            content: '';
            position: absolute;
            opacity: 0;
            z-index: -1;
            top: 0;
            left: 1rem;
            width: 3.5rem;
            height: 3.5rem;
            background: #406ff3;
            border-radius: 17.5px;
            transition: 250ms cubic-bezier(1, 0.2, 0.1, 1.2) all;
        }

        .navbar__item:hover:before {
            opacity: 1;
            animation: gooeyEffect 250ms 1;
        }

        .navbar__item:last-child:before {
            content: none;
        }

        @keyframes gooeyEffect {
            0% {
                transform: scale(1, 1);
            }

            50% {
                transform: scale(1.1, 1.1);
            }

            100% {
                transform: scale(1, 1);
            }
        }

        /* Adjust main content margin */
        main {
            margin-left: 7rem;
            /* Adjust based on your navbar width */
            padding: 2rem;
        }

        /* Responsive adjustments */
        @media (max-height: 600px) {
            .navbar {
                padding: 0.5rem 0;
            }

            .navbar__link {
                height: 2.5rem;
            }
        }
    </style>

    @filamentStyles
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
</head>

<body class="antialiased">
    @auth
    <div class="flex min-h-screen">
        <!-- Updated Navbar -->
        <nav class="navbar">
            <ul class="navbar__menu">
                <li class="navbar__item">
                    <a href="{{ route('dashboard') }}" class="navbar__link"><i class="fas fa-home"></i><span>Dashboard</span></a>
                </li>
                <li class="navbar__item">
                    <a href="{{ route('waterlevel') }}" class="navbar__link"><i class="fas fa-map-marker-alt"></i><span>Water Level</span></a>
                </li>
                <li class="navbar__item">
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
                </li>
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
                {{ $slot }}
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
    @vite('resources/js/app.js')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Remove old sidebar-related JavaScript
            // feather.replace();
        });
    </script>
</body>

</html>