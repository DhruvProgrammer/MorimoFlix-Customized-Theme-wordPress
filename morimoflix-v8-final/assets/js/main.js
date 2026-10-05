/**
 * MorimoFlix Theme - Main JavaScript
 * 
 * @package MorimoFlix
 * @version 5.5.0
 */

(function() {
    'use strict';

    // NOTE: Mobile menu open/close toggle lives ONLY in footer.php inline script.
    // A duplicate toggle here cancelled it out (two handlers = open then instant close),
    // which is why the hamburger appeared dead. This file keeps idempotent helpers only.
    function closeMenuFallback() {
        var mobileMenu = document.getElementById('mobileMenu');
        var menuOverlay = document.getElementById('mobileMenuOverlay');
        var menuBtn = document.getElementById('mobileMenuBtn');
        if (mobileMenu) mobileMenu.classList.remove('active');
        if (menuOverlay) menuOverlay.classList.remove('active');
        if (menuBtn) {
            menuBtn.textContent = 'menu';
            menuBtn.setAttribute('aria-expanded', 'false');
        }
        document.body.classList.remove('mobile-menu-open');
    }

    // The search overlay is position:fixed at z-index 200, above the navbar. Rotating a
    // tablet to landscape (>=1024px, where `lg` kicks in) used to leave it open, blocking
    // the whole navbar with no trigger left to close it (#mobileSearchBtn is display:none
    // at that width). Delegate to footer.php's owner rather than duplicating the state
    // transitions here — two sources of truth is how the hamburger dead-toggle bug
    // documented at the top of this file happened.
    function closeSearchFallback() {
        if (window.morimoflixCloseSearch) {
            window.morimoflixCloseSearch();
            return;
        }
        var searchOverlay = document.getElementById('mobileSearchOverlay');
        var searchBtn = document.getElementById('mobileSearchBtn');
        if (searchOverlay) searchOverlay.classList.remove('active');
        if (searchBtn) searchBtn.setAttribute('aria-expanded', 'false');
        document.body.classList.remove('search-open');
    }

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeMenuFallback();
            closeSearchFallback();
        }
    });

    // Close menu on resize
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 1024) {
            closeMenuFallback();
            closeSearchFallback();
        }
    });

    // Console branding
    console.log(
        '%c FLIXGARDEN %c v5.5.0 ',
        'background: #ff3131; color: #fff; font-weight: bold; padding: 4px 8px;',
        'background: #131313; color: #00dce6; padding: 4px 8px;'
    );
})();
