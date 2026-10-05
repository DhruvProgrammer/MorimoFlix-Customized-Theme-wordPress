<?php
/**
 * Genre Taxonomy Template
 */

get_header();

$queried_object = get_queried_object();
$paged = max( 1, get_query_var( 'paged' ) ? absint( get_query_var( 'paged' ) ) : 1 );
$posts_per_page = 18;

$genre_query = new WP_Query( array(
    'post_type'      => array( 'movie', 'tv' ),
    'posts_per_page' => $posts_per_page,
    'paged'          => $paged,
    'orderby'        => 'date',
    'order'          => 'DESC',
    'tax_query'      => array(
        array(
            'taxonomy' => $queried_object->taxonomy,
            'field'    => 'term_id',
            'terms'    => $queried_object->term_id,
        ),
    ),
) );
?>

<style>
/* 350px overflow fix — genre grid: minmax + pagination shrink */
html,body{overflow-x:hidden!important;max-width:100%}
*,*::before,*::after{box-sizing:border-box}
.max-w-container-max,.grid,.glass-card,.poster-aspect{box-sizing:border-box;max-width:100%;min-width:0}
.movie-grid{grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}
.movie-grid > *{min-width:0;max-width:100%}
.poster-aspect{aspect-ratio:2/3}
/* Visible breadcrumb (mirrors BreadcrumbList JSON-LD) — mobile-safe */
.t-crumb ol{display:flex;flex-wrap:wrap;align-items:center;gap:4px 8px;list-style:none;margin:0 0 14px;padding:0;min-width:0;max-width:100%}
.t-crumb li{display:inline-flex;align-items:center;gap:8px;min-width:0;max-width:100%}
.t-crumb a{display:inline-flex;align-items:center;min-height:26px;font-size:11px;letter-spacing:.14em;text-transform:uppercase;color:#A0A0AA;text-decoration:none}
.t-crumb a:hover{color:#FFB4AB}
.t-crumb .sep{color:#534343;font-size:11px;flex-shrink:0}
.t-crumb [aria-current="page"]{display:inline-flex;align-items:center;min-height:26px;font-size:11px;letter-spacing:.14em;text-transform:uppercase;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:52vw;min-width:0}
@media(max-width:640px){.t-crumb a,.t-crumb [aria-current="page"]{font-size:11px!important;min-height:26px!important}}
@media(max-width:640px){.movie-grid.grid-cols-2{grid-template-columns:repeat(2,minmax(0,1fr))!important;gap:12px!important}}
/* Force desktop poster size — same as front-page */
@media(min-width:641px){.movie-grid.grid-cols-2{grid-template-columns:repeat(3,minmax(0,1fr))!important;gap:10px!important}}
@media(min-width:768px){.movie-grid.grid-cols-2{grid-template-columns:repeat(5,minmax(0,1fr))!important;gap:12px!important}}
@media(min-width:1024px){.movie-grid.grid-cols-2{grid-template-columns:repeat(6,minmax(0,1fr))!important;gap:14px!important}}
@media(min-width:1280px){.movie-grid.grid-cols-2{grid-template-columns:repeat(6,minmax(0,1fr))!important;gap:14px!important}}
@media(min-width:1536px){.movie-grid.grid-cols-2{grid-template-columns:repeat(7,minmax(0,1fr))!important;gap:14px!important}}
@media(max-width:375px){
    .max-w-container-max.mx-auto.px-5{padding-left:14px!important;padding-right:14px!important}
    .flex.flex-wrap.justify-center{gap:8px!important}
    .w-10.h-10{width:36px!important;height:36px!important}
}
@media(max-width:350px){
    .movie-grid{gap:10px!important}
    .flex.flex-wrap.justify-center{gap:6px!important}
    .w-10.h-10{width:34px!important;height:34px!important}
}
</style>
<main class="pt-20">
    <div class="max-w-container-max mx-auto px-5 md:px-margin-desktop py-12">
        <nav class="t-crumb" aria-label="Breadcrumb">
            <ol>
                <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'morimoflix-flicker' ); ?></a><span class="sep" aria-hidden="true">/</span></li>
                <li><a href="<?php echo esc_url( get_post_type_archive_link( 'movie' ) ); ?>"><?php esc_html_e( 'Movies', 'morimoflix-flicker' ); ?></a><span class="sep" aria-hidden="true">/</span></li>
                <li><span aria-current="page"><?php echo esc_html(single_term_title('', false)); ?></span></li>
            </ol>
        </nav>
        <!-- Header with Back Button -->
        <div class="flex items-center gap-4 mb-8">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="flex items-center gap-2 text-muted hover:text-primary transition-colors group">
                <span class="material-symbols-outlined text-xl">arrow_back</span>
            </a>
            <div class="glitch-bar h-10"></div>
            <div>
                <h1 class="font-headline-lg text-3xl tracking-tighter uppercase font-bold text-white"><?php echo esc_html(single_term_title('', false)); ?></h1>
                <p class="font-label-mono-sm text-on-surface-variant/60 tracking-widest uppercase mt-2">
                    <?php echo esc_html( wp_strip_all_tags( term_description() ) ); ?>
                </p>
            </div>
        </div>

        <!-- Content Grid -->
        <h2 class="sr-only"><?php echo esc_html( sprintf( __( 'Titles in %s', 'morimoflix-flicker' ), $queried_object->name ) ); ?></h2>
        <div class="mb-8 rounded-xl border border-white/10 bg-white/[0.03] backdrop-blur-md p-5 md:p-6">
            <h2 class="font-display-hero text-lg md:text-xl font-extrabold tracking-tight text-white mb-3"><?php echo esc_html( $queried_object->name ); ?> Movies — Hindi Dubbed & Telugu HD</h2>
            <?php $g_desc = term_description(); if ( $g_desc ) : ?><div class="font-body-md text-sm leading-relaxed text-[#A0A0AA] mb-3"><?php echo wp_kses_post( $g_desc ); ?></div><?php endif; ?>
            <p class="font-body-md text-sm leading-relaxed text-[#A0A0AA]">Watch <?php echo esc_html( $queried_object->name ); ?> titles in HD on FlixGarden. Each listing shows rating, year, runtime, language, and quality before download. Use the A–Z Library or search to jump to a specific movie.</p>
        </div>
        <?php if ( $genre_query->have_posts() ) : ?>
        <div class="movie-grid grid grid-cols-2 md:grid-cols-5 lg:grid-cols-6 xl:grid-cols-7 2xl:grid-cols-8 gap-3 md:gap-4">
            <?php while ( $genre_query->have_posts() ) : $genre_query->the_post();
                $meta = morimoflix_get_movie_meta();
                $post_type = get_post_type();
            ?>
            <div class="group cursor-pointer relative">
                <a href="<?php the_permalink(); ?>" class="block" aria-label="<?php echo esc_attr( morimoflix_card_label() ); ?>">
                    <div class="glass-card relative poster-aspect overflow-hidden bg-surface-container border border-white/10 group-hover:border-primary transition-all duration-500 group-hover:shadow-[0_0_25px_rgba(255,0,0,0.4)]">
                        <?php if ( has_post_thumbnail() ) : ?>
                            <?php the_post_thumbnail( 'medium', array( 'class' => 'w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 brightness-90 group-hover:brightness-110', 'alt' => morimoflix_poster_alt(), 'loading' => 'lazy', 'decoding' => 'async' ) ); ?>
                        <?php else : ?>
                            <div class="w-full h-full bg-gradient-to-br from-surface to-black flex items-center justify-center p-6 text-center opacity-40 group-hover:opacity-80 transition-opacity">
                                <span class="font-label-mono-sm text-xs tracking-widest text-primary uppercase">No Poster</span>
                            </div>
                        <?php endif; ?>
                        <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-transparent opacity-60"></div>
                    </div>
                </a>
                <?php if ( $meta['quality'] === '4K' ) : ?>
                    <div class="absolute top-2 right-2 pointer-events-none">
                        <span class="glass-chip bg-secondary text-black font-label-mono-sm text-xs px-1 py-0.5 font-bold backdrop-blur-md ring-1 ring-white/20">4K</span>
                    </div>
                <?php elseif ( $meta['quality'] === 'HD' ) : ?>
                    <div class="absolute top-2 right-2 pointer-events-none">
                        <span class="glass-chip bg-primary/80 text-black font-label-mono-sm text-xs px-1 py-0.5 font-bold backdrop-blur-md ring-1 ring-white/20">HD</span>
                    </div>
                <?php endif; ?>
                <div class="mt-3">
                    <h3 title="<?php echo esc_attr( get_the_title() ); ?>" class="font-headline-md text-sm text-white group-hover:text-primary transition-colors normal-case tracking-normal leading-snug line-clamp-2 min-h-[2.6em] break-words"><?php the_title(); ?></h3>
                    <div class="flex items-center gap-3 mt-1.5">
                        <span class="font-label-mono-sm text-xs text-gold font-bold">IMDb <?php echo esc_html( $meta['rating'] ); ?></span>
                        <span class="font-label-mono-sm text-xs text-on-surface-variant/60"><?php echo esc_html( $meta['duration'] ); ?></span>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>

        <!-- Pagination -->
        <?php if ( $genre_query->max_num_pages > 1 ) : ?>
        <div class="flex flex-wrap justify-center items-center gap-3 md:gap-4 mt-12 md:mt-16">
            <?php
            $total_pages = $genre_query->max_num_pages;
            $base_url = get_term_link( $queried_object );

            if ( $paged > 1 ) :
                $prev_url = $paged - 1 > 1 ? trailingslashit( $base_url ) . 'page/' . ( $paged - 1 ) . '/' : $base_url;
            ?>
                <a href="<?php echo esc_url( $prev_url ); ?>" class="flex items-center gap-2 px-4 py-2 bg-white/5 border border-white/10 text-muted hover:text-white hover:border-primary hover:bg-white/10 transition-all font-label-mono-sm text-xs rounded">
                    <span class="material-symbols-outlined text-sm">chevron_left</span>
                    Previous
                </a>
            <?php else : ?>
                <span class="flex items-center gap-2 px-4 py-2 bg-white/5 border border-white/5 text-dim font-label-mono-sm text-xs cursor-not-allowed rounded">
                    <span class="material-symbols-outlined text-sm">chevron_left</span>
                    Previous
                </span>
            <?php endif; ?>

            <div class="flex items-center gap-2">
            <?php
            $start = max( 1, $paged - 2 );
            $end = min( $total_pages, $paged + 2 );

            if ( $start > 1 ) :
                $page1_url = $base_url;
            ?>
                <a href="<?php echo esc_url( $page1_url ); ?>" class="w-10 h-10 flex items-center justify-center bg-white/5 border border-white/10 text-muted hover:text-white hover:border-primary transition-all font-label-mono-sm text-xs rounded"><?php echo 1; ?></a>
                <?php if ( $start > 2 ) : ?>
                    <span class="text-dim font-label-mono-sm px-1">...</span>
                <?php endif; ?>
            <?php endif; ?>

            <?php for ( $i = $start; $i <= $end; $i++ ) : ?>
                <?php
                $page_url = $i > 1 ? trailingslashit( $base_url ) . 'page/' . $i . '/' : $base_url;
                ?>
                <?php if ( $i === $paged ) : ?>
                    <span class="w-10 h-10 flex items-center justify-center bg-primary text-black font-label-mono-sm text-xs font-bold shadow-[0_0_10px_rgba(229,9,20,0.3)] rounded"><?php echo $i; ?></span>
                <?php else : ?>
                    <a href="<?php echo esc_url( $page_url ); ?>" class="w-10 h-10 flex items-center justify-center bg-white/5 border border-white/10 text-muted hover:text-white hover:border-primary transition-all font-label-mono-sm text-xs rounded"><?php echo $i; ?></a>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if ( $end < $total_pages ) : ?>
                <?php if ( $end < $total_pages - 1 ) : ?>
                    <span class="text-dim font-label-mono-sm px-1">...</span>
                <?php endif; ?>
                <?php
                $last_url = trailingslashit( $base_url ) . 'page/' . $total_pages . '/';
                ?>
                <a href="<?php echo esc_url( $last_url ); ?>" class="w-10 h-10 flex items-center justify-center bg-white/5 border border-white/10 text-muted hover:text-white hover:border-primary transition-all font-label-mono-sm text-xs rounded"><?php echo $total_pages; ?></a>
            <?php endif; ?>
            </div>

            <?php if ( $paged < $total_pages ) :
                $next_url = trailingslashit( $base_url ) . 'page/' . ( $paged + 1 ) . '/';
            ?>
                <a href="<?php echo esc_url( $next_url ); ?>" class="flex items-center gap-2 px-4 py-2 bg-white/5 border border-white/10 text-muted hover:text-white hover:border-primary hover:bg-white/10 transition-all font-label-mono-sm text-xs rounded">
                    Next
                    <span class="material-symbols-outlined text-sm">chevron_right</span>
                </a>
            <?php else : ?>
                <span class="flex items-center gap-2 px-4 py-2 bg-white/5 border border-white/5 text-dim font-label-mono-sm text-xs cursor-not-allowed rounded">
                    Next
                    <span class="material-symbols-outlined text-sm">chevron_right</span>
                </span>
            <?php endif; ?>
        </div>
        <?php endif;
        wp_reset_postdata();
        ?>
        <?php else : ?>
        <div class="text-center py-20">
            <span class="material-symbols-outlined text-6xl text-dim mb-6 block">search_off</span>
            <h2 class="font-headline-md text-headline-md text-white mb-4">NO CONTENT FOUND</h2>
            <p class="font-label-mono-sm text-on-surface-variant/60 tracking-widest uppercase">
                No movies found in this category.
            </p>
        </div>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>
