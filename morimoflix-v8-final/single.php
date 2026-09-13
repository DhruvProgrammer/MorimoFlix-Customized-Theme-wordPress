<?php
/**
 * Single Post Template
 */

get_header();
?>

<main class="pt-20">
    <div class="max-w-container-max mx-auto px-5 md:px-margin-desktop py-12">
        <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
        <article>
            <header class="mb-12">
                <div class="flex gap-4 font-label-mono-sm text-[11px] text-on-surface-variant/60 mb-4 tracking-widest uppercase">
                    <span><?php echo get_the_date(); ?></span>
                    <span>by <?php the_author(); ?></span>
                </div>
                <h1 class="font-display-lg text-4xl md:text-6xl text-white mb-8 uppercase tracking-tighter font-black"><?php the_title(); ?></h1>
                <?php if ( has_post_thumbnail() ) : ?>
                    <div class="rounded overflow-hidden border border-white/5">
                        <?php the_post_thumbnail( 'large' ); ?>
                    </div>
                <?php endif; ?>
            </header>
            <div class="font-body-md text-on-surface-variant max-w-3xl leading-relaxed opacity-80">
                <?php the_content(); ?>
            </div>
        </article>
        <?php endwhile; endif; ?>
    </div>
</main>

<?php get_footer(); ?>
