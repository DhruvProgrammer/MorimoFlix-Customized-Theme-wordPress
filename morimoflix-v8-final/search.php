<?php
/**
 * Search Results Template
 */

get_header();
?>
<style>
/* 350px overflow fix — search grid + pagination */
html,body{overflow-x:hidden!important;max-width:100%}
*,*::before,*::after{box-sizing:border-box}
.max-w-container-max,.grid,.glass-card,.poster-aspect{box-sizing:border-box;max-width:100%;min-width:0}
.movie-grid{grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}
.movie-grid > *{min-width:0;max-width:100%}
.poster-aspect{aspect-ratio:2/3}
@media(max-width:640px){.movie-grid.grid-cols-2{grid-template-columns:repeat(2,minmax(0,1fr))!important;gap:12px!important}}
/* Force desktop poster size — same as front-page */
@media(min-width:641px){.movie-grid.grid-cols-2{grid-template-columns:repeat(3,minmax(0,1fr))!important;gap:10px!important}}
@media(min-width:768px){.movie-grid.grid-cols-2{grid-template-columns:repeat(5,minmax(0,1fr))!important;gap:12px!important}}
@media(min-width:1024px){.movie-grid.grid-cols-2{grid-template-columns:repeat(6,minmax(0,1fr))!important;gap:14px!important}}
@media(min-width:1280px){.movie-grid.grid-cols-2{grid-template-columns:repeat(6,minmax(0,1fr))!important;gap:14px!important}}
@media(min-width:1536px){.movie-grid.grid-cols-2{grid-template-columns:repeat(7,minmax(0,1fr))!important;gap:14px!important}}
@media(max-width:375px){
    .max-w-container-max.mx-auto.px-5{padding-left:14px!important;padding-right:14px!important}
    .nav-links{gap:4px!important}
    .nav-links .page-numbers{min-width:36px!important;height:36px!important}
}
@media(max-width:350px){
    .movie-grid{gap:10px!important}
    .nav-links .page-numbers{min-width:34px!important;height:34px!important}
}
</style>
<main class="pt-20">
    <div class="max-w-container-max mx-auto px-5 md:px-margin-desktop py-12">
        <!-- Search Header -->
        <div class="mb-12">
            <div class="flex items-center gap-4 mb-6">
                <div class="glitch-bar h-10"></div>
                <h1 class="font-headline-lg text-3xl tracking-tighter uppercase font-bold text-white">Search Results</h1>
            </div>
            <p class="font-label-mono-sm text-on-surface-variant/60 tracking-widest uppercase">
                QUERY: "<?php echo esc_html( get_search_query() ); ?>"
            </p>
        </div>

        <?php if ( have_posts() ) : ?>
        <h2 class="sr-only"><?php esc_html_e( 'Search results', 'morimoflix-flicker' ); ?></h2>
        <!-- Results Grid -->
        <div class="movie-grid grid grid-cols-2 md:grid-cols-5 lg:grid-cols-6 xl:grid-cols-7 2xl:grid-cols-8 gap-3 md:gap-4">
            <?php while ( have_posts() ) : the_post();
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
                                <span class="font-label-mono-sm text-xs tracking-widest text-primary uppercase"><?php echo $post_type === 'movie' ? 'No_Poster' : 'Post'; ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-transparent opacity-60"></div>
                    </div>
                </a>
                <?php if ( $post_type === 'movie' && $meta['quality'] === '4K' ) : ?>
                    <div class="absolute top-2 right-2 flex flex-col gap-1 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
                        <span class="glass-chip bg-secondary text-black font-label-mono-sm text-xs px-1 font-bold backdrop-blur-md ring-1 ring-white/20">4K</span>
                    </div>
                <?php elseif ( $post_type === 'movie' && $meta['quality'] === 'HD' ) : ?>
                    <div class="absolute top-2 right-2 flex flex-col gap-1 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
                        <span class="glass-chip bg-primary/80 text-black font-label-mono-sm text-xs px-1 font-bold backdrop-blur-md ring-1 ring-white/20">HD</span>
                    </div>
                <?php endif; ?>
                <?php if ( $post_type !== 'movie' ) : ?>
                    <div class="absolute top-2 left-2 pointer-events-none">
                        <span class="glass-chip bg-primary/20 text-primary font-label-mono-sm text-xs px-2 py-1 uppercase backdrop-blur-md ring-1 ring-white/20"><?php echo esc_html( $post_type ); ?></span>
                    </div>
                <?php endif; ?>
                <div class="mt-3">
                    <h3 title="<?php echo esc_attr( get_the_title() ); ?>" class="font-headline-md text-sm text-white group-hover:text-primary transition-colors normal-case tracking-normal leading-snug line-clamp-2 min-h-[2.6em] break-words"><?php the_title(); ?></h3>
                    <?php if ( $post_type === 'movie' ) : ?>
                    <div class="flex items-center gap-3 mt-1.5">
                        <span class="font-label-mono-sm text-xs text-gold font-bold">IMDb <?php echo esc_html( $meta['rating'] ); ?></span>
                        <span class="font-label-mono-sm text-xs text-on-surface-variant/60"><?php echo esc_html( $meta['duration'] ); ?></span>
                    </div>
                    <?php else : ?>
                    <div class="flex items-center gap-3 mt-1.5">
                        <span class="font-label-mono-sm text-xs text-on-surface-variant/60"><?php echo get_the_date(); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endwhile; ?>
        </div>

        <!-- Pagination -->
        <div class="mt-16 flex justify-center">
            <?php
            the_posts_pagination( array(
                'mid_size'  => 2,
                'prev_text' => '<span class="material-symbols-outlined">chevron_left</span>',
                'next_text' => '<span class="material-symbols-outlined">chevron_right</span>',
            ) );
            ?>
        </div>

        <?php else : ?>
        <!-- No Results -->
        <div class="text-center py-20">
            <span class="material-symbols-outlined text-6xl text-dim mb-6 block">search_off</span>
            <h2 class="font-headline-md text-headline-md text-white mb-4">NO RESULTS FOUND</h2>
            <p class="font-label-mono-sm text-on-surface-variant/60 tracking-widest uppercase mb-8">
                QUERY "<?php echo esc_html( get_search_query() ); ?>" RETURNED NO MATCHES
            </p>
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn-gradient btn-primary inline-block px-8 py-3 text-xs tracking-[0.4em] font-bold uppercase text-white rounded-full">
                Return Home
            </a>
        </div>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>
