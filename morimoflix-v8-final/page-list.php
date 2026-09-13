<?php
/*
Template Name: A-Z Library
*/

get_header();

$all_movies = new WP_Query(array(
    'post_type'      => array('movie', 'tv'),
    'posts_per_page' => -1,
    'orderby'        => 'title',
    'order'          => 'ASC',
    'post_status'    => 'publish',
));

$grouped = array();
$letters = array('#');

if ($all_movies->have_posts()) {
    while ($all_movies->have_posts()) {
        $all_movies->the_post();
        $title = get_the_title();
        $first_char = strtoupper(substr($title, 0, 1));

        if (preg_match('/[0-9]/', $first_char)) {
            if (!isset($grouped['#'])) $grouped['#'] = array();
            $grouped['#'][] = get_the_ID();
        } elseif (preg_match('/[A-Z]/', $first_char)) {
            if (!isset($grouped[$first_char])) $grouped[$first_char] = array();
            $grouped[$first_char][] = get_the_ID();
        }
    }
    wp_reset_postdata();
}

ksort($grouped);
?>

<div class="max-w-container-max mx-auto px-5 md:px-margin-desktop py-8 md:py-12">
    <h1 class="font-headline-lg text-3xl md:text-5xl text-white mb-8 uppercase tracking-tighter font-bold">A-Z Library</h1>

    <!-- Alphabet Nav -->
    <div class="glass flex flex-wrap gap-2 mb-10 sticky top-16 md:top-20 z-30 bg-[#0A0505]/70 backdrop-blur-md py-3 border-b border-white/10">
        <?php
        $alpha = array_merge(array('#'), range('A', 'Z'));
        foreach ($alpha as $letter) :
            $has_movies = isset($grouped[$letter]) && !empty($grouped[$letter]);
        ?>
            <a href="#letter-<?php echo esc_attr($letter); ?>"
               class="w-8 h-8 md:w-10 md:h-10 flex items-center justify-center font-label-mono-sm text-[10px] md:text-xs border border-white/10 rounded transition-all <?php echo $has_movies ? 'text-primary hover:bg-primary/10 hover:border-primary' : 'text-neutral-600 cursor-default border-neutral-800'; ?>">
                <?php echo esc_html($letter); ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Movie Groups -->
    <?php foreach ($grouped as $letter => $movie_ids) : ?>
        <section id="letter-<?php echo esc_attr($letter); ?>" class="mb-10 scroll-mt-32">
            <div class="flex items-center gap-3 mb-5">
                <span class="w-10 h-10 flex items-center justify-center bg-primary text-black font-label-mono-md text-sm font-bold rounded"><?php echo esc_html($letter); ?></span>
                <div class="flex-grow h-[1px] bg-white/10"></div>
                <span class="font-label-mono-sm text-[10px] text-neutral-500 uppercase tracking-wider"><?php echo count($movie_ids); ?> title<?php echo count($movie_ids) !== 1 ? 's' : ''; ?></span>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3 md:gap-5">
                <?php foreach ($movie_ids as $post_id) :
                    $meta = morimoflix_get_movie_meta($post_id);
                    $post_type = get_post_type($post_id);
                ?>
                    <div class="group cursor-pointer relative">
                        <a href="<?php echo get_permalink($post_id); ?>" class="block" aria-label="<?php echo esc_attr( morimoflix_card_label($post_id) ); ?>">
                            <div class="glass-card relative poster-aspect overflow-hidden bg-surface-container border border-white/10 group-hover:border-primary transition-all duration-500 group-hover:shadow-[0_0_25px_rgba(0,243,255,0.2)] rounded-lg">
                                <?php if (has_post_thumbnail($post_id)) : ?>
                                    <?php echo get_the_post_thumbnail($post_id, 'medium', array('class' => 'w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 brightness-90 group-hover:brightness-110', 'alt' => morimoflix_poster_alt($post_id), 'loading' => 'lazy', 'decoding' => 'async')); ?>
                                <?php else : ?>
                                    <div class="w-full h-full bg-gradient-to-br from-surface to-black flex items-center justify-center p-4 text-center opacity-40 group-hover:opacity-80 transition-opacity">
                                        <span class="font-label-mono-sm text-[8px] md:text-[10px] tracking-widest text-primary uppercase"><?php esc_html_e('No Poster', 'morimoflix-flicker'); ?></span>
                                    </div>
                                <?php endif; ?>
                                <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-transparent opacity-60"></div>
                            </div>
                        </a>
                        <div class="absolute top-2 left-2 pointer-events-none">
                            <span class="glass-chip bg-surface/80 backdrop-blur-md text-on-surface font-label-mono-sm text-[8px] md:text-[9px] px-2 py-0.5 border border-white/10 uppercase rounded">
                                <?php echo esc_html($post_type === 'tv' ? __('Show', 'morimoflix-flicker') : __('Movie', 'morimoflix-flicker')); ?>
                            </span>
                        </div>
                        <?php if ($meta['quality'] === '4K') : ?>
                            <div class="absolute top-2 right-2 pointer-events-none">
                                <span class="glass-chip bg-secondary text-black font-label-mono-sm text-[8px] md:text-[9px] px-1 py-0.5 font-bold rounded backdrop-blur-md ring-1 ring-white/20"><?php esc_html_e('4K', 'morimoflix-flicker'); ?></span>
                            </div>
                        <?php elseif ($meta['quality'] === 'HD') : ?>
                            <div class="absolute top-2 right-2 pointer-events-none">
                                <span class="glass-chip bg-primary/80 text-black font-label-mono-sm text-[8px] md:text-[9px] px-1 py-0.5 font-bold rounded backdrop-blur-md ring-1 ring-white/20"><?php esc_html_e('HD', 'morimoflix-flicker'); ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="mt-2 md:mt-3">
                            <h3 class="font-headline-md text-[11px] md:text-sm text-white group-hover:text-primary transition-colors truncate uppercase tracking-tight"><?php echo esc_html(get_the_title($post_id)); ?></h3>
                            <div class="flex items-center gap-2 md:gap-3 mt-1">
                                <span class="font-label-mono-sm text-[9px] md:text-[10px] text-secondary font-bold"><?php echo esc_html($meta['year']); ?></span>
                                <span class="font-label-mono-sm text-[9px] md:text-[10px] text-on-surface-variant/60">⭐ <?php echo esc_html($meta['rating']); ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endforeach; ?>

    <?php if (empty($grouped)) : ?>
        <div class="text-center py-20">
            <span class="material-symbols-outlined text-6xl text-neutral-700 mb-6 block">video_library</span>
            <h2 class="font-headline-md text-headline-md text-white mb-4"><?php esc_html_e('NO CONTENT FOUND', 'morimoflix-flicker'); ?></h2>
            <p class="font-label-mono-sm text-on-surface-variant/60 tracking-widest uppercase"><?php esc_html_e('Import movies to see them here', 'morimoflix-flicker'); ?></p>
        </div>
    <?php endif; ?>
</div>

<div id="backToTop" class="fixed bottom-20 right-6 z-50 opacity-0 pointer-events-none transition-opacity duration-300">
    <a href="#" class="flex items-center justify-center w-10 h-10 bg-primary text-black rounded-full shadow-[0_0_15px_rgba(0,220,230,0.4)] hover:scale-110 transition-transform" title="Scroll to Top" aria-label="Scroll to top">
        <span class="material-symbols-outlined text-lg">keyboard_arrow_up</span>
    </a>
</div>

<script>
(function() {
    var backToTop = document.getElementById('backToTop');
    if (!backToTop) return;
    window.addEventListener('scroll', function() {
        if (window.scrollY > 500) {
            backToTop.classList.remove('opacity-0', 'pointer-events-none');
            backToTop.classList.add('opacity-100', 'pointer-events-auto');
        } else {
            backToTop.classList.add('opacity-0', 'pointer-events-none');
            backToTop.classList.remove('opacity-100', 'pointer-events-auto');
        }
    });
})();
</script>

<?php get_footer(); ?>
