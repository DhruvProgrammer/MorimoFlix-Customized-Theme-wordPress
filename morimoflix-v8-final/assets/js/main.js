/**
 * MorimoFlix Theme - Main JavaScript
 * 
 * @package MorimoFlix
 * @version 5.2.0
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

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeMenuFallback();
        }
    });

    // Close menu on resize
    window.addEventListener('resize', function() {
        if (window.innerWidth > 1024) {
            closeMenuFallback();
        }
    });

    // Console branding
    console.log(
        '%c MORIMOFLIX %c v5.2.0 ',
        'background: #ff3131; color: #fff; font-weight: bold; padding: 4px 8px;',
        'background: #131313; color: #00dce6; padding: 4px 8px;'
    );
})();
