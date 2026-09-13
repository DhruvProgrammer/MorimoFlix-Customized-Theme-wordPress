<?php
/**
 * Page Template
 *
 * @package MorimoFlix
 */

get_header();
?>

<main class="pt-20">
    <div class="max-w-container-max mx-auto px-5 md:px-margin-desktop py-12">
        <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
        <article>
            <h1 class="font-headline-lg text-4xl md:text-5xl text-white mb-8"><?php the_title(); ?></h1>
            <div class="font-body-md text-neutral-400 max-w-3xl leading-relaxed">
                <?php the_content(); ?>
            </div>
        </article>
        <?php endwhile; endif; ?>
    </div>
</main>

<?php get_footer(); ?>
