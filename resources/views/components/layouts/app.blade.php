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

        /* Improved Floating Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            background-color: #fff;
            transition: width 0.3s ease;
            width: 4rem;
            overflow: hidden;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        .sidebar:hover,
        .sidebar.expanded {
            width: 16rem;
        }

        .sidebar .icon {
            font-size: 1.25rem;
            color: var(--primary-color);
            transition: transform 0.3s;
        }

        .sidebar-nav {
            display: flex;
            flex-direction: column;
            padding-top: 2rem;
        }

        .sidebar-nav a {
            color: var(--text-color);
            text-decoration: none;
            padding: 1rem;
            transition: background-color 0.3s;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .sidebar-nav a:hover {
            background-color: var(--hover-color);
        }

        .sidebar-nav span {
            opacity: 0;
            transition: opacity 0.3s;
        }

        .sidebar:hover .sidebar-nav span,
        .sidebar.expanded .sidebar-nav span {
            opacity: 1;
        }

        /* Main Content */
        main {
            margin-left: 4rem;
            padding: 2rem;
            transition: margin-left 0.3s ease;
        }

        .sidebar:hover~main,
        .sidebar.expanded~main {
            margin-left: 16rem;
        }

        /* Topbar */
        .topbar {
            background-color: #fff;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .topbar h1 {
            font-size: 1.5rem;
            color: var(--primary-color);
        }

        /* Add more styles as needed */
    </style>

    @filamentStyles
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
</head>

<body class="antialiased">
    @auth
    <div class="flex min-h-screen">
        <!-- Improved Floating Sidebar -->
        <aside class="sidebar" id="sidebar">
            <nav class="sidebar-nav">
                <a href="{{ route('dashboard') }}">
                    <i class="fas fa-th-large icon"></i>
                    <span>Dashboard</span>
                </a>
                <a href="#">
                    <i class="fas fa-map-marker-alt icon"></i>
                    <span>Location</span>
                </a>
                <a href="#">
                    <i class="fas fa-chart-line icon"></i>
                    <span>Analytics</span>
                </a>
                <a href="#">
                    <i class="fas fa-calendar-alt icon"></i>
                    <span>Calendar</span>
                </a>
                <a href="#">
                    <i class="fas fa-cog icon"></i>
                    <span>Settings</span>
                </a>
                <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt icon"></i>
                    <span>Logout</span>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </nav>
        </aside>

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
            const sidebar = document.getElementById('sidebar');
            const toggleSidebar = document.getElementById('toggleSidebar');

            // Toggle sidebar on mobile
            toggleSidebar.addEventListener('click', function() {
                sidebar.classList.toggle('expanded');
            });

            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function(event) {
                if (!sidebar.contains(event.target) && !toggleSidebar.contains(event.target)) {
                    sidebar.classList.remove('expanded');
                }
            });

            // Add active class to current page link
            const currentPath = window.location.pathname;
            const navLinks = sidebar.querySelectorAll('a');
            navLinks.forEach(link => {
                if (link.getAttribute('href') === currentPath) {
                    link.classList.add('bg-blue-100', 'text-blue-600');
                }
            });

            // Smooth scrolling for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    e.preventDefault();
                    document.querySelector(this.getAttribute('href')).scrollIntoView({
                        behavior: 'smooth'
                    });
                });
            });
        });
    </script>
</body>

</html>