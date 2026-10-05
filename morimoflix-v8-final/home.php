<?php
/**
 * Blog posts archive.
 */
get_header();
?>
<main class="pt-20">
    <div class="max-w-container-max mx-auto px-5 md:px-margin-desktop py-12">
        <div class="flex items-center gap-4 mb-8">
            <div class="glitch-bar h-10"></div>
            <div>
                <h1 class="font-headline-lg text-3xl tracking-tighter uppercase font-bold text-white"><?php esc_html_e( 'Blog', 'morimoflix-flicker' ); ?></h1>
                <p class="font-label-mono-sm text-on-surface-variant/60 tracking-widest uppercase mt-2"><?php esc_html_e( 'Guides, news, reviews and updates', 'morimoflix-flicker' ); ?></p>
            </div>
        </div>

        <?php if ( have_posts() ) : ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            <?php while ( have_posts() ) : the_post(); ?>
            <article class="glass-panel bg-white/[0.03] border border-white/10 rounded-lg overflow-hidden">
                <a href="<?php the_permalink(); ?>" class="block">
                    <?php if ( has_post_thumbnail() ) : ?>
                        <div class="aspect-video bg-surface-container">
                            <?php the_post_thumbnail( 'large', array( 'class' => 'w-full h-full object-cover', 'alt' => get_the_title() ) ); ?>
                        </div>
                    <?php endif; ?>
                </a>
                <div class="p-4">
                    <p class="font-label-mono-sm text-xs text-muted mb-2"><?php echo esc_html( get_the_date() ); ?></p>
                    <h2 class="font-headline-md text-lg text-white mb-2">
                        <a href="<?php the_permalink(); ?>" class="hover:text-primary transition-colors"><?php the_title(); ?></a>
                    </h2>
                    <p class="text-muted text-sm line-clamp-3"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 24 ) ); ?></p>
                    <a href="<?php the_permalink(); ?>" class="inline-block mt-4 text-[#FFB4AB] text-xs uppercase tracking-widest"><?php esc_html_e( 'Read more', 'morimoflix-flicker' ); ?> →</a>
                </div>
            </article>
            <?php endwhile; ?>
        </div>
        <div class="mt-12 flex justify-center">
            <?php the_posts_pagination( array( 'mid_size' => 2, 'prev_text' => __( 'Previous', 'morimoflix-flicker' ), 'next_text' => __( 'Next', 'morimoflix-flicker' ) ) ); ?>
        </div>
        <?php else : ?>
        <div class="text-center py-20">
            <p class="text-muted"><?php esc_html_e( 'No blog posts yet. Add posts from WordPress Admin → Posts.', 'morimoflix-flicker' ); ?></p>
        </div>
        <?php endif; ?>
    </div>
</main>
<?php get_footer();
