// @ts-ignore
import $ from 'jquery';
window.$ = $;
window.jQuery = $;

$(function() {
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const navbar = document.getElementById('navbar');
    const sidebarOverlay = document.getElementById('sidebar-overlay');

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

    mobileMenuButton.addEventListener('click', toggleSidebar);
    sidebarOverlay.addEventListener('click', hideSidebar);

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
