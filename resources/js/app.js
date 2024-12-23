// @ts-ignore
import $ from 'jquery';
window.$ = $;
window.jQuery = $;
import L from 'leaflet';
window.L = L;
import 'leaflet/dist/leaflet.css';
import Highcharts from 'highcharts';
import HighchartsAccessibility from 'highcharts/modules/accessibility';
import '@fortawesome/fontawesome-free/css/all.min.css';
import lottie from 'lottie-web';
import * as d3 from "d3";
window.lottie = lottie;
window.d3 = d3;
// Initialize the accessibility module
HighchartsAccessibility(Highcharts);
Highcharts.setOptions({
    accessibility: {
        enabled: false
    }
});
window.Highcharts = Highcharts;
import 'leaflet/dist/leaflet.css';
import 'leaflet-defaulticon-compatibility/dist/leaflet-defaulticon-compatibility.css';
import 'leaflet-defaulticon-compatibility';
import ApexCharts from 'apexcharts'
window.ApexCharts = ApexCharts;
import 'leaflet-control-geocoder';
import 'leaflet-control-geocoder/dist/Control.Geocoder.css';
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
    // Define isLoginPage at the beginning
    const isLoginPage = window.location.pathname === '/login';
    
    let sidebarTimer;
    const SIDEBAR_HIDE_DELAY = 1000;
    let mobileMenuButton, navbar, sidebarOverlay, $navbar, $sidebarToggle, $main;

    // Define all sidebar-related functions at the top level
    function hideSidebar() {
        navbar.classList.add('hidden');
        sidebarOverlay.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
        document.querySelector('main').classList.remove('sidebar-open');
        
        if (mobileMenuButton) {
            mobileMenuButton.classList.remove('menu-open');
        }
    }

    function toggleSidebar() {
        navbar.classList.toggle('hidden');
        sidebarOverlay.classList.toggle('hidden');
        document.body.classList.toggle('overflow-hidden');
        document.querySelector('main').classList.toggle('sidebar-open');
        
        if (mobileMenuButton) {
            mobileMenuButton.classList.toggle('menu-open');
        }
    }

    function toggleDesktopSidebar() {
        $navbar.toggleClass('collapsed');
        $main.toggleClass('sidebar-collapsed');
    }

    function startSidebarTimer() {
        clearTimeout(sidebarTimer);
        sidebarTimer = setTimeout(() => {
            if (!$navbar.is(':hover')) {
                $navbar.addClass('collapsed');
                $main.addClass('sidebar-collapsed');
            }
        }, SIDEBAR_HIDE_DELAY);
    }

    function handleSidebarHover() {
        if (window.innerWidth > 768) {
            $navbar.on('mouseenter', function() {
                clearTimeout(sidebarTimer);
                $navbar.removeClass('collapsed');
                $main.removeClass('sidebar-collapsed');
            });

            $navbar.on('mouseleave', startSidebarTimer);
            startSidebarTimer();
        }
    }

    function handleResize() {
        if (navbar) {
            if (window.innerWidth > 768) {
                navbar.classList.remove('hidden');
                sidebarOverlay.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
                handleSidebarHover();
            } else {
                $navbar.off('mouseenter mouseleave');
                clearTimeout(sidebarTimer);
                navbar.classList.add('hidden');
                sidebarOverlay.classList.add('hidden');
                $navbar.removeClass('collapsed');
                $main.removeClass('sidebar-collapsed');
            }
        }
    }

    // Initialize elements and add event listeners only if not on login page
    if (!isLoginPage) {
        mobileMenuButton = document.getElementById('mobile-menu-button');
        navbar = document.getElementById('navbar');
        sidebarOverlay = document.getElementById('sidebar-overlay');
        $navbar = $('.navbar');
        $sidebarToggle = $('#sidebar-toggle');
        $main = $('main');

        if (mobileMenuButton) {
            mobileMenuButton.addEventListener('click', toggleSidebar);
        }
        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', hideSidebar);
        }

        $sidebarToggle.on('click', function() {
            if (window.innerWidth <= 768) {
                toggleSidebar();
            } else {
                toggleDesktopSidebar();
            }
        });

        // Add mobile nav link click handler
        const navLinks = document.querySelectorAll('.navbar__link');
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth <= 768) {
                    hideSidebar();
                }
            });
        });

        window.addEventListener('resize', handleResize);
        handleResize();
    }

    const ThemeManager = {
        init() {
            this.$themeToggleBtn = $('#theme-toggle');
            this.$html = $('html');
            this.setupInitialTheme();
            this.bindEvents();
        },
        
        updateIcon() {
            const isDark = this.$html.hasClass('dark');
            $('.fa-sun').toggleClass('hidden', !isDark);
            $('.fa-moon').toggleClass('hidden', isDark);
        },
        
        setTheme(isDark) {
            this.$html.toggleClass('dark', isDark);
            localStorage.theme = isDark ? 'dark' : 'light';
            this.updateIcon();
        },
        
        setupInitialTheme() {
            const isDark = localStorage.theme === 'dark' || 
                (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches);
            this.setTheme(isDark);
        },
        
        bindEvents() {
            this.$themeToggleBtn.on('click', () => {
                this.setTheme(!this.$html.hasClass('dark'));
            });
        }
    };

    // Initialize theme manager
    ThemeManager.init();

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

    // Create a LoadingScreen module
    const LoadingScreen = {
        show() {
            const loadingScreen = document.getElementById('loading-screen');
            loadingScreen.style.display = 'flex';
            loadingScreen.offsetHeight; // Force a reflow
            loadingScreen.classList.add('visible');
            document.body.style.overflow = 'hidden';
        },
        
        hide() {
            const loadingScreen = document.getElementById('loading-screen');
            loadingScreen.classList.remove('visible');
            setTimeout(() => {
                loadingScreen.style.display = 'none';
                document.body.style.overflow = '';
            }, 300);
        }
    };

    // Use it consistently throughout the code
    window.showLoadingScreen = LoadingScreen.show;
    window.hideLoadingScreen = LoadingScreen.hide;

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
            LoadingScreen.show();
            setTimeout(() => {
                window.location.href = $(this).attr('href');
            }, 500);
        }
    });

    // Hide loading screen when pagec is fully loaded
    $(window).on('load', function() {
        hideLoadingScreen();
    });

    // // For Livewire navigation
    // document.addEventListener('livewire:load', function() {
    //     // console.log('pageChanged');
        
    //     hideLoadingScreen(); // Hide on initial load
    //     Livewire.on('pageChanged', hideLoadingScreen);
    // });

    // Show loading screen on Livewire navigation start
    document.addEventListener('livewire:navigating', showLoadingScreen);

    // Immediately hide loading screen if no navigation occurs
    setTimeout(hideLoadingScreen, 1000);

    // Expose the function globally
    window.initializeScrollNavigation = initializeScrollNavigation;

    // Update the global loading screen functions
    window.showLoadingScreen = function() {
        const loadingScreen = document.getElementById('loading-screen');
        loadingScreen.style.display = 'flex';
        loadingScreen.offsetHeight; // Force a reflow
        loadingScreen.classList.add('visible');
        document.body.style.overflow = 'hidden';
    };

    window.hideLoadingScreen = function() {
        const loadingScreen = document.getElementById('loading-screen');
        loadingScreen.classList.remove('visible');
        setTimeout(() => {
            loadingScreen.style.display = 'none';
            document.body.style.overflow = '';
        }, 300);
    };
    
    
    // Remove or comment out the existing changePage function since we're using showLoadingScreen directly
    // window.changePage = function() { ... }

    // Move the Sidebar initialization inside the jQuery ready function
    if (!isLoginPage) {
        Sidebar.init({
            mobileMenuButton: document.getElementById('mobile-menu-button'),
            navbar: document.getElementById('navbar'),
            overlay: document.getElementById('sidebar-overlay'),
            $navbar: $('.navbar'),
            $main: $('main')
        });
    }
});

// For the logout handler error, add this to your global scope:
window.handleLogout = function() {
    // Show loading screen before logout
    if (window.showLoadingScreen) {
        window.showLoadingScreen();
    }
    
    // Find and submit the logout form
    const logoutForm = document.getElementById('logout-form');
    if (logoutForm) {
        setTimeout(() => {
            logoutForm.submit();
        }, 500);
    }
};

// Add the loadWeatherAnimation function
window.loadWeatherAnimation = function(condition, containerId) {
    const isNight = new Date().getHours() >= 18 || new Date().getHours() < 6;
    let animationPath = '';

    switch (condition.toLowerCase()) {
        case 'clear':
            animationPath = isNight ? '/weather/sunnynight.json' : '/weather/sunnydaylight.json';
            break;
        case 'cloudy':
            animationPath = isNight ? '/weather/cloudnight.json' : '/weather/cloudydaylight.json';
            break;
        case 'rain':
            animationPath = isNight ? '/weather/rainnight.json' : '/weather/raindaylight.json';
            break;
        case 'storm':
            animationPath = isNight ? '/weather/rainstromnight.json' : '/weather/rainstromdaylight.json';
            break;
        default:
            animationPath = isNight ? '/weather/sunnynight.json' : '/weather/sunnydaylight.json';
    }

    const container = document.getElementById(containerId);
    if (!container) {
        console.error(`Container with id '${containerId}' not found`);
        return;
    }

    // Store animation instance in container's data attribute
    const existingAnimation = container.animation;
    if (existingAnimation) {
        existingAnimation.destroy();
    }

    const animation = lottie.loadAnimation({
        container: container,
        renderer: 'svg',
        loop: true,
        autoplay: true,
        path: animationPath,
        rendererSettings: {
            preserveAspectRatio: 'xMidYMid slice',
            clearCanvas: true
        }
    });

    // Adjust these styles to control the animation size and position
    container.style.width = '100%';  // Make it slightly larger than container
    container.style.height = '100%'; // Make it slightly larger than container
    container.style.position = 'absolute';
    container.style.top = '-10%';    // Adjust vertical position
    container.style.left = '-10%';   // Adjust horizontal position
    container.style.zIndex = '0';
    container.style.opacity = '0.8';  // Adjust transparency (0-1)
    
    // Optional: Add transform scale
    container.style.transform = 'scale(0.7)'; // Adjust scale factor as needed

    // Optional: Add specific SVG styling
    const svg = container.querySelector('svg');
    if (svg) {
        svg.style.width = '100%';
        svg.style.height = '100%';
        svg.style.transform = 'scale(1.2)'; // Adjust scale factor as needed
    }

    // Store animation reference
    container.animation = animation;

    return animation;
};

let touchStartX = 0;
let touchEndX = 0;

document.addEventListener('touchstart', e => {
    touchStartX = e.changedTouches[0].screenX;
});

document.addEventListener('touchend', e => {
    touchEndX = e.changedTouches[0].screenX;
    handleSwipe();
});

function handleSwipe() {
    const swipeDistance = touchEndX - touchStartX;
    if (Math.abs(swipeDistance) > 100) { // Min swipe distance
        if (swipeDistance > 0) {
            // Swipe right - open menu
            if (navbar.classList.contains('hidden')) {
                toggleSidebar();
            }
        } else {
            // Swipe left - close menu
            if (!navbar.classList.contains('hidden')) {
                hideSidebar();
            }
        }
    }
}

const Sidebar = {
    timer: null,
    HIDE_DELAY: 1000,
    
    init(elements) {
        this.elements = elements;
        this.bindEvents();
        this.handleResize();
    },
    
    bindEvents() {
        const { mobileMenuButton, overlay, $navbar, $main } = this.elements;
        
        // Mobile menu button click
        if (mobileMenuButton) {
            mobileMenuButton.addEventListener('click', () => this.toggle());
        }
        
        // Overlay click
        if (overlay) {
            overlay.addEventListener('click', () => this.hide());
        }
        
        // Desktop hover events
        if (window.innerWidth > 768) {
            $navbar.on('mouseenter', () => {
                clearTimeout(this.timer);
                $navbar.removeClass('collapsed');
                $main.removeClass('sidebar-collapsed');
            });

            $navbar.on('mouseleave', () => this.startTimer());
        }
        
        // Window resize event
        window.addEventListener('resize', () => this.handleResize());
    },
    
    hide() {
        const { navbar, overlay, mobileMenuButton } = this.elements;
        navbar.classList.add('hidden');
        overlay.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
        document.querySelector('main').classList.remove('sidebar-open');
        
        if (mobileMenuButton) {
            mobileMenuButton.classList.remove('menu-open');
        }
    },
    
    toggle() {
        const { navbar, overlay } = this.elements;
        navbar.classList.toggle('hidden');
        overlay.classList.toggle('hidden');
        document.body.classList.toggle('overflow-hidden');
        document.querySelector('main').classList.toggle('sidebar-open');
        
        if (this.elements.mobileMenuButton) {
            this.elements.mobileMenuButton.classList.toggle('menu-open');
        }
    },
    
    handleResize() {
        const { navbar, overlay, $navbar, $main } = this.elements;
        
        if (navbar) {
            if (window.innerWidth > 768) {
                navbar.classList.remove('hidden');
                overlay.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
                
                // Setup hover behavior for desktop
                $navbar.on('mouseenter mouseleave');
                this.startTimer();
            } else {
                $navbar.off('mouseenter mouseleave');
                clearTimeout(this.timer);
                navbar.classList.add('hidden');
                overlay.classList.add('hidden');
                $navbar.removeClass('collapsed');
                $main.removeClass('sidebar-collapsed');
            }
        }
    },
    
    startTimer() {
        const { $navbar, $main } = this.elements;
        clearTimeout(this.timer);
        this.timer = setTimeout(() => {
            if (!$navbar.is(':hover')) {
                $navbar.addClass('collapsed');
                $main.addClass('sidebar-collapsed');
            }
        }, this.HIDE_DELAY);
    }
};