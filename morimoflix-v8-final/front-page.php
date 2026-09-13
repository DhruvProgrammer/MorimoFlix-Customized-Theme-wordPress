<?php
/**
 * Front Page Template
 *
 * WordPress loads this BEFORE checking main query pagination,
 * which prevents 404 on ?paged=2 for custom WP_Query.
 *
 * @package MorimoFlix
 */

get_header();
?>

<?php
$current_type = isset( $_GET['type'] ) ? sanitize_key( $_GET['type'] ) : 'all';
if ( ! in_array( $current_type, array( 'all', 'movies', 'series' ), true ) ) {
    $current_type = 'all';
}

$paged = isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1;
$posts_per_page = 18;

$query_args = array(
    'posts_per_page' => $posts_per_page,
    'paged'          => $paged,
    'orderby'        => 'date',
    'order'          => 'DESC',
);

if ( $current_type === 'movies' ) {
    $query_args['post_type'] = 'movie';
} elseif ( $current_type === 'series' ) {
    $query_args['post_type'] = 'tv';
} else {
    $query_args['post_type'] = array( 'movie', 'tv' );
}

$main_query = new WP_Query( $query_args );
?>

<div class="max-w-container-max mx-auto px-5 md:px-margin-desktop pt-20 md:pt-24 pb-8 md:pb-12">
    <?php
    $hero_query = new WP_Query( array(
        'post_type'      => array( 'movie', 'tv' ),
        'posts_per_page' => 5,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ) );
    ?>
    <style>
    #heroCarousel{position:relative;}
    #heroCarousel .g-backdrop{position:absolute;inset:0;overflow:hidden;border-radius:inherit;}
    #heroCarousel .g-backdrop img{width:100%;height:100%;object-fit:cover;filter:blur(28px) brightness(0.5) saturate(1.2);transform:scale(1.12);opacity:1;transition:opacity 0.6s ease;}
    #heroCarousel .g-backdrop::after{content:"";position:absolute;inset:0;background:radial-gradient(ellipse 80% 60% at 50% 0%,rgba(229,9,20,0.18),transparent 65%),linear-gradient(to bottom,rgba(10,5,5,0.55),rgba(10,5,5,0.88) 70%,#0A0505);}
    #heroCarousel .g-grid{position:relative;z-index:2;display:grid;grid-template-columns:1fr;gap:8px;height:100%;align-items:center;padding:18px 16px 34px;}
    @media (min-width:1024px){#heroCarousel .g-grid{grid-template-columns:1fr 1fr;gap:24px;padding:24px 48px 40px;}}
    #heroCarousel .g-fan-col{order:1;min-width:0;}
    #heroCarousel .g-info-col{order:2;min-width:0;}
    @media (min-width:1024px){#heroCarousel .g-fan-col{order:2;}#heroCarousel .g-info-col{order:1;}}
    #heroCarousel .g-stage{position:relative;width:100%;height:300px;perspective:1200px;overflow:hidden;background:transparent;}
    @media (min-width:768px){#heroCarousel .g-stage{height:360px;}}
    #heroCarousel .g-slide{position:absolute;left:50%;top:0;transition:transform 0.6s ease,opacity 0.6s ease,filter 0.6s ease;will-change:transform;filter:brightness(0.5) saturate(0.85);}
    #heroCarousel .g-slide.active{filter:none;}
    #heroCarousel .g-card{position:relative;width:210px;margin:20px auto 0;border-radius:14px;overflow:hidden;background:rgba(255,255,255,0.06);backdrop-filter:blur(14px);-webkit-backdrop-filter:blur(14px);border:1px solid rgba(255,255,255,0.16);box-shadow:0 18px 50px rgba(0,0,0,0.6),0 0 24px rgba(229,9,20,0.15);cursor:pointer;}
    #heroCarousel .g-slide.active .g-card{border-color:rgba(255,180,171,0.45);box-shadow:0 24px 60px rgba(0,0,0,0.65),0 0 34px rgba(229,9,20,0.35);}
    #heroCarousel .g-card .g-poster{display:block;width:100%;height:280px;object-fit:cover;-webkit-box-reflect:below 6px linear-gradient(transparent 60%,rgba(0,0,0,0.32));}
    #heroCarousel .g-card .g-noimg{display:flex;align-items:center;justify-content:center;height:280px;background:linear-gradient(135deg,#1D0E0E,#0A0505);color:rgba(255,255,255,0.25);}
    #heroCarousel .g-card .g-noimg .material-symbols-outlined{font-size:56px;}
    #heroCarousel .g-card .g-quality{position:absolute;top:8px;right:8px;z-index:2;background:rgba(255,180,171,0.2);color:#FFB4AB;border:1px solid rgba(255,180,171,0.5);backdrop-filter:blur(8px);-webkit-backdrop-filter:blur(8px);font-size:10px;letter-spacing:0.1em;padding:2px 6px;border-radius:4px;}
    #heroCarousel .g-card .g-new{position:absolute;top:8px;left:8px;z-index:2;background:#E50914;color:#fff;font-size:9px;font-weight:700;letter-spacing:0.12em;padding:3px 7px;border-radius:4px;text-transform:uppercase;box-shadow:0 2px 10px rgba(229,9,20,0.6);}
    #heroCarousel .g-reflect{height:30px;width:80%;margin:0 auto;background:linear-gradient(to bottom,rgba(255,255,255,0.13),transparent);filter:blur(2px);border-radius:0 0 50% 50%;pointer-events:none;}
    #heroCarousel .g-info-col .g-info{display:none;text-align:left;max-width:560px;}
    #heroCarousel .g-info-col .g-info.active{display:block;}
    #heroCarousel .g-info-col .g-excerpt{display:-webkit-box;line-clamp:2;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}
    #heroCarousel .g-fan-wrap{position:relative;}
    #heroCarousel .g-dots{position:absolute;left:50%;transform:translateX(-50%);bottom:-26px;display:flex;gap:2px;z-index:30;}
    #heroCarousel .g-dots button{width:26px;height:26px;display:flex;align-items:center;justify-content:center;background:transparent;border:0;cursor:pointer;padding:0;}
    #heroCarousel .g-dots button::after{content:"";width:8px;height:8px;border-radius:9999px;background:rgba(255,255,255,0.35);transition:all 0.3s ease;}
    #heroCarousel .g-dots button.active::after{width:22px;background:#E50914;box-shadow:0 0 8px rgba(229,9,20,0.8);}
    #heroCarousel .g-nav{position:absolute;top:50%;transform:translateY(-50%);z-index:30;}
    @media (max-width:640px){#heroCarousel .g-card{width:150px;margin-top:14px;}#heroCarousel .g-card .g-poster,#heroCarousel .g-card .g-noimg{height:190px;-webkit-box-reflect:unset;}#heroCarousel .g-reflect{display:none;}#heroCarousel .g-stage{height:230px;}#heroCarousel .g-grid{padding:14px 14px 40px;}#heroCarousel .g-info-col .g-info{text-align:left;}}
    @media (prefers-reduced-motion:reduce){#heroCarousel .g-slide,#heroCarousel .g-backdrop img,#heroCarousel .g-info-col .g-info,#heroCarousel .g-dots button::after{transition:none;}}
    </style>
    <?php
    if ( $hero_query->have_posts() ) :
    ?>
    <h1 class="sr-only"><?php esc_html_e( 'Watch & Download Movies in HD — Telugu, Hindi & English', 'morimoflix-flicker' ); ?></h1>
    <section id="heroCarousel" class="mb-8 md:mb-12 relative overflow-hidden min-h-[560px] lg:h-[520px] rounded-xl shadow-[0_0_50px_rgba(0,0,0,0.8)] border border-[#534343]/30 red-glow bg-[#0A0505]">
        <?php
        $hero_slides = array();
        while ( $hero_query->have_posts() ) :
            $hero_query->the_post();
            $is_first = empty( $hero_slides );
            $hero_slides[] = array(
                'permalink' => get_permalink(),
                'title'     => get_the_title(),
                'excerpt'   => wp_trim_words( get_the_excerpt(), 30 ),
                'meta'      => morimoflix_get_movie_meta(),
                'thumb'     => get_the_post_thumbnail( get_the_ID(), 'medium', array( 'class' => 'g-poster', 'alt' => morimoflix_poster_alt(), 'loading' => $is_first ? 'eager' : 'lazy', 'decoding' => 'async', 'fetchpriority' => $is_first ? 'high' : 'auto' ) ),
                'has_thumb' => has_post_thumbnail(),
                'backdrop'  => get_the_post_thumbnail_url( get_the_ID(), 'large' ),
            );
        endwhile;
        wp_reset_postdata();
        $hero_count = count( $hero_slides );
        $hero_first_backdrop = ( $hero_count > 0 && ! empty( $hero_slides[0]['backdrop'] ) ) ? $hero_slides[0]['backdrop'] : '';
        ?>
        <div class="g-backdrop" aria-hidden="true">
            <?php if ( $hero_first_backdrop ) : ?>
            <img id="gBackdropFront" src="<?php echo esc_url( $hero_first_backdrop ); ?>" alt="" aria-hidden="true" loading="eager" fetchpriority="high" decoding="async">
            <?php else : ?>
            <img id="gBackdropFront" src="" alt="" aria-hidden="true" loading="eager" fetchpriority="high" decoding="async">
            <?php endif; ?>
        </div>
        <div class="g-grid">
            <div class="g-fan-col">
                <div class="g-fan-wrap">
                    <div class="g-stage">
                        <?php foreach ( $hero_slides as $hero_i => $hero_s ) : ?>
                        <div class="g-slide<?php echo $hero_i === 0 ? ' active' : ''; ?>" data-i="<?php echo (int) $hero_i; ?>" data-permalink="<?php echo esc_url( $hero_s['permalink'] ); ?>" data-backdrop="<?php echo esc_url( $hero_s['backdrop'] ); ?>">
                            <div class="g-card glass-card">
                                <?php if ( $hero_s['has_thumb'] ) : ?>
                                    <?php echo $hero_s['thumb']; ?>
                                <?php else : ?>
                                    <div class="g-noimg"><span class="material-symbols-outlined">movie</span></div>
                                <?php endif; ?>
                                <span class="g-new"><?php esc_html_e( 'Newly Released', 'morimoflix-flicker' ); ?></span>
                                <span class="g-quality"><?php echo esc_html( $hero_s['meta']['quality'] ); ?></span>
                            </div>
                            <div class="g-reflect" aria-hidden="true"></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <button id="heroPrevBtn" type="button" aria-label="Previous slide" class="g-nav absolute left-1 md:left-2 top-1/2 -translate-y-1/2 z-30 w-11 h-11 min-w-[44px] min-h-[44px] flex items-center justify-center rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-white hover:bg-[#E50914] hover:border-[#E50914] transition-all">
                        <span class="material-symbols-outlined">chevron_left</span>
                    </button>
                    <button id="heroNextBtn" type="button" aria-label="Next slide" class="g-nav absolute right-1 md:right-2 top-1/2 -translate-y-1/2 z-30 w-11 h-11 min-w-[44px] min-h-[44px] flex items-center justify-center rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-white hover:bg-[#E50914] hover:border-[#E50914] transition-all">
                        <span class="material-symbols-outlined">chevron_right</span>
                    </button>
                    <div class="g-dots">
                        <?php for ( $d = 0; $d < $hero_count; $d++ ) : ?>
                        <button type="button" aria-label="<?php echo esc_attr( sprintf( __( 'Go to slide %d', 'morimoflix-flicker' ), $d + 1 ) ); ?>"<?php echo $d === 0 ? ' class="active"' : ''; ?>></button>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>
            <div class="g-info-col">
                <?php foreach ( $hero_slides as $hero_i => $hero_s ) : ?>
                <?php
                $hero_title = trim( (string) $hero_s['title'] );
                $hero_t1 = $hero_title;
                $hero_t2 = '';
                if ( preg_match( '/^(.*?)(\d+)$/', $hero_title, $hero_m ) ) {
                    $hero_t1 = rtrim( $hero_m[1] );
                    $hero_t2 = $hero_m[2];
                }
                ?>
                <div class="g-info<?php echo $hero_i === 0 ? ' active' : ''; ?>" data-i="<?php echo (int) $hero_i; ?>"<?php echo $hero_i === 0 ? '' : ' aria-hidden="true"'; ?>>
                    <span class="inline-block bg-[#E50914] text-white font-label-caps text-[9px] md:text-[10px] font-bold px-2.5 md:px-3 py-1 uppercase tracking-[0.2em] rounded-sm mb-3 md:mb-4"><?php esc_html_e( 'Newly Released', 'morimoflix-flicker' ); ?></span>
                    <h2 class="font-display-hero text-4xl md:text-5xl lg:text-6xl text-[#FFEAE9] mb-3 md:mb-4 leading-[1.05] tracking-tighter font-extrabold text-shadow-[0_0_30px_rgba(255,0,0,0.3)]"<?php echo $hero_i === 0 ? '' : ' aria-hidden="true"'; ?>><a href="<?php echo esc_url( $hero_s['permalink'] ); ?>"<?php echo $hero_i === 0 ? '' : ' tabindex="-1"'; ?> class="hover:text-[#FFB4AB] transition-colors"><?php echo esc_html( $hero_t1 ); ?><?php if ( $hero_t2 !== '' ) : ?> <span class="text-[#E50914]"><?php echo esc_html( $hero_t2 ); ?></span><?php endif; ?></a></h2>
                    <div class="flex items-center flex-wrap gap-x-3 gap-y-1 text-metadata font-metadata text-[#FFEAE9] mb-3 md:mb-4">
                        <span class="text-[#F5C518] flex items-center gap-1"><span class="material-symbols-outlined text-[16px]" style="font-variation-settings: 'FILL' 1;">star</span> <?php echo esc_html( $hero_s['meta']['rating'] ); ?></span>
                        <span class="text-[#D8C2C2]"><?php echo esc_html( $hero_s['meta']['year'] ); ?></span>
                        <span class="text-[#D8C2C2] flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">schedule</span> <?php echo esc_html( $hero_s['meta']['duration'] ); ?></span>
                        <span class="bg-[#FFB4AB]/20 text-[#FFB4AB] border border-[#FFB4AB]/50 font-label-caps text-label-caps px-1.5 py-0.5 rounded-sm"><?php echo esc_html( $hero_s['meta']['quality'] ); ?></span>
                    </div>
                    <p class="g-excerpt line-clamp-2 text-[#D8C2C2] font-body-md text-body-lg mb-6 md:mb-8 max-w-xl leading-relaxed"><?php echo esc_html( $hero_s['excerpt'] ); ?></p>
                    <div class="flex flex-col sm:flex-row gap-2.5 md:gap-4">
                        <a href="<?php echo esc_url( $hero_s['permalink'] ); ?>"<?php echo $hero_i === 0 ? '' : ' tabindex="-1"'; ?> class="bg-gradient-to-r from-red-600 to-red-800 text-white px-5 py-3.5 md:px-8 md:py-4 rounded-lg font-headline-md text-sm md:text-body-lg hover:brightness-110 transition-all shadow-[0_0_20px_rgba(255,0,0,0.6)] hover:shadow-[0_0_30px_rgba(255,0,0,0.8)] inline-flex items-center justify-center gap-2 min-h-[48px]">
                            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">play_arrow</span> <?php esc_html_e( 'Watch Now', 'morimoflix-flicker' ); ?>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php else : ?>
    <h1 class="sr-only"><?php esc_html_e( 'Watch & Download Movies in HD — Telugu, Hindi & English', 'morimoflix-flicker' ); ?></h1>
    <section id="heroCarousel" class="mb-12 md:mb-20 mt-16 md:mt-20 relative overflow-hidden rounded-xl shadow-[0_0_50px_rgba(0,0,0,0.8)] border border-white/5 bg-[#0A0505]">
        <div class="g-backdrop" aria-hidden="true"></div>
        <div class="g-grid">
            <div class="g-fan-col">
                <div class="g-fan-wrap">
                    <div class="g-stage">
                        <div class="g-slide active" data-i="0" data-permalink="" data-backdrop="">
                            <div class="g-card glass-card">
                                <div class="g-noimg"><span class="material-symbols-outlined">movie</span></div>
                            </div>
                            <div class="g-reflect" aria-hidden="true"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="g-info-col">
                <div class="g-info active" data-i="0">
                    <span class="inline-block bg-[#E50914] text-white font-label-caps text-[9px] md:text-[10px] font-bold px-2.5 md:px-3 py-1 uppercase tracking-[0.2em] rounded-sm mb-3 md:mb-4"><?php esc_html_e( 'Newly Released', 'morimoflix-flicker' ); ?></span>
                    <h2 class="font-display-hero text-4xl md:text-5xl text-[#FFEAE9] mb-3 md:mb-4 leading-[1.05] tracking-tighter font-extrabold">Synthetic Soul</h2>
                    <p class="g-excerpt line-clamp-2 text-[#D8C2C2] font-body-md text-body-lg mb-6 md:mb-8 max-w-xl leading-relaxed">In a world where memories can be manufactured, one synth discovers a glitch that reveals the true origin of its consciousness.</p>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var section = document.getElementById('heroCarousel');
    if (!section) return;
    var slides = section.querySelectorAll('.g-slide');
    if (!slides || slides.length === 0) return;
    var n = slides.length;
    var prevBtn = document.getElementById('heroPrevBtn');
    var nextBtn = document.getElementById('heroNextBtn');
    var dots = section.querySelectorAll('.g-dots button');
    var infos = section.querySelectorAll('.g-info-col .g-info');
    var backdropImg = document.getElementById('gBackdropFront');
    var current = 0;
    var timer = null;
    var touchX = null;
    var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    function spacing() {
        var card = section.querySelector('.g-card');
        var cw = (card && card.offsetWidth) ? card.offsetWidth : 210;
        return Math.round(cw * 0.72);
    }
    function norm(i) { return ((i % n) + n) % n; }
    function rel(i) {
        var d = i - current;
        if (d > n / 2) d -= n;
        if (d < -n / 2) d += n;
        return d;
    }
    function layout() {
        var sp = spacing();
        for (var k = 0; k < n; k++) {
            var s = slides[k];
            if (!s) continue;
            var off = rel(k);
            var a = Math.abs(off);
            var x = off * sp;
            var z = a === 0 ? 0 : (a === 1 ? -180 : -340);
            var r = off === 0 ? 0 : (off > 0 ? -42 : 42);
            var sc = a === 0 ? 1 : (a === 1 ? 0.86 : 0.72);
            s.style.transform = 'translateX(-50%) translateX(' + x + 'px) rotateY(' + r + 'deg) translateZ(' + z + 'px) scale(' + sc + ')';
            s.style.zIndex = String(10 - a);
            if (a > 2) { s.style.opacity = '0'; s.style.visibility = 'hidden'; }
            else { s.style.opacity = a === 0 ? '1' : '0.9'; s.style.visibility = 'visible'; }
        }
    }
    function setBackdrop(url) {
        if (!backdropImg || !url) return;
        if (backdropImg.getAttribute('src') === url) return;
        var pre = new Image();
        pre.onload = function() {
            if (reduceMotion) { backdropImg.setAttribute('src', url); return; }
            backdropImg.style.opacity = '0';
            setTimeout(function() {
                backdropImg.setAttribute('src', url);
                backdropImg.style.opacity = '1';
            }, 150);
        };
        pre.onerror = function() {};
        pre.src = url;
    }
    function go(i) {
        current = norm(i);
        for (var k = 0; k < n; k++) {
            var s = slides[k];
            if (!s) continue;
            var on = (k === current);
            if (on) s.classList.add('active');
            else s.classList.remove('active');
        }
        if (infos && infos.length) {
            for (var m = 0; m < infos.length; m++) {
                var info = infos[m];
                if (!info) continue;
                var onInfo = (m === current);
                if (onInfo) { info.classList.add('active'); info.removeAttribute('aria-hidden'); }
                else { info.classList.remove('active'); info.setAttribute('aria-hidden', 'true'); }
                var h2 = info.querySelector('h2');
                if (h2) {
                    if (onInfo) h2.removeAttribute('aria-hidden');
                    else h2.setAttribute('aria-hidden', 'true');
                }
                var links = info.querySelectorAll('a');
                for (var j = 0; j < links.length; j++) {
                    if (!links[j]) continue;
                    if (onInfo) links[j].removeAttribute('tabindex');
                    else links[j].setAttribute('tabindex', '-1');
                }
            }
        }
        if (dots && dots.length) {
            for (var q = 0; q < dots.length; q++) {
                if (!dots[q]) continue;
                if (q === current) { dots[q].classList.add('active'); dots[q].setAttribute('aria-current', 'true'); }
                else { dots[q].classList.remove('active'); dots[q].removeAttribute('aria-current'); }
            }
        }
        if (slides[current]) {
            var bd = slides[current].getAttribute('data-backdrop');
            if (bd) setBackdrop(bd);
        }
        layout();
    }
    function stop() { if (timer) { clearInterval(timer); timer = null; } }
    function start() {
        if (reduceMotion || n <= 1) return;
        stop();
        timer = setInterval(function() { if (!document.hidden) go(current + 1); }, 5000);
    }

    if (prevBtn) prevBtn.addEventListener('click', function(e) { if (e) e.preventDefault(); stop(); go(current - 1); start(); });
    if (nextBtn) nextBtn.addEventListener('click', function(e) { if (e) e.preventDefault(); stop(); go(current + 1); start(); });
    if (dots && dots.length) {
        for (var d = 0; d < dots.length; d++) {
            (function(idx) {
                if (dots[idx]) dots[idx].addEventListener('click', function() { stop(); go(idx); start(); });
            })(d);
        }
    }
    for (var k = 0; k < n; k++) {
        (function(idx) {
            if (slides[idx]) slides[idx].addEventListener('click', function(e) {
                if (e && e.target && e.target.closest && e.target.closest('a,button')) return;
                if (idx === current) {
                    var link = slides[idx].getAttribute('data-permalink');
                    if (link) window.location.href = link;
                } else { stop(); go(idx); start(); }
            });
        })(k);
    }
    section.addEventListener('pointerenter', stop);
    section.addEventListener('pointerleave', start);
    section.addEventListener('touchstart', function(e) { stop(); if (e && e.touches && e.touches[0]) touchX = e.touches[0].clientX; }, { passive: true });
    section.addEventListener('touchend', function(e) {
        var dx = 0;
        if (e && e.changedTouches && e.changedTouches[0] && touchX !== null) dx = e.changedTouches[0].clientX - touchX;
        touchX = null;
        if (Math.abs(dx) > 40) go(current + (dx < 0 ? 1 : -1));
        start();
    }, { passive: true });
    document.addEventListener('visibilitychange', function() { if (document.hidden) stop(); else start(); });
    window.addEventListener('resize', layout);

    go(0);
    start();
});
</script>

    <!-- Filter Bar -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 md:gap-8 mb-8 md:mb-10 border-b border-white/5 pb-4">
        <div class="flex gap-2 md:gap-4">
            <?php
            $total_movies = wp_count_posts( 'movie' );
            $movie_count = $total_movies->publish ?? 0;
            ?>
            <a href="<?php echo esc_url( remove_query_arg( 'type' ) ); ?>"
               class="flex items-center gap-2 px-4 py-2 font-label-mono-sm text-[10px] md:text-xs tracking-[0.15em] transition-all text-primary bg-primary/10 border-b-2 border-primary shadow-[0_0_10px_rgba(0,220,230,0.3)]">
                <span class="material-symbols-outlined text-sm">list</span>
                <?php printf( esc_html__( 'ALL (%d)', 'morimoflix-flicker' ), $movie_count ); ?>
            </a>
        </div>
        <div class="flex items-center gap-2 md:gap-4">
            <span class="font-label-mono-sm text-[11px] md:text-xs text-on-surface-variant/60 uppercase tracking-wider"><?php esc_html_e( 'Sort By', 'morimoflix-flicker' ); ?></span>
            <select onchange="if(this.value) window.location.href=this.value;" class="bg-white/5 border border-white/10 text-primary font-label-mono-sm text-[11px] md:text-xs focus:ring-0 focus:border-primary/50 py-2 px-3 md:px-4 tracking-wider uppercase cursor-pointer rounded">
                <option value="<?php echo esc_url( get_post_type_archive_link( 'movie' ) ); ?>"><?php esc_html_e( 'All Categories', 'morimoflix-flicker' ); ?></option>
                <?php
                $genres = get_terms( array(
                    'taxonomy' => 'genre',
                    'hide_empty' => false,
                ) );
                if ( ! is_wp_error( $genres ) && ! empty( $genres ) ) {
                    foreach ( $genres as $genre ) {
                        echo '<option value="' . esc_url( get_term_link( $genre ) ) . '">' . esc_html( strtoupper( $genre->name ) ) . '</option>';
                    }
                }
                ?>
            </select>
        </div>
    </div>

    <!-- Content Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4 md:gap-6">
        <?php
        if ( $main_query->have_posts() ) :
            while ( $main_query->have_posts() ) :
                $main_query->the_post();
                $meta = morimoflix_get_movie_meta();
                $post_type = get_post_type();
        ?>
        <div class="group cursor-pointer relative">
            <a href="<?php the_permalink(); ?>" class="block" aria-label="<?php echo esc_attr( morimoflix_card_label() ); ?>">
                <div class="relative poster-aspect overflow-hidden bg-surface-container border border-white/10 group-hover:border-primary transition-all duration-500 group-hover:shadow-[0_0_25px_rgba(0,243,255,0.2)] rounded-lg">
                    <?php if ( has_post_thumbnail() ) : ?>
                        <?php the_post_thumbnail( 'medium', array( 'class' => 'w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 brightness-90 group-hover:brightness-110', 'alt' => morimoflix_poster_alt(), 'loading' => 'lazy', 'decoding' => 'async' ) ); ?>
                    <?php else : ?>
                        <div class="w-full h-full bg-gradient-to-br from-surface to-black flex items-center justify-center p-4 md:p-6 text-center opacity-40 group-hover:opacity-80 transition-opacity">
                            <span class="font-label-mono-sm text-[8px] md:text-[10px] tracking-widest text-primary uppercase"><?php esc_html_e( 'No Poster', 'morimoflix-flicker' ); ?></span>
                        </div>
                    <?php endif; ?>
                    <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-transparent opacity-60"></div>
                </div>
            </a>
            <div class="absolute top-2 left-2 pointer-events-none">
                <span class="bg-surface/80 backdrop-blur-md text-on-surface font-label-mono-sm text-[8px] md:text-[9px] px-2 py-0.5 border border-white/10 uppercase rounded">
                    <?php echo esc_html( $post_type === 'tv' ? __( 'Show', 'morimoflix-flicker' ) : __( 'Movie', 'morimoflix-flicker' ) ); ?>
                </span>
            </div>
            <?php if ( $meta['quality'] === '4K' ) : ?>
                <div class="absolute top-2 right-2 pointer-events-none">
                    <span class="bg-secondary text-black font-label-mono-sm text-[8px] md:text-[9px] px-1 py-0.5 font-bold rounded"><?php esc_html_e( '4K', 'morimoflix-flicker' ); ?></span>
                </div>
            <?php elseif ( $meta['quality'] === 'HD' ) : ?>
                <div class="absolute top-2 right-2 pointer-events-none">
                    <span class="bg-primary/80 text-black font-label-mono-sm text-[8px] md:text-[9px] px-1 py-0.5 font-bold rounded"><?php esc_html_e( 'HD', 'morimoflix-flicker' ); ?></span>
                </div>
            <?php endif; ?>
            <div class="mt-2 md:mt-3">
                <h3 class="font-headline-md text-[11px] md:text-sm text-white group-hover:text-primary transition-colors truncate uppercase tracking-tight"><?php the_title(); ?></h3>
                <div class="flex items-center gap-2 md:gap-3 mt-1 md:mt-1.5">
                    <span class="font-label-mono-sm text-[9px] md:text-[10px] text-secondary font-bold"><?php printf( esc_html__( 'IMDb %s', 'morimoflix-flicker' ), esc_html( $meta['rating'] ) ); ?></span>
                    <span class="font-label-mono-sm text-[9px] md:text-[10px] text-on-surface-variant/60"><?php echo esc_html( $meta['duration'] ); ?></span>
                </div>
            </div>
        </div>
        <?php
            endwhile;
            wp_reset_postdata();
        else :
        ?>
        <div class="col-span-full text-center py-20">
            <span class="material-symbols-outlined text-6xl text-neutral-700 mb-6 block">search_off</span>
            <h2 class="font-headline-md text-headline-md text-white mb-4"><?php esc_html_e( 'NO CONTENT FOUND', 'morimoflix-flicker' ); ?></h2>
            <p class="font-label-mono-sm text-on-surface-variant/60 tracking-widest uppercase">
                <?php
                if ( $current_type === 'movies' ) {
                    esc_html_e( 'NO MOVIES FOUND', 'morimoflix-flicker' );
                } elseif ( $current_type === 'series' ) {
                    esc_html_e( 'NO SERIES FOUND', 'morimoflix-flicker' );
                } else {
                    esc_html_e( 'NO CONTENT FOUND', 'morimoflix-flicker' );
                }
                ?>
            </p>
        </div>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if ( $main_query->max_num_pages > 1 ) : ?>
    <div class="flex flex-wrap justify-center items-center gap-3 md:gap-4 mt-12 md:mt-16">
        <?php
        $total_pages = $main_query->max_num_pages;
        $base_query_args = array();
        if ( $current_type !== 'all' ) {
            $base_query_args['type'] = $current_type;
        }

        if ( $paged > 1 ) :
            $prev_url = add_query_arg( array_merge( $base_query_args, array( 'paged' => $paged - 1 ) ) );
        ?>
            <a href="<?php echo esc_url( $prev_url ); ?>" class="flex items-center gap-2 px-4 py-2 bg-white/5 border border-white/10 text-neutral-400 hover:text-white hover:border-primary hover:bg-white/10 transition-all font-label-mono-sm text-xs rounded">
                <span class="material-symbols-outlined text-sm">chevron_left</span>
                Previous
            </a>
        <?php else : ?>
            <span class="flex items-center gap-2 px-4 py-2 bg-white/5 border border-white/5 text-neutral-600 font-label-mono-sm text-xs cursor-not-allowed rounded">
                <span class="material-symbols-outlined text-sm">chevron_left</span>
                Previous
            </span>
        <?php endif; ?>

        <div class="flex items-center gap-2">
        <?php
        $start = max( 1, $paged - 2 );
        $end = min( $total_pages, $paged + 2 );

        if ( $start > 1 ) :
        ?>
            <a href="<?php echo esc_url( add_query_arg( array_merge( $base_query_args, array( 'paged' => 1 ) ) ) ); ?>" class="w-10 h-10 flex items-center justify-center bg-white/5 border border-white/10 text-neutral-400 hover:text-white hover:border-primary transition-all font-label-mono-sm text-xs rounded"><?php echo 1; ?></a>
            <?php if ( $start > 2 ) : ?>
                <span class="text-neutral-600 font-label-mono-sm px-1">...</span>
            <?php endif; ?>
        <?php endif; ?>

        <?php for ( $i = $start; $i <= $end; $i++ ) : ?>
            <?php if ( $i === $paged ) : ?>
                <span class="w-10 h-10 flex items-center justify-center bg-primary text-black font-label-mono-sm text-xs font-bold shadow-[0_0_10px_rgba(0,220,230,0.3)] rounded"><?php echo $i; ?></span>
            <?php else : ?>
                <a href="<?php echo esc_url( add_query_arg( array_merge( $base_query_args, array( 'paged' => $i ) ) ) ); ?>" class="w-10 h-10 flex items-center justify-center bg-white/5 border border-white/10 text-neutral-400 hover:text-white hover:border-primary transition-all font-label-mono-sm text-xs rounded"><?php echo $i; ?></a>
            <?php endif; ?>
        <?php endfor; ?>

        <?php if ( $end < $total_pages ) : ?>
            <?php if ( $end < $total_pages - 1 ) : ?>
                <span class="text-neutral-600 font-label-mono-sm px-1">...</span>
            <?php endif; ?>
            <a href="<?php echo esc_url( add_query_arg( array_merge( $base_query_args, array( 'paged' => $total_pages ) ) ) ); ?>" class="w-10 h-10 flex items-center justify-center bg-white/5 border border-white/10 text-neutral-400 hover:text-white hover:border-primary transition-all font-label-mono-sm text-xs rounded"><?php echo $total_pages; ?></a>
        <?php endif; ?>
        </div>

        <?php if ( $paged < $total_pages ) :
            $next_url = add_query_arg( array_merge( $base_query_args, array( 'paged' => $paged + 1 ) ) );
        ?>
            <a href="<?php echo esc_url( $next_url ); ?>" class="flex items-center gap-2 px-4 py-2 bg-white/5 border border-white/10 text-neutral-400 hover:text-white hover:border-primary hover:bg-white/10 transition-all font-label-mono-sm text-xs rounded">
                Next
                <span class="material-symbols-outlined text-sm">chevron_right</span>
            </a>
        <?php else : ?>
            <span class="flex items-center gap-2 px-4 py-2 bg-white/5 border border-white/5 text-neutral-600 font-label-mono-sm text-xs cursor-not-allowed rounded">
                Next
                <span class="material-symbols-outlined text-sm">chevron_right</span>
            </span>
        <?php endif; ?>
    </div>
    <?php endif;
    wp_reset_postdata();
    ?>

    <!-- Explore by Genre -->
    <section id="explore-genres" class="mt-10 md:mt-16 scroll-mt-24">
        <div class="glass-panel rounded-2xl border border-white/10 bg-white/[0.03] backdrop-blur-xl p-5 md:p-8">
            <div class="mb-5 md:mb-6">
                <h2 class="font-display-hero text-xl md:text-2xl font-extrabold tracking-tight text-[#FFEAE9]"><?php esc_html_e( 'Explore by Genre', 'morimoflix-flicker' ); ?></h2>
                <p class="font-body-md text-sm text-[#A0A0AA] mt-1"><?php esc_html_e( 'Find your next favorite movie', 'morimoflix-flicker' ); ?></p>
            </div>
            <div class="flex gap-3 overflow-x-auto no-scrollbar pb-1 -mx-1 px-1">
                <?php
                $genre_icons = array(
                    'action' => 'bolt', 'comedy' => 'sentiment_very_satisfied', 'romance' => 'favorite',
                    'thriller' => 'warning', 'sci-fi' => 'rocket_launch', 'drama' => 'theater_comedy',
                    'horror' => 'skull', 'crime' => 'gavel', 'adventure' => 'explore', 'fantasy' => 'auto_awesome',
                    'mystery' => 'help', 'war' => 'shield', 'animation' => 'animation', 'documentary' => 'description',
                );
                $panel_genres = get_terms( array(
                    'taxonomy' => 'genre',
                    'hide_empty' => true,
                    'number' => 6,
                    'orderby' => 'count',
                    'order' => 'DESC',
                ) );

                if ( ! is_wp_error( $panel_genres ) && ! empty( $panel_genres ) ) :
                    $gi = 0;
                    foreach ( $panel_genres as $pg ) :
                        $pg_icon = isset( $genre_icons[ $pg->slug ] ) ? $genre_icons[ $pg->slug ] : 'movie';
                        $pg_link = get_term_link( $pg );
                        if ( is_wp_error( $pg_link ) ) continue;
                        $pg_active = $gi === 0 ? 'border-[#E50914]/50 bg-[#E50914]/10 text-[#FFB4AB] shadow-[0_0_15px_rgba(229,9,20,0.25)]' : 'border-white/10 bg-white/5 text-[#D8C2C2] hover:border-white/25 hover:text-white';
                ?>
                <a href="<?php echo esc_url( $pg_link ); ?>" class="glass-chip shrink-0 inline-flex items-center gap-2.5 px-5 py-3 rounded-xl border backdrop-blur-md font-label-caps text-[12px] tracking-wider uppercase transition-all <?php echo esc_attr( $pg_active ); ?>">
                    <span class="material-symbols-outlined text-lg" aria-hidden="true"><?php echo esc_html( $pg_icon ); ?></span>
                    <?php echo esc_html( $pg->name ); ?>
                </a>
                <?php
                        $gi++;
                    endforeach;
                endif;
                ?>
                <a href="<?php echo esc_url( home_url( '/list/' ) ); ?>" class="shrink-0 inline-flex items-center gap-2.5 px-5 py-3 rounded-xl border border-white/10 bg-white/5 backdrop-blur-md text-[#A0A0AA] hover:border-white/25 hover:text-white font-label-caps text-[12px] tracking-wider uppercase transition-all">
                    <span class="material-symbols-outlined text-lg" aria-hidden="true">more_horiz</span>
                    <?php esc_html_e( 'More', 'morimoflix-flicker' ); ?>
                </a>
            </div>
        </div>
    </section>

    <!-- SEO Intro — TGMovies (Audit Tier 1.5 + 3.3): kept last so carousel + movies come first -->
    <section id="about-tgmovies" class="max-w-3xl mx-auto md:mx-0 mt-10 md:mt-16 rounded-xl border border-[#E50914]/25 bg-[#15151B] p-6 md:p-10 shadow-[0_4px_24px_rgba(0,0,0,0.45)]">
        <p class="font-label-caps text-[10px] tracking-[0.25em] uppercase text-[#FFB4AB] mb-3">About TGMovies</p>
        <h2 class="font-display-hero text-2xl md:text-3xl font-extrabold tracking-tight text-[#FFEAE9] mb-4">Latest Movies in HD — Telugu, Hindi &amp; English</h2>
        <p class="font-body-md text-sm md:text-base leading-relaxed text-white mb-4" style="color:#ffffff !important;">Welcome to <strong class="text-[#FFEAE9]">TGMovies MORIMOFLIX</strong> — your home for curated HD movie downloads. Browse more than 1,500 hand-picked titles across Telugu, Hindi, English, Tamil and Malayalam cinema, from sprawling action blockbusters and slow-burn dramas to sci-fi epics and weekend comedies. Every listing shows the IMDb rating, runtime, audio language and quality badge (HD, 4K or SD) up front, so you always know exactly what you are getting before you tap download.</p>
        <h2 class="font-display-hero text-xl md:text-2xl font-extrabold tracking-tight text-[#FFEAE9] mt-8 mb-3">How do I request a movie on TGMovies?</h2>
        <p class="font-body-md text-sm md:text-base leading-relaxed text-white mb-4" style="color:#ffffff !important;">Open the <a href="#" id="seoRequestLink" class="text-[#FFB4AB] underline underline-offset-2 hover:text-[#FFEAE9]">Request a Movie</a> form, fill in the title, release year, and (optionally) an IMDb link. We add requested titles within 24–48 hours and notify you on the <a href="https://t.me/MorimoFlix_Zone" target="_blank" rel="noopener" class="text-[#FFB4AB] underline underline-offset-2 hover:text-[#FFEAE9]">Telegram channel</a>, which also posts daily new-release alerts you will not find by refreshing the homepage.</p>
        <h2 class="font-display-hero text-xl md:text-2xl font-extrabold tracking-tight text-[#FFEAE9] mt-8 mb-3">Browse Movies by Language and Genre</h2>
        <p class="font-body-md text-sm md:text-base leading-relaxed text-white" style="color:#ffffff !important;">Prefer to explore? Start with the <?php $seo_genres = get_terms( array( 'taxonomy' => 'genre', 'hide_empty' => true, 'number' => 3, 'orderby' => 'count', 'order' => 'DESC' ) ); if ( ! is_wp_error( $seo_genres ) && ! empty( $seo_genres ) ) : $seo_links = array(); foreach ( $seo_genres as $seo_g ) : $seo_links[] = '<a href="' . esc_url( get_term_link( $seo_g ) ) . '" class="text-[#FFB4AB] underline underline-offset-2 hover:text-[#FFEAE9]">' . esc_html( $seo_g->name ) . ' movies</a>'; endforeach; echo implode( ', ', $seo_links ); else : ?>genre shelves<?php endif; ?>, open the full <a href="<?php echo esc_url( home_url( '/list/' ) ); ?>" class="text-[#FFB4AB] underline underline-offset-2 hover:text-[#FFEAE9]">A–Z library</a> for an alphabetical index of every title, or type any actor, director or keyword into <a href="<?php echo esc_url( home_url( '/?s=' ) ); ?>" class="text-[#FFB4AB] underline underline-offset-2 hover:text-[#FFEAE9]">search</a>. New to the site? The carousel above always shows this week's five freshest additions.</p>
    </section>
</div>

<!-- Motion Animations -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof window.MotionAnimate === 'undefined' || typeof window.MotionStagger === 'undefined') return;

    // Movie cards — animate only cards below the fold on scroll
    var movieGrid = document.querySelector('.grid');
    if (movieGrid) {
        var allCards = movieGrid.querySelectorAll('.group');
        var belowFold = [];
        allCards.forEach(function(card) {
            if (card.getBoundingClientRect().top >= window.innerHeight) {
                card.style.opacity = '0';
                belowFold.push(card);
            }
        });
        if (belowFold.length > 0) {
            var cardObserver = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        window.MotionAnimate(entry.target, { opacity: [0, 1], y: [20, 0] }, { duration: 0.4, ease: 'ease-out' });
                        cardObserver.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.1 });
            belowFold.forEach(function(card) { cardObserver.observe(card); });
        }
    }

    // Category cards — animate only on scroll
    var catGrid = document.querySelector('.grid.grid-cols-2');
    if (catGrid) {
        var allCats = catGrid.querySelectorAll('a.group');
        var belowFoldCats = [];
        allCats.forEach(function(card) {
            if (card.getBoundingClientRect().top >= window.innerHeight) {
                card.style.opacity = '0';
                belowFoldCats.push(card);
            }
        });
        if (belowFoldCats.length > 0) {
            var catObserver = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        window.MotionAnimate(entry.target, { opacity: [0, 1], scale: [0.9, 1] }, { duration: 0.3, ease: 'ease-out' });
                        catObserver.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.1 });
            belowFoldCats.forEach(function(card) { catObserver.observe(card); });
        }
    }

    // Pagination hover scale
    var pageLinks = document.querySelectorAll('.page-numbers a, .page-numbers span:not(.dots)');
    pageLinks.forEach(function(link) {
        link.addEventListener('mouseenter', function() { window.MotionAnimate(link, { scale: 1.08 }, { duration: 0.2 }); });
        link.addEventListener('mouseleave', function() { window.MotionAnimate(link, { scale: 1 }, { duration: 0.2 }); });
    });

});
</script>

<?php get_footer(); ?>
