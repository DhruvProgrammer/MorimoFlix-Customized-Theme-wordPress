<?php
/**
 * Single Movie Template
 *
 * @package MorimoFlix
 */

get_header();

if ( have_posts() ) :
    while ( have_posts() ) :
        the_post();
        $meta = morimoflix_get_movie_meta();
        $genres = get_the_terms( get_the_ID(), 'genre' );
        $download_links = get_post_meta( get_the_ID(), '_download_links', true );
?>

<main class="max-w-[1200px] mx-auto px-6 md:px-margin-desktop py-12 md:py-20">
    <!-- Main Content -->
    <div class="relative mb-16 md:mb-24">
        <!-- Blurred Background Image -->
        <?php if ( has_post_thumbnail() ) : ?>
            <div class="absolute inset-0 z-0 overflow-hidden rounded-xl" aria-hidden="true">
                <?php the_post_thumbnail( 'large', array( 'class' => 'w-full h-full object-cover blur-2xl opacity-20 scale-110', 'alt' => '', 'loading' => 'lazy', 'decoding' => 'async' ) ); ?>
            </div>
            <div class="glass absolute inset-0 z-[1] overflow-hidden rounded-xl bg-black/40 backdrop-blur-[2px]" aria-hidden="true"></div>
        <?php endif; ?>
        
        <div class="relative z-10 grid grid-cols-1 lg:grid-cols-2 gap-8 md:gap-16 lg:gap-24 items-center py-8 md:py-12">
            <!-- Left: Hero Poster -->
            <div class="order-2 lg:order-1">
                <div class="poster-frame glass-card overflow-hidden mx-auto max-w-[400px] lg:max-w-none">
                    <?php if ( has_post_thumbnail() ) : ?>
                        <?php the_post_thumbnail( 'large', array( 'class' => 'w-full h-auto object-cover rounded-sm grayscale hover:grayscale-0 transition-all duration-1000 ease-in-out', 'alt' => morimoflix_poster_alt(), 'fetchpriority' => 'high', 'loading' => 'eager', 'decoding' => 'async' ) ); ?>
                    <?php else : ?>
                        <div class="w-full h-[500px] md:h-[650px] bg-gradient-to-br from-surface to-black rounded-sm"></div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Right: Content & Action -->
            <div class="order-1 lg:order-2 flex flex-col justify-center text-center lg:text-left">
                <!-- Genre Tags -->
                <div class="flex gap-3 mb-4 flex-wrap justify-center lg:justify-start">
                    <?php if ( $genres && ! is_wp_error( $genres ) ) : ?>
                        <?php foreach ( $genres as $genre ) : ?>
                            <span class="glass-chip text-[10px] tracking-[0.2em] text-cyan-400 uppercase bg-white/5 backdrop-blur-md border border-white/10 rounded-full px-3 py-1"><?php echo esc_html( $genre->name ); ?></span>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                
                <!-- Title -->
                <h1 class="font-headline text-4xl md:text-6xl lg:text-7xl font-bold tracking-tighter mb-4 leading-none">
                    <?php the_title(); ?>
                </h1>
                
                <!-- Tagline -->
                <?php if ( ! empty( $meta['tagline'] ) ) : ?>
                    <p class="font-body text-neutral-600 text-sm md:text-base italic mb-4"><?php echo esc_html( $meta['tagline'] ); ?></p>
                <?php endif; ?>
                
                <!-- Description -->
                <p class="font-body text-neutral-500 text-sm md:text-base leading-relaxed max-w-md mx-auto lg:mx-0 font-light mb-8">
                    <?php echo wp_trim_words( get_the_excerpt(), 50 ); ?>
                </p>
                
                <!-- Metadata -->
                <div class="flex items-center justify-center lg:justify-start gap-4 md:gap-6 flex-wrap mb-8">
                    <div class="glass-chip text-center bg-white/[0.04] backdrop-blur-md border border-white/10 rounded-lg px-3 py-2">
                        <p class="text-[10px] text-cyan-400 tracking-[0.2em] mb-1 uppercase"><?php esc_html_e( 'Rating', 'morimoflix-flicker' ); ?></p>
                        <p class="text-sm font-light text-white"><?php printf( esc_html__( '%s IMDb', 'morimoflix-flicker' ), esc_html( $meta['rating'] ) ); ?></p>
                    </div>
                    <div class="w-[1px] h-6 bg-neutral-800 hidden md:block"></div>
                    <div class="glass-chip text-center bg-white/[0.04] backdrop-blur-md border border-white/10 rounded-lg px-3 py-2">
                        <p class="text-[10px] text-cyan-400 tracking-[0.2em] mb-1 uppercase"><?php esc_html_e( 'Duration', 'morimoflix-flicker' ); ?></p>
                        <p class="text-sm font-light text-white"><?php echo esc_html( $meta['duration'] ); ?></p>
                    </div>
                    <div class="w-[1px] h-6 bg-neutral-800 hidden md:block"></div>
                    <div class="glass-chip text-center bg-white/[0.04] backdrop-blur-md border border-white/10 rounded-lg px-3 py-2">
                        <p class="text-[10px] text-cyan-400 tracking-[0.2em] mb-1 uppercase"><?php esc_html_e( 'Year', 'morimoflix-flicker' ); ?></p>
                        <p class="text-sm font-light text-white"><?php echo esc_html( $meta['year'] ); ?></p>
                    </div>
                    <div class="w-[1px] h-6 bg-neutral-800 hidden md:block"></div>
                    <div class="glass-chip text-center bg-white/[0.04] backdrop-blur-md border border-white/10 rounded-lg px-3 py-2">
                        <p class="text-[10px] text-cyan-400 tracking-[0.2em] mb-1 uppercase"><?php esc_html_e( 'Quality', 'morimoflix-flicker' ); ?></p>
                        <p class="text-sm font-light text-white"><?php echo esc_html( $meta['quality'] ); ?></p>
                    </div>
                    <?php if ( ! empty( $meta['language'] ) ) : ?>
                    <div class="w-[1px] h-6 bg-neutral-800 hidden md:block"></div>
                    <div class="glass-chip text-center bg-white/[0.04] backdrop-blur-md border border-white/10 rounded-lg px-3 py-2">
                        <p class="text-[10px] text-cyan-400 tracking-[0.2em] mb-1 uppercase"><?php esc_html_e( 'Language', 'morimoflix-flicker' ); ?></p>
                        <p class="text-sm font-light text-white"><?php echo esc_html( $meta['language'] ); ?></p>
                    </div>
                    <?php endif; ?>
                    <?php if ( ! empty( $meta['posted_by'] ) ) : ?>
                    <div class="w-[1px] h-6 bg-neutral-800 hidden md:block"></div>
                    <div class="glass-chip text-center bg-white/[0.04] backdrop-blur-md border border-white/10 rounded-lg px-3 py-2">
                        <p class="text-[10px] text-cyan-400 tracking-[0.2em] mb-1 uppercase"><?php esc_html_e( 'Posted By', 'morimoflix-flicker' ); ?></p>
                        <p class="text-sm font-light text-white"><a href="<?php echo esc_url( get_author_posts_url( get_post_field( 'post_author' ) ) ); ?>" rel="author" class="hover:text-[#FFB4AB] transition-colors"><?php echo esc_html( $meta['posted_by'] ); ?></a></p>
                    </div>
                    <?php endif; ?>
                    <div class="w-[1px] h-6 bg-neutral-800 hidden md:block"></div>
                    <div class="glass-chip text-center bg-white/[0.04] backdrop-blur-md border border-white/10 rounded-lg px-3 py-2">
                        <p class="text-[10px] text-cyan-400 tracking-[0.2em] mb-1 uppercase"><?php esc_html_e( 'Updated', 'morimoflix-flicker' ); ?></p>
                        <p class="text-sm font-light text-white"><time datetime="<?php echo esc_attr( get_the_modified_date( 'c' ) ); ?>"><?php echo esc_html( get_the_modified_date() ); ?></time></p>
                    </div>
                </div>
                
                <!-- Download Button -->
                <div class="flex justify-center lg:justify-start">
                    <a href="#download-section" class="glass btn-gradient inline-flex items-center gap-3 px-8 py-4 text-[10px] tracking-[0.4em] font-bold uppercase text-white rounded-full text-center border border-white/20 backdrop-blur-md">
                        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1; vertical-align: middle;">download</span>
                        <?php esc_html_e( 'Download Link', 'morimoflix-flicker' ); ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Description Section -->
    <section class="mb-16 md:mb-24">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-[10px] tracking-[0.5em] uppercase font-bold" style="color: #ff6600; text-shadow: 0 0 8px rgba(255,102,0,0.4);"><?php esc_html_e( 'DESCRIPTION', 'morimoflix-flicker' ); ?></h2>
            <div class="flex-grow mx-8 h-[1px]" style="background: linear-gradient(90deg, rgba(255,102,0,0.3), transparent);"></div>
        </div>
        <div class="max-w-3xl glass-panel bg-white/[0.03] backdrop-blur-md border border-white/10 rounded-xl p-5 md:p-8">
            <div class="font-body text-neutral-500 text-sm md:text-base leading-relaxed font-light">
                <?php the_content(); ?>
            </div>
            <?php $sm_genres = get_the_terms( get_the_ID(), 'genre' ); ?>
            <?php if ( $sm_genres && ! is_wp_error( $sm_genres ) ) : ?>
            <p class="font-body text-sm text-neutral-500 mt-6">
                <?php printf( esc_html__( 'Browse more %1$s in HD ·', 'morimoflix-flicker' ), '<a class="text-[#FFB4AB] hover:underline" href="' . esc_url( get_term_link( $sm_genres[0] ) ) . '">' . esc_html( $sm_genres[0]->name ) . ' movies</a>' ); ?>
                <a class="text-[#FFB4AB] hover:underline" href="<?php echo esc_url( home_url( '/list/' ) ); ?>"><?php esc_html_e( 'Full A–Z library', 'morimoflix-flicker' ); ?></a>
            </p>
            <?php endif; ?>
            <div class="glass-panel mt-6 rounded-lg border border-[#534343]/30 bg-[#1D0E0E]/50 backdrop-blur-md p-5 md:p-6">
                <h2 class="font-display-hero text-lg md:text-xl font-extrabold tracking-tight text-[#FFEAE9] mb-3"><?php echo esc_html( sprintf( '%s (%s) — Quick Facts', get_the_title(), $meta['year'] ) ); ?></h2>
                <?php $tg_autoseo_block = get_post_meta( get_the_ID(), '_tg_autoseo_block', true ); ?>
                <?php if ( ! empty( $tg_autoseo_block ) ) : ?>
                    <?php echo wp_kses_post( $tg_autoseo_block ); ?>
                <?php else : ?>
                    <?php $tg_qf_genre = ( $sm_genres && ! is_wp_error( $sm_genres ) && isset( $sm_genres[0] ) ) ? $sm_genres[0]->name : __( 'movie', 'morimoflix-flicker' ); ?>
                    <p class="font-body text-sm text-neutral-500 leading-relaxed"><?php printf( esc_html__( '%1$s (%2$s) is a %3$s %4$s film presented in %5$s. It is rated %6$s IMDb with a runtime of %7$s.', 'morimoflix-flicker' ), esc_html( get_the_title() ), esc_html( $meta['year'] ), esc_html( $meta['language'] ), esc_html( $tg_qf_genre ), esc_html( $meta['quality'] ), esc_html( $meta['rating'] ), esc_html( $meta['duration'] ) ); ?></p>
                    <p class="font-body text-sm text-neutral-500 mt-3">
                        <?php if ( $sm_genres && ! is_wp_error( $sm_genres ) ) : ?>
                            <?php printf( esc_html__( 'Browse more %1$s in HD ·', 'morimoflix-flicker' ), '<a class="text-[#FFB4AB] hover:underline" href="' . esc_url( get_term_link( $sm_genres[0] ) ) . '">' . esc_html( $sm_genres[0]->name ) . ' movies</a>' ); ?>
                        <?php endif; ?>
                        <a class="text-[#FFB4AB] hover:underline" href="<?php echo esc_url( home_url( '/list/' ) ); ?>"><?php esc_html_e( 'Full A–Z library', 'morimoflix-flicker' ); ?></a>
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </section>
    
    <!-- Download Links Section -->
    <section id="download-section" class="mb-16 md:mb-24">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-[10px] tracking-[0.5em] uppercase font-bold" style="color: #ff6600; text-shadow: 0 0 8px rgba(255,102,0,0.4);"><?php esc_html_e( 'DOWNLOAD LINKS', 'morimoflix-flicker' ); ?></h2>
            <div class="flex-grow mx-8 h-[1px]" style="background: linear-gradient(90deg, rgba(255,102,0,0.3), transparent);"></div>
        </div>
        <div class="max-w-3xl glass-panel bg-white/[0.03] backdrop-blur-md border border-white/10 rounded-xl p-5 md:p-8">
            <?php if ( ! empty( $download_links ) ) :
                $links = explode( "\n", $download_links );
                $quality_groups = array();
                
                foreach ( $links as $link ) :
                    $link = trim( $link );
                    if ( '' === $link ) continue;
                    
                    $parts = array_map( 'trim', explode( '|', $link ) );
                    $quality = $parts[0] ?? 'Download';
                    $url = $parts[1] ?? '';
                    $size = $parts[2] ?? '';
                    
                    // Validate URL
                    if ( empty( $url ) || ! filter_var( $url, FILTER_VALIDATE_URL ) ) {
                        continue;
                    }
                    
                    $quality_groups[] = array(
                        'quality' => $quality,
                        'url'     => $url,
                        'size'    => $size,
                    );
                endforeach;
                
                if ( ! empty( $quality_groups ) ) :
            ?>
                <!-- Quality Dropdown -->
                <div class="mb-6">
                    <label class="font-label-mono-md text-xs text-blue-400 uppercase tracking-[0.2em] mb-3 block font-bold"><?php esc_html_e( 'Select Quality', 'morimoflix-flicker' ); ?></label>
                    <div class="relative">
                        <select id="quality-selector" class="glass-input w-full bg-[#1a1a1a] backdrop-blur-md border-2 border-blue-500/60 text-white font-label-mono-md text-base md:text-lg px-5 py-4 rounded cursor-pointer focus:border-blue-400 focus:outline-none focus:shadow-[0_0_15px_rgba(0,102,255,0.3)] transition-all" onchange="document.getElementById('download-url').href=this.value">
                            <?php foreach ( $quality_groups as $index => $item ) : ?>
                                <option value="<?php echo esc_url( $item['url'] ); ?>" <?php echo $index === 0 ? 'selected' : ''; ?>>
                                    <?php echo esc_html( $item['quality'] ); ?><?php echo ! empty( $item['size'] ) ? ' — ' . esc_html( $item['size'] ) : ''; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-blue-400">expand_more</span>
                    </div>
                </div>
                
                <!-- Download Button -->
                <a id="download-url" href="<?php echo esc_url( $quality_groups[0]['url'] ); ?>" target="_blank" rel="noopener noreferrer" class="glass flex items-center justify-center gap-3 px-8 py-4 bg-gradient-to-r from-orange-500 to-pink-500 text-white font-label-mono-md text-sm font-bold uppercase rounded border border-white/20 backdrop-blur-md hover:opacity-90 transition-all shadow-[0_0_20px_rgba(255,102,0,0.3)]">
                    <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">download</span>
                    <?php esc_html_e( 'Download Now', 'morimoflix-flicker' ); ?>
                </a>
                <?php endif; ?>
            <?php else : ?>
                <p class="text-neutral-600 font-label-mono-sm text-sm"><?php esc_html_e( 'No valid download links available yet.', 'morimoflix-flicker' ); ?></p>
            <?php endif; ?>
        </div>
    </section>
    
    <!-- Related Movies Section -->
    <section class="mb-8">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-[10px] tracking-[0.5em] text-white uppercase bg-orange-500 px-4 py-2 font-bold"><?php esc_html_e( 'YOU ALSO MAY LIKE', 'morimoflix-flicker' ); ?></h2>
            <div class="flex-grow mx-8 h-[1px] bg-neutral-900"></div>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
            <?php
            $related = new WP_Query( array(
                'post_type'      => get_post_type(),
                'posts_per_page' => 4,
                'post__not_in'   => array( get_the_ID() ),
                'orderby'        => 'rand',
            ) );

            if ( $related->have_posts() ) :
                while ( $related->have_posts() ) :
                    $related->the_post();
                    $rmeta = morimoflix_get_movie_meta();
            ?>
            <div class="group cursor-pointer related-card block">
                <a href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr( morimoflix_card_label() ); ?>" class="block">
                    <div class="related-card-image glass-card overflow-hidden mb-3 rounded-lg border border-white/10 bg-white/[0.04] backdrop-blur-md group-hover:border-primary transition-all duration-500 relative" style="aspect-ratio: 2/3;">
                        <?php if ( has_post_thumbnail() ) : ?>
                            <?php the_post_thumbnail( 'medium', array( 'class' => 'w-full h-full object-cover transition-transform duration-500 group-hover:scale-110', 'alt' => morimoflix_poster_alt(), 'loading' => 'lazy', 'decoding' => 'async' ) ); ?>
                        <?php else : ?>
                            <div class="w-full h-full bg-gradient-to-br from-surface to-black"></div>
                        <?php endif; ?>
                    </div>
                </a>
                <h3 class="text-sm text-neutral-400 group-hover:text-white transition-colors truncate uppercase tracking-wider font-medium">
                    <a href="<?php the_permalink(); ?>"><?php echo esc_html( get_the_title() ); ?></a>
                </h3>
                <p class="text-[10px] text-neutral-600 mt-1"><span class="rating"><?php printf( esc_html__( '%s IMDb', 'morimoflix-flicker' ), esc_html( $rmeta['rating'] ) ); ?></span></p>
            </div>
            <?php
                endwhile;
                wp_reset_postdata();
            endif;
            ?>
        </div>
    </section>
</main>

<?php
    endwhile;
endif;

get_footer();
?>
