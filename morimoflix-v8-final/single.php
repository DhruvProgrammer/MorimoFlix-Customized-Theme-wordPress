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
                <div class="flex gap-4 font-label-mono-sm text-xs text-on-surface-variant/60 mb-4 tracking-widest uppercase">
                    <span><?php echo get_the_date(); ?></span>
                    <span>by <?php the_author(); ?></span>
                </div>
                <h1 class="font-display-lg text-4xl md:text-6xl text-white mb-8 normal-case tracking-tight font-black"><?php the_title(); ?></h1>
                <?php if ( has_post_thumbnail() ) : ?>
                    <div class="rounded overflow-hidden border border-white/5">
                        <?php the_post_thumbnail( 'large', array( 'loading' => 'lazy', 'decoding' => 'async', 'alt' => the_title_attribute( array( 'echo' => false ) ) ) ); ?>
                    </div>
                <?php endif; ?>
            </header>
            <div class="font-body-md text-on-surface-variant max-w-3xl leading-relaxed opacity-80">
                <?php the_content(); ?>
                <?php $wc = str_word_count(wp_strip_all_tags(get_the_content('', false, get_the_ID()), true)); if ($wc < 180) : ?>
                <div class="mt-8 p-5 rounded-xl border border-white/10 bg-white/[0.03]">
                    <h2 class="font-display-hero text-lg font-bold text-white mb-3">About this story</h2>
                    <p class="text-sm text-muted leading-relaxed">Explore more cool movies, south movie hindi dubbed & Telugu movies on FlixGarden — new south movie hindi dubbed 2025-2026, Hindi dubbed movies & new south movies in HD. Browse by genre or the A–Z Library to find any title fast.</p>
                </div>
                <?php endif; ?>
            </div>
            <h2 class="sr-only">More to explore</h2>
            <div class="mt-12 flex flex-wrap gap-3">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="px-4 py-2 rounded-full border border-white/10 bg-white/5 text-sm text-muted hover:border-[#E50914]/50">Home</a>
                <a href="<?php echo esc_url(home_url('/list/')); ?>" class="px-4 py-2 rounded-full border border-white/10 bg-white/5 text-sm text-muted hover:border-[#E50914]/50">A–Z Library</a>
                <a href="<?php echo esc_url(get_post_type_archive_link('movie')); ?>" class="px-4 py-2 rounded-full border border-white/10 bg-white/5 text-sm text-muted hover:border-[#E50914]/50">Movies</a>
            </div>
        </article>
        <?php endwhile; endif; ?>
    </div>
</main>

<?php get_footer(); ?>
