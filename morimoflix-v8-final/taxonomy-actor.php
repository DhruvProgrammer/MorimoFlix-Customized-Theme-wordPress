<?php
/**
 * Actor Taxonomy Template
 * Shows movies featuring a specific actor
 */

get_header();

$queried_object = get_queried_object();
$actor_name = single_term_title( '', false );
$paged = max( 1, get_query_var( 'paged' ) ? absint( get_query_var( 'paged' ) ) : 1 );
$posts_per_page = 18;

$actor_query = new WP_Query( array(
    'post_type'      => array( 'movie', 'tv' ),
    'posts_per_page' => $posts_per_page,
    'paged'          => $paged,
    'orderby'        => 'date',
    'order'          => 'DESC',
    'tax_query'      => array(
        array(
            'taxonomy' => $queried_object->taxonomy,
            'field'    => 'term_id',
            'terms'    => $queried_object->term_id,
        ),
    ),
) );
?>

<main class="pt-20">
    <div class="max-w-container-max mx-auto px-5 md:px-margin-desktop py-12">
        <!-- Actor Header with Back Button -->
        <div class="flex items-center gap-4 mb-8">
            <button onclick="history.back()" class="flex items-center justify-center w-10 h-10 bg-white/5 border border-white/10 rounded hover:border-primary hover:text-primary transition-all">
                <span class="material-symbols-outlined">arrow_back</span>
            </button>
            <div class="glitch-bar h-10"></div>
            <div>
                <h1 class="font-headline-lg text-3xl tracking-tighter uppercase font-bold text-white"><?php echo esc_html( $actor_name ); ?></h1>
                <p class="font-label-mono-sm text-on-surface-variant/60 tracking-widest uppercase mt-2">
                    <?php echo esc_html( $actor_query->found_posts ); ?> Movies Found
                </p>
            </div>
        </div>

        <!-- Movies Grid -->
        <?php if ( $actor_query->have_posts() ) : ?>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3 md:gap-6">
            <?php while ( $actor_query->have_posts() ) : $actor_query->the_post();
                $meta = morimoflix_get_movie_meta();
                $post_type = get_post_type();
            ?>
            <div class="group cursor-pointer relative">
                <a href="<?php the_permalink(); ?>" class="block" aria-label="<?php echo esc_attr( morimoflix_card_label() ); ?>">
                    <div class="glass-card relative poster-aspect overflow-hidden bg-surface-container border border-white/10 group-hover:border-primary transition-all duration-500 group-hover:shadow-[0_0_25px_rgba(0,243,255,0.2)]">
                        <?php if ( has_post_thumbnail() ) : ?>
                            <?php the_post_thumbnail( 'medium', array( 'class' => 'w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 brightness-90 group-hover:brightness-110', 'alt' => morimoflix_poster_alt(), 'loading' => 'lazy', 'decoding' => 'async' ) ); ?>
                        <?php else : ?>
                            <div class="w-full h-full bg-gradient-to-br from-surface to-black flex items-center justify-center p-6 text-center opacity-40 group-hover:opacity-80 transition-opacity">
                                <span class="font-label-mono-sm text-[10px] tracking-widest text-primary uppercase">No Poster</span>
                            </div>
                        <?php endif; ?>
                        <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-transparent opacity-60"></div>
                    </div>
                </a>
                <?php if ( $meta['quality'] === '4K' ) : ?>
                    <div class="absolute top-2 right-2 pointer-events-none">
                        <span class="glass-chip bg-secondary text-black font-label-mono-sm text-[9px] px-1 py-0.5 font-bold backdrop-blur-md ring-1 ring-white/20">4K</span>
                    </div>
                <?php elseif ( $meta['quality'] === 'HD' ) : ?>
                    <div class="absolute top-2 right-2 pointer-events-none">
                        <span class="glass-chip bg-primary/80 text-black font-label-mono-sm text-[9px] px-1 py-0.5 font-bold backdrop-blur-md ring-1 ring-white/20">HD</span>
                    </div>
                <?php endif; ?>
                <div class="mt-3">
                    <h3 class="font-headline-md text-sm text-white group-hover:text-primary transition-colors truncate uppercase tracking-tight"><?php the_title(); ?></h3>
                    <div class="flex items-center gap-3 mt-1.5">
                        <span class="font-label-mono-sm text-[10px] text-secondary font-bold">IMDb <?php echo esc_html( $meta['rating'] ); ?></span>
                        <span class="font-label-mono-sm text-[10px] text-on-surface-variant/60"><?php echo esc_html( $meta['duration'] ); ?></span>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>

        <!-- Pagination -->
        <?php if ( $actor_query->max_num_pages > 1 ) : ?>
        <div class="flex flex-wrap justify-center items-center gap-3 md:gap-4 mt-12 md:mt-16">
            <?php
            $total_pages = $actor_query->max_num_pages;
            $base_url = get_term_link( $queried_object );

            if ( $paged > 1 ) :
                $prev_url = $paged - 1 > 1 ? trailingslashit( $base_url ) . 'page/' . ( $paged - 1 ) . '/' : $base_url;
            ?>
                <a href="<?php echo esc_url( $prev_url ); ?>" class="flex items-center gap-2 px-4 py-2 bg-white/5 border border-white/10 text-neutral-400 hover:text-white hover:border-primary hover:bg-white/10 transition-all font-label-mono-sm text-xs rounded">
                    <span class="material-symbols-outlined text-sm">chevron_left</span>
                    Previous
                </a>
            <?php endif; ?>

            <div class="flex items-center gap-2">
            <?php
            $start = max( 1, $paged - 2 );
            $end = min( $total_pages, $paged + 2 );

            if ( $start > 1 ) :
                $page1_url = $base_url;
            ?>
                <a href="<?php echo esc_url( $page1_url ); ?>" class="w-10 h-10 flex items-center justify-center bg-white/5 border border-white/10 text-neutral-400 hover:text-white hover:border-primary transition-all font-label-mono-sm text-xs rounded"><?php echo 1; ?></a>
                <?php if ( $start > 2 ) : ?>
                    <span class="text-neutral-600 font-label-mono-sm px-1">...</span>
                <?php endif; ?>
            <?php endif; ?>

            <?php for ( $i = $start; $i <= $end; $i++ ) : ?>
                <?php
                $page_url = $i > 1 ? trailingslashit( $base_url ) . 'page/' . $i . '/' : $base_url;
                ?>
                <?php if ( $i === $paged ) : ?>
                    <span class="w-10 h-10 flex items-center justify-center bg-primary text-black font-label-mono-sm text-xs font-bold shadow-[0_0_10px_rgba(0,220,230,0.3)] rounded"><?php echo $i; ?></span>
                <?php else : ?>
                    <a href="<?php echo esc_url( $page_url ); ?>" class="w-10 h-10 flex items-center justify-center bg-white/5 border border-white/10 text-neutral-400 hover:text-white hover:border-primary transition-all font-label-mono-sm text-xs rounded"><?php echo $i; ?></a>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if ( $end < $total_pages ) : ?>
                <?php if ( $end < $total_pages - 1 ) : ?>
                    <span class="text-neutral-600 font-label-mono-sm px-1">...</span>
                <?php endif; ?>
                <?php
                $last_url = trailingslashit( $base_url ) . 'page/' . $total_pages . '/';
                ?>
                <a href="<?php echo esc_url( $last_url ); ?>" class="w-10 h-10 flex items-center justify-center bg-white/5 border border-white/10 text-neutral-400 hover:text-white hover:border-primary transition-all font-label-mono-sm text-xs rounded"><?php echo $total_pages; ?></a>
            <?php endif; ?>
            </div>

            <?php if ( $paged < $total_pages ) :
                $next_url = trailingslashit( $base_url ) . 'page/' . ( $paged + 1 ) . '/';
            ?>
                <a href="<?php echo esc_url( $next_url ); ?>" class="flex items-center gap-2 px-4 py-2 bg-white/5 border border-white/10 text-neutral-400 hover:text-white hover:border-primary hover:bg-white/10 transition-all font-label-mono-sm text-xs rounded">
                    Next
                    <span class="material-symbols-outlined text-sm">chevron_right</span>
                </a>
            <?php endif; ?>
        </div>
        <?php endif;
        wp_reset_postdata();
        ?>

        <?php else : ?>
        <div class="text-center py-20">
            <span class="material-symbols-outlined text-6xl text-neutral-700 mb-6 block">person_off</span>
            <h2 class="font-headline-md text-headline-md text-white mb-4">NO MOVIES FOUND</h2>
            <p class="font-label-mono-sm text-on-surface-variant/60 tracking-widest uppercase mb-8">
                No movies found for this actor.
            </p>
            <button onclick="history.back()" class="bg-primary text-black px-8 py-3 font-label-mono-md text-xs font-bold uppercase rounded hover:bg-primary-container transition-all">
                Go Back
            </button>
        </div>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>
