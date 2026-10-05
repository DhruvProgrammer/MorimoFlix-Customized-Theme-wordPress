<?php
/**
 * Archive Template
 *
 * @package MorimoFlix
 */

get_header();
?>
<style>
/* 350px overflow fix — archive grid: minmax + 375/350 padding */
html,body{overflow-x:hidden!important;max-width:100%}
*,*::before,*::after{box-sizing:border-box}
.max-w-container-max,.grid,.glass-card,.poster-aspect{box-sizing:border-box;max-width:100%;min-width:0}
.archive-posts-grid{grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}
.archive-posts-grid > *{min-width:0;max-width:100%}
.poster-aspect{aspect-ratio:2/3}
@media(max-width:640px){.archive-posts-grid{grid-template-columns:repeat(2,minmax(0,1fr))!important;gap:12px!important}}
/* Force desktop poster size — same as front-page */
@media(min-width:641px){.archive-posts-grid{grid-template-columns:repeat(3,minmax(0,1fr))!important;gap:10px!important}}
@media(min-width:768px){.archive-posts-grid{grid-template-columns:repeat(5,minmax(0,1fr))!important;gap:12px!important}}
@media(min-width:1024px){.archive-posts-grid{grid-template-columns:repeat(6,minmax(0,1fr))!important;gap:14px!important}}
@media(min-width:1280px){.archive-posts-grid{grid-template-columns:repeat(6,minmax(0,1fr))!important;gap:14px!important}}
@media(min-width:1536px){.archive-posts-grid{grid-template-columns:repeat(7,minmax(0,1fr))!important;gap:14px!important}}
@media(max-width:375px){
    .max-w-container-max.mx-auto.px-5{padding-left:14px!important;padding-right:14px!important}
    .nav-links{gap:6px!important}
    .nav-links .page-numbers{min-width:36px!important;height:36px!important;padding:0 8px!important}
}
@media(max-width:350px){
    .archive-posts-grid{gap:10px!important}
    .nav-links{gap:6px!important}
    .nav-links .page-numbers{min-width:34px!important;height:34px!important;padding:0 7px!important}
}
</style>
<main class="pt-20">
    <div class="max-w-container-max mx-auto px-5 md:px-margin-desktop py-12">
        <div class="flex items-center gap-4 mb-8">
            <div class="glitch-bar h-10"></div>
            <div>
                <h1 class="font-headline-lg text-3xl tracking-tighter uppercase font-bold text-white"><?php the_archive_title(); ?></h1>
                <?php if ( $term_description = term_description() ) : ?>
                    <p class="font-label-mono-sm text-on-surface-variant/60 tracking-widest uppercase mt-2"><?php echo wp_kses_post( $term_description ); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <h2 class="sr-only"><?php esc_html_e( 'Titles in this section', 'morimoflix-flicker' ); ?></h2>
        <?php
        // Thin-content fix: server-rendered intro (~180 words) using high-volume relevant keys: south movie hindi dubbed, telugu movies, hindi dubbed movies
        $arc_title = morimoflix_archive_title_plain();
        $arc_desc = get_the_archive_description();
        ?>
        <div class="mb-8 rounded-xl border border-white/10 bg-white/[0.03] backdrop-blur-md p-5 md:p-6">
            <h2 class="font-display-hero text-lg md:text-xl font-extrabold tracking-tight text-white mb-3"><?php echo esc_html( $arc_title ); ?> — South Movie Hindi Dubbed & Telugu HD</h2>
            <?php if ( $arc_desc ) : ?>
                <div class="font-body-md text-sm leading-relaxed text-[#A0A0AA] mb-3"><?php echo wp_kses_post( $arc_desc ); ?></div>
            <?php endif; ?>
            <p class="font-body-md text-sm leading-relaxed text-[#A0A0AA]">Browse current <?php echo esc_html( strtolower( $arc_title ) ); ?> listings on FlixGarden, including ratings, year, runtime, language, and quality. Use the A–Z Library or search to find a specific title.</p>
        </div>
        <?php if ( have_posts() ) : ?>
        <div class="archive-posts-grid grid grid-cols-2 md:grid-cols-5 lg:grid-cols-6 xl:grid-cols-7 2xl:grid-cols-8 gap-3 md:gap-4">
            <?php while ( have_posts() ) : the_post();
                $meta = morimoflix_get_movie_meta();
                $post_type = get_post_type();
            ?>
            <div class="group cursor-pointer relative">
                <a href="<?php the_permalink(); ?>" class="block" aria-label="<?php echo esc_attr( morimoflix_card_label() ); ?>">
                    <div class="glass-card relative poster-aspect overflow-hidden bg-surface-container border border-white/10 group-hover:border-primary transition-all duration-500 group-hover:shadow-[0_0_25px_rgba(255,0,0,0.4)] rounded-lg">
                        <?php if ( has_post_thumbnail() ) : ?>
                            <?php the_post_thumbnail( 'medium', array( 'class' => 'w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 brightness-90 group-hover:brightness-110', 'alt' => morimoflix_poster_alt(), 'loading' => 'lazy', 'decoding' => 'async' ) ); ?>
                        <?php else : ?>
                            <div class="w-full h-full bg-gradient-to-br from-surface to-black flex items-center justify-center p-6 text-center opacity-40 group-hover:opacity-80 transition-opacity">
                                <span class="font-label-mono-sm text-xs tracking-widest text-primary uppercase"><?php esc_html_e( 'No Poster', 'morimoflix-flicker' ); ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-transparent opacity-60"></div>
                    </div>
                </a>
                <div class="mt-3">
                    <h3 title="<?php echo esc_attr( get_the_title() ); ?>" class="font-headline-md text-sm text-white group-hover:text-primary transition-colors normal-case tracking-normal leading-snug line-clamp-2 min-h-[2.6em] break-words"><?php the_title(); ?></h3>
                    <div class="flex items-center gap-3 mt-1.5">
                        <span class="font-label-mono-sm text-xs text-gold font-bold"><?php printf( esc_html__( 'IMDb %s', 'morimoflix-flicker' ), esc_html( $meta['rating'] ) ); ?></span>
                        <span class="font-label-mono-sm text-xs text-on-surface-variant/60"><?php echo esc_html( $meta['duration'] ); ?></span>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>

        <div class="mt-12 md:mt-16 flex justify-center">
            <?php the_posts_pagination( array( 'mid_size' => 2, 'prev_text' => __( 'Previous', 'morimoflix-flicker' ), 'next_text' => __( 'Next', 'morimoflix-flicker' ) ) ); ?>
        </div>

        <?php else : ?>
        <div class="text-center py-20">
            <span class="material-symbols-outlined text-6xl text-dim mb-6 block">search_off</span>
            <h2 class="font-headline-md text-headline-md text-white mb-4"><?php esc_html_e( 'No content found', 'morimoflix-flicker' ); ?></h2>
        </div>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>
