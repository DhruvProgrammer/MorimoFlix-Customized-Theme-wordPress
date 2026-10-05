<?php get_header(); ?>
<main class="pt-20">
    <div class="max-w-container-max mx-auto px-5 md:px-margin-desktop py-12">
        <?php while ( have_posts() ) : the_post(); ?>
        <article class="max-w-3xl mx-auto">
            <p class="font-label-mono-sm text-muted mb-4 uppercase tracking-widest"><?php echo esc_html( get_the_date() ); ?> · <?php esc_html_e( 'Blog', 'morimoflix-flicker' ); ?></p>
            <h1 class="font-headline-lg text-4xl md:text-5xl text-white mb-8"><?php the_title(); ?></h1>
            <?php if ( has_post_thumbnail() ) : ?>
                <div class="aspect-video mb-8 rounded-lg overflow-hidden border border-white/10">
                    <?php the_post_thumbnail( 'large', array( 'class' => 'w-full h-full object-cover', 'alt' => get_the_title() ) ); ?>
                </div>
            <?php endif; ?>
            <div class="font-body-md text-muted leading-relaxed prose prose-invert max-w-none">
                <?php the_content(); ?>
            </div>
        </article>
        <?php endwhile; ?>
    </div>
</main>
<?php get_footer();
