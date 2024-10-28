// @ts-ignore
import $ from 'jquery';
window.$ = $;
window.jQuery = $;
import L from 'leaflet';
window.L = L;
import 'leaflet/dist/leaflet.css';


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

    $navLinks.on('click', function(e) {
        // Don't show loading screen for logout
        if (!$(this).attr('href').includes('logout')) {
            e.preventDefault();
            $loadingScreen.removeClass('hidden');
            setTimeout(() => {
                window.location.href = $(this).attr('href');
            }, 500); // Delay to show loading screen
        }
    });

    // Hide loading screen when page is fully loaded
    $(window).on('load', function() {
        $loadingScreen.addClass('hidden');
    });

    // For Livewire navigation
    $(document).on('livewire:load', function() {
        Livewire.on('pageChanged', () => {
            $loadingScreen.addClass('hidden');
        });
    });

    
});
