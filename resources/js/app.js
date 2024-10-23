// @ts-ignore
import $ from 'jquery';
window.$ = $;
window.jQuery = $;

$(function() {
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
