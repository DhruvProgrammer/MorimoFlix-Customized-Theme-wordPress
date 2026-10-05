<!-- Floating Telegram Button — Pantone 279 C #418FDE → #229ED9 -->
<a href="https://t.me/MorimoFlix_Zone" target="_blank" rel="noopener noreferrer"
   style="bottom:calc(1rem + env(safe-area-inset-bottom));right:max(1rem,calc((100vw - 1440px)/2 + 1.5rem));"
   class="fixed md:bottom-6 z-50 flex items-center gap-2.5 bg-[#15151B]/90 backdrop-blur-md text-white p-3 md:px-4 md:py-2.5 rounded-full shadow-[0_4px_16px_rgba(0,0,0,0.5)] hover:-translate-y-0.5 hover:border-white/30 hover:shadow-[0_6px_20px_rgba(0,0,0,0.6)] transition-all duration-300 border border-white/15 group"
   title="Join our Telegram" aria-label="Join our Telegram">
    <svg class="w-6 h-6 fill-white group-hover:scale-110 transition-transform" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.68 1.03-.58.05-1.02-.38-1.58-.75-.88-.58-1.38-.94-2.23-1.5-.99-.65-.35-1.01.22-1.59.15-.15 2.71-2.48 2.76-2.69a.2.2 0 00-.05-.18c-.06-.05-.14-.03-.21-.02-.09.02-1.49.95-4.22 2.79-.4.27-.76.41-1.08.4-.36-.01-1.04-.2-1.55-.37-.63-.2-1.12-.31-1.08-.66.02-.18.27-.36.74-.55 2.92-1.27 4.86-2.11 5.83-2.51 2.78-1.16 3.35-1.36 3.73-1.36.08 0 .27.02.39.12.1.08.13.19.14.27-.01.06.01.24 0 .38z"/>
    </svg>
    <span class="font-label-caps text-xs font-bold tracking-wider uppercase hidden md:inline">Join Telegram</span>
</a>

<!-- Footer — Premium Cinema -->
<footer class="bg-[#0A0505] py-12 border-t border-[#534343]/20 mt-auto">
    <div class="max-w-container-max mx-auto px-5 md:px-margin-desktop">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 md:gap-12">
            <div class="col-span-2 md:col-span-1 glass-panel bg-white/[0.03] backdrop-blur-md border border-white/10 rounded-xl p-4">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="font-display-hero text-xl font-extrabold tracking-tighter text-[#FFB4AB]">FlixGarden</a>
                <p class="text-muted font-body-md text-sm mt-3 leading-relaxed">Premium cinema experience — curated HD movies, request-a-movie, Telegram updates.</p>
            </div>
            <div class="glass-panel bg-white/[0.03] backdrop-blur-md border border-white/10 rounded-xl p-4">
                <h2 class="font-label-caps text-label-caps text-white uppercase tracking-widest mb-4">Explore</h2>
                <div class="flex flex-col gap-2">
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="text-muted hover:text-[#FFB4AB] font-body-md text-sm transition-colors">Home</a>
                    <a href="<?php echo esc_url(home_url('/list/')); ?>" class="text-muted hover:text-[#FFB4AB] font-body-md text-sm transition-colors">A–Z Library</a>
                    <a href="<?php echo esc_url(get_post_type_archive_link('movie')); ?>" class="text-muted hover:text-[#FFB4AB] font-body-md text-sm transition-colors">Movies</a>
                    <a href="<?php echo esc_url(home_url('/blog/')); ?>" class="text-muted hover:text-[#FFB4AB] font-body-md text-sm transition-colors">Blog</a>
                    <a href="<?php echo esc_url(home_url('/about/')); ?>" class="text-muted hover:text-[#FFB4AB] font-body-md text-sm transition-colors">About Us</a>
                    <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="text-muted hover:text-[#FFB4AB] font-body-md text-sm transition-colors">Contact</a>
                    <a href="<?php echo esc_url(home_url('/editorial-policy/')); ?>" class="text-muted hover:text-[#FFB4AB] font-body-md text-sm transition-colors">Editorial Policy</a>
                    <a href="<?php echo esc_url(home_url('/privacy-policy/')); ?>" class="text-muted hover:text-[#FFB4AB] font-body-md text-sm transition-colors">Privacy Policy</a>
                    <a href="<?php echo esc_url(home_url('/dmca/')); ?>" class="text-muted hover:text-[#FFB4AB] font-body-md text-sm transition-colors">DMCA</a>
                </div>
            </div>
            <div class="glass-panel bg-white/[0.03] backdrop-blur-md border border-white/10 rounded-xl p-4">
                <h2 class="font-label-caps text-label-caps text-white uppercase tracking-widest mb-4">Genres</h2>
                <div class="flex flex-col gap-2">
                    <?php
                    // SAFE perf: cache footer genre name+URL pairs 12h (invalidated on edited_term/delete_term for genre).
                    $morimoflix_footer_genres = get_transient( 'morimoflix_footer_genres' );
                    if ( false === $morimoflix_footer_genres ) {
                        $morimoflix_footer_genres = array();
                        $footer_genres = get_terms( array( 'taxonomy' => 'genre', 'hide_empty' => false, 'number' => 8, 'orderby' => 'count', 'order' => 'DESC' ) );
                        if ( ! is_wp_error( $footer_genres ) && ! empty( $footer_genres ) ) {
                            foreach ( $footer_genres as $fg ) {
                                $fg_link = get_term_link( $fg );
                                if ( is_wp_error( $fg_link ) ) continue;
                                $morimoflix_footer_genres[] = array( 'name' => $fg->name, 'url' => $fg_link );
                            }
                        }
                        set_transient( 'morimoflix_footer_genres', $morimoflix_footer_genres, 12 * HOUR_IN_SECONDS );
                    }
                    if ( ! empty( $morimoflix_footer_genres ) ) : foreach ( $morimoflix_footer_genres as $fg ) : ?>
                    <a href="<?php echo esc_url( $fg['url'] ); ?>" class="text-muted hover:text-[#FFB4AB] font-body-md text-sm transition-colors"><?php echo esc_html( $fg['name'] ); ?> Movies</a>
                    <?php endforeach; endif; ?>
                </div>
            </div>
            <div class="glass-panel bg-white/[0.03] backdrop-blur-md border border-white/10 rounded-xl p-4">
                <h2 class="font-label-caps text-label-caps text-white uppercase tracking-widest mb-4">Connect</h2>
                <a href="https://t.me/MorimoFlix_Zone" target="_blank" rel="noopener" class="btn-primary rounded-full shadow-[0_0_20px_rgba(255,0,0,0.6)] hover:shadow-[0_0_30px_rgba(255,0,0,0.8)]">
                    <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">send</span> Telegram
                </a>
            </div>
        </div>
        <div class="mt-8 pt-6 border-t border-[#534343]/20 text-center">
            <p class="font-body-md text-xs text-muted tracking-wide">
                Copyright &copy; <?php echo date( 'Y' ); ?> FlixGarden — Premium Cinema · All Rights Reserved
            </p>
        </div>
    </div>
</footer>

<script>
// Mobile menu — SINGLE toggle owner (see assets/js/main.js note). Do not duplicate this handler.
(function() {
var menuBtn = document.getElementById('mobileMenuBtn');
var mobileMenu = document.getElementById('mobileMenu');
var menuOverlay = document.getElementById('mobileMenuOverlay');

function closeMenu() {
    if (mobileMenu) mobileMenu.classList.remove('active');
    if (menuOverlay) menuOverlay.classList.remove('active');
    if (menuBtn) {
        menuBtn.textContent = 'menu';
        menuBtn.setAttribute('aria-expanded', 'false');
    }
    document.body.classList.remove('mobile-menu-open');
}

function openMenu() {
    if (mobileMenu) mobileMenu.classList.add('active');
    if (menuOverlay) menuOverlay.classList.add('active');
    if (menuBtn) {
        menuBtn.textContent = 'close';
        menuBtn.setAttribute('aria-expanded', 'true');
    }
    document.body.classList.add('mobile-menu-open');
    // Close search if open
    if (window.morimoflixCloseSearch) window.morimoflixCloseSearch();
}

if (menuBtn && mobileMenu) {
    menuBtn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        if (mobileMenu.classList.contains('active')) {
            closeMenu();
        } else {
            openMenu();
        }
    });
    // Close menu when a link inside it is tapped
    mobileMenu.addEventListener('click', function(e) {
        if (e.target.closest('a')) closeMenu();
    });
}

if (menuOverlay) {
    menuOverlay.addEventListener('click', closeMenu);
}
window.morimoflixCloseMenu = closeMenu;
})();

// Mobile search
var searchBtn = document.getElementById('mobileSearchBtn');
var searchOverlay = document.getElementById('mobileSearchOverlay');
var searchClose = document.getElementById('mobileSearchClose');

function openSearch() {
    if (!searchOverlay) return;
    searchOverlay.classList.add('active');
    if (searchBtn) searchBtn.setAttribute('aria-expanded', 'true');
    // Lock page scroll (body.search-open) — the overlay is fixed + not scrollable, so
    // without this the page scrolls behind it.
    document.body.classList.add('search-open');
    if (window.morimoflixCloseMenu) window.morimoflixCloseMenu();
    // Focus SYNCHRONOUSLY, inside the click handler. A setTimeout() here severs the user
    // gesture and iOS Safari then refuses to raise the software keyboard, forcing a
    // second tap on the field.
    var input = searchOverlay.querySelector('input[type="search"]');
    if (input) input.focus();
}
function closeSearch() {
    if (!searchOverlay) return;
    searchOverlay.classList.remove('active');
    if (searchBtn) searchBtn.setAttribute('aria-expanded', 'false');
    document.body.classList.remove('search-open');
}
window.morimoflixCloseSearch = closeSearch;

if (searchBtn && searchOverlay) {
    searchBtn.addEventListener('click', openSearch);
}

if (searchClose && searchOverlay) {
    searchClose.addEventListener('click', function() {
        closeSearch();
        if (searchBtn) searchBtn.focus();
    });
}

// Close on Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeSearch();
        if (window.morimoflixCloseMenu) window.morimoflixCloseMenu();
    }
});
</script>

<!-- Welcome Popup -->
<div id="welcomePopup" class="fixed inset-0 z-[400] hidden">
    <div class="absolute inset-0 bg-black/85 backdrop-blur-sm" id="welcomePopupOverlay"></div>
    <div class="relative flex items-center justify-center min-h-screen p-4">
        <div class="glass-strong relative w-full max-w-sm mx-2 bg-[#161616]/70 backdrop-blur-xl border border-primary/20 rounded-lg shadow-[0_0_30px_rgba(229,9,20,0.25)]">
            <button type="button" id="welcomePopupClose" class="absolute top-3 right-3 text-muted hover:text-white transition-colors p-1 z-10">
                <span class="material-symbols-outlined text-xl">close</span>
            </button>
            <div class="flex flex-col items-center text-center p-6 pt-8 space-y-5">
                <span class="material-symbols-outlined text-primary text-5xl" style="font-variation-settings: 'FILL' 1;">movie</span>
                <h2 class="text-xl md:text-2xl font-bold text-white">Can't find your movie?</h2>
                <p class="text-sm text-on-surface-variant">Request it and we'll add it for you!</p>
                <div class="flex flex-col w-full gap-3 mt-2">
                    <button type="button" id="welcomePopupRequest" class="btn-primary w-full uppercase tracking-wider text-sm">
                        Request Now
                    </button>
                    <button type="button" id="welcomePopupNoThanks" class="w-full bg-transparent text-muted hover:text-white font-medium text-sm py-2 transition-colors">
                        No Thanks
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    var dismissedAt = null;
    try { dismissedAt = localStorage.getItem('morimoflix_welcome_dismissed_at'); } catch (e) {}
    if (dismissedAt && (Date.now() - parseInt(dismissedAt, 10)) < 600000) return;
    var popup = document.getElementById('welcomePopup');
    if (!popup) return;
    setTimeout(function() {
        popup.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }, 1600);

    function closePopup() {
        popup.classList.add('hidden');
        document.body.style.overflow = '';
        try { localStorage.setItem('morimoflix_welcome_dismissed_at', Date.now().toString()); } catch (e) {}
    }

    document.getElementById('welcomePopupClose').addEventListener('click', closePopup);
    document.getElementById('welcomePopupOverlay').addEventListener('click', closePopup);
    document.getElementById('welcomePopupNoThanks').addEventListener('click', closePopup);
    document.getElementById('welcomePopupRequest').addEventListener('click', function() {
        closePopup();
        if (window.morimoflixOpenRequest) { window.morimoflixOpenRequest(); return; }
        var modal = document.getElementById('movieRequestModal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.removeAttribute('hidden');
            modal.setAttribute('aria-hidden', 'false');
            document.body.classList.add('movie-request-modal-active');
        }
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !popup.classList.contains('hidden')) closePopup();
    });
})();
</script>

<!-- Motion (standalone Framer Motion) — SAFE: only { animate } used (scroll/stagger unused, verified). -->
<script type="module">
  if (!window.matchMedia || !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    import("https://cdn.jsdelivr.net/npm/motion@11.11.13/+esm").then(function(m) { window.MotionAnimate = m.animate; }).catch(function() {});
  }
</script>

<?php wp_footer(); ?>
</body>
</html>
