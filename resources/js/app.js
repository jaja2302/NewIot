// @ts-ignore
import $ from 'jquery';
window.$ = $;
window.jQuery = $;
import L from 'leaflet';
window.L = L;
import 'leaflet/dist/leaflet.css';
import Highcharts from 'highcharts';
window.Highcharts = Highcharts;
$(function() {
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const navbar = document.getElementById('navbar');
    const sidebarOverlay = document.getElementById('sidebar-overlay');
    const $navbar = $('.navbar');
    const $sidebarToggle = $('#sidebar-toggle');
    const $main = $('main');

    function toggleSidebar() {
        navbar.classList.toggle('hidden');
        sidebarOverlay.classList.toggle('hidden');
        document.body.classList.toggle('overflow-hidden');
    }

    function hideSidebar() {
        navbar.classList.add('hidden');
        sidebarOverlay.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    function toggleDesktopSidebar() {
        $navbar.toggleClass('collapsed');
        $main.toggleClass('sidebar-collapsed');
    }

    mobileMenuButton.addEventListener('click', toggleSidebar);
    sidebarOverlay.addEventListener('click', hideSidebar);

    $sidebarToggle.on('click', function() {
        if (window.innerWidth <= 768) {
            toggleSidebar();
        } else {
            toggleDesktopSidebar();
        }
    });

    // Function to handle resize events
    function handleResize() {
        if (window.innerWidth > 768) {
            navbar.classList.remove('hidden');
            sidebarOverlay.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            // Preserve desktop sidebar state
            if ($navbar.hasClass('collapsed')) {
                $main.addClass('sidebar-collapsed');
            } else {
                $main.removeClass('sidebar-collapsed');
            }
        } else {
            // Always hide sidebar in mobile view
            navbar.classList.add('hidden');
            sidebarOverlay.classList.add('hidden');
            // Remove desktop-specific classes
            $navbar.removeClass('collapsed');
            $main.removeClass('sidebar-collapsed');
        }
    }

    // Add resize event listener
    window.addEventListener('resize', handleResize);

    // Initial call to set correct state
    handleResize();

    // Hide sidebar when clicking a nav link on mobile
    const navLinks = document.querySelectorAll('.navbar__link');
    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth <= 768) {
                hideSidebar();
            }
        });
    });

    const $themeToggleBtn = $('#theme-toggle');

    $themeToggleBtn.on('click', function() {
        $('html').toggleClass('dark');

        if ($('html').hasClass('dark')) {
            localStorage.theme = 'dark';
        } else {
            localStorage.theme = 'light';
        }
    });
    // Lottie animations
    $('.lottie-animation').each(function() {
        var animationPath = $(this).data('animation-path');
        lottie.loadAnimation({
            container: this,
            renderer: 'svg',
            loop: true,
            autoplay: true,
            path: animationPath,
            rendererSettings: {
                preserveAspectRatio: 'xMidYMid slice'
            }
        });
    });

    // Loading screen and navigation
    const $loadingScreen = $('#loading-screen');
    const $navLinks = $('.navbar__link');

    // Show loading screen function
    function showLoadingScreen() {
        $loadingScreen.css('display', 'flex');
        $('body').css({
            'overflow': 'hidden',
            'height': '100vh'
        });
    }

    // Hide loading screen function
    function hideLoadingScreen() {
        $loadingScreen.css('display', 'none');
        $('body').css({
            'overflow': '',
            'height': ''
        });
    }

    $navLinks.on('click', function(e) {
        if (!$(this).attr('href').includes('logout')) {
            e.preventDefault();
            showLoadingScreen();
            setTimeout(() => {
                window.location.href = $(this).attr('href');
            }, 500);
        }
    });

    // Hide loading screen when page is fully loaded
    $(window).on('load', function() {
        hideLoadingScreen();
    });

    // For Livewire navigation
    document.addEventListener('livewire:load', function() {
        hideLoadingScreen(); // Hide on initial load
        Livewire.on('pageChanged', hideLoadingScreen);
    });

    // Show loading screen on Livewire navigation start
    document.addEventListener('livewire:navigating', showLoadingScreen);

    // Immediately hide loading screen if no navigation occurs
    setTimeout(hideLoadingScreen, 1000);
});
