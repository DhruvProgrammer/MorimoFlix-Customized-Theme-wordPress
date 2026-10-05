<?php
/*
Template Name: A-Z Library
*/

get_header();

// Performance fix for audit slow-response 4859ms: cache grouped IDs 12h, no change to poster size/grid
// SAFE: MySQL orderby title ASC already sorted — no PHP usort (removed).
// Thundering-herd guard: 60s lock so concurrent cache misses don't all rebuild.
$grouped = get_transient('morimoflix_list_grouped');
if (false === $grouped) {
    if (false !== get_transient('morimoflix_list_grouped_lock')) {
        $grouped = array();
    } else {
        set_transient('morimoflix_list_grouped_lock', 1, 60);
        $all_movies = new WP_Query(array(
            'post_type'      => array('movie', 'tv'),
            'posts_per_page' => -1,
            'orderby'        => 'title',
            'order'          => 'ASC',
            'post_status'    => 'publish',
            'no_found_rows'  => true,
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false,
            'fields'         => 'ids',
        ));
        $grouped = array();
        if (!empty($all_movies->posts)) {
            foreach ($all_movies->posts as $pid) {
                $title = get_the_title($pid);
                $first_char = strtoupper(substr($title, 0, 1));
                if (preg_match('/[0-9]/', $first_char)) {
                    $grouped['#'][] = $pid;
                } elseif (preg_match('/[A-Z]/', $first_char)) {
                    $grouped[$first_char][] = $pid;
                }
            }
            // MySQL orderby title ASC already sorted — no PHP usort needed.
        }
        ksort($grouped);
        set_transient('morimoflix_list_grouped', $grouped, 12 * HOUR_IN_SECONDS);
        delete_transient('morimoflix_list_grouped_lock');
    }
}

// BeyondSEO P1 (Oversized A-Z listing): paginate by letter groups so no
// single response ever carries the whole library. 6 letters/page keeps
// output far under the 500 KB acceptance bar even as titles grow.
$list_groups_per_page = 6;
$list_letters = array_keys($grouped);
$list_total_pages = max(1, (int) ceil(count($list_letters) / $list_groups_per_page));
$list_page = isset($_GET['lpage']) ? absint($_GET['lpage']) : 1;
if ($list_page < 1) $list_page = 1;
if ($list_page > $list_total_pages) $list_page = $list_total_pages;
$list_paged_keys = array_slice($list_letters, ($list_page - 1) * $list_groups_per_page, $list_groups_per_page);
$list_paged = array();
foreach ($list_paged_keys as $lk) {
    $list_paged[$lk] = $grouped[$lk];
}
$list_page_url = get_permalink(get_queried_object_id());
if (!$list_page_url) $list_page_url = home_url('/list/');
?>

<style>
/* 350px overflow fix — A-Z Library: box-sizing, grid minmax, alphabet nav wrap */
html,body{overflow-x:hidden!important;max-width:100%}
*,*::before,*::after{box-sizing:border-box}
.max-w-container-max,.grid,.glass,.glass-card,.poster-aspect{box-sizing:border-box;max-width:100%;min-width:0}
.movie-grid{grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}
.movie-grid > *{min-width:0;max-width:100%}
.poster-aspect{aspect-ratio:2/3}
.glass.flex.flex-wrap{gap:8px}
@media(max-width:640px){.movie-grid{grid-template-columns:repeat(2,minmax(0,1fr))!important;gap:12px!important}}
/* Force desktop poster size — same as front-page */
@media(min-width:641px){.movie-grid{grid-template-columns:repeat(3,minmax(0,1fr))!important;gap:10px!important}}
@media(min-width:768px){.movie-grid{grid-template-columns:repeat(5,minmax(0,1fr))!important;gap:12px!important}}
@media(min-width:1024px){.movie-grid{grid-template-columns:repeat(6,minmax(0,1fr))!important;gap:14px!important}}
@media(min-width:1280px){.movie-grid{grid-template-columns:repeat(6,minmax(0,1fr))!important;gap:14px!important}}
@media(min-width:1536px){.movie-grid{grid-template-columns:repeat(7,minmax(0,1fr))!important;gap:14px!important}}
@media(max-width:375px){
    .max-w-container-max.mx-auto.px-5{padding-left:14px!important;padding-right:14px!important}
    .movie-grid{gap:10px!important}
    .glass.flex.flex-wrap{gap:6px!important}
    .glass.flex.flex-wrap a{width:32px!important;height:32px!important}
}
@media(max-width:350px){
    .movie-grid{gap:10px!important}
    .glass.flex.flex-wrap a{width:30px!important;height:30px!important;font-size:11px!important}
}
</style>
<div class="max-w-container-max mx-auto px-5 md:px-margin-desktop py-8 md:py-12">
    <h1 class="font-headline-lg text-3xl md:text-5xl text-white mb-8 uppercase tracking-tighter font-bold">A-Z Library</h1>

    <!-- Alphabet Nav -->
    <div class="glass flex flex-wrap gap-2 mb-10 sticky top-16 md:top-20 z-30 bg-[#0A0505]/70 backdrop-blur-md py-3 border-b border-white/10">
        <?php
        $alpha = array_merge(array('#'), range('A', 'Z'));
        foreach ($alpha as $letter) :
            $has_movies = isset($grouped[$letter]) && !empty($grouped[$letter]);
            $letter_href = '#letter-' . $letter;
            if ($has_movies) {
                $li = array_search($letter, $list_letters, true);
                $lp = (int) floor($li / $list_groups_per_page) + 1;
                if ($lp !== $list_page) {
                    $letter_href = esc_url(add_query_arg('lpage', $lp, $list_page_url)) . '#letter-' . $letter;
                } else {
                    $letter_href = '#letter-' . $letter;
                }
            }
        ?>
            <a href="<?php echo $has_movies ? esc_url($letter_href) : '#letter-' . esc_attr($letter); ?>"
               class="w-8 h-8 md:w-10 md:h-10 flex items-center justify-center font-label-mono-sm text-xs border border-white/10 rounded transition-all <?php echo $has_movies ? 'text-primary hover:bg-primary/10 hover:border-primary' : 'text-dim cursor-default border-neutral-800'; ?>">
                <?php echo esc_html($letter); ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Movie Groups (paginated: 6 letters per ?lpage) -->
    <?php foreach ($list_paged as $letter => $movie_ids) : ?>
        <section id="letter-<?php echo esc_attr($letter); ?>" class="mb-10 scroll-mt-32" aria-labelledby="heading-letter-<?php echo esc_attr($letter); ?>">
            <div class="flex items-center gap-3 mb-5">
                <h2 id="heading-letter-<?php echo esc_attr($letter); ?>" class="w-10 h-10 flex items-center justify-center bg-primary text-black font-label-mono-md text-sm font-bold rounded"><?php echo esc_html($letter); ?></h2>
                <div class="flex-grow h-[1px] bg-white/10"></div>
                <span class="font-label-mono-sm text-xs text-muted uppercase tracking-wider"><?php echo count($movie_ids); ?> title<?php echo count($movie_ids) !== 1 ? 's' : ''; ?></span>
            </div>
            <div class="movie-grid grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 lg:grid-cols-6 xl:grid-cols-7 2xl:grid-cols-8 gap-3 md:gap-4">
                <?php foreach ($movie_ids as $post_id) :
                    $meta = morimoflix_get_movie_meta($post_id);
                    $post_type = get_post_type($post_id);
                ?>
                    <div class="group cursor-pointer relative">
                        <a href="<?php echo esc_url(get_permalink($post_id)); ?>" class="block" aria-label="<?php echo esc_attr( morimoflix_card_label($post_id) ); ?>">
                            <div class="glass-card relative poster-aspect overflow-hidden bg-surface-container border border-white/10 group-hover:border-primary transition-all duration-500 group-hover:shadow-[0_0_25px_rgba(255,0,0,0.4)] rounded-lg">
                                <?php if (has_post_thumbnail($post_id)) : ?>
                                    <?php echo get_the_post_thumbnail($post_id, 'medium', array('class' => 'w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 brightness-90 group-hover:brightness-110', 'alt' => morimoflix_poster_alt($post_id), 'loading' => 'lazy', 'decoding' => 'async')); ?>
                                <?php else : ?>
                                    <div class="w-full h-full bg-gradient-to-br from-surface to-black flex items-center justify-center p-4 text-center opacity-40 group-hover:opacity-80 transition-opacity">
                                        <span class="font-label-mono-sm text-xs tracking-widest text-primary uppercase"><?php esc_html_e('No Poster', 'morimoflix-flicker'); ?></span>
                                    </div>
                                <?php endif; ?>
                                <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-transparent opacity-60"></div>
                            </div>
                        </a>
                        <div class="absolute top-2 left-2 pointer-events-none">
                            <span class="glass-chip bg-surface/80 backdrop-blur-md text-on-surface font-label-mono-sm text-xs px-2 py-0.5 border border-white/10 uppercase rounded">
                                <?php echo esc_html($post_type === 'tv' ? __('Show', 'morimoflix-flicker') : __('Movie', 'morimoflix-flicker')); ?>
                            </span>
                        </div>
                        <?php if ($meta['quality'] === '4K') : ?>
                            <div class="absolute top-2 right-2 pointer-events-none">
                                <span class="glass-chip bg-secondary text-black font-label-mono-sm text-xs px-1 py-0.5 font-bold rounded backdrop-blur-md ring-1 ring-white/20"><?php esc_html_e('4K', 'morimoflix-flicker'); ?></span>
                            </div>
                        <?php elseif ($meta['quality'] === 'HD') : ?>
                            <div class="absolute top-2 right-2 pointer-events-none">
                                <span class="glass-chip bg-primary/80 text-black font-label-mono-sm text-xs px-1 py-0.5 font-bold rounded backdrop-blur-md ring-1 ring-white/20"><?php esc_html_e('HD', 'morimoflix-flicker'); ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="mt-2 md:mt-3">
                            <h3 title="<?php echo esc_attr( get_the_title( $post_id ) ); ?>" class="font-headline-md text-sm text-white group-hover:text-primary transition-colors normal-case tracking-normal leading-snug line-clamp-2 min-h-[2.6em] break-words"><?php echo esc_html(get_the_title($post_id)); ?></h3>
                            <div class="flex items-center gap-2 md:gap-3 mt-1">
                                <span class="font-label-mono-sm text-xs text-gold font-bold"><?php echo esc_html($meta['year']); ?></span>
                                <span class="font-label-mono-sm text-xs text-on-surface-variant/60">⭐ <?php echo esc_html($meta['rating']); ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endforeach; ?>

    <?php if (empty($grouped)) : ?>
        <div class="text-center py-20">
            <span class="material-symbols-outlined text-6xl text-dim mb-6 block">video_library</span>
            <h2 class="font-headline-md text-headline-md text-white mb-4"><?php esc_html_e('NO CONTENT FOUND', 'morimoflix-flicker'); ?></h2>
            <p class="font-label-mono-sm text-on-surface-variant/60 tracking-widest uppercase"><?php esc_html_e('Import movies to see them here', 'morimoflix-flicker'); ?></p>
        </div>
    <?php endif; ?>
</div>

    <!-- A-Z Pagination (?lpage) -->
    <?php if ($list_total_pages > 1) : ?>
    <nav class="flex flex-wrap justify-center items-center gap-3 md:gap-4 mt-4 mb-2" aria-label="<?php esc_attr_e('A-Z pages', 'morimoflix-flicker'); ?>">
        <?php if ($list_page > 1) : ?>
            <a href="<?php echo esc_url($list_page - 1 > 1 ? add_query_arg('lpage', $list_page - 1, $list_page_url) : remove_query_arg('lpage', $list_page_url)); ?>" class="flex items-center gap-2 px-4 py-2 bg-white/5 border border-white/10 text-muted hover:text-white hover:border-primary hover:bg-white/10 transition-all font-label-mono-sm text-xs rounded">
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
        <?php for ($i = 1; $i <= $list_total_pages; $i++) : ?>
            <?php if ($i === $list_page) : ?>
                <span class="w-10 h-10 flex items-center justify-center bg-primary text-black font-label-mono-sm text-xs font-bold shadow-[0_0_10px_rgba(229,9,20,0.3)] rounded"><?php echo $i; ?></span>
            <?php else : ?>
                <a href="<?php echo esc_url($i > 1 ? add_query_arg('lpage', $i, $list_page_url) : remove_query_arg('lpage', $list_page_url)); ?>" class="w-10 h-10 flex items-center justify-center bg-white/5 border border-white/10 text-muted hover:text-white hover:border-primary transition-all font-label-mono-sm text-xs rounded"><?php echo $i; ?></a>
            <?php endif; ?>
        <?php endfor; ?>
        </div>
        <?php if ($list_page < $list_total_pages) : ?>
            <a href="<?php echo esc_url(add_query_arg('lpage', $list_page + 1, $list_page_url)); ?>" class="flex items-center gap-2 px-4 py-2 bg-white/5 border border-white/10 text-muted hover:text-white hover:border-primary hover:bg-white/10 transition-all font-label-mono-sm text-xs rounded">
                Next
                <span class="material-symbols-outlined text-sm">chevron_right</span>
            </a>
        <?php else : ?>
            <span class="flex items-center gap-2 px-4 py-2 bg-white/5 border border-white/5 text-dim font-label-mono-sm text-xs cursor-not-allowed rounded">
                Next
                <span class="material-symbols-outlined text-sm">chevron_right</span>
            </span>
        <?php endif; ?>
    </nav>
    <?php endif; ?>

<div id="backToTop" style="bottom:calc(5rem + env(safe-area-inset-bottom))" class="fixed bottom-20 right-6 z-50 opacity-0 pointer-events-none transition-opacity duration-300">
    <a href="#" class="flex items-center justify-center w-10 h-10 bg-primary text-black rounded-full shadow-[0_0_15px_rgba(229,9,20,0.4)] hover:scale-110 transition-transform" title="Scroll to Top" aria-label="Scroll to top">
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
