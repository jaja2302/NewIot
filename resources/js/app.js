// @ts-ignore
import $ from 'jquery';
window.$ = $;
window.jQuery = $;
import L from 'leaflet';
window.L = L;
import 'leaflet/dist/leaflet.css';
import Highcharts from 'highcharts';
window.Highcharts = Highcharts;

function initializeScrollNavigation(upRoute, downRoute, options = {}) {
    // Adjusted default settings
    const config = {
        scrollThreshold: 800,          // Increased time between scroll triggers (ms)
        scrollTriggerDistance: 100,    // Increased scroll strength needed
        boundaryDistance: 100,         // Reduced boundary distance to require scrolling closer to edge
        scrollProgress: 0,             // Track cumulative scroll
        scrollProgressThreshold: 150,  // Required cumulative scroll before triggering
        ...options
    };

    let isRedirecting = false;
    let lastScrollTime = 0;
    let touchStartY;
    let scrollProgress = 0;
    let lastScrollDirection = null;

    const main = document.querySelector('main');
    
    // Modified wheel event handler
    main.addEventListener('wheel', (e) => {
        if (isRedirecting) return;
        
        const now = Date.now();
        if (now - lastScrollTime < config.scrollThreshold) return;

        const scrollTop = main.scrollTop;
        const scrollHeight = main.scrollHeight;
        const clientHeight = main.clientHeight;
        
        // Determine scroll direction
        const currentDirection = e.deltaY > 0 ? 'down' : 'up';
        
        // Reset progress if direction changed
        if (lastScrollDirection !== currentDirection) {
            scrollProgress = 0;
            lastScrollDirection = currentDirection;
        }

        // Accumulate scroll progress
        scrollProgress += Math.abs(e.deltaY);

        // Check if we've scrolled enough and are in the boundary area
        if (scrollProgress >= config.scrollProgressThreshold) {
            if (scrollTop <= config.boundaryDistance && e.deltaY < 0) {
                e.preventDefault();
                handleDirectionalScroll(false);
                lastScrollTime = now;
                scrollProgress = 0;
            } 
            else if (scrollTop + clientHeight >= scrollHeight - config.boundaryDistance && e.deltaY > 0) {
                e.preventDefault();
                handleDirectionalScroll(true);
                lastScrollTime = now;
                scrollProgress = 0;
            }
        }
    }, { passive: false });

    // Modified touch handlers
    main.addEventListener('touchmove', (e) => {
        if (isRedirecting) return;
        const touchY = e.touches[0].clientY;
        const touchDiff = touchStartY - touchY;
        
        // Determine direction
        const currentDirection = touchDiff > 0 ? 'down' : 'up';
        
        // Reset progress if direction changed
        if (lastScrollDirection !== currentDirection) {
            scrollProgress = 0;
            lastScrollDirection = currentDirection;
        }

        // Accumulate touch progress
        scrollProgress += Math.abs(touchDiff);

        const scrollTop = main.scrollTop;
        const scrollHeight = main.scrollHeight;
        const clientHeight = main.clientHeight;
        
        const now = Date.now();
        if (now - lastScrollTime < config.scrollThreshold) return;

        // Check scroll boundaries
        if (Math.abs(touchDiff) > config.scrollTriggerDistance) {
            if (scrollTop <= 0 && touchDiff < 0) {
                handleDirectionalScroll(false);
                lastScrollTime = now;
            } else if (scrollTop + clientHeight >= scrollHeight && touchDiff > 0) {
                handleDirectionalScroll(true);
                lastScrollTime = now;
            }
        }
    });

    // Wheel event handler for desktop
    main.addEventListener('wheel', (e) => {
        if (isRedirecting) return;
        
        const now = Date.now();
        if (now - lastScrollTime < config.scrollThreshold) return;

        const scrollTop = main.scrollTop;
        const scrollHeight = main.scrollHeight;
        const clientHeight = main.clientHeight;

        if (Math.abs(e.deltaY) > config.scrollTriggerDistance) {
            // For upward scroll, check if we're within boundaryDistance of the top
            if (scrollTop <= config.boundaryDistance && e.deltaY < 0) {
                e.preventDefault();
                handleDirectionalScroll(false);
                lastScrollTime = now;
            } 
            // For downward scroll, check if we're within boundaryDistance of the bottom
            else if (scrollTop + clientHeight >= scrollHeight - config.boundaryDistance && e.deltaY > 0) {
                e.preventDefault();
                handleDirectionalScroll(true);
                lastScrollTime = now;
            }
        }
    }, { passive: false });

    function handleDirectionalScroll(isScrollingDown) {
        if (isRedirecting) return;
        
        isRedirecting = true;
        const route = isScrollingDown ? downRoute : upRoute;

        // Add fade-out effect
        document.body.style.opacity = '1';
        document.body.style.transition = 'opacity 0.5s ease-in-out';

        setTimeout(() => {
            document.body.style.opacity = '0';
        }, 100);

        // Redirect after fade
        setTimeout(() => {
            window.location.href = route;
        }, 600);
    }
}

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
        mobileMenuButton.classList.toggle('menu-open');
    }

    function hideSidebar() {
        navbar.classList.add('hidden');
        sidebarOverlay.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
        mobileMenuButton.classList.remove('menu-open');
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
    const $html = $('html');

    function updateThemeIcon() {
        const isDark = $html.hasClass('dark');
        $('.fa-sun').toggleClass('hidden', !isDark);
        $('.fa-moon').toggleClass('hidden', isDark);
    }

    function setTheme(isDark) {
        if (isDark) {
            $html.addClass('dark');
            localStorage.theme = 'dark';
        } else {
            $html.removeClass('dark');
            localStorage.theme = 'light';
        }
        updateThemeIcon();
    }

    // Initialize theme based on localStorage or system preference
    if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        setTheme(true);
    } else {
        setTheme(false);
    }

    // Theme toggle click handler
    $themeToggleBtn.on('click', function() {
        const isDark = !$html.hasClass('dark');
        setTheme(isDark);
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
        const loadingScreen = document.getElementById('loading-screen');
        loadingScreen.style.display = 'flex';
        // Force a reflow
        loadingScreen.offsetHeight;
        loadingScreen.classList.add('visible');
        document.body.style.overflow = 'hidden';
    }

    // Hide loading screen function
    function hideLoadingScreen() {
        const loadingScreen = document.getElementById('loading-screen');
        loadingScreen.classList.remove('visible');
        setTimeout(() => {
            loadingScreen.style.display = 'none';
            document.body.style.overflow = '';
        }, 300);
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

    // Expose the function globally
   
});
window.initializeScrollNavigation = initializeScrollNavigation;