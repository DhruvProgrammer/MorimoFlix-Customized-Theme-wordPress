<?php
/**
 * Main Template File
 *
 * @package MorimoFlix
 */

get_header();
?>

<?php
// Get current filter with proper validation
$current_type = isset( $_GET['type'] ) ? sanitize_key( $_GET['type'] ) : 'all';
if ( ! in_array( $current_type, array( 'all', 'movies', 'series' ), true ) ) {
    $current_type = 'all';
}

// Get current page - use $_GET directly
$paged = isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1;
$posts_per_page = 18;

// Build query args based on filter
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
    // Hero Carousel Section - Shows 5 latest movies
    $hero_query = new WP_Query( array(
        'post_type'      => array( 'movie', 'tv' ),
        'posts_per_page' => 5,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ) );
    ?>
    <style>
    #heroCarousel{position:relative;overflow:hidden;background:#0A0505;}
    #heroCarousel .g-backdrop{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;opacity:0.32;filter:blur(2px) brightness(0.7) saturate(1.1);transition:opacity 0.45s ease;pointer-events:none;}
    #heroCarousel .g-veil{position:absolute;inset:0;background:linear-gradient(to bottom,rgba(10,5,5,0.55),rgba(10,5,5,0.82) 60%,#0A0505 100%),radial-gradient(ellipse 80% 60% at 50% 0%,rgba(229,9,20,0.18),transparent 65%);pointer-events:none;}
    #heroCarousel .g-grid{position:relative;z-index:2;display:grid;grid-template-columns:minmax(0,1fr);gap:20px;padding:20px 16px 56px;}
    @media (min-width:1024px){#heroCarousel .g-grid{grid-template-columns:minmax(0,1.05fr) minmax(0,1fr);align-items:center;gap:32px;padding:36px 40px 64px;}}
    #heroCarousel .g-infos{min-width:0;}
    #heroCarousel .g-info{display:none;min-width:0;}
    #heroCarousel .g-info.active{display:block;}
    #heroCarousel .g-eyebrow{display:inline-flex;align-items:center;gap:8px;background:rgba(229,9,20,0.9);color:#fff;font-size:10px;letter-spacing:0.2em;font-weight:700;padding:4px 10px;border-radius:3px;text-transform:uppercase;}
    #heroCarousel .g-title-accent{color:#FFB4AB;}
    #heroCarousel .g-excerpt{display:-webkit-box;-webkit-line-clamp:2;line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}
    #heroCarousel .g-fan{display:flex;align-items:flex-end;justify-content:center;gap:8px;min-width:0;}
    @media (min-width:1024px){#heroCarousel .g-fan{gap:12px;}}
    #heroCarousel .g-fancard{position:relative;flex:0 1 auto;width:56px;border-radius:10px;overflow:hidden;background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.16);cursor:pointer;transition:transform 0.4s ease,border-color 0.3s ease,box-shadow 0.3s ease,opacity 0.3s ease;opacity:0.65;min-width:0;}
    @media (min-width:640px){#heroCarousel .g-fancard{width:88px;}}
    @media (min-width:1024px){#heroCarousel .g-fancard{width:120px;}}
    #heroCarousel .g-fancard.active{opacity:1;border-color:rgba(255,180,171,0.55);box-shadow:0 18px 50px rgba(0,0,0,0.6),0 0 28px rgba(229,9,20,0.35);transform:translateY(-6px) scale(1.06);}
    #heroCarousel .g-fancard img.g-poster{display:block;width:100%;height:96px;object-fit:cover;}
    @media (min-width:640px){#heroCarousel .g-fancard img.g-poster{height:140px;}}
    @media (min-width:1024px){#heroCarousel .g-fancard img.g-poster{height:180px;}}
    #heroCarousel .g-fancard .g-noimg{display:flex;align-items:center;justify-content:center;height:96px;background:linear-gradient(135deg,#1D0E0E,#0A0505);color:rgba(255,255,255,0.25);}
    @media (min-width:640px){#heroCarousel .g-fancard .g-noimg{height:140px;}}
    @media (min-width:1024px){#heroCarousel .g-fancard .g-noimg{height:180px;}}
    #heroCarousel .g-fancard .g-noimg .material-symbols-outlined{font-size:28px;}
    #heroCarousel .g-quality{position:absolute;top:6px;right:6px;z-index:2;background:rgba(20,10,10,0.72);color:#FFB4AB;border:1px solid rgba(255,180,171,0.5);backdrop-filter:blur(8px);-webkit-backdrop-filter:blur(8px);font-size:9px;letter-spacing:0.1em;padding:2px 6px;border-radius:4px;}
    #heroCarousel .g-rank{position:absolute;top:6px;left:6px;z-index:2;background:rgba(229,9,20,0.9);color:#fff;font-size:9px;font-weight:700;padding:2px 6px;border-radius:4px;letter-spacing:0.08em;}
    #heroCarousel .g-dots{position:absolute;left:50%;transform:translateX(-50%);bottom:12px;display:flex;gap:2px;z-index:30;}
    #heroCarousel .g-dots button{width:26px;height:26px;display:flex;align-items:center;justify-content:center;background:transparent;border:0;cursor:pointer;padding:0;}
    #heroCarousel .g-dots button::after{content:"";width:8px;height:8px;border-radius:9999px;background:rgba(255,255,255,0.35);transition:all 0.3s ease;}
    #heroCarousel .g-dots button.active::after{width:22px;background:#E50914;box-shadow:0 0 8px rgba(229,9,20,0.8);}
    @media (max-width:640px){#heroCarousel .g-grid{padding:16px 14px 54px;gap:16px;}}
    @media (prefers-reduced-motion:reduce){#heroCarousel .g-backdrop,#heroCarousel .g-fancard,#heroCarousel .g-dots button::after{transition:none;}}
    </style>
    <?php
    if ( $hero_query->have_posts() ) :
    ?>
    <h1 class="sr-only"><?php esc_html_e( 'Watch & Download Movies in HD — Telugu, Hindi & English', 'morimoflix-flicker' ); ?></h1>
    <?php
    $hero_first_backdrop = '';
    if ( isset( $hero_query->posts[0] ) && isset( $hero_query->posts[0]->ID ) ) {
        $hero_first_backdrop = morimoflix_get_backdrop( $hero_query->posts[0]->ID );
    }
    ?>
    <section id="heroCarousel" class="mb-8 md:mb-12 relative overflow-hidden rounded-xl shadow-[0_0_50px_rgba(0,0,0,0.8)] border border-[#534343]/30 red-glow bg-[#0A0505]">
        <img id="gBackdropIndex" src="<?php echo esc_url( $hero_first_backdrop ); ?>" alt="" aria-hidden="true" class="g-backdrop" fetchpriority="high" decoding="async">
        <div class="g-veil" aria-hidden="true"></div>
        <div class="g-grid">
            <div class="g-fan order-1 lg:order-2" role="tablist" aria-label="<?php echo esc_attr__( 'Featured titles', 'morimoflix-flicker' ); ?>">
                <?php
                $hero_index = 0;
                while ( $hero_query->have_posts() ) :
                    $hero_query->the_post();
                    $meta = morimoflix_get_movie_meta();
                    $hero_backdrop = morimoflix_get_backdrop();
                ?>
                <div class="g-fancard<?php echo $hero_index === 0 ? ' active' : ''; ?>" data-i="<?php echo (int) $hero_index; ?>" data-permalink="<?php echo esc_url( get_permalink() ); ?>" data-backdrop="<?php echo esc_url( $hero_backdrop ); ?>" data-title="<?php echo esc_attr( get_the_title() ); ?>" role="tab" aria-selected="<?php echo $hero_index === 0 ? 'true' : 'false'; ?>" tabindex="<?php echo $hero_index === 0 ? '0' : '-1'; ?>" aria-label="<?php echo esc_attr( get_the_title() ); ?>">
                    <?php if ( has_post_thumbnail() ) : ?>
                        <?php the_post_thumbnail( 'medium', array( 'class' => 'g-poster', 'alt' => morimoflix_poster_alt(), 'loading' => $hero_index === 0 ? 'eager' : 'lazy', 'decoding' => 'async', 'fetchpriority' => $hero_index === 0 ? 'high' : 'auto' ) ); ?>
                    <?php else : ?>
                        <div class="g-noimg"><span class="material-symbols-outlined" aria-hidden="true">movie</span></div>
                    <?php endif; ?>
                    <span class="g-rank">#<?php echo (int) ( $hero_index + 1 ); ?></span>
                    <span class="g-quality"><?php echo esc_html( $meta['quality'] ); ?></span>
                </div>
                <?php
                    $hero_index++;
                endwhile;
                $hero_query->rewind_posts();
                ?>
            </div>
            <div class="g-infos order-2 lg:order-1">
                <?php
                $hero_index = 0;
                while ( $hero_query->have_posts() ) :
                    $hero_query->the_post();
                    $meta = morimoflix_get_movie_meta();
                    $hero_raw_title = get_the_title();
                    $hero_base = $hero_raw_title;
                    $hero_num = '';
                    if ( preg_match( '/^(.*?)(\d+)\s*$/', $hero_raw_title, $hero_m ) ) {
                        $hero_base = trim( $hero_m[1] );
                        if ( '' === $hero_base ) {
                            $hero_base = $hero_raw_title;
                            $hero_num = '';
                        } else {
                            $hero_num = $hero_m[2];
                        }
                    }
                ?>
                <div class="g-info<?php echo $hero_index === 0 ? ' active' : ''; ?>" data-i="<?php echo (int) $hero_index; ?>"<?php echo $hero_index === 0 ? '' : ' aria-hidden="true"'; ?>>
                    <div class="flex items-center flex-wrap gap-2 mb-3">
                        <span class="g-eyebrow"><?php esc_html_e( 'Newly Released', 'morimoflix-flicker' ); ?> · #<?php echo (int) ( $hero_index + 1 ); ?></span>
                        <span class="bg-amber-500/20 text-amber-400 border border-amber-500/30 font-label-caps text-[9px] md:text-[10px] px-2 py-0.5 rounded-sm">Top 10 Today</span>
                    </div>
                    <h2 class="font-display-hero text-[1.65rem] leading-[1.12] md:text-4xl text-[#FFEAE9] mb-3 md:leading-tight tracking-tighter font-extrabold"<?php echo $hero_index === 0 ? '' : ' aria-hidden="true"'; ?>><a href="<?php the_permalink(); ?>"<?php echo $hero_index === 0 ? '' : ' tabindex="-1"'; ?> class="hover:text-[#FFB4AB] transition-colors"><?php echo esc_html( $hero_base ); ?><?php if ( '' !== $hero_num ) : ?> <span class="g-title-accent"><?php echo esc_html( $hero_num ); ?></span><?php endif; ?></a></h2>
                    <div class="flex items-center flex-wrap gap-x-3 gap-y-1 text-metadata font-metadata text-[#FFEAE9] mb-3">
                        <span class="flex items-center gap-1 text-[#F5C518]"><span class="material-symbols-outlined text-[16px]" style="font-variation-settings: 'FILL' 1;" aria-hidden="true">star</span> <?php echo esc_html( $meta['rating'] ); ?></span>
                        <span class="flex items-center gap-1 text-[#D8C2C2]"><span class="material-symbols-outlined text-[16px]" aria-hidden="true">schedule</span> <?php echo esc_html( $meta['duration'] ); ?></span>
                        <span class="text-[#D8C2C2]"><?php echo esc_html( $meta['year'] ); ?></span>
                        <span class="bg-[#FFB4AB]/20 text-[#FFB4AB] border border-[#FFB4AB]/50 font-label-caps text-label-caps px-1.5 py-0.5 rounded-sm"><?php echo esc_html( $meta['quality'] ); ?></span>
                    </div>
                    <p class="g-excerpt text-[#D8C2C2] font-body-md text-sm md:text-body-lg mb-5 max-w-xl leading-relaxed"><?php echo wp_trim_words( get_the_excerpt(), 20 ); ?></p>
                    <div class="flex flex-col sm:flex-row gap-2.5 md:gap-4">
                        <a href="<?php the_permalink(); ?>"<?php echo $hero_index === 0 ? '' : ' tabindex="-1"'; ?> class="bg-gradient-to-r from-red-600 to-red-800 text-white px-5 py-3.5 md:px-8 md:py-4 rounded-lg font-headline-md text-sm md:text-body-lg hover:brightness-110 transition-all shadow-[0_0_20px_rgba(255,0,0,0.6)] hover:shadow-[0_0_30px_rgba(255,0,0,0.8)] inline-flex items-center justify-center gap-2 min-h-[48px]">
                            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;" aria-hidden="true">play_arrow</span> <?php esc_html_e( 'Watch Now', 'morimoflix-flicker' ); ?>
                        </a>
                    </div>
                </div>
                <?php
                    $hero_index++;
                endwhile;
                wp_reset_postdata();
                ?>
            </div>
        </div>

            <!-- Navigation Buttons -->
            <button id="heroPrevBtn" type="button" aria-label="Previous slide" class="absolute left-2 md:left-6 top-24 lg:top-1/2 lg:-translate-y-1/2 z-30 w-11 h-11 min-w-[44px] min-h-[44px] flex items-center justify-center rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-white hover:bg-[#E50914] hover:border-[#E50914] transition-all">
                <span class="material-symbols-outlined">chevron_left</span>
            </button>
            <button id="heroNextBtn" type="button" aria-label="Next slide" class="absolute right-2 md:right-6 top-24 lg:top-1/2 lg:-translate-y-1/2 z-30 w-11 h-11 min-w-[44px] min-h-[44px] flex items-center justify-center rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-white hover:bg-[#E50914] hover:border-[#E50914] transition-all">
                <span class="material-symbols-outlined">chevron_right</span>
            </button>
            <div class="g-dots">
                <?php for ( $d = 0; $d < $hero_index; $d++ ) : ?>
                <button type="button" aria-label="<?php echo esc_attr( sprintf( __( 'Go to slide %d', 'morimoflix-flicker' ), $d + 1 ) ); ?>"<?php echo $d === 0 ? ' class="active"' : ''; ?>></button>
                <?php endfor; ?>
            </div>
    </section>
    <?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var section = document.getElementById('heroCarousel');
    if (!section) return;
    var infos = section.querySelectorAll('.g-info');
    var cards = section.querySelectorAll('.g-fancard');
    if (!infos || infos.length === 0 || !cards || cards.length === 0) return;
    var n = Math.min(infos.length, cards.length);
    var backdrop = document.getElementById('gBackdropIndex');
    var prevBtn = document.getElementById('heroPrevBtn');
    var nextBtn = document.getElementById('heroNextBtn');
    var dots = section.querySelectorAll('.g-dots button');
    var current = 0;
    var timer = null;
    var touchX = null;
    var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    function norm(i) { return ((i % n) + n) % n; }
    function swapBackdrop(src) {
        if (!backdrop || !src) return;
        if (backdrop.getAttribute('src') === src) return;
        if (reduceMotion) { backdrop.setAttribute('src', src); return; }
        backdrop.style.opacity = '0';
        setTimeout(function() {
            backdrop.setAttribute('src', src);
            var done = function() { backdrop.style.opacity = ''; };
            backdrop.onload = done;
            setTimeout(done, 400);
        }, 160);
    }
    function go(i) {
        current = norm(i);
        for (var k = 0; k < n; k++) {
            var on = (k === current);
            var info = infos[k];
            var card = cards[k];
            if (info) {
                if (on) info.classList.add('active');
                else info.classList.remove('active');
                if (on) info.removeAttribute('aria-hidden');
                else info.setAttribute('aria-hidden', 'true');
                var h2 = info.querySelector('h2');
                if (h2) {
                    if (on) h2.removeAttribute('aria-hidden');
                    else h2.setAttribute('aria-hidden', 'true');
                }
                var links = info.querySelectorAll('a');
                for (var j = 0; j < links.length; j++) {
                    if (!links[j]) continue;
                    if (on) links[j].removeAttribute('tabindex');
                    else links[j].setAttribute('tabindex', '-1');
                }
            }
            if (card) {
                if (on) card.classList.add('active');
                else card.classList.remove('active');
                card.setAttribute('aria-selected', on ? 'true' : 'false');
                card.setAttribute('tabindex', on ? '0' : '-1');
            }
        }
        if (dots && dots.length) {
            for (var q = 0; q < dots.length; q++) {
                if (!dots[q]) continue;
                if (q === current) { dots[q].classList.add('active'); dots[q].setAttribute('aria-current', 'true'); }
                else { dots[q].classList.remove('active'); dots[q].removeAttribute('aria-current'); }
            }
        }
        if (cards[current]) {
            var src = cards[current].getAttribute('data-backdrop');
            if (!src) {
                var im = cards[current].querySelector('img');
                if (im) src = im.getAttribute('src');
            }
            swapBackdrop(src);
        }
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
            var card = cards[idx];
            if (!card) return;
            card.addEventListener('click', function(e) {
                if (e && e.target && e.target.closest && e.target.closest('a,button')) return;
                if (idx === current) {
                    var link = card.getAttribute('data-permalink');
                    if (link) window.location.href = link;
                } else { stop(); go(idx); start(); }
            });
            card.addEventListener('keydown', function(e) {
                if (!e) return;
                if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); stop(); go(idx); start(); }
                if (e.key === 'ArrowRight') { e.preventDefault(); stop(); go(current + 1); start(); }
                if (e.key === 'ArrowLeft') { e.preventDefault(); stop(); go(current - 1); start(); }
            });
        })(k);
    }
    section.addEventListener('pointerenter', stop);
    section.addEventListener('pointerleave', start);
    section.addEventListener('focusin', stop);
    section.addEventListener('focusout', start);
    section.addEventListener('touchstart', function(e) { stop(); if (e && e.touches && e.touches[0]) touchX = e.touches[0].clientX; }, { passive: true });
    section.addEventListener('touchend', function(e) {
        var dx = 0;
        if (e && e.changedTouches && e.changedTouches[0] && touchX !== null) dx = e.changedTouches[0].clientX - touchX;
        touchX = null;
        if (Math.abs(dx) > 40) go(current + (dx < 0 ? 1 : -1));
        start();
    }, { passive: true });
    document.addEventListener('visibilitychange', function() { if (document.hidden) stop(); else start(); });
    document.addEventListener('keydown', function(e) {
        if (!e || !section.contains(document.activeElement)) return;
        if (e.key === 'ArrowRight') { stop(); go(current + 1); start(); }
        if (e.key === 'ArrowLeft') { stop(); go(current - 1); start(); }
    });
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
            <!-- Type Badge -->
            <div class="absolute top-2 left-2 pointer-events-none">
                <span class="bg-surface/80 backdrop-blur-md text-on-surface font-label-mono-sm text-[8px] md:text-[9px] px-2 py-0.5 border border-white/10 uppercase rounded">
                    <?php echo esc_html( $post_type === 'tv' ? __( 'Show', 'morimoflix-flicker' ) : __( 'Movie', 'morimoflix-flicker' ) ); ?>
                </span>
            </div>
            <!-- Quality Badge -->
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
