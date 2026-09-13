<!-- Floating Telegram Button — Pantone 279 C #418FDE → #229ED9 -->
<a href="https://t.me/MorimoFlix_Zone" target="_blank" rel="noopener noreferrer" 
   class="fixed bottom-4 right-4 md:bottom-6 md:right-6 z-50 flex items-center gap-3 bg-gradient-to-r from-red-600 to-red-800 text-white p-3.5 md:px-5 md:py-3 rounded-full shadow-[0_0_20px_rgba(255,0,0,0.6)] hover:shadow-[0_0_30px_rgba(255,0,0,0.8)] hover:scale-105 hover:-translate-y-1 transition-all duration-300 border border-white/25 group"
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
                <a href="<?php echo esc_url(home_url('/')); ?>" class="font-display-hero text-xl font-extrabold tracking-tighter text-[#FFB4AB]">MorimoFlix</a>
                <p class="text-[#D8C2C2] font-body-md text-sm mt-3 leading-relaxed">Premium cinema experience — curated HD movies, request-a-movie, Telegram updates.</p>
            </div>
            <div class="glass-panel bg-white/[0.03] backdrop-blur-md border border-white/10 rounded-xl p-4">
                <h4 class="font-label-caps text-label-caps text-[#FFEAE9] uppercase tracking-widest mb-4">Explore</h4>
                <div class="flex flex-col gap-2">
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="text-[#A0A0AA] hover:text-[#FFB4AB] font-body-md text-sm transition-colors">Home</a>
                    <a href="<?php echo esc_url(home_url('/list/')); ?>" class="text-[#A0A0AA] hover:text-[#FFB4AB] font-body-md text-sm transition-colors">A–Z Library</a>
                    <a href="<?php echo esc_url(get_post_type_archive_link('movie')); ?>" class="text-[#A0A0AA] hover:text-[#FFB4AB] font-body-md text-sm transition-colors">Movies</a>
                </div>
            </div>
            <div class="glass-panel bg-white/[0.03] backdrop-blur-md border border-white/10 rounded-xl p-4">
                <h4 class="font-label-caps text-label-caps text-[#FFEAE9] uppercase tracking-widest mb-4">Genres</h4>
                <div class="flex flex-col gap-2">
                    <?php $footer_genres = get_terms( array( 'taxonomy' => 'genre', 'hide_empty' => false, 'number' => 8, 'orderby' => 'count', 'order' => 'DESC' ) ); if ( ! is_wp_error( $footer_genres ) && ! empty( $footer_genres ) ) : foreach ( $footer_genres as $fg ) : ?>
                    <a href="<?php echo esc_url( get_term_link( $fg ) ); ?>" class="text-[#A0A0AA] hover:text-[#FFB4AB] font-body-md text-sm transition-colors"><?php echo esc_html( $fg->name ); ?> Movies</a>
                    <?php endforeach; endif; ?>
                </div>
            </div>
            <div class="glass-panel bg-white/[0.03] backdrop-blur-md border border-white/10 rounded-xl p-4">
                <h4 class="font-label-caps text-label-caps text-[#FFEAE9] uppercase tracking-widest mb-4">Connect</h4>
                <a href="https://t.me/MorimoFlix_Zone" target="_blank" rel="noopener" class="inline-flex items-center gap-2 bg-gradient-to-r from-red-600 to-red-800 text-white px-6 py-3 rounded-full font-bold shadow-[0_0_20px_rgba(255,0,0,0.6)] hover:shadow-[0_0_30px_rgba(255,0,0,0.8)] transition-all">
                    <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">send</span> Telegram
                </a>
            </div>
        </div>
        <div class="mt-8 pt-6 border-t border-[#534343]/20 text-center">
            <p class="font-label-caps text-[11px] text-[#6B6B75] tracking-wider">
                Copyright &copy; <?php echo date( 'Y' ); ?> MorimoFlix — Premium Cinema · Pantone 186 C Crimson · All Rights Reserved
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
    var searchOverlay = document.getElementById('mobileSearchOverlay');
    if (searchOverlay) searchOverlay.classList.remove('active');
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

if (searchBtn && searchOverlay) {
    searchBtn.addEventListener('click', function() {
        searchOverlay.classList.add('active');
        if (window.morimoflixCloseMenu) window.morimoflixCloseMenu();
        // Focus input
        var input = searchOverlay.querySelector('input[type="search"]');
        if (input) setTimeout(function() { input.focus(); }, 100);
    });
}

if (searchClose && searchOverlay) {
    searchClose.addEventListener('click', function() {
        searchOverlay.classList.remove('active');
    });
}

// Close on Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        if (searchOverlay) searchOverlay.classList.remove('active');
        if (window.morimoflixCloseMenu) window.morimoflixCloseMenu();
    }
});
</script>

<!-- Welcome Popup -->
<div id="welcomePopup" class="fixed inset-0 z-[400] hidden">
    <div class="absolute inset-0 bg-black/85 backdrop-blur-sm" id="welcomePopupOverlay"></div>
    <div class="relative flex items-center justify-center min-h-screen p-4">
        <div class="glass-strong relative w-full max-w-sm mx-2 bg-[#161616]/70 backdrop-blur-xl border border-primary/20 rounded-lg shadow-[0_0_30px_rgba(0,220,230,0.15)]">
            <button type="button" id="welcomePopupClose" class="absolute top-3 right-3 text-neutral-400 hover:text-white transition-colors p-1 z-10">
                <span class="material-symbols-outlined text-xl">close</span>
            </button>
            <div class="flex flex-col items-center text-center p-6 pt-8 space-y-5">
                <span class="material-symbols-outlined text-primary text-5xl" style="font-variation-settings: 'FILL' 1;">movie</span>
                <h3 class="text-xl md:text-2xl font-bold text-white">Can't find your movie?</h3>
                <p class="text-sm text-on-surface-variant">Request it and we'll add it for you!</p>
                <div class="flex flex-col w-full gap-3 mt-2">
                    <button type="button" id="welcomePopupRequest" class="w-full bg-gradient-to-r from-orange-500 to-pink-500 text-white font-bold uppercase tracking-wider py-3 px-6 rounded hover:opacity-90 transition-all text-sm">
                        Request Now
                    </button>
                    <button type="button" id="welcomePopupNoThanks" class="w-full bg-transparent text-neutral-400 hover:text-white font-medium text-sm py-2 transition-colors">
                        No Thanks
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    var dismissedAt = localStorage.getItem('morimoflix_welcome_dismissed_at');
    if (dismissedAt && (Date.now() - parseInt(dismissedAt, 10)) < 600000) return;
    var popup = document.getElementById('welcomePopup');
    if (!popup) return;
    popup.classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    function closePopup() {
        popup.classList.add('hidden');
        document.body.style.overflow = '';
        localStorage.setItem('morimoflix_welcome_dismissed_at', Date.now().toString());
    }

    document.getElementById('welcomePopupClose').addEventListener('click', closePopup);
    document.getElementById('welcomePopupOverlay').addEventListener('click', closePopup);
    document.getElementById('welcomePopupNoThanks').addEventListener('click', closePopup);
    document.getElementById('welcomePopupRequest').addEventListener('click', function() {
        closePopup();
        var modal = document.getElementById('movieRequestModal');
        if (modal) {
            modal.classList.remove('hidden');
            document.body.classList.add('movie-request-modal-active');
        }
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !popup.classList.contains('hidden')) closePopup();
    });
})();
</script>

<!-- Motion (standalone Framer Motion) -->
<script type="module">
  import { animate, scroll, stagger } from "https://cdn.jsdelivr.net/npm/motion@11.11.13/+esm"
  window.MotionAnimate = animate
  window.MotionScroll = scroll
  window.MotionStagger = stagger
</script>

<?php wp_footer(); ?>
</body>
</html>
