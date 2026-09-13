<?php
/**
 * Archive Template
 *
 * @package MorimoFlix
 */

get_header();
?>

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

        <?php if ( have_posts() ) : ?>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4 md:gap-6">
            <?php while ( have_posts() ) : the_post();
                $meta = morimoflix_get_movie_meta();
                $post_type = get_post_type();
            ?>
            <div class="group cursor-pointer relative">
                <a href="<?php the_permalink(); ?>" class="block" aria-label="<?php echo esc_attr( morimoflix_card_label() ); ?>">
                    <div class="glass-card relative poster-aspect overflow-hidden bg-surface-container border border-white/10 group-hover:border-primary transition-all duration-500 group-hover:shadow-[0_0_25px_rgba(0,243,255,0.2)] rounded-lg">
                        <?php if ( has_post_thumbnail() ) : ?>
                            <?php the_post_thumbnail( 'medium', array( 'class' => 'w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 brightness-90 group-hover:brightness-110', 'alt' => morimoflix_poster_alt(), 'loading' => 'lazy', 'decoding' => 'async' ) ); ?>
                        <?php else : ?>
                            <div class="w-full h-full bg-gradient-to-br from-surface to-black flex items-center justify-center p-6 text-center opacity-40 group-hover:opacity-80 transition-opacity">
                                <span class="font-label-mono-sm text-[10px] tracking-widest text-primary uppercase"><?php esc_html_e( 'No Poster', 'morimoflix-flicker' ); ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-transparent opacity-60"></div>
                    </div>
                </a>
                <div class="mt-3">
                    <h3 class="font-headline-md text-sm text-white group-hover:text-primary transition-colors truncate uppercase tracking-tight"><?php the_title(); ?></h3>
                    <div class="flex items-center gap-3 mt-1.5">
                        <span class="font-label-mono-sm text-[10px] text-secondary font-bold"><?php printf( esc_html__( 'IMDb %s', 'morimoflix-flicker' ), esc_html( $meta['rating'] ) ); ?></span>
                        <span class="font-label-mono-sm text-[10px] text-on-surface-variant/60"><?php echo esc_html( $meta['duration'] ); ?></span>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>

        <?php the_posts_pagination(); ?>

        <?php else : ?>
        <div class="text-center py-20">
            <span class="material-symbols-outlined text-6xl text-neutral-700 mb-6 block">search_off</span>
            <h2 class="font-headline-md text-headline-md text-white mb-4"><?php esc_html_e( 'No content found', 'morimoflix-flicker' ); ?></h2>
        </div>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>
