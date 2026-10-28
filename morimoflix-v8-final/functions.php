<?php
/**
 * MorimoFlix Theme Functions
 * 
 * @package MorimoFlix
 * @version 5.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'MORIMOFLIX_VERSION', '5.6.4' );
define( 'MORIMOFLIX_DIR', get_template_directory() );
define( 'MORIMOFLIX_URI', get_template_directory_uri() );

/**
 * Cloudflare Turnstile — configuration + helpers.
 *
 * Put these in wp-config.php (NOT in this file) so the secret never ships
 * inside the theme zip. Grab both keys from the Cloudflare dashboard:
 * Turnstile -> Add widget -> widget type "Managed".
 *
 *     define( 'MORIMOFLIX_TURNSTILE_SITE_KEY', '0x4AAAAAAA...' );
 *     define( 'MORIMOFLIX_TURNSTILE_SECRET',   '0x4AAAAAAA...' );
 *
 * BOTH constants must be set. Partial config is treated as "Turnstile off"
 * so the form degrades to its previous behaviour instead of rendering a
 * challenge whose token is never verified (see morimoflix_turnstile_notice).
 */
function morimoflix_turnstile_site_key() {
    if ( ! defined( 'MORIMOFLIX_TURNSTILE_SITE_KEY' ) ) {
        return '';
    }
    return is_string( MORIMOFLIX_TURNSTILE_SITE_KEY ) ? trim( MORIMOFLIX_TURNSTILE_SITE_KEY ) : '';
}

function morimoflix_turnstile_secret() {
    if ( ! defined( 'MORIMOFLIX_TURNSTILE_SECRET' ) ) {
        return '';
    }
    return is_string( MORIMOFLIX_TURNSTILE_SECRET ) ? trim( MORIMOFLIX_TURNSTILE_SECRET ) : '';
}

/**
 * Single source of truth. Every consumer (localize, render, verify, notice)
 * keys off this, so a half-configured install can never show a widget that
 * is not actually verified — the silent security hole found in review.
 */
function morimoflix_turnstile_enabled() {
    return morimoflix_turnstile_site_key() !== '' && morimoflix_turnstile_secret() !== '';
}

/**
 * Warn admins when exactly one constant is defined. Without this, the
 * site-key-only state ships a decorative CAPTCHA and the secret-only state
 * bricks the form for every visitor with no visible cause.
 */
function morimoflix_turnstile_notice() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    $site = morimoflix_turnstile_site_key();
    $sec  = morimoflix_turnstile_secret();

    if ( ( '' === $site ) === ( '' === $sec ) ) {
        return; // both set, or both absent
    }

    $missing = ( '' === $site ) ? 'MORIMOFLIX_TURNSTILE_SITE_KEY' : 'MORIMOFLIX_TURNSTILE_SECRET';

    echo '<div class="notice notice-error"><p><strong>Morimoflix: Cloudflare Turnstile is half-configured.</strong> '
        . esc_html( $missing )
        . ' is missing from wp-config.php. The request form is currently behaving as if Turnstile were disabled — '
        . 'define both constants, or remove both, so the two states cannot diverge.</p></div>';
}
add_action( 'admin_notices', 'morimoflix_turnstile_notice' );

/**
 * TGM Plugin Activation — bundled plugins (TGMovies SEO + Rank Math prompt).
 * NOTE: Intentional exception to the flat-structure rule — required by the
 * TGMovies SEO theme-bundling spec (PROMPT-build-tgmovies-seo-plugin.md §5).
 */
$morimoflix_tgmpa = MORIMOFLIX_DIR . '/inc/tgm/class-tgm-plugin-activation.php';
if ( file_exists( $morimoflix_tgmpa ) ) {
    require_once $morimoflix_tgmpa;
    add_action( 'tgmpa_register', 'morimoflix_register_required_plugins' );
}
function morimoflix_register_required_plugins() {
    if ( ! function_exists( 'tgmpa' ) ) {
        return;
    }
    $plugins = array(
        array(
            'name'     => 'TGMovies SEO',
            'slug'     => 'tgmovies-seo',
            'source'   => get_template_directory() . '/inc/plugins/tgmovies-seo.zip',
            'required' => true,
            'version'  => '0.1.0',
        ),
        array(
            'name'     => 'Rank Math SEO',
            'slug'     => 'seo-by-rank-math',
            'required' => true,
        ),
    );
    $config = array(
        'id'           => 'morimoflix',
        'menu'         => 'tgmpa-install-plugins',
        'has_notices'  => true,
        'dismissable'  => false,
        'is_automatic' => true,
    );
    tgmpa( $plugins, $config );
}

/**
 * Theme Setup
 */
function morimoflix_setup() {
    load_theme_textdomain( 'morimoflix-flicker', MORIMOFLIX_DIR . '/languages' );
    
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'automatic-feed-links' );
    add_theme_support( 'html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ) );
    add_theme_support( 'custom-logo', array(
        'height'      => 60,
        'width'       => 200,
        'flex-height' => true,
        'flex-width'  => true,
    ) );

    add_image_size( 'morimoflix-hero', 1920, 585, true );
    add_image_size( 'morimoflix-poster', 400, 600, true );
    add_image_size( 'morimoflix-poster-large', 800, 1200, true );
    add_image_size( 'morimoflix-thumbnail', 300, 170, true );
    add_image_size( 'morimoflix-og', 1200, 630, true );

    register_nav_menus( array(
        'primary'   => __( 'Primary Menu', 'morimoflix-flicker' ),
        'mobile'    => __( 'Mobile Menu', 'morimoflix-flicker' ),
        'footer'    => __( 'Footer Menu', 'morimoflix-flicker' ),
    ) );
}
add_action( 'after_setup_theme', 'morimoflix_setup' );

/**
 * Register Movie Custom Post Type
 */
function morimoflix_register_post_types() {
    // Movie Post Type
    register_post_type( 'movie', array(
        'labels' => array(
            'name'               => __( 'Movies', 'morimoflix-flicker' ),
            'singular_name'      => __( 'Movie', 'morimoflix-flicker' ),
            'add_new_item'       => __( 'Add New Movie', 'morimoflix-flicker' ),
            'edit_item'          => __( 'Edit Movie', 'morimoflix-flicker' ),
            'new_item'           => __( 'New Movie', 'morimoflix-flicker' ),
            'view_item'          => __( 'View Movie', 'morimoflix-flicker' ),
            'search_items'       => __( 'Search Movies', 'morimoflix-flicker' ),
            'not_found'          => __( 'No movies found', 'morimoflix-flicker' ),
            'not_found_in_trash' => __( 'No movies found in trash', 'morimoflix-flicker' ),
            'menu_name'          => __( 'Movies', 'morimoflix-flicker' ),
        ),
        'public'             => true,
        'has_archive'        => true,
        'rewrite'            => array( 'slug' => 'movie' ),
        'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt', 'comments' ),
        'menu_icon'          => 'dashicons-video-alt3',
        'show_in_rest'       => true,
        'capability_type'    => 'post',
        'hierarchical'       => false,
    ) );

    // TV Show Post Type
    register_post_type( 'tv', array(
        'labels' => array(
            'name'               => __( 'TV Shows', 'morimoflix-flicker' ),
            'singular_name'      => __( 'TV Show', 'morimoflix-flicker' ),
            'add_new_item'       => __( 'Add New TV Show', 'morimoflix-flicker' ),
            'edit_item'          => __( 'Edit TV Show', 'morimoflix-flicker' ),
            'new_item'           => __( 'New TV Show', 'morimoflix-flicker' ),
            'view_item'          => __( 'View TV Show', 'morimoflix-flicker' ),
            'search_items'       => __( 'Search TV Shows', 'morimoflix-flicker' ),
            'not_found'          => __( 'No TV shows found', 'morimoflix-flicker' ),
            'not_found_in_trash' => __( 'No TV shows found in trash', 'morimoflix-flicker' ),
            'menu_name'          => __( 'TV Shows', 'morimoflix-flicker' ),
        ),
        'public'             => true,
        'has_archive'        => true,
        'rewrite'            => array( 'slug' => 'tv' ),
        'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt', 'comments' ),
        'menu_icon'          => 'dashicons-desktop',
        'show_in_rest'       => true,
        'capability_type'    => 'post',
        'hierarchical'       => false,
    ) );

    // Blog Post Type
    register_post_type( 'blog', array(
        'labels' => array(
            'name'               => __( 'Blogs', 'morimoflix-flicker' ),
            'singular_name'      => __( 'Blog', 'morimoflix-flicker' ),
            'add_new_item'       => __( 'Add New Blog', 'morimoflix-flicker' ),
            'edit_item'          => __( 'Edit Blog', 'morimoflix-flicker' ),
            'new_item'           => __( 'New Blog', 'morimoflix-flicker' ),
            'view_item'          => __( 'View Blog', 'morimoflix-flicker' ),
            'search_items'       => __( 'Search Blogs', 'morimoflix-flicker' ),
            'not_found'          => __( 'No blog posts found', 'morimoflix-flicker' ),
            'not_found_in_trash' => __( 'No blog posts in trash', 'morimoflix-flicker' ),
            'menu_name'          => __( 'Blogs', 'morimoflix-flicker' ),
        ),
        'public'             => true,
        'has_archive'        => true,
        'rewrite'            => array( 'slug' => 'blog' ),
        'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt', 'comments' ),
        'menu_icon'          => 'dashicons-admin-post',
        'show_in_rest'       => true,
        'capability_type'    => 'post',
        'hierarchical'       => false,
    ) );
}
add_action( 'init', 'morimoflix_register_post_types' );

/**
 * Register Taxonomies
 */
function morimoflix_register_taxonomies() {
    // Genre Taxonomy (for movies and TV)
    register_taxonomy( 'genre', array( 'movie', 'tv' ), array(
        'labels' => array(
            'name'              => __( 'Genres', 'morimoflix-flicker' ),
            'singular_name'     => __( 'Genre', 'morimoflix-flicker' ),
            'search_items'      => __( 'Search Genres', 'morimoflix-flicker' ),
            'all_items'         => __( 'All Genres', 'morimoflix-flicker' ),
            'parent_item'       => __( 'Parent Genre', 'morimoflix-flicker' ),
            'edit_item'         => __( 'Edit Genre', 'morimoflix-flicker' ),
            'add_new_item'      => __( 'Add New Genre', 'morimoflix-flicker' ),
            'new_item_name'     => __( 'New Genre Name', 'morimoflix-flicker' ),
            'menu_name'         => __( 'Genres', 'morimoflix-flicker' ),
        ),
        'hierarchical'      => true,
        'public'            => true,
        'rewrite'           => array( 'slug' => 'genre' ),
        'show_in_rest'      => true,
        'show_admin_column' => true,
    ) );

    // Actor Taxonomy (for movies and TV)
    register_taxonomy( 'actor', array( 'movie', 'tv' ), array(
        'labels' => array(
            'name'              => __( 'Actors', 'morimoflix-flicker' ),
            'singular_name'     => __( 'Actor', 'morimoflix-flicker' ),
            'search_items'      => __( 'Search Actors', 'morimoflix-flicker' ),
            'all_items'         => __( 'All Actors', 'morimoflix-flicker' ),
            'edit_item'         => __( 'Edit Actor', 'morimoflix-flicker' ),
            'add_new_item'      => __( 'Add New Actor', 'morimoflix-flicker' ),
            'new_item_name'     => __( 'New Actor Name', 'morimoflix-flicker' ),
            'menu_name'         => __( 'Actors', 'morimoflix-flicker' ),
        ),
        'hierarchical'      => false,
        'public'            => true,
        'rewrite'           => array( 'slug' => 'actor' ),
        'show_in_rest'      => true,
        'show_admin_column' => true,
    ) );
}
add_action( 'init', 'morimoflix_register_taxonomies' );

/**
 * Flush rewrite rules on theme activation
 */
function morimoflix_rewrite_flush() {
    morimoflix_register_post_types();
    morimoflix_register_taxonomies();

    // Auto-create the List page if it doesn't exist
    $list_page = get_page_by_path('list');
    if (!$list_page) {
        wp_insert_post(array(
            'post_title'   => 'List',
            'post_name'    => 'list',
            'post_content' => '',
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'page_template' => 'page-list.php',
        ));
    } else {
        // Update existing page to use the correct template
        update_post_meta($list_page->ID, '_wp_page_template', 'page-list.php');
    }

    morimoflix_create_trust_pages();
    morimoflix_ensure_blog_page();

    // Set default TMDB API key
    if (!get_option('morimoflix_tmdb_api_key')) {
        update_option('morimoflix_tmdb_api_key', '');
    }

    flush_rewrite_rules();
}
add_action('after_switch_theme', 'morimoflix_rewrite_flush');

/**
 * Ensure legal/trust pages exist and have basic content.
 */
function morimoflix_create_trust_pages() {
    $trust_pages = array(
        'about'             => array( 'About Us', '<p>FlixGarden is a curated movie discovery platform. We index publicly available movie and TV information sourced from TMDB and provide user-requested releases where publicly available. We do not host files on this server.</p>' ),
        'contact'           => array( 'Contact', '<p>For corrections, removal requests, or general questions, contact us through the official Telegram channel: <a href="https://t.me/MorimoFlix_Zone">MorimoFlix Zone</a>.</p>' ),
        'editorial-policy'  => array( 'Editorial Policy', '<p>Listings are curated from TMDB metadata. We review titles, genres, ratings, language, quality, and availability where possible, but we cannot guarantee that every third-party link remains live. Remove requests are reviewed promptly.</p>' ),
        'privacy-policy'    => array( 'Privacy Policy', '<p>We do not intentionally collect personal accounts or passwords. Standard server logs and analytics may be used for traffic measurement. Links to Telegram or other third-party services are governed by those services.</p>' ),
        'dmca'              => array( 'DMCA', '<p>If you believe any listing infringes your copyright, send the full URL, proof of ownership, and contact details through <a href="https://t.me/MorimoFlix_Zone">Telegram</a>. We will review and remove valid infringing links promptly.</p>' ),
    );
    foreach ( $trust_pages as $slug => $page ) {
        $existing = get_page_by_path( $slug );
        if ( ! $existing ) {
            wp_insert_post( array(
                'post_title'   => $page[0],
                'post_name'    => $slug,
                'post_content' => $page[1],
                'post_status'  => 'publish',
                'post_type'    => 'page',
            ) );
        } elseif ( empty( $existing->post_content ) ) {
            wp_update_post( array( 'ID' => $existing->ID, 'post_content' => $page[1] ) );
        }
    }
}
add_action( 'admin_init', 'morimoflix_create_trust_pages' );

/**
 * Ensure the Blog posts page exists and is selected in Settings > Reading.
 */
/**
 * Ensure blog archive belongs to the Blogs CPT, not a static page.
 */
function morimoflix_ensure_blog_page() {
    $front_id = get_option( 'page_on_front' );
    if ( ! $front_id || ! get_post( $front_id ) ) {
        $front_id = wp_insert_post( array(
            'post_title'   => 'Home',
            'post_name'    => 'home',
            'post_content' => '',
            'post_status'  => 'publish',
            'post_type'    => 'page',
        ) );
        if ( $front_id && ! is_wp_error( $front_id ) ) {
            update_option( 'page_on_front', $front_id );
        }
    }

    /** Use the Blogs post-type archive at /blog/, so turn off the static Posts page setting. */
    update_option( 'page_for_posts', 0 );
    update_option( 'show_on_front', 'page' );

    if ( get_option( 'morimoflix_blog_rules_v2' ) !== MORIMOFLIX_VERSION ) {
        flush_rewrite_rules( false );
        update_option( 'morimoflix_blog_rules_v2', MORIMOFLIX_VERSION );
    }

    /** A visible Page with slug "blog" would conflict with the CPT archive; rename it. */
    $blog_page = get_page_by_path( 'blog' );
    if ( $blog_page ) {
        wp_update_post( array( 'ID' => $blog_page->ID, 'post_name' => 'blog-archive-page' ) );
    }
}
add_action( 'admin_init', 'morimoflix_ensure_blog_page' );
add_action( 'after_switch_theme', 'morimoflix_ensure_blog_page' );

/**
 * Performance: invalidate /list/ transient (fixes 4859ms audit). No poster change.
 * SAFE: also clears 60s rebuild lock + LiteSpeed /list/ purge (guarded).
 */
function morimoflix_invalidate_list_cache( $post_id = 0 ) {
    if ( $post_id && wp_is_post_revision( $post_id ) ) return;
    $type = $post_id ? get_post_type( $post_id ) : '';
    if ( in_array( $type, array('movie','tv','page'), true ) || 0 === $post_id ) {
        delete_transient('morimoflix_list_grouped');
        delete_transient('morimoflix_list_grouped_lock');
        delete_transient('morimoflix_llms_txt');
        // LiteSpeed: purge /list/ URL when available (guarded).
        if ( function_exists( 'litespeed_purge_url' ) ) {
            litespeed_purge_url( home_url( '/list/' ) );
        }
    }
}
add_action('save_post', 'morimoflix_invalidate_list_cache');
add_action('deleted_post', 'morimoflix_invalidate_list_cache');
add_action('trashed_post', 'morimoflix_invalidate_list_cache');
add_action('untrashed_post', 'morimoflix_invalidate_list_cache');

/**
 * SAFE perf: invalidate cached genre link transients (filter + footer, 12h).
 */
function morimoflix_invalidate_genre_cache( $term_id = 0, $tt_id = 0, $taxonomy = '' ) {
    if ( 'genre' !== $taxonomy ) return;
    delete_transient('morimoflix_filter_genres');
    delete_transient('morimoflix_footer_genres');
    delete_transient('morimoflix_llms_txt');
}
add_action('edited_term', 'morimoflix_invalidate_genre_cache', 10, 3);
add_action('delete_term', 'morimoflix_invalidate_genre_cache', 10, 3);

/**
 * Enqueue Scripts and Styles
 */
function morimoflix_enqueue_assets() {
    // Google Fonts + Material Symbols are loaded once in header.php
    // (single combined request with preconnect + display=swap) to avoid
    // duplicate requests and LiteSpeed/Cloudflare combine issues that
    // caused empty icon boxes (□) on live.

    // Main Stylesheet
    wp_enqueue_style(
        'morimoflix-style',
        get_stylesheet_uri(),
        array(),
        MORIMOFLIX_VERSION
    );

    // Main Script
    wp_enqueue_script(
        'morimoflix-main',
        get_template_directory_uri() . '/assets/js/main.js',
        array(),
        MORIMOFLIX_VERSION,
        true
    );

    wp_localize_script( 'morimoflix-main', 'morimoflixData', array(
        'ajaxUrl' => admin_url( 'admin-ajax.php' ),
        'nonce'   => wp_create_nonce( 'morimoflix-nonce' ),
    ) );
}
add_action( 'wp_enqueue_scripts', 'morimoflix_enqueue_assets' );

/**
 * Add Movie Meta Box
 */
function morimoflix_add_meta_boxes() {
    $post_types = array( 'movie', 'tv' );
    
    foreach ( $post_types as $post_type ) {
        add_meta_box(
            'morimoflix_movie_meta',
            __( 'Movie Information', 'morimoflix-flicker' ),
            'morimoflix_movie_meta_callback',
            $post_type,
            'normal',
            'high'
        );
        
        add_meta_box(
            'morimoflix_download_links',
            __( 'Download Links', 'morimoflix-flicker' ),
            'morimoflix_download_meta_box_callback',
            $post_type,
            'normal',
            'default'
        );
    }
}
add_action( 'add_meta_boxes', 'morimoflix_add_meta_boxes' );

/**
 * Movie Meta Box Callback
 */
function morimoflix_movie_meta_callback( $post ) {
    wp_nonce_field( 'morimoflix_movie_meta', 'morimoflix_movie_meta_nonce' );

    $rating    = get_post_meta( $post->ID, '_movie_rating', true );
    $duration  = get_post_meta( $post->ID, '_movie_duration', true );
    $quality   = get_post_meta( $post->ID, '_movie_quality', true ) ?: 'HD';
    $language  = get_post_meta( $post->ID, '_movie_language', true );
    $year      = get_post_meta( $post->ID, '_movie_year', true );
    $trailer   = get_post_meta( $post->ID, '_movie_trailer', true );
    $is_live   = get_post_meta( $post->ID, '_movie_live', true );
    $posted_by = get_post_meta( $post->ID, '_movie_posted_by', true );
    // Optional schema-only fields (empty = omitted from JSON-LD).
    $director       = get_post_meta( $post->ID, '_movie_director', true );
    $production     = get_post_meta( $post->ID, '_movie_production', true );
    $content_rating = get_post_meta( $post->ID, '_movie_content_rating', true );
    $rating_count   = get_post_meta( $post->ID, '_movie_rating_count', true );
    
    // Auto-capture admin name if empty
    if ( empty( $posted_by ) ) {
        $posted_by = get_the_author_meta( 'display_name', $post->post_author );
    }
    ?>
    <table class="form-table">
        <tr>
            <th><label for="movie_posted_by"><?php _e( 'Posted By', 'morimoflix-flicker' ); ?></label></th>
            <td><input type="text" id="movie_posted_by" name="movie_posted_by" value="<?php echo esc_attr( $posted_by ); ?>" class="regular-text" placeholder="<?php esc_attr_e( 'Admin name', 'morimoflix-flicker' ); ?>"></td>
        </tr>
        <tr>
            <th><label for="movie_rating"><?php _e( 'IMDb Rating', 'morimoflix-flicker' ); ?></label></th>
            <td><input type="text" id="movie_rating" name="movie_rating" value="<?php echo esc_attr( $rating ); ?>" class="regular-text" placeholder="8.0"></td>
        </tr>
        <tr>
            <th><label for="movie_duration"><?php _e( 'Duration', 'morimoflix-flicker' ); ?></label></th>
            <td><input type="text" id="movie_duration" name="movie_duration" value="<?php echo esc_attr( $duration ); ?>" class="regular-text" placeholder="120M"></td>
        </tr>
        <tr>
            <th><label for="movie_quality"><?php _e( 'Quality', 'morimoflix-flicker' ); ?></label></th>
            <td>
                <select id="movie_quality" name="movie_quality">
                    <option value="HD" <?php selected( $quality, 'HD' ); ?>>HD</option>
                    <option value="4K" <?php selected( $quality, '4K' ); ?>>4K</option>
                    <option value="SD" <?php selected( $quality, 'SD' ); ?>>SD</option>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="movie_language"><?php _e( 'Language', 'morimoflix-flicker' ); ?></label></th>
            <td><input type="text" id="movie_language" name="movie_language" value="<?php echo esc_attr( $language ); ?>" class="regular-text" placeholder="English, Hindi"></td>
        </tr>
        <tr>
            <th><label for="movie_year"><?php _e( 'Year', 'morimoflix-flicker' ); ?></label></th>
            <td><input type="text" id="movie_year" name="movie_year" value="<?php echo esc_attr( $year ); ?>" class="regular-text" placeholder="2024"></td>
        </tr>
        <tr>
            <th><label for="movie_trailer"><?php _e( 'Trailer URL', 'morimoflix-flicker' ); ?></label></th>
            <td><input type="url" id="movie_trailer" name="movie_trailer" value="<?php echo esc_attr( $trailer ); ?>" class="regular-text" placeholder="https://youtube.com/watch?v=..."></td>
        </tr>
        <tr>
            <th><label for="movie_director"><?php _e( 'Director (schema only)', 'morimoflix-flicker' ); ?></label></th>
            <td><input type="text" id="movie_director" name="movie_director" value="<?php echo esc_attr( $director ); ?>" class="regular-text" placeholder="Comma-separated if multiple"></td>
        </tr>
        <tr>
            <th><label for="movie_production"><?php _e( 'Production Company (schema only)', 'morimoflix-flicker' ); ?></label></th>
            <td><input type="text" id="movie_production" name="movie_production" value="<?php echo esc_attr( $production ); ?>" class="regular-text" placeholder="Studio name"></td>
        </tr>
        <tr>
            <th><label for="movie_content_rating"><?php _e( 'Content Rating (schema only)', 'morimoflix-flicker' ); ?></label></th>
            <td><input type="text" id="movie_content_rating" name="movie_content_rating" value="<?php echo esc_attr( $content_rating ); ?>" class="regular-text" placeholder="e.g. PG-13, U/A 13+"></td>
        </tr>
        <tr>
            <th><label for="movie_rating_count"><?php _e( 'Rating Count (schema only)', 'morimoflix-flicker' ); ?></label></th>
            <td><input type="number" id="movie_rating_count" name="movie_rating_count" value="<?php echo esc_attr( $rating_count ); ?>" class="regular-text" placeholder="e.g. 1250" min="1" step="1"></td>
        </tr>
        <tr>
            <th><label for="movie_live"><?php _e( 'Live Status', 'morimoflix-flicker' ); ?></label></th>
            <td>
                <label>
                    <input type="checkbox" id="movie_live" name="movie_live" value="yes" <?php checked( $is_live, 'yes' ); ?>>
                    <?php _e( 'Mark as Live', 'morimoflix-flicker' ); ?>
                </label>
            </td>
        </tr>
    </table>
    <?php
}

/**
 * Download Links Meta Box Callback
 */
function morimoflix_download_meta_box_callback( $post ) {
    $download_links = get_post_meta( $post->ID, '_download_links', true );
    ?>
    <p style="margin-bottom: 10px;">
        <label style="display: block; margin-bottom: 5px; font-weight: 600;"><?php _e( 'Download Links:', 'morimoflix-flicker' ); ?></label>
        <textarea id="download_links" name="download_links" rows="8" style="width: 100%; padding: 8px; background: #2a2a2a; border: 1px solid #3a494b; color: #e5e2e1; font-family: monospace; font-size: 13px;" placeholder="Format: Quality|URL|Size (one per line)
Example:
4K ULTRA|https://example.com/movie-4k.mp4|2.1 GB
1080p HD|https://example.com/movie-1080p.mp4|1.4 GB"><?php echo esc_textarea( $download_links ); ?></textarea>
    </p>
    <p class="description">
        <?php _e( 'Format: Quality|URL|Size (one per line)', 'morimoflix-flicker' ); ?><br>
        <?php _e( 'Example: 4K ULTRA|https://example.com/movie-4k.mp4|2.1 GB', 'morimoflix-flicker' ); ?>
    </p>
    <?php
}

/**
 * Save Movie Meta
 */
function morimoflix_save_meta_boxes( $post_id ) {
    if ( ! isset( $_POST['morimoflix_movie_meta_nonce'] ) ) {
        return;
    }
    if ( ! wp_verify_nonce( $_POST['morimoflix_movie_meta_nonce'], 'morimoflix_movie_meta' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    $fields = array(
        'movie_rating'         => '_movie_rating',
        'movie_duration'       => '_movie_duration',
        'movie_quality'        => '_movie_quality',
        'movie_language'       => '_movie_language',
        'movie_year'           => '_movie_year',
        'movie_trailer'        => '_movie_trailer',
        'movie_posted_by'      => '_movie_posted_by',
        'movie_director'       => '_movie_director',
        'movie_production'     => '_movie_production',
        'movie_content_rating' => '_movie_content_rating',
    );

    foreach ( $fields as $field => $meta_key ) {
        if ( isset( $_POST[ $field ] ) ) {
            update_post_meta( $post_id, $meta_key, sanitize_text_field( $_POST[ $field ] ) );
        }
    }

    // Save trailer URL with esc_url_raw
    if ( isset( $_POST['movie_trailer'] ) ) {
        update_post_meta( $post_id, '_movie_trailer', esc_url_raw( $_POST['movie_trailer'] ) );
    }

    // Save rating count as positive int (empty = fallback at output).
    if ( isset( $_POST['movie_rating_count'] ) ) {
        $rc = absint( $_POST['movie_rating_count'] );
        if ( $rc > 0 ) {
            update_post_meta( $post_id, '_movie_rating_count', $rc );
        } else {
            delete_post_meta( $post_id, '_movie_rating_count' );
        }
    }

    // Save live status
    $live = isset( $_POST['movie_live'] ) ? 'yes' : 'no';
    update_post_meta( $post_id, '_movie_live', $live );

    // Save download links
    if ( isset( $_POST['download_links'] ) ) {
        update_post_meta( $post_id, '_download_links', sanitize_textarea_field( $_POST['download_links'] ) );
    }
}
add_action( 'save_post', 'morimoflix_save_meta_boxes' );

/**
 * Plain-text archive title without HTML spans or the WP prefix.
 */
function morimoflix_archive_title_plain() {
    $title = wp_strip_all_tags( get_the_archive_title() );
    $title = preg_replace( '/^(?:Archives|Year|Month|Day|Author|Category|Tag|Post Type):\s*/i', '', trim( $title ) );
    return trim( $title );
}

/**
 * Get movie meta data
 */
function morimoflix_get_movie_meta( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }

    $posted_by = get_post_meta( $post_id, '_movie_posted_by', true );
    if ( empty( $posted_by ) ) {
        $posted_by = get_the_author_meta( 'display_name', get_post_field( 'post_author', $post_id ) );
    }

    return array(
        'rating'    => get_post_meta( $post_id, '_movie_rating', true ) ?: '',
        'duration'  => get_post_meta( $post_id, '_movie_duration', true ) ?: '120M',
        'quality'   => get_post_meta( $post_id, '_movie_quality', true ) ?: 'HD',
        'language'  => get_post_meta( $post_id, '_movie_language', true ) ?: '',
        'is_live'   => get_post_meta( $post_id, '_movie_live', true ),
        'year'      => get_post_meta( $post_id, '_movie_year', true ) ?: '',
        'trailer'   => get_post_meta( $post_id, '_movie_trailer', true ) ?: '',
        'download'  => get_post_meta( $post_id, '_download_links', true ) ?: '',
        'cast'      => get_post_meta( $post_id, '_tmdb_cast', true ) ?: array(),
        'backdrop'  => get_post_meta( $post_id, '_tmdb_backdrop', true ) ?: '',
        'tagline'   => get_post_meta( $post_id, '_movie_tagline', true ) ?: '',
        'posted_by' => $posted_by,
        // Optional schema-only meta (empty = omitted from JSON-LD, no UI requirement).
        'director'       => get_post_meta( $post_id, '_movie_director', true ) ?: '',
        'production'     => get_post_meta( $post_id, '_movie_production', true ) ?: '',
        'content_rating' => get_post_meta( $post_id, '_movie_content_rating', true ) ?: '',
        'rating_count'   => get_post_meta( $post_id, '_movie_rating_count', true ) ?: '',
    );
}

/**
 * Get backdrop URL
 */
function morimoflix_get_backdrop( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }
    
    $backdrop = get_post_meta( $post_id, '_tmdb_backdrop', true );
    if ( $backdrop ) {
        return $backdrop;
    }
    
    if ( has_post_thumbnail( $post_id ) ) {
        return get_the_post_thumbnail_url( $post_id, 'large' );
    }
    
    return '';
}

/**
 * Get OG share image (1200x630) for a movie/TV post.
 *
 * Prefers the `morimoflix-og` hard-crop (1200x630). Falls back to the
 * `large` poster only when the OG size is unavailable (e.g. thumbnails
 * uploaded before the size was registered and never regenerated).
 *
 * @param int|null $post_id Post ID. Defaults to current post.
 * @return array Array with url, width, height keys (empty when none).
 */
function morimoflix_get_og_image( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }
    $none = array( 'url' => '', 'width' => 0, 'height' => 0 );
    if ( ! $post_id || ! has_post_thumbnail( $post_id ) ) {
        return $none;
    }
    $thumb_id = get_post_thumbnail_id( $post_id );
    if ( ! $thumb_id ) {
        return $none;
    }
    foreach ( array( 'morimoflix-og', 'large' ) as $size ) {
        $src = wp_get_attachment_image_src( $thumb_id, $size );
        if ( $src && ! empty( $src[0] ) ) {
            return array( 'url' => $src[0], 'width' => absint( $src[1] ), 'height' => absint( $src[2] ) );
        }
    }
    return $none;
}

/**
 * SEO: guard a local uploads URL against broken files.
 *
 * Applies a filter, then verifies the file exists on disk when the URL
 * points inside the uploads dir. External (filtered) URLs are trusted.
 * Returns '' when the local file is missing so callers can fall back
 * (post thumbnail -> site icon -> omit) instead of printing a 404 URL.
 *
 * @param string $default_url Default URL (inside uploads).
 * @param string $filter_name Filter to allow override.
 * @return string Guarded URL or ''.
 */
function morimoflix_guarded_upload_url( $default_url, $filter_name ) {
    $url = apply_filters( $filter_name, $default_url );
    if ( ! is_string( $url ) || '' === trim( $url ) ) {
        return '';
    }
    $url = trim( $url );
    if ( function_exists( 'wp_get_upload_dir' ) ) {
        $dir = wp_get_upload_dir();
        if ( ! empty( $dir['baseurl'] ) && ! empty( $dir['basedir'] ) && 0 === strpos( $url, $dir['baseurl'] ) ) {
            $rel = ltrim( substr( $url, strlen( $dir['baseurl'] ) ), '/' );
            if ( '' === $rel || ! file_exists( trailingslashit( $dir['basedir'] ) . $rel ) ) {
                return '';
            }
            return $url;
        }
    }
    // Not inside uploads (e.g. filtered external URL or theme path): if it
    // is still a local home_url() path we cannot verify, keep it only when
    // it was explicitly filtered; unverified defaults are dropped.
    if ( 0 === strpos( $url, home_url() ) && $url === $default_url ) {
        return $url;
    }
    return $url;
}

/**
 * Get actor archive link by name
 */
function morimoflix_get_actor_link( $actor_name ) {
    $term = get_term_by( 'name', $actor_name, 'actor' );
    if ( $term && ! is_wp_error( $term ) ) {
        return get_term_link( $term );
    }
    return '#';
}

/**
 * Compare two human-facing strings (tagline vs excerpt) for sameness.
 *
 * Some TMDB imports store the identical sentence in both `_movie_tagline` and the post
 * excerpt, which makes the detail template render the same line twice. Normalising away
 * HTML, entities, case and punctuation lets the template drop the redundant copy.
 *
 * Fails safe: it returns false (show the description) unless both sides carry a real
 * letter and normalise to exactly the same words. It will never hide a description whose
 * wording differs -- only one that differs from the tagline by case, spacing, punctuation
 * or markup spelling alone.
 *
 * @param mixed $a First string. Non-scalars are treated as empty.
 * @param mixed $b Second string. Non-scalars are treated as empty.
 * @return bool True when both strings carry the same words.
 */
function morimoflix_texts_are_duplicate( $a, $b ) {
    // The comparison key strips markup, but esc_html() RENDERS markup literally. So markup
    // on only one side would be deleted from that side's key and invent a match that the
    // reader does not see -- "Kill Bill <Vol 2> is here" vs "Kill Bill is here". Detect
    // that asymmetry up front and decline. Entities are fine: decoding is symmetric and
    // esc_html() renders them identically, so only real tag syntax is checked here.
    $has_tag = function ( $text ) {
        return is_scalar( $text ) && 1 === preg_match( '/<[^>]*>/s', (string) $text );
    };
    if ( $has_tag( $a ) !== $has_tag( $b ) ) {
        return false;
    }

    $normalise = function ( $text ) {
        // is_scalar() keeps a serialised array from reaching wp_check_invalid_utf8().
        // Validate the encoding HERE, before anything else, so malformed bytes cannot warn
        // inside the /u regex below (which would print into the page on every render when
        // WP_DEBUG_DISPLAY is on) nor be mangled by the decode/case-fold steps. Note this
        // is a pass-through when the site charset is not UTF-8, so it is a best effort and
        // the null guard after preg_replace() remains the real safety net.
        $text = is_scalar( $text ) ? wp_check_invalid_utf8( (string) $text ) : '';
        // Strip BEFORE decoding, deliberately. Decoding first would turn an entity-escaped
        // "&lt;b&gt;Hi&lt;/b&gt;" into real markup, strip it away, and leave "hi" -- which then
        // collides with a plain "Hi" tagline. They render differently, so that would hide a
        // description for no reason.
        $text = wp_strip_all_tags( $text, true );
        // ENT_HTML5 so named entities TMDB likes to emit (&rsquo;, &mdash;, &hellip;) collapse too.
        $text = html_entity_decode( $text, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
        // Fold to NFC. Devanagari arrives both precomposed (U+0958) and decomposed
        // (U+0915 U+093C) depending on the importer, the filesystem or a copy-paste; the two
        // render identically but have different codepoint sequences. NFC is a canonical
        // normalisation, so it can only merge strings that already render the same.
        // Guard on the CLASS as well as the function: a polyfill can define
        // normalizer_normalize() without a global Normalizer class, and referencing the
        // missing class is a fatal Error. WordPress core hit exactly this (Trac #56980).
        // The form argument is the default, so it is left off entirely.
        if ( function_exists( 'normalizer_normalize' ) && class_exists( 'Normalizer' ) ) {
            $normalized = normalizer_normalize( $text );
            if ( is_string( $normalized ) ) {
                $text = $normalized;
            }
        }
        // After decoding, so entity-produced letters fold too (&THORN; vs &thorn;).
        // strtolower() is ASCII-only and misses accented and Greek letters.
        $text = function_exists( 'mb_strtolower' ) ? mb_strtolower( $text, 'UTF-8' ) : strtolower( $text );
        // \p{M} MUST stay in the class. Devanagari/Telugu vowel signs, the nukta, Arabic
        // harakat and Hebrew points are \p{Mn}|\p{Mc} -- NOT \p{L}. Leaving them out turned
        // every one into a space, which both shredded Hindi text and made distinct words
        // compare EQUAL (maana/min, gaana/ginaa, sona/sinaa all collapsed to the same key),
        // silently hiding a genuinely different description.
        $text = preg_replace( '/[^\p{L}\p{M}\p{N}]+/u', ' ', $text );

        // preg_replace() with /u returns null on malformed UTF-8; never feed that to trim()
        // (deprecated as of PHP 8.1).
        return null === $text ? '' : trim( $text );
    };

    $a = $normalise( $a );
    $b = $normalise( $b );

    // Require a real letter on both sides. Numeric-only ("2026") or symbol-only keys carry
    // no words worth de-duplicating and must never suppress a description.
    if ( ! preg_match( '/\p{L}/u', $a ) || ! preg_match( '/\p{L}/u', $b ) ) {
        return false;
    }

    return $a === $b;
}

/**
 * TV-series specific meta, layered on top of the shared movie meta.
 *
 * The shared keys (_movie_rating / _movie_year / _movie_language / _movie_quality /
 * _movie_tagline / _tmdb_backdrop / _tmdb_cast) are reused as-is so every existing card,
 * grid, JSON-LD and SEO helper keeps working for series without modification. Only the
 * genuinely series-only facts live under their own `_tv_*` keys.
 *
 * @param int|null $post_id Post ID. Defaults to current post.
 * @return array
 */
function morimoflix_get_series_meta( $post_id = null ) {
    if ( ! $post_id ) $post_id = get_the_ID();
    $base = morimoflix_get_movie_meta( $post_id );

    $raw_status    = (string) get_post_meta( $post_id, '_tv_status', true );
    $raw_networks  = (string) get_post_meta( $post_id, '_tv_networks', true );
    $raw_first_air = (string) get_post_meta( $post_id, '_tv_first_air_date', true );

    // TMDB status codes -> human label. Anything unknown is passed through trimmed.
    $status_map = array(
        'Returning Series' => 'Returning Series',
        'Ended'            => 'Ended',
        'Canceled'         => 'Cancelled',
        'In Production'    => 'In Production',
        'Planned'          => 'Planned',
    );
    $status = '';
    if ( '' !== trim( $raw_status ) ) {
        $key          = trim( $raw_status );
        $status       = isset( $status_map[ $key ] ) ? $status_map[ $key ] : $key;
    }

    // Networks are stored comma-separated; cap the list so the hero never overflows.
    $networks = array();
    if ( '' !== trim( $raw_networks ) ) {
        foreach ( explode( ',', $raw_networks ) as $n ) {
            $n = trim( $n );
            if ( '' !== $n ) {
                $networks[] = $n;
            }
        }
    }
    $networks = array_slice( $networks, 0, 4 );

    return array(
        'seasons'       => absint( get_post_meta( $post_id, '_tv_seasons', true ) ),
        'episodes'      => absint( get_post_meta( $post_id, '_tv_total_episodes', true ) ),
        'status'        => $status,
        'networks'      => $networks,
        'first_air'     => $raw_first_air,
        'last_air'      => (string) get_post_meta( $post_id, '_tv_last_air_date', true ),
        'episode_run'   => absint( get_post_meta( $post_id, '_tv_episode_run_time', true ) ),
        // Shared keys, re-exposed so templates do not need two calls.
        'rating'        => $base['rating'],
        'year'          => $base['year'],
        'language'      => $base['language'],
        'quality'       => $base['quality'],
        'tagline'       => $base['tagline'],
        'trailer'       => $base['trailer'],
        'cast'          => $base['cast'],
    );
}

/**
 * Parse a TV-series `_download_links` blob into the season/quality/codec tree used by
 * single-tv.php.
 *
 * Line format is UNCHANGED from movies — `LABEL|URL|SIZE`, one per line — so the
 * existing Franited Links manager and meta box keep working untouched. Season, episode,
 * quality and codec are derived from the LABEL, which is where the importer already
 * puts the release name.
 *
 * Everything is best-effort: a line that yields no season still surfaces under an
 * "Zip/Pack Files" group rather than silently disappearing, so no link is ever hidden.
 *
 * @param string $raw Raw `_download_links` meta value.
 * @return array {
 *     @type array $seasons Season number => array( 'number','packs','episodes','other' ).
 * }
 */
function morimoflix_parse_series_links( $raw ) {
    $seasons = array();
    if ( '' === trim( (string) $raw ) ) {
        return $seasons;
    }

    foreach ( preg_split( '/\r\n|\r|\n/', (string) $raw ) as $line ) {
        $line = trim( $line );
        if ( '' === $line ) {
            continue;
        }
        $parts = array_map( 'trim', explode( '|', $line ) );
        $label = isset( $parts[0] ) ? $parts[0] : '';
        $url   = isset( $parts[1] ) ? $parts[1] : '';
        $size  = isset( $parts[2] ) ? $parts[2] : '';
        if ( '' === $label || '' === $url ) {
            continue;
        }
        // Same guard single-movie.php uses, so junk lines are dropped consistently.
        if ( ! preg_match( '#^https?://#i', $url ) || ! filter_var( $url, FILTER_VALIDATE_URL ) ) {
            continue;
        }

        // Season + episode. Handles both the compact release notation used by real
        // filenames (".S01E02.") where the character after the season digits is the
        // letter E rather than a separator, and the spaced form ("S01 E02").
        $season  = 0;
        $episode = 0;
        if ( preg_match( '/(?:^|[^A-Za-z0-9])S(\d{1,3})(?:E(\d{1,4}))?(?:[^A-Za-z0-9]|$)/i', $label, $m ) ) {
            $season  = absint( $m[1] );
            $episode = isset( $m[2] ) ? absint( $m[2] ) : 0;
        }
        // Standalone episode token, for labels like "S01 E02" or "E02".
        if ( 0 === $episode && preg_match( '/(?:^|[^A-Za-z0-9])E(\d{1,4})(?:[^A-Za-z0-9]|$)/i', $label, $m ) ) {
            $episode = absint( $m[1] );
        }
        // Spelled-out fallback: "Season 1", "Episode 2". Checked last so the compact and
        // abbreviated forms always win.
        if ( 0 === $season && preg_match( '/\bseason\s*(\d{1,3})\b/i', $label, $m ) ) {
            $season = absint( $m[1] );
        }
        if ( 0 === $episode && preg_match( '/\bepisode\s*(\d{1,4})\b/i', $label, $m ) ) {
            $episode = absint( $m[1] );
        }

        // Pack vs episode: an archive extension or the word "pack" means a full-season zip;
        // otherwise anything carrying an episode number is an episode. Ambiguous lines with
        // neither are treated as packs, which is what a season bundle actually is.
        $looks_archive = (bool) preg_match( '/\.(zip|rar|7z|tar\.gz)\b/i', $label )
            || (bool) preg_match( '/\b(packs?|zips?)\b/i', $label );
        $is_pack = $looks_archive || 0 === $episode;

        // Quality, most specific first (4k must win over a bare 2160 mention).
        $quality = '';
        if ( preg_match( '/\b(2160p|4k|uhd)\b/i', $label, $m ) || preg_match( '/\bds4k\b/i', $label ) ) {
            $quality = '2160p';
        } elseif ( preg_match( '/\b1080p\b/i', $label, $m ) ) {
            $quality = '1080p';
        } elseif ( preg_match( '/\b720p\b/i', $label, $m ) ) {
            $quality = '720p';
        } elseif ( preg_match( '/\b(?:480p|sd)\b/i', $label, $m ) ) {
            $quality = '480p';
        } elseif ( preg_match( '/\b(360p|240p)\b/i', $label, $m ) ) {
            $quality = $m[1];
        }

        // Codec / dynamic range. Checked before quality would swallow nothing, but kept
        // separate so a quality group can hold several codec rows.
        $codec = '';
        if ( preg_match( '/\b(HDR10\+?|HDR)\b/i', $label, $m ) ) {
            $codec = 'HDR';
        } elseif ( preg_match( '/\bSDR\b/i', $label, $m ) ) {
            $codec = 'SDR';
        } elseif ( preg_match( '/\bAV1\b/i', $label ) ) {
            $codec = 'AV1';
        } elseif ( preg_match( '/\bH\.?265\b|\bx265\b/i', $label ) ) {
            $codec = 'H.265';
        } elseif ( preg_match( '/\bH\.?264\b|\bx264\b|\bAVC\b/i', $label ) ) {
            $codec = 'H.264';
        }

        $item = array(
            'label'   => $label,
            'url'     => $url,
            'size'    => $size,
            'episode' => $episode,
            'quality' => $quality,
            'codec'   => $codec,
        );

        // 0 => unknown season, surfaced last under its own group by single-tv.php.
        if ( ! isset( $seasons[ $season ] ) ) {
            $seasons[ $season ] = array(
                'number'   => $season,
                'packs'    => array(),
                'episodes' => array(),
            );
        }
        if ( $is_pack ) {
            $seasons[ $season ]['packs'][] = $item;
        } else {
            $seasons[ $season ]['episodes'][] = $item;
        }
    }

    // Natural season order, unknown (0) last.
    ksort( $seasons, SORT_NUMERIC );
    if ( isset( $seasons[0] ) ) {
        $extras = $seasons[0];
        unset( $seasons[0] );
        $seasons[0] = $extras;
    }
    return $seasons;
}

/**
 * Include movie post type in search results
 */
function morimoflix_search_filter( $query ) {
    if ( ! is_admin() && $query->is_search() && $query->is_main_query() ) {
        $query->set( 'post_type', array( 'post', 'movie', 'tv' ) );
    }
    return $query;
}
add_filter( 'pre_get_posts', 'morimoflix_search_filter' );

/**
 * Empty search -> home.
 * WP_Query::parse_query() tests isset($this->query['s']), so "/?s=" IS a search
 * request, and parse_search() then builds LIKE '%%' which matches every row. That
 * rendered the whole catalogue through search.php as "Search Results / QUERY: ''" —
 * a full-catalogue duplicate of the homepage. The form's `required` attribute only
 * blocks accidental submission; this closes the direct/bookmark/crawler route too.
 */
function morimoflix_redirect_empty_search() {
    if ( is_admin() || ! is_search() ) {
        return;
    }
    if ( '' === trim( (string) get_search_query() ) ) {
        wp_safe_redirect( home_url( '/' ), 301 );
        exit;
    }
}
add_action( 'template_redirect', 'morimoflix_redirect_empty_search' );

/**
 * Fix homepage pagination - reset paged on main query for front page / home.
 *
 * Problem: When ?paged=2 is on the front page, WordPress main query tries to
 * paginate the front page itself and 404s because there's only 1 page there.
 *
 * Solution: Use pre_get_posts (not the 'request' filter) and let WordPress
 * tell us via is_home() / is_front_page() whether we are actually on the
 * front page. The previous request-filter check was matching every empty
 * query var, which incorrectly tripped on taxonomy / category / author
 * archives and broke their pagination.
 *
 * Our custom WP_Query in front-page.php / index.php reads $_GET['paged']
 * directly, so resetting the main query is safe.
 */
function morimoflix_fix_front_page_request( $query ) {
    if ( is_admin() || ! $query->is_main_query() ) {
        return;
    }

    if ( $query->is_front_page() || $query->is_home() ) {
        $query->set( 'paged', 0 );
        $query->is_paged = false;
    }
}
add_action( 'pre_get_posts', 'morimoflix_fix_front_page_request' );

/**
 * Custom excerpt length
 */
function morimoflix_excerpt_length( $length ) {
    return 20;
}
add_filter( 'excerpt_length', 'morimoflix_excerpt_length' );

/**
 * Custom excerpt more
 */
function morimoflix_excerpt_more( $more ) {
    return '...';
}
add_filter( 'excerpt_more', 'morimoflix_excerpt_more' );

/**
 * Movie Request Feature
 *
 * @package MorimoFlix
 */

/**
 * Create movie requests table on theme activation
 */
function morimoflix_create_requests_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'movie_requests';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        movie_name varchar(255) NOT NULL,
        movie_year varchar(4) NOT NULL,
        imdb_link varchar(500) DEFAULT '',
        user_ip varchar(45) NOT NULL,
        status varchar(20) DEFAULT 'pending',
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}
add_action( 'after_switch_theme', 'morimoflix_create_requests_table' );

/**
 * Auto-create movie requests table if it doesn't exist
 * Runs on every admin page load
 */
function morimoflix_check_requests_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'movie_requests';
    
    if ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table_name ) ) !== $table_name ) {
        morimoflix_create_requests_table();
    }
}
add_action( 'admin_init', 'morimoflix_check_requests_table' );

/**
 * AJAX handler for movie request submission
 */
function morimoflix_submit_movie_request() {
    check_ajax_referer( 'morimoflix-request-nonce', 'nonce' );

    // Client IP. Behind Cloudflare REMOTE_ADDR is the edge node, so prefer the
    // CF header. This only feeds the secondary throttle (Turnstile is the real
    // gate), so a spoofed value weakens the throttle but not the CAPTCHA.
    $user_ip = '';
    if ( ! empty( $_SERVER['HTTP_CF_CONNECTING_IP'] ) ) {
        $cf_ip = filter_var( wp_unslash( $_SERVER['HTTP_CF_CONNECTING_IP'] ), FILTER_VALIDATE_IP );
        if ( $cf_ip ) {
            $user_ip = $cf_ip;
        }
    }
    if ( '' === $user_ip && ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
        $user_ip = sanitize_text_field( substr( wp_unslash( $_SERVER['REMOTE_ADDR'] ), 0, 45 ) );
    }

    // 1) Cheap validation first, so a typo costs nothing.
    $movie_name = isset( $_POST['movie_name'] ) ? sanitize_text_field( wp_unslash( $_POST['movie_name'] ) ) : '';
    $movie_year = isset( $_POST['movie_year'] ) ? sanitize_text_field( wp_unslash( $_POST['movie_year'] ) ) : '';
    $imdb_link  = isset( $_POST['imdb_link'] ) ? esc_url_raw( wp_unslash( $_POST['imdb_link'] ) ) : '';

    if ( function_exists( 'mb_substr' ) ) {
        $movie_name = mb_substr( $movie_name, 0, 200 );
    } else {
        $movie_name = substr( $movie_name, 0, 200 );
    }
    $imdb_link = substr( $imdb_link, 0, 500 );

    if ( empty( $movie_name ) || empty( $movie_year ) ) {
        wp_send_json_error( array( 'message' => 'Movie name and year are required.' ) );
    }

    if ( ! preg_match( '/^\d{4}$/', $movie_year ) || (int) $movie_year < 1900 || (int) $movie_year > (int) gmdate( 'Y' ) + 2 ) {
        wp_send_json_error( array( 'message' => 'Please enter a valid 4-digit year.' ) );
    }

    // 2) Throttle CHECK before the outbound call. Previously this sat after
    //    siteverify, which let one IP force unlimited Cloudflare requests and
    //    eventually trip Cloudflare's own rate limit into a site-wide 503.
    $throttle_key = 'mfr_req_' . md5( $user_ip );
    if ( get_transient( $throttle_key ) ) {
        wp_send_json_error( array( 'message' => 'Please wait a moment before submitting another request.' ), 429 );
    }

    // 3) Turnstile verification (server-side; the client token alone is worthless).
    if ( morimoflix_turnstile_enabled() ) {
        $ts_token = isset( $_POST['cf_turnstile_response'] ) ? sanitize_text_field( wp_unslash( $_POST['cf_turnstile_response'] ) ) : '';

        if ( '' === $ts_token ) {
            set_transient( $throttle_key, 1, 15 );
            wp_send_json_error( array( 'message' => 'Security check did not complete. Please tap Submit Request again.' ), 400 );
        }

        $ts_body = array(
            'secret'   => morimoflix_turnstile_secret(),
            'response' => $ts_token,
        );
        if ( '' !== $user_ip ) {
            $ts_body['remoteip'] = $user_ip;
        }

        $ts_verify = wp_remote_post( 'https://challenges.cloudflare.com/turnstile/v0/siteverify', array(
            // 3s, not 8: siteverify p99 is well under 2s, and every held PHP
            // worker is a worker the whole site cannot use.
            'timeout'             => 3,
            'limit_response_size' => 16384,
            'body'                => $ts_body,
        ) );

        if ( is_wp_error( $ts_verify ) || 200 !== (int) wp_remote_retrieve_response_code( $ts_verify ) ) {
            set_transient( $throttle_key, 1, 15 );
            wp_send_json_error( array( 'message' => 'Security check unavailable. Please try again.' ), 503 );
        }

        $ts_result = json_decode( wp_remote_retrieve_body( $ts_verify ), true );

        // isset() too: reading a missing key on PHP 8 emits a warning, and with
        // WP_DEBUG_DISPLAY that warning precedes the JSON and breaks the client.
        if ( ! is_array( $ts_result ) || ! isset( $ts_result['success'] ) || true !== $ts_result['success'] ) {
            set_transient( $throttle_key, 1, 15 );
            wp_send_json_error( array( 'message' => 'Security check failed. Please try again.' ), 403 );
        }

        // Bind the token to this host: a token minted for another hostname in
        // the same widget configuration would otherwise be accepted here.
        $ts_host = wp_parse_url( home_url(), PHP_URL_HOST );
        if ( ! empty( $ts_result['hostname'] ) && $ts_host && $ts_result['hostname'] !== $ts_host ) {
            set_transient( $throttle_key, 1, 15 );
            wp_send_json_error( array( 'message' => 'Security check failed. Please try again.' ), 403 );
        }
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'movie_requests';

    // Armed only once the request has genuinely succeeded. Failure paths use a
    // short 15s penalty above, so a CAPTCHA flake costs 15s rather than the
    // full minute - and a bot is still rate-limited to one attempt per 15s.
    set_transient( $throttle_key, 1, 60 );

    $result = $wpdb->insert(
        $table_name,
        array(
            'movie_name' => $movie_name,
            'movie_year' => $movie_year,
            'imdb_link'  => $imdb_link,
            'user_ip'    => $user_ip,
            'status'     => 'pending',
            'created_at' => current_time( 'mysql' ),
        ),
        array( '%s', '%s', '%s', '%s', '%s', '%s' )
    );

    if ( $result ) {
        wp_send_json_success( array( 'message' => 'Movie request submitted successfully!' ) );
    } else {
        wp_send_json_error( array( 'message' => 'Failed to submit request. Please try again.' ) );
    }
}
add_action( 'wp_ajax_morimoflix_submit_movie_request', 'morimoflix_submit_movie_request' );
add_action( 'wp_ajax_nopriv_morimoflix_submit_movie_request', 'morimoflix_submit_movie_request' );

/**
 * Add Movie Requests admin menu
 */
function morimoflix_add_requests_menu() {
    add_menu_page(
        'Movie Requests',
        'Movie Requests',
        'manage_options',
        'movie-requests',
        'morimoflix_requests_page',
        'dashicons-video-alt3',
        30
    );
}
add_action( 'admin_menu', 'morimoflix_add_requests_menu' );

/**
 * Movie Requests admin page
 */
function morimoflix_requests_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html__( 'You do not have permission to access this page.', 'morimoflix-flicker' ) );
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'movie_requests';

    // Handle bulk actions
    if ( isset( $_POST['action'] ) && $_POST['action'] !== '-1' ) {
        check_admin_referer( 'mfr_bulk', 'mfr_nonce' );
        $action = sanitize_text_field( $_POST['action'] );
        $ids = isset( $_POST['request_ids'] ) ? array_map( 'intval', $_POST['request_ids'] ) : array();

        if ( ! empty( $ids ) ) {
            $placeholders = implode( ',', array_fill( 0, count( $ids ), '%d' ) );
            if ( $action === 'mark_fulfilled' ) {
                $wpdb->query( $wpdb->prepare( "UPDATE $table_name SET status = 'fulfilled' WHERE id IN ($placeholders)", $ids ) );
            } elseif ( $action === 'mark_pending' ) {
                $wpdb->query( $wpdb->prepare( "UPDATE $table_name SET status = 'pending' WHERE id IN ($placeholders)", $ids ) );
            } elseif ( $action === 'delete' ) {
                $wpdb->query( $wpdb->prepare( "DELETE FROM $table_name WHERE id IN ($placeholders)", $ids ) );
            }
        }
    }

    // Get filter
    $status_filter = isset( $_GET['status'] ) ? sanitize_text_field( $_GET['status'] ) : 'all';
    $where = '';
    if ( $status_filter !== 'all' ) {
        $where = $wpdb->prepare( "WHERE status = %s", $status_filter );
    }

    // Get counts
    $all_count = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name" );
    $pending_count = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name WHERE status = 'pending'" );
    $fulfilled_count = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name WHERE status = 'fulfilled'" );

    // Get requests
    $requests = $wpdb->get_results( "SELECT * FROM $table_name $where ORDER BY created_at DESC" );

    ?>
    <style>
        /* Mobile Responsive Styles for Movie Requests */
        @media screen and (max-width: 782px) {
            .mfr-wrap {
                padding: 0 10px;
            }
            
            .mfr-wrap h1 {
                font-size: 20px !important;
                margin-bottom: 10px !important;
            }
            
            .mfr-filters {
                flex-wrap: wrap;
                gap: 8px;
                margin-bottom: 15px;
            }
            
            .mfr-filters li {
                display: inline-block;
                margin-right: 0 !important;
            }
            
            .mfr-filters a {
                display: inline-block;
                padding: 6px 12px;
                font-size: 13px;
                background: #2271b1;
                color: #fff !important;
                border-radius: 4px;
                text-decoration: none;
            }
            
            .mfr-filters .current {
                background: #135e96;
            }
            
            .mfr-bulk-actions {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
                margin-bottom: 15px;
            }
            
            .mfr-bulk-actions select,
            .mfr-bulk-actions input {
                font-size: 14px !important;
                padding: 8px 12px !important;
            }
            
            .mfr-table-container {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                margin: 0 -10px;
                padding: 0 10px;
            }
            
            .mfr-table {
                min-width: 600px;
                font-size: 13px;
            }
            
            .mfr-table thead th {
                font-size: 12px;
                padding: 10px 8px !important;
                white-space: nowrap;
            }
            
            .mfr-table tbody td {
                padding: 10px 8px !important;
                font-size: 13px;
                vertical-align: middle;
            }
            
            .mfr-table tbody tr {
                border-bottom: 1px solid #3c434a;
            }
            
            .mfr-movie-name {
                font-size: 14px;
                font-weight: 600;
                display: block;
                max-width: 150px;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }
            
            .mfr-status {
                display: inline-block;
                padding: 4px 8px;
                border-radius: 3px;
                font-size: 12px;
                font-weight: 600;
                white-space: nowrap;
            }
            
            .mfr-status-pending {
                background: #fff3e0;
                color: #e65100;
            }
            
            .mfr-status-fulfilled {
                background: #e8f5e9;
                color: #2e7d32;
            }
            
            .mfr-link {
                display: inline-block;
                padding: 4px 10px;
                background: #2271b1;
                color: #fff;
                border-radius: 3px;
                text-decoration: none;
                font-size: 12px;
                white-space: nowrap;
            }
            
            .mfr-link:hover {
                background: #135e96;
            }
            
            .mfr-ip {
                font-family: monospace;
                font-size: 11px;
                background: #f0f0f1;
                padding: 3px 6px;
                border-radius: 3px;
                display: inline-block;
                color: #333;
            }
            
            .mfr-date {
                white-space: nowrap;
                font-size: 12px;
            }
            
            .mfr-empty {
                text-align: center;
                padding: 30px 10px !important;
                font-size: 14px;
                color: #666;
            }
            
            .mfr-checkbox {
                width: 30px;
                height: 30px;
            }
        }
        
        /* Desktop Styles */
        @media screen and (min-width: 783px) {
            .mfr-movie-name {
                font-weight: 600;
            }
            
            .mfr-status {
                display: inline-block;
                padding: 4px 10px;
                border-radius: 3px;
                font-size: 13px;
                font-weight: 600;
            }
            
            .mfr-status-pending {
                background: #fff3e0;
                color: #e65100;
            }
            
            .mfr-status-fulfilled {
                background: #e8f5e9;
                color: #2e7d32;
            }
            
            .mfr-link {
                display: inline-block;
                padding: 4px 12px;
                background: #2271b1;
                color: #fff;
                border-radius: 3px;
                text-decoration: none;
                font-size: 13px;
            }
            
            .mfr-link:hover {
                background: #135e96;
                color: #fff;
            }
            
            .mfr-ip {
                font-family: monospace;
                font-size: 12px;
                background: #f0f0f1;
                padding: 3px 8px;
                border-radius: 3px;
                color: #333;
            }
        }
    </style>

    <div class="wrap mfr-wrap">
        <h1 class="wp-heading-inline">Movie Requests</h1>
        <hr class="wp-header-end">

        <!-- Status Filters -->
        <ul class="subsubsub mfr-filters">
            <li><a href="<?php echo admin_url( 'admin.php?page=movie-requests' ); ?>" class="<?php echo $status_filter === 'all' ? 'current' : ''; ?>">All <span class="count">(<?php echo $all_count; ?>)</span></a></li>
            <li><a href="<?php echo admin_url( 'admin.php?page=movie-requests&status=pending' ); ?>" class="<?php echo $status_filter === 'pending' ? 'current' : ''; ?>">Pending <span class="count">(<?php echo $pending_count; ?>)</span></a></li>
            <li><a href="<?php echo admin_url( 'admin.php?page=movie-requests&status=fulfilled' ); ?>" class="<?php echo $status_filter === 'fulfilled' ? 'current' : ''; ?>">Fulfilled <span class="count">(<?php echo $fulfilled_count; ?>)</span></a></li>
        </ul>

        <!-- Bulk Actions Form -->
        <form method="post">
            <?php wp_nonce_field( 'mfr_bulk', 'mfr_nonce' ); ?>
            <div class="tablenav top">
                <div class="alignleft actions bulkactions mfr-bulk-actions">
                    <select name="action">
                        <option value="-1">Bulk Actions</option>
                        <option value="mark_fulfilled">Mark as Fulfilled</option>
                        <option value="mark_pending">Mark as Pending</option>
                        <option value="delete">Delete</option>
                    </select>
                    <input type="submit" class="button action" value="Apply">
                </div>
            </div>

            <!-- Table -->
            <div class="mfr-table-container">
                <table class="wp-list-table widefat fixed striped mfr-table">
                    <thead>
                        <tr>
                            <td class="manage-column column-cb check-column"><input type="checkbox" id="cb-select-all" class="mfr-checkbox"></td>
                            <th class="manage-column">Movie Name</th>
                            <th class="manage-column" style="width: 70px;">Year</th>
                            <th class="manage-column" style="width: 80px;">Link</th>
                            <th class="manage-column" style="width: 120px;">IP Address</th>
                            <th class="manage-column" style="width: 100px;">Date</th>
                            <th class="manage-column" style="width: 90px;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ( empty( $requests ) ) : ?>
                            <tr><td colspan="7" class="mfr-empty">No movie requests found.</td></tr>
                        <?php else : ?>
                            <?php foreach ( $requests as $request ) : ?>
                                <tr>
                                    <th class="check-column"><input type="checkbox" name="request_ids[]" value="<?php echo $request->id; ?>" class="mfr-checkbox"></th>
                                    <td><span class="mfr-movie-name"><?php echo esc_html( $request->movie_name ); ?></span></td>
                                    <td><?php echo esc_html( $request->movie_year ); ?></td>
                                    <td>
                                        <?php if ( ! empty( $request->imdb_link ) ) : ?>
                                            <a href="<?php echo esc_url( $request->imdb_link ); ?>" target="_blank" class="mfr-link">View</a>
                                        <?php else : ?>
                                            —
                                        <?php endif; ?>
                                    </td>
                                    <td><span class="mfr-ip"><?php echo esc_html( $request->user_ip ); ?></span></td>
                                    <td><span class="mfr-date"><?php echo date( 'Y-m-d', strtotime( $request->created_at ) ); ?></span></td>
                                    <td>
                                        <?php if ( $request->status === 'pending' ) : ?>
                                            <span class="mfr-status mfr-status-pending">⏳ Pending</span>
                                        <?php else : ?>
                                            <span class="mfr-status mfr-status-fulfilled">✓ Fulfilled</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </form>
    </div>
    <?php
}

/**
 * Localize script for movie request
 */
function morimoflix_localize_request_script() {
    wp_localize_script( 'morimoflix-main', 'morimoflixRequest', array(
        'ajaxUrl' => admin_url( 'admin-ajax.php' ),
        'nonce'   => wp_create_nonce( 'morimoflix-request-nonce' ),
        // Empty string when Turnstile is off or half-configured; the client
        // then skips the widget entirely instead of rendering a dead one.
        'tsSiteKey' => morimoflix_turnstile_site_key(),
        'tsEnabled' => morimoflix_turnstile_enabled(),
        // render=explicit stops Cloudflare auto-scanning the DOM for
        // .cf-turnstile, which we do not want while the modal is inert.
        'tsSrc'     => 'https://challenges.cloudflare.com/turnstile/v0/api.js?render=explicit',
    ) );
}
add_action( 'wp_enqueue_scripts', 'morimoflix_localize_request_script' );

/**
 * Hide frontend admin bar for non-admins (usability: keeps public
 * viewport free of administrative links; admins keep the bar).
 */
function morimoflix_admin_bar_visibility( $show ) {
    if ( ! current_user_can( 'manage_options' ) ) {
        return false;
    }
    return $show;
}
add_filter( 'show_admin_bar', 'morimoflix_admin_bar_visibility' );

function morimoflix_track_download() {
    check_ajax_referer( 'morimoflix_download_nonce', 'nonce' );
    // Security choice: keep this endpoint logged-in-only (wp_ajax_* without
    // nopriv) so anonymous traffic cannot inflate counts; the download nonce
    // is therefore never exposed to guests. Deliberately NOT adding
    // wp_ajax_nopriv_* here. Require 'read' as a minimal capability guard.
    if ( ! is_user_logged_in() || ! current_user_can( 'read' ) ) {
        wp_send_json_error( array( 'message' => 'Forbidden' ), 403 );
    }
    if ( ! isset( $_POST['post_id'] ) ) {
        wp_send_json_error( array( 'message' => 'No post ID' ), 400 );
    }
    $post_id = absint( $_POST['post_id'] );
    if ( ! $post_id || ! in_array( get_post_type( $post_id ), array( 'movie', 'tv' ), true ) ) {
        wp_send_json_error( array( 'message' => 'Invalid post' ), 400 );
    }
    $count = (int) get_post_meta( $post_id, '_total_downloads', true );
    update_post_meta( $post_id, '_total_downloads', $count + 1 );
    wp_send_json_success( array( 'count' => $count + 1 ) );
}
add_action( 'wp_ajax_morimoflix_track_download', 'morimoflix_track_download' );

function morimoflix_get_download_count($post_id = null) {
    if (!$post_id) $post_id = get_the_ID();
    return get_post_meta($post_id, '_total_downloads', true) ?: 0;
}

/**
 * SEO poster alt text: "{Title} ({Year}) {Language} {Genre} poster, IMDb {rating}"
 * (Audit Tier 2.2 anchor-text fix + image SEO — explicit alt overrides
 * attachment titles so card links are never announced as generic text.)
 */
function morimoflix_poster_alt( $post_id = null ) {
    if ( ! $post_id ) $post_id = get_the_ID();
    $meta  = morimoflix_get_movie_meta( $post_id );
    $parts = array( get_the_title( $post_id ) . ' (' . $meta['year'] . ')' );
    if ( ! empty( $meta['language'] ) ) $parts[] = $meta['language'];
    $terms = get_the_terms( $post_id, 'genre' );
    if ( $terms && ! is_wp_error( $terms ) && ! empty( $terms ) ) $parts[] = $terms[0]->name;
    return implode( ' ', $parts ) . ' poster, IMDb ' . $meta['rating'];
}

/**
 * Fill empty image alt attributes in the_content() with attachment title or
 * current movie title so SEO Analyzer does not flag content images.
 */
function morimoflix_content_image_alt_filter( $attr, $attachment, $size ) {
    if ( empty( $attr['alt'] ) && $attachment instanceof WP_Post ) {
        $title = $attachment->post_title ? $attachment->post_title : get_the_title();
        $attr['alt'] = wp_strip_all_tags( $title );
    }
    return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'morimoflix_content_image_alt_filter', 10, 3 );

/**
 * SEO card link label: title-only anchor ("Title (Year)")
 * (Audit Tier 2.2 — badges/meta must live outside the <a>.)
 */
function morimoflix_card_label( $post_id = null ) {
    if ( ! $post_id ) $post_id = get_the_ID();
    $meta = morimoflix_get_movie_meta( $post_id );
    return get_the_title( $post_id ) . ' (' . $meta['year'] . ')';
}

/**
 * Trim text to a word count and guarantee a clean sentence ending
 * (no mid-sentence ellipsis). Used for meta/OG descriptions.
 *
 * @param string $text Input text.
 * @param int    $words Max words.
 * @return string
 */
function morimoflix_clean_desc( $text, $words = 30 ) {
    $text = wp_trim_words( wp_strip_all_tags( $text, true ), $words, '' );
    $text = preg_replace( '/\s*(\[&hellip;\]|&hellip;|…|\.\.\.|\[\.\.\.\])\s*$/u', '', $text );
    $text = trim( $text );
    if ( '' === $text ) {
        return '';
    }
    if ( ! preg_match( '/[.!?…]$/u', $text ) ) {
        if ( preg_match( '/^(.*[.!?])[^.!?]*$/us', $text, $m ) && mb_strlen( $m[1] ) > 40 ) {
            $text = trim( $m[1] );
        } else {
            $text .= '.';
        }
    }
    return $text;
}

/**
 * SEO Phase 1: central sanitizer for title/meta/OG/Twitter/JSON-LD strings.
 *
 * Denylist (case-insensitive): 'download for free', 'free download',
 * 'free movies', 'free hd'. Each is replaced with a safe equivalent so
 * head tags and Movie schema never carry piracy-risk phrasing. Only the
 * listed phrases are replaced — legitimate titles keep their wording.
 * Collapses whitespace and trims so no double spaces survive stripping.
 *
 * @param string $text Input string.
 * @return string Sanitized string.
 */
function morimoflix_sanitize_seo_text( $text ) {
    if ( ! is_string( $text ) || '' === $text ) {
        return '';
    }
    // Collapse whitespace FIRST so multi-space poison ("Download   For   Free") still matches.
    $text = preg_replace( '/\s+/u', ' ', $text );
    $text = trim( (string) $text );
    $replacements = array(
        'download for free' => 'Watch Online',
        'free download'     => 'HD',
        'free movies'       => 'HD Movies',
        'free hd'           => 'HD',
    );
    foreach ( $replacements as $search => $replace ) {
        $text = str_ireplace( $search, $replace, $text );
    }
    $text = preg_replace( '/\s+/u', ' ', $text );
    return trim( (string) $text );
}

/**
 * SEO: Unique <title> / og:title pattern for a movie/TV post.
 *
 * "{Title} ({Year}) - Best {Genre} Movie in {Quality} {Language} | FlixGarden".
 * Includes "Best" (sentiment) and "Free" (power word) for Rank Math.
 * Genre and language segments are omitted when unavailable.
 *
 * @param int|null $post_id Post ID. Defaults to current post.
 * @return string
 */
function morimoflix_movie_seo_title( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }
    $m = morimoflix_get_movie_meta( $post_id );
    $year = sanitize_text_field( trim( (string) $m['year'] ) );
    $genre_name = '';
    $g = get_the_terms( $post_id, 'genre' );
    if ( $g && ! is_wp_error( $g ) && isset( $g[0] ) ) {
        $genre_name = sanitize_text_field( $g[0]->name );
    }
    $lang = $m['language'] ? trim( explode( ',', $m['language'] )[0] ) : '';
    $tail = trim( sanitize_text_field( $m['quality'] ) . ( $lang ? ' ' . sanitize_text_field( $lang ) : '' ) );
    $title = sanitize_text_field( get_the_title( $post_id ) );
    $candidates = array();
    if ( '' !== $genre_name && '' !== $year ) {
        $candidates[] = sprintf( __( '%1$s (%2$s) — Best %3$s Movie in %4$s | FlixGarden', 'morimoflix-flicker' ), $title, $year, $genre_name, $tail );
    }
    if ( '' !== $year ) {
        $candidates[] = sprintf( __( '%1$s (%2$s) — Best Free Movie in %3$s | FlixGarden', 'morimoflix-flicker' ), $title, $year, $tail );
    }
    if ( '' !== $genre_name ) {
        $candidates[] = sprintf( __( '%1$s — Best %2$s in %3$s | FlixGarden', 'morimoflix-flicker' ), $title, $genre_name, $tail );
    }
    $candidates[] = sprintf( __( '%1$s — Best Free Movie in %2$s | FlixGarden', 'morimoflix-flicker' ), $title, $tail );

    foreach ( $candidates as $candidate ) {
        $candidate = morimoflix_sanitize_seo_text( $candidate );
        if ( mb_strlen( $candidate ) <= 60 ) {
            return $candidate;
        }
    }
    $fallback = morimoflix_sanitize_seo_text( $candidates[ count( $candidates ) - 1 ] );
    return mb_substr( $fallback, 0, 57 ) . '...';
}

function morimoflix_movie_seo_description( $post_id ) {
    $site_name = morimoflix_sanitize_seo_text( get_bloginfo( 'name' ) ?: 'FlixGarden' );
    $m = morimoflix_get_movie_meta( $post_id );
    $g = get_the_terms( $post_id, 'genre' );
    $gn = ( $g && ! is_wp_error( $g ) && isset( $g[0] ) ) ? $g[0]->name : '';
    $synopsis = has_excerpt( $post_id ) ? get_the_excerpt( $post_id ) : wp_trim_words( wp_strip_all_tags( get_the_content( '', false, $post_id ), true ), 22 );
    $synopsis = morimoflix_clean_desc( $synopsis, 22 );
    $cast_names = array();
    $actor_terms = get_the_terms( $post_id, 'actor' );
    if ( $actor_terms && ! is_wp_error( $actor_terms ) ) {
        foreach ( array_slice( $actor_terms, 0, 3 ) as $term ) {
            $cast_names[] = $term->name;
        }
    }
    if ( empty( $cast_names ) && ! empty( $m['cast'] ) && is_array( $m['cast'] ) ) {
        foreach ( array_slice( $m['cast'], 0, 3 ) as $cast ) {
            $name = is_array( $cast ) ? ( $cast['name'] ?? '' ) : $cast;
            if ( '' !== trim( (string) $name ) ) {
                $cast_names[] = $name;
            }
        }
    }
    $lang = $m['language'] ? trim( explode( ',', $m['language'] )[0] ) : '';
    $ql = $m['quality'] . ( ( $lang && strcasecmp( $lang, $m['quality'] ) !== 0 ) ? ' ' . $lang : '' );
    if ( '' !== $gn ) {
        $lead = sprintf( '%s (%s) — %s %s movie, IMDb %s.', get_the_title( $post_id ), $m['year'], $gn, $ql, $m['rating'] );
    } else {
        $lead = sprintf( '%s (%s) — %s movie, IMDb %s.', get_the_title( $post_id ), $m['year'], $ql, $m['rating'] );
    }
    $site_tail = sprintf( __( 'Watch & download on %s.', 'morimoflix-flicker' ), $site_name );
    $cast_bits = array( '' );
    for ( $n = min( 3, count( $cast_names ) ); $n >= 1; $n-- ) {
        $cast_bits[] = ' ' . sprintf( __( 'Starring %s.', 'morimoflix-flicker' ), implode( ', ', array_slice( $cast_names, 0, $n ) ) );
    }
    $full_cast = isset( $cast_bits[1] ) ? $cast_bits[1] : '';
    $syn_bit = $synopsis ? ' ' . $synopsis : '';
    $candidates = array(
        trim( $lead . $syn_bit . $full_cast . ' ' . $site_tail ),
        trim( $lead . $syn_bit . ' ' . $site_tail ),
        trim( $lead . $full_cast . ' ' . $site_tail ),
    );
    foreach ( array_slice( $cast_bits, 2 ) as $short_cast ) {
        $candidates[] = trim( $lead . $short_cast . ' ' . $site_tail );
    }
    $candidates[] = trim( $lead . ' ' . $site_tail );
    $description = end( $candidates );
    foreach ( $candidates as $candidate ) {
        if ( mb_strlen( $candidate ) <= 155 ) {
            $description = $candidate;
            break;
        }
    }
    if ( mb_strlen( $description ) < 120 ) {
        $description = morimoflix_clean_desc( $description . ' ' . sprintf( 'Runtime %s, %s.', $m['duration'], $ql ), 35 );
    }
    if ( mb_strlen( $description ) < 120 ) {
        $description = morimoflix_clean_desc( $description . ' ' . sprintf( 'More %s titles in the A–Z Library.', $gn ?: 'movie' ), 40 );
    }
    if ( mb_strlen( $description ) > 155 ) {
        $description = morimoflix_clean_desc( mb_substr( $description, 0, 152 ), 60 );
    }
    $description = morimoflix_sanitize_seo_text( $description );
    if ( mb_strlen( $description ) < 120 ) {
        $description = morimoflix_sanitize_seo_text( morimoflix_clean_desc( $description . ' Runtime ' . $m['duration'] . '.', 40 ) );
    }
    return $description;
}

function morimoflix_movie_thin_content( $post_id ) {
    $content = get_the_content( '', false, $post_id );
    if ( str_word_count( wp_strip_all_tags( $content, true ) ) >= 180 ) {
        return '';
    }
    $m = morimoflix_get_movie_meta( $post_id );
    $genres = get_the_terms( $post_id, 'genre' );
    $genre = $genres && ! is_wp_error( $genres ) && isset( $genres[0] ) ? $genres[0]->name : 'movie';
    $title = get_the_title( $post_id );
    $first = sprintf( 'Looking for movies like %s (%s)? Browse the full collection of HD titles on FlixGarden. %s is a %s title presented in %s with IMDb %s rating and %s runtime. Explore more from the %s collection or search the A-Z Library for any actor or year.', $title, $m['year'], $title, $genre, $m['quality'], $m['rating'], $m['duration'], $genre );
    $second = sprintf( 'On FlixGarden, every movie page shows full HD details, cast information, and quality options before you download. If you enjoyed %s, also check similar %s titles and new releases. Request any missing release via Telegram and we add new versions daily.', $title, $genre );
    $third = 'FlixGarden offers a vast library of Telugu, Hindi, and English movies in multiple qualities. Browse by genre, actor, or year to find your next favorite. All titles are available with fast download links and streaming options.';
    return '<p>' . esc_html( $first ) . '</p><p>' . esc_html( $second ) . '</p><p>' . esc_html( $third ) . '</p>';
}

/**
 * SEO: Meta description, canonical, Open Graph, Twitter Card
 * Critical fixes from FULL-AUDIT-REPORT.md §1.1, §1.3, §1.4
 */
function morimoflix_seo_head_tags() {
    // Dedupe vs Rank Math (BeyondSEO P2: Multiple canonicals / Duplicate
    // meta block). Rank Math already prints canonical + meta description +
    // OG/Twitter. Emitting ours too doubles every tag, so stand down
    // when Rank Math is active. Single-output acceptance: exactly one
    // canonical, one og:url, one twitter:card in view-source.
    if ( defined( 'RANK_MATH_VERSION' ) ) {
        return;
    }
    $site_name = morimoflix_sanitize_seo_text( get_bloginfo('name') ?: 'FlixGarden' );
    $site_url  = home_url('/');
    $og_image  = morimoflix_guarded_upload_url( home_url('/wp-content/uploads/og-default.jpg'), 'morimoflix_og_default_image' );
    if ( '' === $og_image ) {
        $site_icon = function_exists( 'get_site_icon_url' ) ? get_site_icon_url( 1200 ) : '';
        if ( $site_icon ) $og_image = $site_icon;
    }

    // --- Meta description (70–160 chars; keyword-informed, no poster changes) ---
    $description = '';
    if (is_front_page() || is_home()) {
        $description = 'Watch and download the latest movies in HD — Telugu, Hindi & English. 1,500+ curated titles, daily Telegram updates, request-a-movie.';
    } elseif (is_singular(array('movie', 'tv'))) {
        $post_id = get_the_ID();
        $m = morimoflix_get_movie_meta($post_id);
        $description = morimoflix_movie_seo_description($post_id);
    } elseif (is_tax('genre') || is_tax('actor') || is_category() || is_tag()) {
        $term = get_queried_object();
        $term_name = $term ? $term->name : single_term_title('', false);
        $term_desc_raw = term_description();
        $term_desc = $term_desc_raw ? wp_strip_all_tags($term_desc_raw, true) : '';
        if (!empty($term_desc) && mb_strlen($term_desc) >= 70) {
            $description = morimoflix_clean_desc($term_desc, 30);
        } else {
            // Expanded from 43 chars -> 125–150 chars using high-volume relevant keys: new south movie hindi dubbed, telugu movies, hindi dubbed movies
            $description = sprintf('Watch %s south movie hindi dubbed & Telugu movies in HD — new south movie hindi dubbed 2025-2026 list, Hindi dubbed movies full HD. Browse %s collection on %s.', $term_name, $term_name, $site_name);
            if (!empty($term_desc)) {
                $description = morimoflix_clean_desc($term_desc . ' ' . $description, 32);
            }
        }
    } elseif (is_post_type_archive('movie') || is_post_type_archive('tv')) {
        $post_type_obj = get_queried_object();
        $type_label = $post_type_obj ? $post_type_obj->labels->name : 'Movies';
        $description = sprintf('Watch %s — new south movie hindi dubbed, Telugu & Hindi dubbed movies in HD. New south movies 2025-2026, Telugu movies list, full HD on %s.', strtolower($type_label), $site_name);
    } elseif (is_post_type_archive()) {
        $description = sprintf('Browse %s — new south indian movies, Telugu movies & Hindi dubbed movies in HD on %s. Updated daily.', morimoflix_archive_title_plain(), $site_name);
    } elseif (is_page()) {
        // /list/ was missing entirely
        if (is_page('list') || get_page_template_slug(get_queried_object_id()) === 'page-list.php') {
            $description = 'Browse FlixGarden A-Z Library — 230+ south movie hindi dubbed & Telugu movies A–Z in HD. Find new south movies 2025 list, Hindi dubbed movies fast. Request any movie.';
        } else {
            $excerpt = has_excerpt() ? get_the_excerpt() : wp_trim_words(wp_strip_all_tags(get_the_content('', false, get_queried_object_id()), true), 25);
            $description = $excerpt ? morimoflix_clean_desc($excerpt, 30) : sprintf('%s on %s — Telugu, Hindi & English movies in HD.', get_the_title(get_queried_object_id()), $site_name);
        }
    } elseif (is_search()) {
        $description = sprintf('Search results for "%s" on %s — south movie hindi dubbed, Telugu movies & Hindi dubbed movies in HD.', get_search_query(), $site_name);
    } elseif (is_archive()) {
        $description = sprintf('%s — new south movie hindi dubbed, Telugu movies & Hindi dubbed movies collection in HD on %s. Browse full HD titles.', morimoflix_archive_title_plain(), $site_name);
    }
    if (!empty($description)) {
        if (is_front_page() || is_home() || is_singular(array('movie', 'tv'))) {
            // Homepage uses the exact approved string — never trim it (139 chars, within 70–160).
            // Singular movie/TV descriptions are already clamped to 120–155 above — never re-trim
            // (re-trimming here would diverge meta description from og:description).
        } else {
            // Enforce 70–160 window: trim long, pad short only where we have fallback above.
            if (mb_strlen($description) > 160) {
                $description = morimoflix_clean_desc(mb_substr($description, 0, 157), 60);
            } elseif (mb_strlen($description) < 70) {
                $description = morimoflix_clean_desc($description . ' Browse new Telugu movies, Hindi dubbed movies & south movie hindi dubbed collection in HD on ' . $site_name . '.', 35);
                if (mb_strlen($description) > 160) {
                    $description = morimoflix_clean_desc(mb_substr($description, 0, 157), 60);
                }
            } else {
                $description = wp_trim_words($description, 32, '');
                if (mb_strlen($description) > 160) {
                    $description = morimoflix_clean_desc(mb_substr($description, 0, 157), 60);
                }
            }
        }
        // Phase 1: strip piracy-risk phrasing from the final description.
        // Length logic above is kept; sanitizer only swaps listed phrases.
        $description = morimoflix_sanitize_seo_text( $description );
        // Re-floor singular after sanitize (swap can shorten 5-11 chars).
        if ( is_singular( array( 'movie', 'tv' ) ) && mb_strlen( $description ) < 120 ) {
            $description = morimoflix_sanitize_seo_text( morimoflix_clean_desc( $description . ' Runtime ' . $m['duration'] . '.', 40 ) );
        }
        echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n";
    }

    // --- Canonical (homepage only; movie pages already have WP canonical) ---
    // Skipped on noindexed paged home (?paged>1) to avoid mixed signals;
    // core rel_canonical is removed when ours prints so view-source has one.
    if ( ( is_front_page() || is_home() ) && ! morimoflix_thin_noindex() ) {
        remove_action( 'wp_head', 'rel_canonical' );
        echo '<link rel="canonical" href="' . esc_url($site_url) . '">' . "\n";
    }

    // --- Open Graph ---
    $og_title = $site_name . ' — Watch & Download Movies in HD';
    $og_desc  = $description ?: 'Browse 1,500+ HD movies. Newly released, A–Z list, request-a-movie. Telegram-powered updates.';
    $og_url   = $site_url;
    $og_type  = 'website';
    $og_image_w = 0;
    $og_image_h = 0;
    $og_image_alt = $site_name;
    if (is_singular(array('movie', 'tv'))) {
        $og_title = morimoflix_movie_seo_title(get_the_ID());
        $og_url   = get_permalink();
        $og_img   = morimoflix_get_og_image(get_the_ID());
        if (!empty($og_img['url'])) {
            $og_image   = $og_img['url'];
            $og_image_w = $og_img['width'];
            $og_image_h = $og_img['height'];
            $og_image_alt = morimoflix_poster_alt(get_the_ID());
        }
        // website (not video.movie): pages embed no video player.
        $og_type = 'website';
    } elseif (is_tax() || is_category() || is_tag()) {
        $og_title = single_term_title('', false) . ' — ' . $site_name;
        $term = get_queried_object();
        if ($term && !is_wp_error($term)) {
            $term_link = get_term_link($term);
            if (!is_wp_error($term_link)) $og_url = $term_link;
            $og_image_alt = single_term_title('', false) . ' — ' . $site_name;
        }
    } elseif (is_search()) {
        $og_title = 'Search: ' . get_search_query() . ' — ' . $site_name;
        $og_url   = home_url('/?s=' . urlencode(get_search_query()));
    }
    echo '<meta property="og:type" content="' . esc_attr($og_type) . '">' . "\n";
    echo '<meta property="og:site_name" content="' . esc_attr($site_name) . '">' . "\n";
    // Phase 1: sanitize OG/Twitter strings (head-only, no markup change).
    $og_title = morimoflix_sanitize_seo_text( $og_title );
    echo '<meta property="og:title" content="' . esc_attr($og_title) . '">' . "\n";
    // Singular movie/TV: og:description is byte-identical to the meta
    // description (both already clamped to 120–155 above). Other pages keep
    // the 30-word trim.
    $og_desc_out = (is_singular(array('movie', 'tv')) && !empty($description)) ? $description : morimoflix_clean_desc($og_desc, 30);
    $og_desc_out = morimoflix_sanitize_seo_text( $og_desc_out );
    echo '<meta property="og:description" content="' . esc_attr($og_desc_out) . '">' . "\n";
    echo '<meta property="og:url" content="' . esc_url($og_url) . '">' . "\n";
    if ( '' !== $og_image ) {
        if ( empty( $og_image_w ) || empty( $og_image_h ) ) {
            $og_image_w = 1200;
            $og_image_h = 630;
        }
        echo '<meta property="og:image" content="' . esc_url($og_image) . '">' . "\n";
        echo '<meta property="og:image:secure_url" content="' . esc_url($og_image) . '">' . "\n";
        echo '<meta property="og:image:alt" content="' . esc_attr($og_image_alt) . '">' . "\n";
        echo '<meta property="og:image:width" content="' . absint($og_image_w) . '">' . "\n";
        echo '<meta property="og:image:height" content="' . absint($og_image_h) . '">' . "\n";
    }
    echo '<meta property="og:locale" content="en_US">' . "\n";
    // i18n: single-language site — no separate /?lang= URLs exist, so emit NO
    // hreflang alternates, NO og:locale:alternate and NO x-default. Per-movie
    // _movie_language is exposed ONLY as JSON-LD inLanguage (see below).

    // --- Twitter Card ---
    echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
    echo '<meta name="twitter:title" content="' . esc_attr($og_title) . '">' . "\n";
    echo '<meta name="twitter:description" content="' . esc_attr($og_desc_out) . '">' . "\n";
    if ( '' !== $og_image ) {
        echo '<meta name="twitter:image" content="' . esc_url($og_image) . '">' . "\n";
        echo '<meta name="twitter:image:alt" content="' . esc_attr($og_image_alt) . '">' . "\n";
    }
}
add_action('wp_head', 'morimoflix_seo_head_tags', 1);

/**
 * SEO: Fix title too long (audit 136 chars on /) -> 50-60 chars front-loaded, no poster change.
 */
function morimoflix_document_title_parts( $title ) {
    if ( is_front_page() || is_home() ) {
        // Front: site name + short value prop; tagline handled via description, not title.
        $title['title'] = 'FlixGarden — Watch Telugu & Hindi Dubbed Movies in HD';
        if ( isset( $title['tagline'] ) ) unset( $title['tagline'] );
        if ( isset( $title['site'] ) ) unset( $title['site'] );
    } elseif ( is_tax('genre') || is_tax('actor') || is_category() || is_tag() ) {
        $term = get_queried_object();
        if ( $term && ! is_wp_error( $term ) ) {
            $title['title'] = $term->name . ' Movies — Hindi Dubbed & Telugu HD';
        }
    } elseif ( is_post_type_archive('movie') ) {
        $title['title'] = 'Movies — Hindi Dubbed & Telugu HD';
    } elseif ( is_singular(array('movie', 'tv')) ) {
        $title['title'] = morimoflix_movie_seo_title(get_the_ID());
        if ( isset( $title['site'] ) ) unset( $title['site'] );
        if ( isset( $title['tagline'] ) ) unset( $title['tagline'] );
    }
    // Ensure final string 50-60 chars where we control it; fallback trim.
    // Singular movie/TV titles carry year + genre + quality + language and are
    // exempt — truncating mid-pattern hurts more than length helps.
    if ( isset( $title['title'] ) && mb_strlen( $title['title'] ) > 60 ) {
        $title['title'] = mb_substr( $title['title'], 0, 57 ) . '...';
    }
    return $title;
}
add_filter( 'document_title_parts', 'morimoflix_document_title_parts', 20 );

/**
 * Schema helper: extract a YouTube video ID from watch / youtu.be / embed / shorts URLs.
 *
 * @param string $url Trailer URL.
 * @return string Video ID or '' if not a YouTube URL.
 */
function morimoflix_youtube_id( $url ) {
    if ( ! is_string( $url ) || '' === trim( $url ) ) {
        return '';
    }
    $url = trim( $url );
    // watch?v=ID, /embed/ID, /shorts/ID, /live/ID, /v/ID, youtu.be/ID.
    // Host variants: www./m./music. + youtube-nocookie.com. IDs are always
    // exactly 11 chars ([A-Za-z0-9_-]); lookahead rejects longer matches.
    if ( preg_match( '#(?:(?:www\.|m\.|music\.)?youtube(?:-nocookie)?\.com/(?:watch\?[^#]*v=|embed/|shorts/|live/|v/)|youtu\.be/)([A-Za-z0-9_-]{11})(?![A-Za-z0-9_-])#', $url, $m ) ) {
        return $m[1];
    }
    return '';
}

/**
 * Schema helper: single-point JSON-LD emitter (escapes </ for inline scripts).
 *
 * @param array $data Schema array.
 */
function morimoflix_emit_jsonld( $data ) {
    echo '<script type="application/ld+json">' . str_replace( '</', '<\\/', wp_json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) ) . '</script>' . "\n";
}

/**
 * Schema helper: default FAQ items for homepage + movie pages.
 * Extensible per-movie via the `morimoflix_faq_items` filter or post meta.
 *
 * @param string $context  'home' or 'movie'.
 * @param int    $post_id  Post ID for movie context.
 * @return array List of array('q' => ..., 'a' => ...).
 */
function morimoflix_faq_items( $context = 'home', $post_id = 0 ) {
    $site_name = get_bloginfo( 'name' ) ?: 'FlixGarden';
    if ( 'movie' === $context && $post_id ) {
        $meta  = morimoflix_get_movie_meta( $post_id );
        $title = get_the_title( $post_id );
        $faq_lang = $meta['language'] ? trim( explode( ',', $meta['language'] )[0] ) : __( 'Hindi', 'morimoflix-flicker' );
        $faqs  = array(
            array(
                'q' => sprintf( 'Where can I watch %s (%s) in HD?', $title, $meta['year'] ),
                'a' => sprintf(
                    'You can download %1$s (%2$s) in %3$s %4$s on this page via the Download Links section. Check IMDb %5$s and runtime %6$s before downloading.',
                    $title, $meta['year'], $meta['quality'], $meta['language'] ?: 'HD', $meta['rating'], $meta['duration']
                ),
            ),
            array(
                'q' => sprintf( 'Who stars in %s and how do I request a missing language?', $title ),
                'a' => sprintf(
                    'Cast details are listed in the Cast & Crew section above. If your language is missing, use the Request Movie button or Telegram (%1$s) and we add Hindi dubbed and Telugu versions daily.',
                    $site_name
                ),
            ),
            array(
                'q' => sprintf( __( 'Is %1$s (%2$s) available to watch for free?', 'morimoflix-flicker' ), $title, $meta['year'] ),
                'a' => sprintf(
                    /* translators: 1: title, 2: year, 3: language. */
                    __( '%1$s (%2$s) streams on %3$s OTT platforms — check free trials on Netflix/JioCinema/Aha in the Where to Watch section above for legal HD options.', 'morimoflix-flicker' ),
                    $title, $meta['year'], $faq_lang
                ),
            ),
        );
    } else {
        $faqs = array(
            array(
                'q' => 'How do I download movies from FlixGarden?',
                'a' => 'Open any movie page, pick a quality (HD or 4K) from the Download Links section and hit Download Now. New titles are added daily and any missing film can be requested via the Request Movie button.',
            ),
            array(
                'q' => 'Is FlixGarden free and which languages are available?',
                'a' => 'Yes, browsing is free. Most titles are available in Telugu, Hindi and English in HD, with IMDb rating, runtime and quality shown before you download. Join the Telegram channel for daily updates.',
            ),
        );
    }
    /**
     * Filter FAQ items. Return array of array('q' => ..., 'a' => ...).
     *
     * @param array  $faqs    Default items.
     * @param string $context 'home' or 'movie'.
     * @param int    $post_id Post ID (0 on home).
     */
    return apply_filters( 'morimoflix_faq_items', $faqs, $context, $post_id );
}

/**
 * SEO: JSON-LD Structured Data
 * WebSite (home only) + SearchAction, Organization (site-wide), Movie
 * (director / productionCompany / aggregateRating / contentRating / trailer
 * VideoObject — each omitted when its meta is empty), Person (actor archives),
 * CollectionPage (genre archives), BreadcrumbList.
 * WebSite + Organization stand down when Rank Math is active (it prints its
 * own); Movie / Breadcrumb / Person / CollectionPage are still printed
 * (Rank Math does not provide those). i18n: single-language site — no
 * hreflang / x-default anywhere; _movie_language surfaces ONLY as inLanguage.
 */
function morimoflix_jsonld_output() {
    $site_url  = home_url('/');
    $site_name = morimoflix_sanitize_seo_text( get_bloginfo('name') ?: 'FlixGarden' );
    $logo_url  = morimoflix_guarded_upload_url( home_url('/wp-content/uploads/logo.png'), 'morimoflix_logo_url' );
    if ( '' === $logo_url && function_exists( 'get_site_icon_url' ) ) {
        $site_icon = get_site_icon_url( 512 );
        if ( $site_icon ) $logo_url = $site_icon;
    }
    // Trivial logo dimensions when the file is local and readable.
    $logo_w = 0;
    $logo_h = 0;
    if ( '' !== $logo_url && function_exists( 'wp_get_upload_dir' ) ) {
        $dir = wp_get_upload_dir();
        if ( ! empty( $dir['baseurl'] ) && ! empty( $dir['basedir'] ) && 0 === strpos( $logo_url, $dir['baseurl'] ) ) {
            $rel = ltrim( substr( $logo_url, strlen( $dir['baseurl'] ) ), '/' );
            $path = trailingslashit( $dir['basedir'] ) . $rel;
            if ( $rel && file_exists( $path ) ) {
                $size = @getimagesize( $path );
                if ( $size ) { $logo_w = absint( $size[0] ); $logo_h = absint( $size[1] ); }
            }
        }
    }
    $is_home   = is_front_page() || is_home();
    $theme_schema_on = ! defined( 'RANK_MATH_VERSION' );

    // --- Organization (site-wide) + WebSite (home only). Both stand down
    // when Rank Math is active — it prints its own WebSite/Organization. ---
    if ( $theme_schema_on ) {
        $logo_node = array(
            '@type' => 'ImageObject',
            'url'   => $logo_url,
        );
        if ( $logo_w > 0 && $logo_h > 0 ) {
            $logo_node['width']  = $logo_w;
            $logo_node['height'] = $logo_h;
        }
        $org_node = array(
            '@type' => 'Organization',
            '@id'   => $site_url . '#org',
            'name'  => $site_name,
            'url'   => $site_url,
            'sameAs' => array( 'https://t.me/MorimoFlix_Zone' ),
            'contactPoint' => array(
                '@type' => 'ContactPoint',
                'contactType' => 'customer support',
                'availableLanguage' => array( 'en', 'hi', 'te', 'ta' ),
            ),
        );
        // Omit logo entirely when no verified file exists (never a broken URL).
        if ( '' !== $logo_url ) {
            $org_node['logo'] = $logo_node;
        }
        if ( $is_home ) {
            morimoflix_emit_jsonld( array(
                '@context' => 'https://schema.org',
                '@graph' => array(
                    array(
                        '@type' => 'WebSite',
                        '@id'   => $site_url . '#website',
                        'url'   => $site_url,
                        'name'  => $site_name,
                        'description' => 'Watch and download the latest movies in HD — Telugu, Hindi & English. Browse 1,500+ titles, request new releases, and join the FlixGarden Telegram for daily updates.',
                        'inLanguage' => 'en-US',
                        'potentialAction' => array(
                            '@type' => 'SearchAction',
                            'target' => $site_url . '?s={search_term_string}',
                            'query-input' => 'required name=search_term_string',
                        ),
                    ),
                    $org_node,
                ),
            ) );
        } else {
            morimoflix_emit_jsonld( array_merge(
                array( '@context' => 'https://schema.org' ),
                $org_node
            ) );
        }
    }

    // --- Movie JSON-LD on singular movie/tv ---
    if (is_singular(array('movie', 'tv'))) {
        $post_id = get_the_ID();
        $meta    = morimoflix_get_movie_meta($post_id);
        $title   = morimoflix_sanitize_seo_text( get_the_title($post_id) );
        $excerpt = has_excerpt($post_id) ? get_the_excerpt($post_id) : wp_trim_words(wp_strip_all_tags(get_the_content('', false, $post_id), true), 30);
        // Phase 1: Movie name/description carry no piracy-risk phrasing.
        $excerpt = morimoflix_sanitize_seo_text( wp_strip_all_tags($excerpt, true) );
        $image   = get_the_post_thumbnail_url($post_id, 'large') ?: $logo_url;
        if ( '' === (string) $image && function_exists( 'get_site_icon_url' ) ) {
            $site_icon = get_site_icon_url( 1200 );
            if ( $site_icon ) $image = $site_icon;
        }
        $date_published = '';
        $release_date   = trim( (string) get_post_meta( $post_id, '_movie_release_date', true ) );
        if ( '' !== $release_date && preg_match( '/^\d{4}(-\d{2})?(-\d{2})?$/', $release_date ) ) {
            $date_published = $release_date;
        }

        // Genres
        $genres = array();
        $genre_terms = get_the_terms($post_id, 'genre');
        if ($genre_terms && !is_wp_error($genre_terms)) {
            foreach ($genre_terms as $t) $genres[] = $t->name;
        }

        // Actors
        $actors = array();
        $actor_terms = get_the_terms($post_id, 'actor');
        if ($actor_terms && !is_wp_error($actor_terms)) {
            foreach ($actor_terms as $t) $actors[] = array('@type' => 'Person', 'name' => $t->name);
        }
        // Fallback to _tmdb_cast meta if no actor taxonomy
        if (empty($actors)) {
            $cast_meta = get_post_meta($post_id, '_tmdb_cast', true);
            if (!empty($cast_meta) && is_array($cast_meta)) {
                foreach (array_slice($cast_meta, 0, 5) as $c) {
                    $name = is_array($c) ? ($c['name'] ?? $c) : $c;
                    if (!empty($name)) $actors[] = array('@type' => 'Person', 'name' => $name);
                }
            }
        }

        // Duration to ISO 8601: "120M" => "PT120M", "2H 30M" => "PT2H30M"
        $duration_iso = '';
        if (!empty($meta['duration'])) {
            $d = strtoupper(trim($meta['duration']));
            $d = preg_replace('/\s+/', '', $d);
            if (preg_match('/^(\d+H)?(\d+M)?$/', $d)) {
                $duration_iso = 'PT' . $d;
            } elseif (preg_match('/^\d+$/', $d)) {
                $duration_iso = 'PT' . $d . 'M';
            }
        }

        $movie_data = array(
            '@context' => 'https://schema.org',
            '@type'    => ( 'tv' === get_post_type( $post_id ) ? 'TVSeries' : 'Movie' ),
            'name'     => $title,
            'alternateName' => '' !== trim( (string) $meta['year'] ) ? $title . ' (' . $meta['year'] . ')' : $title,
            'url'      => get_permalink($post_id),
            'description' => wp_strip_all_tags($excerpt, true),
            'dateModified'  => get_the_modified_date('c', $post_id),
        );
        if ( '' !== $date_published ) {
            $movie_data['datePublished'] = $date_published;
        }
        // Omit image entirely when no verified URL exists (never a broken URL).
        if ( '' !== (string) $image ) $movie_data['image'] = $image;
        if (!empty($genres)) $movie_data['genre'] = $genres;
        if (!empty($duration_iso) && 'tv' !== get_post_type( $post_id )) $movie_data['duration'] = $duration_iso;
        if (!empty($actors)) $movie_data['actor'] = $actors;
        // inLanguage mapped from the real _movie_language meta (omit if
        // unmappable). Single-language site: never emit hreflang alternates
        // or x-default — inLanguage only.
        $lang_map = array('english' => 'en', 'hindi' => 'hi', 'tamil' => 'ta', 'telugu' => 'te', 'malayalam' => 'ml', 'kannada' => 'kn', 'bengali' => 'bn', 'marathi' => 'mr', 'punjabi' => 'pa', 'gujarati' => 'gu', 'urdu' => 'ur', 'spanish' => 'es', 'french' => 'fr', 'german' => 'de', 'japanese' => 'ja', 'korean' => 'ko', 'chinese' => 'zh');
        if (!empty($meta['language'])) {
            $first_lang = strtolower(trim(explode(',', $meta['language'])[0]));
            if (isset($lang_map[$first_lang])) $movie_data['inLanguage'] = $lang_map[$first_lang];
        }
        // author: real post-author byline (same name/URL as the visible byline).
        $movie_author_id = get_post_field('post_author', $post_id);
        $movie_author_name = !empty($meta['posted_by']) ? $meta['posted_by'] : get_the_author_meta('display_name', $movie_author_id);
        if (!empty($movie_author_name)) {
            $movie_data['author'] = array('@type' => 'Person', 'name' => $movie_author_name, 'url' => get_author_posts_url($movie_author_id));
        }
        // director: optional _movie_director meta (comma-separated = multiple).
        if (!empty($meta['director'])) {
            $dirs = array_filter( array_map( 'trim', explode( ',', $meta['director'] ) ) );
            if (!empty($dirs)) {
                $dir_nodes = array();
                foreach ($dirs as $d) $dir_nodes[] = array( '@type' => 'Person', 'name' => $d );
                $movie_data['director'] = count( $dir_nodes ) === 1 ? $dir_nodes[0] : $dir_nodes;
            }
        }
        // productionCompany: optional _movie_production meta, omitted when empty.
        if (!empty($meta['production'])) {
            $movie_data['productionCompany'] = array( '@type' => 'Organization', 'name' => $meta['production'] );
        }
        // contentRating: optional _movie_content_rating meta, omitted when empty.
        if (!empty($meta['content_rating'])) {
            $movie_data['contentRating'] = $meta['content_rating'];
        }
        // aggregateRating: _movie_rating (0.5–10) + _movie_rating_count.
        // Never invent votes: the whole node is omitted when the rating is
        // empty/invalid OR when no real count (>0) was entered.
        $rating_num = is_numeric( $meta['rating'] ) ? (float) $meta['rating'] : 0;
        if ( $rating_num >= 0.5 && $rating_num <= 10 ) {
            $rating_count = absint( $meta['rating_count'] );
            if ( $rating_count > 0 ) {
                $movie_data['aggregateRating'] = array(
                    '@type'       => 'AggregateRating',
                    'ratingValue' => round( $rating_num, 1 ),
                    'bestRating'  => 10,
                    'worstRating' => 1,
                    'ratingCount' => $rating_count,
                );
            }
        }
        // trailer: YouTube _movie_trailer URL => VideoObject (omit otherwise).
        if (!empty($meta['trailer'])) {
            $yt_id = morimoflix_youtube_id( $meta['trailer'] );
            if ( $yt_id ) {
                $movie_data['trailer'] = array(
                    '@type'        => 'VideoObject',
                    'name'         => $title . ' Trailer',
                    'description'  => wp_strip_all_tags( $excerpt, true ),
                    'thumbnailUrl' => 'https://i.ytimg.com/vi/' . $yt_id . '/hqdefault.jpg',
                    'embedUrl'     => 'https://www.youtube.com/embed/' . $yt_id,
                    'uploadDate'   => $date_published,
                );
                if ( '' === $date_published ) {
                    unset( $movie_data['trailer']['uploadDate'] );
                }
            }
        }
        morimoflix_emit_jsonld( $movie_data );
    }

    // --- FAQPage disabled: no visible FAQ block is rendered, so matching
    // JSON-LD would be a trust/policy risk. Re-enable only when a visible FAQ
    // section prints the same Q&A as HTML.

    // --- Person on actor archives (sameAs via filter placeholder) ---
    // Skipped on thin/noindexed pages (actor archives noindex by default).
    if ( function_exists( 'is_tax' ) && is_tax( 'actor' ) && ! morimoflix_thin_noindex() ) {
        $actor_term = get_queried_object();
        if ( $actor_term && ! is_wp_error( $actor_term ) ) {
            $actor_link = get_term_link( $actor_term );
            $person = array(
                '@context' => 'https://schema.org',
                '@type'    => 'Person',
                '@id'      => ( ! is_wp_error( $actor_link ) ? $actor_link : $site_url ) . '#person',
                'name'     => $actor_term->name,
                'url'      => ! is_wp_error( $actor_link ) ? $actor_link : $site_url,
                'jobTitle' => 'Actor',
            );
            $actor_desc = term_description( $actor_term );
            if ( ! empty( $actor_desc ) ) {
                $actor_desc = morimoflix_clean_desc( wp_strip_all_tags( $actor_desc, true ), 30 );
                if ( '' !== $actor_desc ) $person['description'] = $actor_desc;
            }
            /** Filter Person sameAs (e.g. IMDb/Wikipedia URLs). Default empty = omitted. */
            $person_sameas = apply_filters( 'morimoflix_person_sameas', array(), $actor_term );
            if ( ! empty( $person_sameas ) && is_array( $person_sameas ) ) {
                $person['sameAs'] = array_values( array_filter( array_map( 'esc_url_raw', $person_sameas ) ) );
                if ( empty( $person['sameAs'] ) ) unset( $person['sameAs'] );
            }
            morimoflix_emit_jsonld( $person );
        }
    }

    // --- CollectionPage on genre archives (trivial, guarded) ---
    // Skipped on thin/noindexed pages. No isPartOf #website ref: the WebSite
    // node only prints on home, so the ref would dangle elsewhere (drop it).
    if ( function_exists( 'is_tax' ) && is_tax( 'genre' ) && ! morimoflix_thin_noindex() ) {
        $genre_term = get_queried_object();
        if ( $genre_term && ! is_wp_error( $genre_term ) ) {
            $genre_link = get_term_link( $genre_term );
            $genre_url  = ! is_wp_error( $genre_link ) ? $genre_link : $site_url;
            $genre_desc_raw = term_description( $genre_term );
            $genre_desc = $genre_desc_raw
                ? morimoflix_clean_desc( wp_strip_all_tags( $genre_desc_raw, true ), 30 )
                : sprintf( 'Watch %s movies in HD — Hindi dubbed & Telugu collection on %s.', $genre_term->name, $site_name );
            $collection = array(
                '@context'    => 'https://schema.org',
                '@type'       => 'CollectionPage',
                'name'        => $genre_term->name . ' Movies',
                'url'         => $genre_url,
                'description' => $genre_desc,
            );
            if ( isset( $genre_term->count ) ) {
                $collection['numberOfItems'] = absint( $genre_term->count );
            }
            morimoflix_emit_jsonld( $collection );
        }
    }

    // --- BreadcrumbList on interior pages (singular, taxonomy, search, page-list) ---
    // Skipped entirely on thin/noindexed pages to avoid mixed signals.
    // Crumb-2 mirrors the visible templates (Home > Movies > Term).
    if ( ( ! is_front_page() || is_paged() ) && ! morimoflix_thin_noindex() ) {
        $crumbs = array();
        $pos = 1;
        $crumbs[] = array('@type' => 'ListItem', 'position' => $pos++, 'name' => 'Home', 'item' => $site_url);
        if (is_singular(array('movie', 'tv'))) {
            $post_type = get_post_type();
            $archive_link = get_post_type_archive_link($post_type);
            if ($archive_link) {
                $label = $post_type === 'tv' ? 'TV Shows' : 'Movies';
                $crumbs[] = array('@type' => 'ListItem', 'position' => $pos++, 'name' => $label, 'item' => $archive_link);
            }
            $crumbs[] = array('@type' => 'ListItem', 'position' => $pos++, 'name' => get_the_title(), 'item' => get_permalink());
        } elseif (is_tax('genre') || is_tax('actor')) {
            $term = get_queried_object();
            // Parity with taxonomy-genre.php / taxonomy-actor.php visible
            // crumbs: Home > Movies > Term.
            $movie_archive = get_post_type_archive_link( 'movie' );
            $crumbs[] = array('@type' => 'ListItem', 'position' => $pos++, 'name' => 'Movies', 'item' => $movie_archive ? $movie_archive : $site_url);
            if ($term && !is_wp_error($term)) {
                $term_link = get_term_link($term);
                $crumbs[] = array('@type' => 'ListItem', 'position' => $pos++, 'name' => $term->name, 'item' => !is_wp_error($term_link) ? $term_link : $site_url);
            }
        } elseif (is_search()) {
            $crumbs[] = array('@type' => 'ListItem', 'position' => $pos++, 'name' => 'Search: ' . get_search_query(), 'item' => home_url('/?s=' . urlencode(get_search_query())));
        } elseif (is_page()) {
            $crumbs[] = array('@type' => 'ListItem', 'position' => $pos++, 'name' => get_the_title(), 'item' => get_permalink());
        } elseif (is_archive() && !is_tax()) {
            // get_permalink() takes no usable ID here — resolve a per-type
            // URL with $site_url fallback (never false/empty).
            $archive_url = $site_url;
            if ( is_author() ) {
                $author = get_queried_object();
                if ( $author && isset( $author->ID ) ) {
                    $archive_url = get_author_posts_url( $author->ID );
                }
            } elseif ( is_category() || is_tag() ) {
                $t = get_queried_object();
                if ( $t && ! is_wp_error( $t ) ) {
                    $l = get_term_link( $t );
                    if ( ! is_wp_error( $l ) ) $archive_url = $l;
                }
            } elseif ( is_date() ) {
                $y = get_query_var( 'year' );
                $m = get_query_var( 'monthnum' );
                $d = get_query_var( 'day' );
                if ( $y && $m && $d ) {
                    $archive_url = get_day_link( $y, $m, $d );
                } elseif ( $y && $m ) {
                    $archive_url = get_month_link( $y, $m );
                } elseif ( $y ) {
                    $archive_url = get_year_link( $y );
                }
            } elseif ( is_post_type_archive() ) {
                $pt = get_query_var( 'post_type' );
                if ( is_array( $pt ) ) $pt = reset( $pt );
                if ( $pt ) {
                    $l = get_post_type_archive_link( $pt );
                    if ( $l ) $archive_url = $l;
                }
            }
            $crumbs[] = array('@type' => 'ListItem', 'position' => $pos++, 'name' => morimoflix_archive_title_plain(), 'item' => $archive_url);
        }
        // Only output if more than just Home
        if (count($crumbs) > 1) {
            $breadcrumb = array(
                '@context' => 'https://schema.org',
                '@type'    => 'BreadcrumbList',
                'itemListElement' => $crumbs,
            );
            echo '<script type="application/ld+json">' . str_replace( '</', '<\\/', wp_json_encode( $breadcrumb, JSON_UNESCAPED_UNICODE ) ) . '</script>' . "\n";
        }
    }
}
add_action('wp_head', 'morimoflix_jsonld_output', 5);

/**
 * Technical SEO hardening: remove WordPress generator meta
 * (<meta name="generator">) from wp_head and feeds.
 */
remove_action( 'wp_head', 'wp_generator' );
add_filter( 'the_generator', '__return_empty_string' );

/**
 * Security headers (fallback if Cloudflare Transform Rules not set)
 * Fixes §1.6 Missing security headers. Cloudflare is preferred, this is a PHP fallback.
 * NOTE: No Content-Security-Policy is sent from the theme on purpose — this
 * site runs third-party ad tags, Cloudflare beacon, CanvasJS stats and
 * LiteSpeed-inlined data: fonts, none of which survive a strict theme-level
 * CSP (it rendered icons as tofu boxes and killed ad/beacon scripts on live).
 * Manage CSP only at Cloudflare (Report-Only first) if ever needed.
 */
function morimoflix_security_headers() {
    if (is_admin() || headers_sent()) return;
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
}
add_action('send_headers', 'morimoflix_security_headers');

/**
 * robots.txt final state: block thin/dupe paths, allow assets + AJAX,
 * explicitly allow AI crawlers for GEO, advertise sitemap.
 */
function morimoflix_robots_txt($output, $public) {
    if ('0' === (string) $public) {
        return "User-agent: *\nDisallow: /\n";
    }
    $out  = "User-agent: *\n";
    $out .= "Disallow: /wp-admin/\n";
    $out .= "Allow: /wp-admin/admin-ajax.php\n";
    $out .= "Disallow: /?s=\n";
    $out .= "Disallow: /search/\n";
    $out .= "Disallow: /feed/\n";
    $out .= "Disallow: /trackback/\n";
    $out .= "Disallow: /comments/feed/\n";
    $out .= "Disallow: /author/\n";
    $out .= "Allow: /wp-content/uploads/\n";
    $out .= "\n# FlixGarden AI crawler policy - allowed for citation/GEO\n";
    $out .= "User-agent: ChatGPT-User\nAllow: /\n\n";
    $out .= "User-agent: PerplexityBot\nAllow: /\n\n";
    $out .= "User-agent: anthropic-ai\nAllow: /\n\n";
    $out .= "User-agent: FacebookBot\nAllow: /\n";
    $sitemap_path = defined( 'RANK_MATH_VERSION' ) ? '/sitemap_index.xml' : '/wp-sitemap.xml';
    $out .= "\nSitemap: " . esc_url_raw(home_url($sitemap_path)) . "\n";
    return $out;
}
add_filter('robots_txt', 'morimoflix_robots_txt', 10, 2);

/**
 * llms.txt virtual file (fixes §2.7)
 * Serves /llms.txt via rewrite so no physical file needed at WP root.
 */
function morimoflix_llms_rewrite() {
    add_rewrite_rule('^llms\\.txt$', 'index.php?morimoflix_llms=1', 'top');
}
add_action('init', 'morimoflix_llms_rewrite');
function morimoflix_llms_flush_rewrite() {
    morimoflix_llms_rewrite();
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'morimoflix_llms_flush_rewrite');
function morimoflix_llms_query_vars($vars) {
    $vars[] = 'morimoflix_llms';
    return $vars;
}
add_filter('query_vars', 'morimoflix_llms_query_vars');
function morimoflix_llms_template() {
    if (get_query_var('morimoflix_llms')) {
        header('Content-Type: text/plain; charset=utf-8');
        // SAFE perf: allow CDN/edge caching 12h (matches transient TTL).
        header('Cache-Control: public, max-age=43200');
        $cached = get_transient('morimoflix_llms_txt');
        if (false !== $cached && is_string($cached) && '' !== $cached) {
            echo $cached;
            exit;
        }
        $site_url = home_url('/');
        $out = '';
        $out .= "# FlixGarden\n";
        $out .= "> Watch and download the latest movies in HD — Telugu, Hindi & English.\n\n";
        $out .= "## Home\n- [Homepage](" . esc_url($site_url) . ")\n\n";
        $out .= "## Browse\n- [A-Z Library](" . esc_url(home_url('/list/')) . ")\n";
        $out .= "- [Search](" . esc_url(home_url('/?s=')) . ")\n";
        $out .= "- [Request a Movie](" . esc_url($site_url) . ")\n\n";
        $out .= "## Genres\n";
        $genres = get_terms(array('taxonomy' => 'genre', 'hide_empty' => false, 'number' => 8));
        if (!is_wp_error($genres) && !empty($genres)) {
            foreach ($genres as $g) {
                // Plain-text file: strip tags (not esc_html).
                $out .= "- [" . wp_strip_all_tags($g->name) . "](" . esc_url(get_term_link($g)) . ")\n";
            }
        }
        $out .= "\n## Top Picks\n";
        // SAFE perf: no pagination → skip COUNT(*).
        $picks = new WP_Query(array('post_type' => array('movie','tv'), 'posts_per_page' => 6, 'orderby' => 'date', 'order' => 'DESC', 'no_found_rows' => true));
        if ($picks->have_posts()) {
            while ($picks->have_posts()) { $picks->the_post();
                $out .= "- [" . wp_strip_all_tags(get_the_title()) . "](" . esc_url(get_permalink()) . ")\n";
            }
            wp_reset_postdata();
        }
        set_transient('morimoflix_llms_txt', $out, 12 * HOUR_IN_SECONDS);
        echo $out;
        exit;
    }
}
add_action('template_redirect', 'morimoflix_llms_template');

/**
 * BeyondSEO interplay fixes (Rank Math active on live).
 */

function morimoflix_rankmath_sync_post( $post_id ) {
    if ( ! defined( 'RANK_MATH_VERSION' ) || ! $post_id || wp_is_post_revision( $post_id ) ) {
        return;
    }
    $post = get_post( $post_id );
    if ( ! $post || ! in_array( $post->post_type, array( 'movie', 'tv', 'blog' ), true ) ) {
        return;
    }
    $title = sanitize_text_field( $post->post_title );
    if ( '' === $title ) {
        return;
    }
    if ( 'blog' === $post->post_type ) {
        $focus = trim( (string) get_post_meta( $post_id, 'rank_math_focus_keyword', true ) );
        if ( '' === $focus || false === mb_stripos( $title, $focus ) ) {
            update_post_meta( $post_id, 'rank_math_focus_keyword', $title );
        }
        if ( '' === trim( (string) get_post_meta( $post_id, 'rank_math_title', true ) ) ) {
            update_post_meta( $post_id, 'rank_math_title', $title . ' | FlixGarden' );
        }
        if ( '' === trim( (string) get_post_meta( $post_id, 'rank_math_description', true ) ) ) {
            $desc = has_excerpt( $post_id ) ? get_the_excerpt( $post_id ) : wp_trim_words( wp_strip_all_tags( get_the_content( '', false, $post_id ), true ), 30, '...' );
            update_post_meta( $post_id, 'rank_math_description', wp_strip_all_tags( $desc ) );
        }
        return;
    }
    $m = morimoflix_get_movie_meta( $post_id );
    $keyword = trim( (string) get_post_meta( $post_id, 'rank_math_focus_keyword', true ) );
    $keyword_key = strtolower( preg_replace( '/\s+/u', ' ', $keyword ) );
    $legacy_pattern = '/^' . preg_quote( strtolower( $title ), '/' ) . '\s*,?\s*\d{4}$/u';
    $legacy_keyword = 1 === preg_match( $legacy_pattern, $keyword_key );
    if ( '' === $keyword || $legacy_keyword || ( '' !== $keyword && false === mb_stripos( $title, $keyword ) ) ) {
        update_post_meta( $post_id, 'rank_math_focus_keyword', $title );
    }
    $focus_keyword = trim( (string) get_post_meta( $post_id, 'rank_math_focus_keyword', true ) );
    $post_title_keyword = strtolower( preg_replace( '/\s+/u', ' ', $title ) );
    $focus_is_post_title = strtolower( preg_replace( '/\s+/u', ' ', $focus_keyword ) ) === $post_title_keyword;
    $seo_title = trim( (string) get_post_meta( $post_id, 'rank_math_title', true ) );
    $seo_description = trim( (string) get_post_meta( $post_id, 'rank_math_description', true ) );
    $title_has_year = '' !== $m['year'] && false !== mb_stripos( $seo_title, $m['year'] );
    if ( '' === $seo_title || ( $focus_is_post_title && ( 0 !== mb_stripos( $seo_title, $focus_keyword ) || ! $title_has_year ) ) ) {
        $seo_title = morimoflix_movie_seo_title( $post_id );
        update_post_meta( $post_id, 'rank_math_title', $seo_title );
    }
    if ( '' === $seo_description || ( $focus_is_post_title && false === mb_stripos( $seo_description, $focus_keyword ) ) ) {
        update_post_meta( $post_id, 'rank_math_description', morimoflix_movie_seo_description( $post_id ) );
    }
    $og_img = morimoflix_get_og_image( $post_id );
    if ( ! empty( $og_img['url'] ) ) {
        update_post_meta( $post_id, 'rank_math_facebook_image', $og_img['url'] );
        update_post_meta( $post_id, 'rank_math_twitter_image', $og_img['url'] );
    }
}
add_action( 'save_post_movie', 'morimoflix_rankmath_sync_post', 30 );
add_action( 'save_post_tv', 'morimoflix_rankmath_sync_post', 30 );
add_action( 'save_post_blog', 'morimoflix_rankmath_sync_post', 30 );

function morimoflix_rankmath_migrate_posts() {
    if ( ! defined( 'RANK_MATH_VERSION' ) || ! current_user_can( 'edit_posts' ) ) {
        return;
    }
    $migration = '1';
    if ( $migration === get_option( 'morimoflix_rankmath_seo_migration' ) ) {
        return;
    }
    $post_ids = get_posts( array(
        'post_type'        => array( 'movie', 'tv', 'blog' ),
        'post_status'      => 'any',
        'posts_per_page'   => -1,
        'fields'           => 'ids',
        'no_found_rows'    => true,
        'suppress_filters' => false,
    ) );
    foreach ( $post_ids as $post_id ) {
        morimoflix_rankmath_sync_post( $post_id );
    }
    update_option( 'morimoflix_rankmath_seo_migration', $migration, false );
}
add_action( 'admin_init', 'morimoflix_rankmath_migrate_posts', 5 );

function morimoflix_rankmath_analysis_data( $post_id ) {
    $post = get_post( $post_id );
    if ( ! $post ) {
        return array( 'title' => '', 'content' => '' );
    }
    $m = morimoflix_get_movie_meta( $post_id );
    $title = sanitize_text_field( $post->post_title );
    $content = $post->post_content;
    if ( 'blog' === $post->post_type ) {
        return array(
            'title'   => $title . ' | FlixGarden',
            'content' => wp_strip_all_tags( $content, true ),
        );
    }
    $synopsis = has_excerpt( $post_id ) ? get_the_excerpt( $post_id ) : wp_strip_all_tags( $content, true );
    $synopsis = morimoflix_clean_desc( $synopsis, 45 );
    $facts = array(
        sprintf( '%s: %s', __( 'IMDb rating', 'morimoflix-flicker' ), $m['rating'] ),
        sprintf( '%s: %s', __( 'Runtime', 'morimoflix-flicker' ), $m['duration'] ),
        sprintf( '%s: %s', __( 'Quality', 'morimoflix-flicker' ), $m['quality'] ),
        sprintf( '%s: %s', __( 'Language', 'morimoflix-flicker' ), $m['language'] ),
    );
    $genre_links = array();
    $genres = get_the_terms( $post_id, 'genre' );
    if ( $genres && ! is_wp_error( $genres ) ) {
        foreach ( array_slice( $genres, 0, 3 ) as $genre ) {
            $link = get_term_link( $genre );
            if ( ! is_wp_error( $link ) ) {
                $genre_links[] = '<a href="' . esc_url( $link ) . '">' . esc_html( $genre->name ) . '</a>';
            }
        }
    }
    if ( $genre_links ) {
        $facts[] = sprintf( '%s: %s', __( 'Genres', 'morimoflix-flicker' ), implode( ', ', $genre_links ) );
    }
    $actors = get_the_terms( $post_id, 'actor' );
    if ( $actors && ! is_wp_error( $actors ) ) {
        $actor_links = array();
        foreach ( array_slice( $actors, 0, 5 ) as $actor ) {
            $link = get_term_link( $actor );
            if ( ! is_wp_error( $link ) ) {
                $actor_links[] = '<a href="' . esc_url( $link ) . '">' . esc_html( $actor->name ) . '</a>';
            }
        }
        if ( $actor_links ) {
            $facts[] = sprintf( '%s: %s', __( 'Cast', 'morimoflix-flicker' ), implode( ', ', $actor_links ) );
        }
    }
    $query = rawurlencode( trim( $title . ' ' . $m['year'] ) );
    $external_links = array(
        '<a href="' . esc_url( 'https://www.imdb.com/find?q=' . $query ) . '">IMDb</a>',
        '<a href="' . esc_url( 'https://en.wikipedia.org/wiki/Special:Search?search=' . $query ) . '">Wikipedia</a>',
        '<a href="' . esc_url( 'https://www.rottentomatoes.com/search?search=' . $query ) . '">Rotten Tomatoes</a>',
        '<a href="' . esc_url( 'https://www.youtube.com/results?search_query=' . rawurlencode( trim( $title . ' ' . $m['year'] . ' trailer' ) ) ) . '">YouTube Trailer</a>',
    );
    // Rank Math splits paragraphs on double newlines: every rendered chunk
    // gets its own "\n\n" boundary, otherwise the whole extras blob (h2 +
    // synopsis + facts + image + links) merges into ONE >120-word
    // "paragraph" and fails the editor's short-paragraph check.
    $rendered = '<h2>' . esc_html( $title . ' (' . $m['year'] . ')' ) . '</h2>' . "\n\n";
    if ( $synopsis ) {
        $rendered .= '<p>' . esc_html( $synopsis ) . '</p>' . "\n\n";
    }
    $rendered .= '<ul>' . wp_kses_post( '<li>' . implode( '</li><li>', $facts ) . '</li>' ) . '</ul>' . "\n\n";
    if ( has_post_thumbnail( $post_id ) ) {
        $image_url = get_the_post_thumbnail_url( $post_id, 'medium' );
        if ( $image_url ) {
            $rendered .= '<img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( morimoflix_poster_alt( $post_id ) ) . '">' . "\n\n";
        }
    }
    $rendered .= '<p><a href="' . esc_url( home_url( '/list/' ) ) . '">' . esc_html__( 'A-Z movie library', 'morimoflix-flicker' ) . '</a></p>' . "\n\n";
    $rendered .= '<p>' . implode( ' ', $external_links ) . '</p>' . "\n\n";
    $thin = morimoflix_movie_thin_content( $post_id );
    if ( $thin ) {
        $rendered .= $thin . "\n\n";
    }
    $tg_block = get_post_meta( $post_id, '_tg_autoseo_block', true );
    if ( $tg_block ) {
        $rendered .= wp_kses_post( $tg_block ) . "\n\n";
    }
    $rendered = trim( $rendered );
    $rank_title = trim( (string) get_post_meta( $post_id, 'rank_math_title', true ) );
    if ( '' === $rank_title ) {
        $rank_title = morimoflix_movie_seo_title( $post_id );
    } elseif ( false !== strpos( $rank_title, '%' ) ) {
        $rank_title = '';
    }
    return array(
        'title'   => $rank_title,
        // Feed ONLY the generated extras to the analyzer. Appending raw
        // $content here double-counted it (editor already holds post_content),
        // inflating word count and diluting keyword density in every check.
        'content' => $rendered,
    );
}

function morimoflix_enqueue_rankmath_analysis() {
    if ( ! defined( 'RANK_MATH_VERSION' ) ) {
        return;
    }
    $post = $GLOBALS['post'] ?? null;
    $post_id = $post ? (int) $post->ID : 0;
    if ( ! $post_id || ! in_array( get_post_type( $post_id ), array( 'movie', 'tv', 'blog' ), true ) ) {
        return;
    }
    static $enqueued = false;
    if ( $enqueued ) {
        return;
    }
    $enqueued = true;
    wp_register_script( 'morimoflix-rank-math-analysis', false, array( 'wp-hooks', 'rank-math-analyzer' ), MORIMOFLIX_VERSION, true );
    wp_localize_script( 'morimoflix-rank-math-analysis', 'morimoflixRankMathAnalysis', morimoflix_rankmath_analysis_data( $post_id ) );
    wp_add_inline_script( 'morimoflix-rank-math-analysis', <<<'JS'
(function () {
    var data = window.morimoflixRankMathAnalysis || {};
    if (data.title) {
        wp.hooks.addFilter('rank_math_title', 'morimoflix/theme', function (title) {
            return data.title || title;
        }, 11);
    }
    if (data.content) {
        wp.hooks.addFilter('rank_math_content', 'morimoflix/theme', function (content) {
            // Double-newline seam: Rank Math splits paragraphs on "\n\n"; a
            // bare concat merges the article's last paragraph with the
            // extras blob into one over-long "paragraph".
            return (content || '') + '\n\n' + data.content;
        }, 11);
    }
    var refresh = function () {
        if (window.rankMathEditor) {
            window.rankMathEditor.refresh('title');
            window.rankMathEditor.refresh('content');
        }
    };
    if ('loading' === document.readyState) {
        document.addEventListener('DOMContentLoaded', refresh);
    } else {
        window.setTimeout(refresh, 0);
    }
}());
JS
    );
    wp_enqueue_script( 'morimoflix-rank-math-analysis' );
}
add_action( 'enqueue_block_editor_assets', 'morimoflix_enqueue_rankmath_analysis', 20 );

/**
 * P1 Title-leak guard: strip unresolved template artifacts such as
 * "Tgmovies#author_first_name" if the Rank Math title template ever leaks
 * them again. The real fix is Rank Math Dashboard > Titles & Meta >
 * Homepage title (remove the author variable); this only contains damage.
 */
function morimoflix_rankmath_title_guard( $title ) {
    if ( ! is_string( $title ) || '' === $title ) {
        return $title;
    }
    if ( is_front_page() || is_home() ) {
        return 'FlixGarden — Watch Telugu & Hindi Dubbed Movies in HD';
    }
    if ( is_post_type_archive( 'movie' ) ) {
        return 'Movies — Hindi Dubbed & Telugu HD';
    }
    if ( is_post_type_archive( 'tv' ) ) {
        return 'TV Shows — Hindi Dubbed & Telugu HD';
    }
    if ( is_singular( array( 'movie', 'tv' ) ) ) {
        $post_id = get_queried_object_id();
        $post_title = $post_id ? sanitize_text_field( get_the_title( $post_id ) ) : '';
        $focus_keyword = $post_id ? trim( (string) get_post_meta( $post_id, 'rank_math_focus_keyword', true ) ) : '';
        $seo_title = $post_id ? trim( (string) get_post_meta( $post_id, 'rank_math_title', true ) ) : '';
        $m = $post_id ? morimoflix_get_movie_meta( $post_id ) : array( 'year' => '' );
        $focus_is_post_title = '' !== $post_title && strtolower( preg_replace( '/\s+/u', ' ', $focus_keyword ) ) === strtolower( preg_replace( '/\s+/u', ' ', $post_title ) );
        $title_has_year = '' !== $m['year'] && false !== mb_stripos( $seo_title, $m['year'] );
        if ( $post_id && ( '' === $seo_title || ( $focus_is_post_title && ( 0 !== mb_stripos( $seo_title, $focus_keyword ) || ! $title_has_year ) ) ) ) {
            $title = morimoflix_movie_seo_title( $post_id );
        }
        if ( $post_id && mb_strlen( $title ) > 60 ) {
            $title = morimoflix_movie_seo_title( $post_id );
        }
    }
    if ( ! is_string( $title ) || '' === $title ) {
        return $title;
    }
    if ( false !== stripos( $title, 'author_first_name' ) || false !== strpos( $title, '#' ) ) {
        // Only strip # tokens that carry letters (leak artifacts like
        // "Tgmovies#author_first_name"); pure numeric refs ("#1") survive.
        $title = preg_replace( '/\S*#[A-Za-z_]\S*/', '', $title );
        $title = preg_replace( '/\bauthor_first_name\b/i', '', $title );
        $title = preg_replace( '/\s+/', ' ', $title );
        $title = trim( $title, " \t\n\r\0\x0B|/-" );
        $title = preg_replace( '/\s+/', ' ', $title );
    }
    if ( mb_strlen( $title ) > 75 ) {
        $title = mb_substr( $title, 0, 60 );
        if ( false !== mb_strpos( $title, '|' ) ) {
            $title = preg_replace( '/\s*\|\s*.*$/', '', $title );
        }
        $title = rtrim( $title, ' –-—:|' );
    }
    return $title;
}
add_filter( 'rank_math/frontend/title', 'morimoflix_rankmath_title_guard', 20 );

/**
 * Homepage description safety net: applies only when Rank Math is active
 * (theme meta above stands down then) AND its homepage description still
 * carries leak markers. Any intentional dashboard copy is respected.
 */
function morimoflix_rankmath_desc_guard( $desc ) {
    if ( is_singular( array( 'movie', 'tv' ) ) ) {
        $post_id = get_queried_object_id();
        $post_title = $post_id ? sanitize_text_field( get_the_title( $post_id ) ) : '';
        $focus_keyword = $post_id ? trim( (string) get_post_meta( $post_id, 'rank_math_focus_keyword', true ) ) : '';
        $seo_description = $post_id ? trim( (string) get_post_meta( $post_id, 'rank_math_description', true ) ) : '';
        $focus_is_post_title = '' !== $post_title && strtolower( preg_replace( '/\s+/u', ' ', $focus_keyword ) ) === strtolower( preg_replace( '/\s+/u', ' ', $post_title ) );
        if ( $post_id && ( '' === $seo_description || ( $focus_is_post_title && false === mb_stripos( $seo_description, $focus_keyword ) ) ) ) {
            return morimoflix_movie_seo_description( $post_id );
        }
    }
    if ( ( is_front_page() || is_home() ) && is_string( $desc )
        && ( false !== strpos( $desc, '#' ) || false !== stripos( $desc, 'author_first_name' ) ) ) {
        return 'Watch and download the latest movies in HD — Telugu, Hindi & English. 1,500+ curated titles, daily Telegram updates, request-a-movie.';
    }
    return $desc;
}
add_filter( 'rank_math/frontend/description', 'morimoflix_rankmath_desc_guard', 20 );

/**
 * Thin/dupe pages that must never index: actor archives (~10K), search
 * results, author archives, tag/date archives, attachment pages, paginated
 * home (?paged>1) + paginated static pages (/list/page/N/, ?lpage>1).
 * Single source of truth for both the Rank Math guard and the
 * no-Rank-Math fallback below.
 */
function morimoflix_thin_noindex() {
    if ( is_admin() ) {
        return false;
    }
    if ( is_search() || is_author() || is_tag() || is_date() || is_attachment() ) {
        return true;
    }
    if ( function_exists( 'is_tax' ) && is_tax( 'actor' ) ) {
        return true;
    }
    // Paginated home (?paged>1 / ?page>1 — main query paged is reset to 0
    // by morimoflix_fix_front_page_request, so check $_GET directly too).
    if ( function_exists( 'is_front_page' ) && ( is_front_page() || is_home() ) ) {
        if ( is_paged() ) {
            return true;
        }
        foreach ( array( 'paged', 'page' ) as $key ) {
            if ( isset( $_GET[ $key ] ) && absint( $_GET[ $key ] ) > 1 ) {
                return true;
            }
        }
    }
    // Paginated static pages (/list/page/N/).
    if ( function_exists( 'is_page' ) && is_page() && is_paged() ) {
        return true;
    }
    if ( function_exists( 'is_page' ) && is_page()
        && isset( $_GET['lpage'] ) && absint( $_GET['lpage'] ) > 1 ) {
        return true;
    }
    return false;
}

/**
 * Thin pages at scale: noindex,follow (Rank Math path).
 */
function morimoflix_rankmath_robots_guard( $robots ) {
    if ( ! is_array( $robots ) ) {
        return $robots;
    }
    if ( morimoflix_thin_noindex() ) {
        $robots['index']  = 'noindex';
        $robots['follow'] = 'follow';
    }
    return $robots;
}
add_filter( 'rank_math/frontend/robots', 'morimoflix_rankmath_robots_guard', 20 );

/**
 * Fallback robots meta when Rank Math is NOT active.
 */
function morimoflix_thin_robots_fallback() {
    if ( defined( 'RANK_MATH_VERSION' ) ) {
        return;
    }
    if ( morimoflix_thin_noindex() ) {
        echo '<meta name="robots" content="noindex,follow">' . "\n";
    }
}
add_action( 'wp_head', 'morimoflix_thin_robots_fallback', 2 );

/**
 * Paginated genre/actor canonical sanity: normalize junk ?page=N / ?paged=N
 * variants to the proper /page/N/ URL (page-1 variants to the clean term
 * URL). Shared normalizer — Rank Math path.
 *
 * @param string $canonical Incoming canonical URL.
 * @return string
 */
function morimoflix_paged_canonical_fix( $canonical ) {
    // Never emit a canonical for noindexed pages (mixed index/canonical).
    if ( function_exists( 'morimoflix_thin_noindex' ) && morimoflix_thin_noindex() ) {
        return $canonical;
    }
    if ( ! function_exists( 'is_tax' ) || ( ! is_tax( 'genre' ) && ! is_tax( 'actor' ) ) ) {
        return $canonical;
    }
    $term = get_queried_object();
    if ( ! $term || is_wp_error( $term ) ) {
        return $canonical;
    }
    $link = get_term_link( $term );
    if ( is_wp_error( $link ) ) {
        return $canonical;
    }
    foreach ( array( 'page', 'paged' ) as $key ) {
        if ( isset( $_GET[ $key ] ) && is_numeric( $_GET[ $key ] ) ) {
            $n = absint( $_GET[ $key ] );
            return $n > 1 ? trailingslashit( $link ) . 'page/' . $n . '/' : $link;
        }
    }
    return $canonical;
}
add_filter( 'rank_math/frontend/canonical', 'morimoflix_paged_canonical_fix', 20 );

/**
 * Paginated genre/actor canonical fallback when Rank Math is NOT active
 * (WP core prints no canonical on archives, so no duplication risk).
 * Self-canonical for /page/N/; normalized canonical for ?page=N junk.
 * Skipped on noindexed pages to avoid mixed index/canonical signals.
 */
function morimoflix_paged_canonical_fallback() {
    if ( defined( 'RANK_MATH_VERSION' ) ) {
        return;
    }
    if ( ! function_exists( 'is_tax' ) || ( ! is_tax( 'genre' ) && ! is_tax( 'actor' ) ) ) {
        return;
    }
    if ( morimoflix_thin_noindex() ) {
        return;
    }
    if ( is_paged() ) {
        remove_action( 'wp_head', 'rel_canonical' );
        echo '<link rel="canonical" href="' . esc_url( get_pagenum_link( get_query_var( 'paged' ) ) ) . '">' . "\n";
        return;
    }
    if ( isset( $_GET['page'] ) || isset( $_GET['paged'] ) ) {
        $fixed = morimoflix_paged_canonical_fix( '' );
        if ( '' !== $fixed ) {
            remove_action( 'wp_head', 'rel_canonical' );
            echo '<link rel="canonical" href="' . esc_url( $fixed ) . '">' . "\n";
        }
    }
}
add_action( 'wp_head', 'morimoflix_paged_canonical_fallback', 3 );

/**
 * Empty actor archives (0 posts) 302 to /list/ instead of serving
 * thin doorway pages (temporary: restores automatically when filled).
 */
function morimoflix_actor_empty_redirect() {
    if ( is_admin() || ! function_exists( 'is_tax' ) || ! is_tax( 'actor' ) ) {
        return;
    }
    $term = get_queried_object();
    if ( $term && ! is_wp_error( $term ) && isset( $term->count ) && 0 === (int) $term->count ) {
        wp_safe_redirect( home_url( '/list/' ), 302 );
        exit;
    }
}
add_action( 'template_redirect', 'morimoflix_actor_empty_redirect' );

/**
 * Canonical archive redirects: map plural movie archive URLs and broken
 * page query strings to the real post-type archive pagination URLs.
 */
function morimoflix_movie_archive_canonical_redirects() {
    if ( is_admin() ) {
        return;
    }

    $request_uri = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
    $path        = trim( (string) wp_parse_url( $request_uri, PHP_URL_PATH ), '/' );
    $home_path   = trim( (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH ), '/' );
    if ( '' !== $home_path && 0 === strpos( $path, $home_path . '/' ) ) {
        $path = substr( $path, strlen( $home_path ) + 1 );
    }

    if ( preg_match( '#^movies(?:/page/([0-9]+))?/?$#i', $path, $matches ) ) {
        if ( is_page() ) {
            return;
        }
        $target = get_post_type_archive_link( 'movie' );
        if ( ! $target ) {
            $target = home_url( '/movie/' );
        }
        $page = isset( $matches[1] ) ? absint( $matches[1] ) : 1;
        $movie_paged = ( isset( $_GET['paged'] ) && is_scalar( $_GET['paged'] ) ) ? trim( (string) wp_unslash( $_GET['paged'] ) ) : '';
        if ( $page <= 1 && '' !== $movie_paged && preg_match( '/^[0-9]+$/', $movie_paged ) ) {
            $page = absint( $movie_paged );
        }
        if ( $page > 1 ) {
            $target = trailingslashit( $target ) . 'page/' . $page . '/';
        }
        wp_safe_redirect( $target, 301 );
        exit;
    }

    if ( preg_match( '#^(movie|tv)/page/(?:0|[-]+[0-9]+)/?$#i', $path, $m ) ) {
        $post_type = strtolower( $m[1] );
        $target    = get_post_type_archive_link( $post_type );
        if ( ! $target ) {
            $target = home_url( '/' . $post_type . '/' );
        }
        wp_safe_redirect( $target, 301 );
        exit;
    }

    if ( is_post_type_archive( array( 'movie', 'tv' ) ) ) {
        $queried   = get_queried_object();
        $post_type = ( $queried && ! is_wp_error( $queried ) && isset( $queried->name ) && in_array( $queried->name, array( 'movie', 'tv' ), true ) ) ? $queried->name : ( is_post_type_archive( 'tv' ) ? 'tv' : 'movie' );
        $archive   = get_post_type_archive_link( $post_type );
        if ( ! $archive ) {
            $archive = home_url( '/' . $post_type . '/' );
        }

        if ( isset( $_GET['page'] ) ) {
            wp_safe_redirect( remove_query_arg( 'page' ), 301 );
            exit;
        }
        if ( isset( $_GET['paged'] ) ) {
            $paged = ( isset( $_GET['paged'] ) && is_scalar( $_GET['paged'] ) ) ? trim( (string) sanitize_text_field( wp_unslash( $_GET['paged'] ) ) ) : '';
            if ( '' === $paged || ! preg_match( '/^[0-9]+$/', $paged ) || (int) $paged <= 1 ) {
                wp_safe_redirect( remove_query_arg( 'paged' ), 301 );
                exit;
            }
            $target = (int) $paged > 1 ? trailingslashit( $archive ) . 'page/' . (int) $paged . '/' : $archive;
            wp_safe_redirect( $target, 301 );
            exit;
        }
    }
}
add_action( 'template_redirect', 'morimoflix_movie_archive_canonical_redirects', 5 );

/* ============================================================
 * SEO Auto-Apply (Rank Math friendly) - REST endpoint
 * ------------------------------------------------------------
 * Route:    POST /wp-json/morimoflix/v1/seo-update
 * Auth:     logged-in user with `edit_others_posts` capability
 *           (application password or cookie auth).
 * Purpose:  Apply Rank Math SEO meta and rebuild long-form content in
 *           proper Gutenberg block format server-side, then verify and
 *           score against the Rank Math content checklist. Doing the
 *           block serialization in PHP avoids the REST escaping
 *           pitfalls of pushing long HTML through the JSON API.
 * Modes:    apply=full (default) | meta | content | check
 *           force=1 rebuilds content even if this template already ran
 *           verify=1 (default) also checks the rendered page head
 * ============================================================ */

/**
 * Register the seo-update REST route.
 */
function morimoflix_register_seo_update_route() {
    register_rest_route(
        'morimoflix/v1',
        '/seo-update',
        array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => 'morimoflix_seo_update_endpoint',
            'permission_callback' => 'morimoflix_seo_update_permission',
            'args'                => array(
                'objectID'      => array(
                    'required'          => true,
                    'type'              => 'integer',
                    'sanitize_callback' => 'absint',
                ),
                'objectType'    => array(
                    'type'              => 'string',
                    'default'           => 'movie',
                    'enum'              => array( 'movie', 'tv' ),
                    'sanitize_callback' => 'sanitize_key',
                ),
                'apply'         => array(
                    'type'              => 'string',
                    'default'           => 'full',
                    'enum'              => array( 'full', 'meta', 'content', 'check' ),
                    'sanitize_callback' => 'sanitize_key',
                ),
                'force'         => array(
                    'type'    => 'boolean',
                    'default' => false,
                ),
                'verify'        => array(
                    'type'    => 'boolean',
                    'default' => true,
                ),
                'title'         => array(
                    'type'              => 'string',
                    'sanitize_callback' => 'sanitize_text_field',
                ),
                'description'   => array(
                    'type'              => 'string',
                    'sanitize_callback' => 'sanitize_text_field',
                ),
                'focus_keyword' => array(
                    'type'              => 'string',
                    'sanitize_callback' => 'sanitize_text_field',
                ),
                'canonical_url' => array(
                    'type'              => 'string',
                    'sanitize_callback' => 'esc_url_raw',
                ),
            ),
        )
    );
}
add_action( 'rest_api_init', 'morimoflix_register_seo_update_route' );

/**
 * Permission check: anyone who can edit others' posts (admin/app-password user).
 */
function morimoflix_seo_update_permission() {
    return is_user_logged_in() && current_user_can( 'edit_others_posts' );
}

/**
 * Default Rank Math meta for a post (movie/series aware).
 *
 * @param WP_Post $post     Post object.
 * @param string  $kind     "Movie" or "Series".
 * @param string  $kind_lc  "movie" or "series".
 * @return array
 */
function morimoflix_seo_default_meta( $post, $kind, $kind_lc ) {
    $title = sanitize_text_field( get_the_title( $post ) );

    $title_candidates = array(
        $title . ' ' . $kind . ' Download - Best Free HD 4K | FlixGarden',
        $title . ' ' . $kind . ' Download - Best HD | FlixGarden',
        mb_substr( $title, 0, 60 ) . ' ' . $kind . ' Download | FlixGarden',
    );
    $seo_title = $title_candidates[0];
    foreach ( $title_candidates as $candidate ) {
        if ( mb_strlen( $candidate ) <= 60 ) {
            $seo_title = $candidate;
            break;
        }
    }
    if ( mb_strlen( $seo_title ) > 60 ) {
        $seo_title = mb_substr( $seo_title, 0, 57 ) . '...';
    }

    $desc_candidates = array(
        $title . ' ' . $kind_lc . ' download free in 1080p HD. Full ' . $kind_lc . ' direct download links, verified sources, and fast download speeds on FlixGarden. Download now!',
        $title . ' ' . $kind_lc . ' download free in 1080p HD. Direct download links and fast speeds on FlixGarden. Download now!',
        $title . ' ' . $kind_lc . ' download free in HD on FlixGarden. Download now!',
    );
    $desc = $desc_candidates[0];
    foreach ( $desc_candidates as $candidate ) {
        if ( mb_strlen( $candidate ) <= 160 && mb_strlen( $candidate ) >= 120 ) {
            $desc = $candidate;
            break;
        }
    }

    return array(
        'rank_math_title'          => $seo_title,
        'rank_math_description'    => $desc,
        // Primary keyword must slug-match the post slug (/movie/{title-slug}/):
        // Rank Math's keywordInPermalink check is slugify(keyword) contained in URL.
        'rank_math_focus_keyword'  => $title . ', ' . $title . ' download, ' . $title . ' full ' . $kind_lc . ', ' . $title . ' hindi dubbed',
        'rank_math_canonical_url'  => get_permalink( $post ),
    );
}

/**
 * Serialize one Gutenberg block.
 *
 * @param string $type   Block type (paragraph, heading, etc.).
 * @param string $attrs  Block attribute JSON string or ''.
 * @param string $html   Inner HTML.
 * @return string
 */
function morimoflix_seo_block( $type, $attrs, $html ) {
    $open = '<!-- wp:' . $type . ( '' !== $attrs ? ' ' . $attrs : '' ) . ' -->';
    return $open . "\n" . $html . "\n<!-- /wp:" . $type . ' -->';
}

/**
 * Serialize an H2 heading block with an anchor id.
 */
function morimoflix_seo_heading( $id, $text ) {
    return morimoflix_seo_block(
        'heading',
        '{"level":2}',
        '<h2 id="' . esc_attr( $id ) . '" class="wp-block-heading">' . esc_html( $text ) . '</h2>'
    );
}

/**
 * Build the long-form SEO content in Gutenberg blocks for a post.
 *
 * @param int $post_id Post ID.
 * @return string
 */
function morimoflix_seo_build_content( $post_id ) {
    $post  = get_post( $post_id );
    $title = sanitize_text_field( get_the_title( $post ) );
    $m     = morimoflix_get_movie_meta( $post_id );
    $kind  = 'tv' === $post->post_type ? 'Series' : 'Movie';

    $raw_content = (string) get_the_content( '', false, $post_id );
    $synopsis    = wp_strip_all_tags( $raw_content, true );
    $synopsis    = trim( preg_replace( '/\s+/u', ' ', $synopsis ) );
    // Previous template run: do not recycle template boilerplate as the synopsis.
    if ( false !== strpos( $raw_content, 'mfseo-h1' ) ) {
        $synopsis = '';
    }
    if ( '' === $synopsis ) {
        $synopsis = 'Experience ' . $title . ', now available for download on FlixGarden.';
    }
    // Rank Math fails any paragraph over 120 words; entities can inflate the
    // token count past the cap, so keep a margin (cap at 100).
    $syn_words = preg_split( '/\s+/u', $synopsis, -1, PREG_SPLIT_NO_EMPTY );
    if ( is_array( $syn_words ) && count( $syn_words ) > 100 ) {
        $synopsis = implode( ' ', array_slice( $syn_words, 0, 100 ) );
    }
    if ( '' === $synopsis ) {
        $synopsis = 'Experience ' . $title . ', now available for download on FlixGarden.';
    }

    $genre_links = array();
    $genres      = get_the_terms( $post_id, 'genre' );
    if ( $genres && ! is_wp_error( $genres ) ) {
        foreach ( array_slice( $genres, 0, 3 ) as $genre ) {
            $link = get_term_link( $genre );
            if ( ! is_wp_error( $link ) ) {
                $genre_links[] = '<a href="' . esc_url( $link ) . '">' . esc_html( $genre->name ) . '</a>';
            }
        }
    }
    $actor_links = array();
    $actors      = get_the_terms( $post_id, 'actor' );
    if ( $actors && ! is_wp_error( $actors ) ) {
        foreach ( array_slice( $actors, 0, 4 ) as $actor ) {
            $link = get_term_link( $actor );
            if ( ! is_wp_error( $link ) ) {
                $actor_links[] = '<a href="' . esc_url( $link ) . '">' . esc_html( $actor->name ) . '</a>';
            }
        }
    }

    $og = morimoflix_get_og_image( $post_id );

    $p = static function ( $html ) {
        return morimoflix_seo_block( 'paragraph', '', '<p>' . wp_kses_post( $html ) . '</p>' );
    };
    $t = static function ( $text ) {
        return wp_kses_post( $text );
    };

    $blocks = array();

    // Intro paragraphs.
    $blocks[] = $p( 'If you are looking for an intense movie experience, ' . esc_html( $title ) . ' ' . esc_html( strtolower( $kind ) ) . ' download deserves a spot at the top of your watchlist. On FlixGarden, we provide the full release in high-efficiency 1080p HD quality with crisp original audio so every scene lands with full impact.' );
    $blocks[] = $p( 'Released in ' . esc_html( (string) $m['year'] ) . ', the film throws viewers straight into the story without wasting time on slow filler. Across its runtime, the pacing builds relentlessly from start to finish, giving you a complete cinematic experience from the comfort of your home.' );

    // Cross-links block.
    $blocks[] = morimoflix_seo_block( 'separator', '', '<hr class="wp-block-separator has-alpha-channel-opacity" />' );
    $blocks[] = $p( 'Watch all hentai hindi - <a href="https://hentaidekho.mom/" target="_blank" rel="noopener">Hentaidekho.mom</a>' );
    $blocks[] = $p( 'For adult manhwa hindi - <a href="https://comic.hentaidekho.mom/" target="_blank" rel="noopener">Comic.hentaidekho.mom</a>' );
    $blocks[] = $p( 'For web series hindi - <a href="https://morimoflix.xyz/" target="_blank" rel="noopener">https://morimoflix.xyz/</a>' );
    $blocks[] = $p( 'For movie hindi - <a href="' . esc_url( home_url( '/' ) ) . '">flixgarden.site</a>' );
    $blocks[] = morimoflix_seo_block( 'separator', '', '<hr class="wp-block-separator has-alpha-channel-opacity" />' );

    // Images - UNIQUE artwork only (assets credit, RM:test contentHasAssets).
    // The poster/OG variants are the SAME file at different sizes (-400x600,
    // -800x1200, full): stack them and the article shows duplicated figures
    // (the "two images" bug). Dedupe by file stem, keep the lean poster crop
    // and the real TMDB backdrop.
    $img_urls = array();
    $seen_stems = array();
    $img_stem = function ( $u ) {
        $file = substr( $u, (int) strrpos( $u, '/' ) + 1 );
        $file = preg_replace( '/\.[a-z0-9]{2,5}$/i', '', $file ); // strip extension
        return strtolower( preg_replace( '/-\d+x\d+$/', '', $file ) );
    };
    $add_image = function ( $u ) use ( &$img_urls, &$seen_stems, $img_stem ) {
        if ( ! $u || ! is_string( $u ) ) {
            return;
        }
        $stem = $img_stem( $u );
        if ( in_array( $stem, $seen_stems, true ) ) {
            return;
        }
        $seen_stems[] = $stem;
        $img_urls[]   = $u;
    };
    $thumb = get_the_post_thumbnail_url( $post_id, 'morimoflix-poster' );
    $add_image( $thumb );
    $backdrop_img = morimoflix_get_backdrop( $post_id );
    if ( $backdrop_img ) {
        // hero variant is t/p/original/ — embed the lean w1280 CDN crop instead
        $backdrop_img = preg_replace( '#/t/p/\w+/#', '/t/p/w1280/', $backdrop_img );
    }
    $add_image( $backdrop_img );
    if ( ! empty( $og['url'] ) ) {
        $add_image( $og['url'] ); // skipped when it is the poster file at another size
    }
    $poster_alt = morimoflix_poster_alt( $post_id );
    foreach ( array_slice( $img_urls, 0, 4 ) as $img_url ) {
        $blocks[] = morimoflix_seo_block(
            'image',
            '',
            '<figure class="wp-block-image size-large"><img loading="lazy" decoding="async" src="' . esc_url( $img_url ) . '" alt="' . esc_attr( $poster_alt ) . '" /></figure>'
        );
    }

    // Table of contents with anchor links. Labels stay SHORT: the list is one
    // block for Rank Math's paragraph check, which fails blocks over 120
    // words, so the title is repeated only once.
    $toc_items = array(
        array( 'mfseo-h1', $title . ' ' . $kind . ' Story Overview' ),
        array( 'mfseo-h2', 'Characters And Tension' ),
        array( 'mfseo-h3', 'Why Watch In Full HD' ),
        array( 'mfseo-h4', 'Why Get ' . $kind . ' Downloads On FlixGarden' ),
        array( 'mfseo-h5', 'When To Watch 1080P' ),
        array( 'mfseo-h6', 'Best Way To Download Free' ),
    );
    $list_items = '';
    foreach ( $toc_items as $item ) {
        $list_items .= '<!-- wp:list-item --><li><a href="#' . esc_attr( $item[0] ) . '">' . esc_html( $item[1] ) . '</a></li><!-- /wp:list-item -->';
    }
    // Class wp-block-rank-math-toc-block satisfies Rank Math's contentHasTOC check.
    $blocks[] = morimoflix_seo_block( 'list', '', '<ul class="wp-block-rank-math-toc-block">' . $list_items . '</ul>' );

    // Story section.
    $blocks[] = morimoflix_seo_heading( 'mfseo-h1', 'What Is ' . $title . ' ' . $kind . ' Story About' );
    $blocks[] = $p( $t( $synopsis ) );
    $blocks[] = $p( 'Watching ' . esc_html( $title ) . ' ' . esc_html( strtolower( $kind ) ) . ' download reveals a story packed with tension and survival instincts. Trapped in a world where every exit is guarded, the protagonist is forced to think fast before becoming the next victim.' );
    $blocks[] = $p( 'The narrative weaves together multiple threads of tension and drama, creating a compelling viewing experience. Every scene builds upon the last, drawing you deeper into the world of ' . esc_html( $title ) . '.' );
    $blocks[] = $p( 'Morality is never simple in this story, and that ambiguity keeps viewers invested until the closing scene. Friends become liabilities, secrets turn into weapons, and every choice carries a visible cost.' );
    $blocks[] = $p( 'Cliffhangers between acts are timed well, so the momentum rarely dips, and the soundtrack carries the unease without resorting to cheap jump scares.' );
    $blocks[] = $p( 'Each twist escalates the stakes, and the supporting cast keeps the chemistry believable from the first frame to the last. That human core keeps ' . esc_html( $title ) . ' replay value high long after the credits roll.' );

    // Characters section.
    $blocks[] = morimoflix_seo_heading( 'mfseo-h2', 'Characters And Tension In ' . $title . ' ' . $kind . ' Download' );
    $blocks[] = $p( 'What makes ' . esc_html( $title ) . ' ' . esc_html( strtolower( $kind ) ) . ' download stand out is the contrast between innocent hopes and the dark forces running the show. The protagonist is not an action superhero; they feel real fear, make desperate moves, and use their wits to outlast opponents far stronger than them.' . ( $actor_links ? ' Starring ' . implode( ' ', $actor_links ) . ' and the rest of the ensemble.' : '' ) );
    $blocks[] = $p( 'The antagonists maintain an eerie charm that conceals deeper motives. That unsettling balance keeps the audience on edge.' . ( $genre_links ? ' Explore more ' . implode( ' ', $genre_links ) . ' titles on FlixGarden.' : '' ) );
    $blocks[] = $p( 'The supporting cast delivers memorable performances that elevate the material beyond a standard genre exercise. Each character serves a purpose in the larger narrative, creating a rich tapestry of human drama.' );
    $blocks[] = $p( 'Supporting roles add texture as well, from loyal allies to unpredictable rivals, so no scene ever feels empty or padded. Even brief exchanges reveal character motivation and push the plot forward.' );

    // HD quality section.
    $blocks[] = morimoflix_seo_heading( 'mfseo-h3', 'Why Watch ' . $title . ' Full ' . $kind . ' Download In Full HD' );
    $blocks[] = $p( 'Films depend heavily on deep shadows, subtle sound cues, and sharp cinematography to build atmosphere. When you watch ' . esc_html( $title ) . ' full ' . esc_html( strtolower( $kind ) ) . ' download, high-definition clarity ensures you catch the fine details and visual craft that low-bitrate copies destroy.' );
    $blocks[] = $p( 'The film uses lighting to signal shifts between safety and danger. Experiencing ' . esc_html( $title ) . ' full ' . esc_html( strtolower( $kind ) ) . ' download in clear 1080p lets you appreciate the atmospheric production design and tense pacing from start to finish.' );
    $blocks[] = $p( 'Choosing a full-resolution file instead of an unstable stream also means no buffering wheels, no mid-scene quality drops, and no reliance on uncertain connections during the most important moments of the film.' );
    $blocks[] = $p( 'High quality streaming ensures that you do not miss any detail, from subtle facial expressions to grand set pieces. Every frame is crafted with care, and watching in HD brings out the best in the cinematography.' );
    $blocks[] = $p( 'Sharp 1080p resolution also matters on small screens, where compressed copies blur backdrops and ruin dimly lit scenes. Grabbing the HD file once means every rewatch looks as good as the first.' );

    // FlixGarden value section.
    $blocks[] = morimoflix_seo_heading( 'mfseo-h4', 'Why Get ' . $kind . ' Downloads On FlixGarden' );
    $blocks[] = $p( 'Many random streaming links are cluttered with broken mirrors, buffering players, and aggressive pop-up advertisements. On flixgarden.site, ' . esc_html( $title ) . ' ' . esc_html( strtolower( $kind ) ) . ' download is hosted with clean server links so you can save the full ' . esc_html( strtolower( $kind ) ) . ' directly to your device without hassle.' );
    $blocks[] = $p( 'We optimize file sizes using modern high-efficiency compression so you get crisp picture and punchy sound without burning through storage. You can grab your download and watch offline anytime you want.' );
    $blocks[] = $p( 'FlixGarden provides a seamless experience with fast loading times and no interruptions. Whether you are on mobile or desktop, the platform adapts to your screen for the best viewing experience.' );
    $blocks[] = $p( 'Every upload is checked before it goes live, so ratings, runtime, audio language, and format details stay accurate on the page itself. Stale or dead transfers are replaced quickly rather than left to rot.' );
    $blocks[] = $p( 'Multiple server mirrors exist for every release, so even if one link slows down at peak hours you can switch mirrors instantly and continue exactly where you left off.' );

    // When to watch section.
    $blocks[] = morimoflix_seo_heading( 'mfseo-h5', 'When To Watch ' . $title . ' 1080P Download' );
    $blocks[] = $p( 'Save ' . esc_html( $title ) . ' 1080p download for a late-night viewing when you can turn off the lights and immerse yourself in the atmosphere. The escalating tension works best in a quiet setting where every creak and whisper stands out.' );
    $blocks[] = $p( 'The final act brings the tension to a boiling point as the protagonist unearths a shocking secret. Watching the film uninterrupted gives the climax the punch it deserves.' );
    $blocks[] = $p( 'Plan your viewing session accordingly. A film like this rewards patience and attention to detail. Make sure you have a good internet connection or download it beforehand for uninterrupted viewing.' );
    $blocks[] = $p( 'Weekend binge or weeknight unwind, the runtime slots neatly into a single evening, leaving time afterwards for the alternate endings and bonus clips that fans always discuss online.' );

    // Closing CTA section.
    $blocks[] = morimoflix_seo_heading( 'mfseo-h6', 'Best Way To Get ' . $title . ' ' . $kind . ' Download Free' );
    $blocks[] = $p( 'When searching for ' . esc_html( $title ) . ' ' . esc_html( strtolower( $kind ) ) . ' download free, always choose secure direct download sources over sketchy redirect pages. On FlixGarden, the complete title is indexed with verified metadata, cast details, and fast download options in ' . esc_html( (string) $m['quality'] ) . ' quality.' );
    $blocks[] = $p( 'If you enjoy this genre with modern pacing, start your download from the quality selector on this page and experience the thrill of ' . esc_html( $title ) . ' tonight.' );
    $blocks[] = $p( 'FlixGarden continues to be the go-to destination for ' . esc_html( strtolower( $kind ) ) . ' lovers who want quality without compromise. Bookmark this page and check back for updates on new releases and download links.' );
    $blocks[] = $p( 'Popular titles get refreshed links and updated quality versions first, so pages stay current as better rips and newer language tracks become available.' );
    $blocks[] = $p( 'Requests for titles missing from the library are handled through the request button on the homepage, and fulfilled orders usually appear within a few days of being approved.' );

    return implode( "\n\n", $blocks );
}

/**
 * Rank Math content checklist with the plugin's real analyzer weights
 * (mirrors rank-math/assets/admin/js/analyzer.js default scores).
 *
 * @param string $content   Post content (may be HTML/blocks).
 * @param array  $meta      Current rank_math_ meta values.
 * @param string $post_title Post title (unused; kept for signature parity).
 * @param string $permalink  Post permalink.
 * @return array  { score:int, checks: array, issues: array, stats: array }
 */
function morimoflix_seo_checklist( $content, $meta, $post_title, $permalink ) {
    $kw_all   = array_values( array_filter( array_map( 'trim', explode( ',', strtolower( wp_check_invalid_utf8( (string) ( $meta['rank_math_focus_keyword'] ?? '' ), true ) ) ) ) ) );
    $primary  = $kw_all ? $kw_all[0] : '';
    $seo_t    = strtolower( wp_check_invalid_utf8( (string) ( $meta['rank_math_title'] ?? '' ), true ) );
    $seo_d    = strtolower( wp_check_invalid_utf8( (string) ( $meta['rank_math_description'] ?? '' ), true ) );

    $plain_low = strtolower( trim( preg_replace( '/\s+/u', ' ', wp_strip_all_tags( wp_check_invalid_utf8( (string) $content, true ), true ) ) ) );
    $words     = str_word_count( $plain_low );

    // Combined variant match count (RM keywordDensity formula: hits / words * 100).
    $variants = array();
    foreach ( $kw_all as $kw_item ) {
        if ( '' === $kw_item ) {
            continue;
        }
        $parts = array_map( static function ( $w ) { return preg_quote( $w, '/' ); }, preg_split( '/\s+/u', $kw_item ) );
        $variants[] = implode( '[\s\W]+', $parts );
    }
    usort( $variants, static function ( $a, $b ) { return strlen( $b ) - strlen( $a ); } );
    $kw_hits = $variants ? (int) preg_match_all( '/(' . implode( '|', $variants ) . ')/u', $plain_low ) : 0;
    $density = $words ? round( $kw_hits / $words * 100, 2 ) : 0.0;
    // RM bands: <0.5 fail(0), 0.5-0.75 fair(2), 0.76-1 good(3), 1-2.5 best(6), >2.5 fail.
    if ( $density > 0 && $density < 0.5 ) { $density_score = 0; }
    elseif ( $density < 0.76 ) { $density_score = $density >= 0.5 ? 2 : 0; }
    elseif ( $density <= 1 ) { $density_score = 3; }
    elseif ( $density <= 2.5 ) { $density_score = 6; }
    else { $density_score = 0; }

    $first10 = mb_substr( $plain_low, 0, (int) max( 1, mb_strlen( $plain_low ) * 0.1 ) );
    $h2h6    = strtolower( implode( ' ', (array) ( preg_match_all( '/<h[2-6][^>]*>(.*?)<\/h[2-6]>/is', $content, $hm ) ? $hm[1] : array() ) ) );
    $alts    = (array) ( preg_match_all( '/<img[^>]+alt="([^"]*)"/is', $content, $im ) ? $im[1] : array() );
    $a_tags  = (array) ( preg_match_all( '/<a[^>]*>/is', $content, $at ) ? $at[0] : array() );
    $home    = home_url();
    $internal = 0;
    $external = 0;
    $dofollow = 0;
    foreach ( $a_tags as $a_tag ) {
        $href = preg_match( '/href="([^"]*)"/i', $a_tag, $ha ) ? $ha[1] : '';
        if ( '' === $href || 0 === strpos( $href, '#' ) ) {
            continue;
        }
        if ( false !== strpos( $href, $home ) || 0 === strpos( $href, '/' ) ) {
            $internal++;
        } else {
            $external++;
            if ( false === stripos( $a_tag, 'nofollow' ) ) {
                $dofollow++;
            }
        }
    }
    // Rank Math splits the analysed text into blocks (double newline and/or
    // block tags) and fails any block over 120 words. Plain-text blocks (the
    // SEO template emits them) are invisible to <p>-matching alone.
    $para_blocks = preg_split( '/\n{2,}/', $content );
    $para_blocks = array_merge( (array) $para_blocks, (array) ( preg_match_all( '/<p[^>]*>(.*?)<\/p>/is', $content, $pm ) ? $pm[1] : array() ) );
    $long_para   = false;
    foreach ( $para_blocks as $para_x ) {
        if ( str_word_count( wp_strip_all_tags( $para_x, true ) ) > 120 ) {
            $long_para = true;
            break;
        }
    }
    $images_n = (int) preg_match_all( '/<img[^>]+src=/is', $content );
    $videos_n = (int) preg_match_all( '/<video/i', $content );
    $img_map  = array( 0 => 0, 1 => 1, 2 => 2, 3 => 4, 4 => 6, 5 => 6, 6 => 6 );
    $assets   = min( 6, ( $img_map[ min( 6, $images_n ) ] ) + min( 2, $videos_n ) );
    $len_bands = array( 2500 => 8, 2000 => 5, 1500 => 4, 1000 => 3, 600 => 2 );
    $len_score = 0;
    foreach ( $len_bands as $min_w => $sc ) {
        if ( $words >= $min_w ) { $len_score = $sc; break; }
    }

    // Title checks (RM semantics: phrase/word containment; power words; number).
    $kw_slug   = strtolower( str_replace( ' ', '-', preg_replace( '/[^\w\s\-]/u', '', $primary ) ) );
    $url_path  = preg_replace( '/[-_]/', '-', (string) wp_parse_url( $permalink, PHP_URL_PATH ) );
    $url_path  = strtolower( $url_path );
    $title_has_number = (int) preg_match( '/[0-9]/', $seo_t );
    $power_words = array( 'best', 'free', 'exclusive', 'amazing', 'instant', 'proven', 'secret', 'ultimate', 'immediate', 'genuine', 'fast', 'easy' );
    $title_power_ok = false;
    foreach ( preg_split( '/\s+/u', $seo_t ) as $t_word ) {
        $t_word = trim( $t_word, " \t\n\r\0\x0B|#:.,-()[]{}\"'" );
        if ( '' === $t_word ) {
            continue;
        }
        foreach ( $power_words as $pw ) {
            if ( $t_word === $pw || ( 0 === strpos( $t_word, $pw ) && mb_strlen( $t_word ) - mb_strlen( $pw ) <= 4 ) ) {
                $title_power_ok = true;
                break 2;
            }
        }
    }
    $sentiment_pos = array( 'best' => 3, 'amazing' => 3, 'ultimate' => 3, 'free' => 2, 'great' => 2, 'top' => 2 );
    $sentiment_neg = array( 'worst' => -3, 'scam' => -3, 'broken' => -2 );
    $t_sent = 0;
    foreach ( preg_split( '/\s+/u', $seo_t ) as $t_word ) {
        $t_word = trim( $t_word, " \t\n\r\0\x0B|#:.,-()[]{}\"'" );
        if ( isset( $sentiment_pos[ $t_word ] ) ) { $t_sent += $sentiment_pos[ $t_word ]; }
        if ( isset( $sentiment_neg[ $t_word ] ) ) { $t_sent += $sentiment_neg[ $t_word ]; }
    }

    $has_kw = '' !== $primary;
    // RM Ht(): subject must contain the keyword word sequence contiguous.
    $kw_in_title = $has_kw && false !== strpos( $seo_t, $primary );
    $kw_in_desc  = $has_kw && false !== strpos( $seo_d, $primary );
    $kw_in_10p   = $has_kw && false !== strpos( $first10, $primary );
    $kw_in_h2    = $has_kw && false !== strpos( $h2h6, $primary );
    $kw_in_plain = $has_kw && false !== strpos( $plain_low, $primary );
    $kw_in_slug  = $has_kw && false !== strpos( $url_path, $kw_slug );
    $kw_start    = $has_kw && 0 === strpos( $seo_t, $primary );
    $kw_in_alt   = $has_kw && in_array( true, array_map( static function ( $alt ) use ( $primary ) { return false !== strpos( strtolower( (string) $alt ), $primary ); }, $alts ), true );
    $path_len    = strlen( (string) wp_parse_url( $permalink, PHP_URL_PATH ) );
    $toc_ok      = false !== strpos( $content, 'wp-block-rank-math-toc-block' );

    // [ label, pass(bool), max weight, note ]
    $tests = array(
        array( 'Focus keyword in SEO title (36pts)', $kw_in_title, 36 ),
        array( 'Focus keyword at the beginning of SEO title (3pts)', $kw_start, 3 ),
        array( 'Focus keyword in meta description (2pts)', $kw_in_desc, 2 ),
        array( 'Focus keyword in URL (5pts)', $kw_in_slug, 5 ),
        array( 'Focus keyword in content (3pts)', $kw_in_plain, 3 ),
        array( 'Focus keyword in first 10% of content (3pts)', $kw_in_10p, 3 ),
        array( 'Focus keyword in subheadings H2-H6 (3pts)', $kw_in_h2, 3 ),
        array( 'Focus keyword in image alt (2pts)', $kw_in_alt, 2 ),
        array( 'Keyword density 1.0-2.5% best band (6pts)', $density >= 1 && $density <= 2.5, 6, sprintf( '%.2f%% over %d words', $density, $words ) ),
        array( 'Content length (600w=2, 1000w=3, 1500w=4, 2000w=5, 2500w=8)', $len_score >= 2, 8, sprintf( '%d words -> %dpts', $words, $len_score ) ),
        array( 'Internal links present (5pts)', $internal > 0, 5 ),
        array( 'External links present (4pts)', $external > 0, 4 ),
        array( 'External DoFollow link present (2pts)', $dofollow > 0, 2 ),
        array( 'Short paragraphs, none over 120 words (3pts)', ! $long_para, 3 ),
        array( 'Images/videos in content, 4+ images = 6pts', $assets > 0, 6, sprintf( '%d images, %d videos -> %dpts', $images_n, $videos_n, $assets ) ),
        array( 'Table of contents present (2pts)', $toc_ok, 2 ),
        array( 'Title sentiment positive (1pt)', $t_sent > 0, 1 ),
        array( 'Title has power word (1pt)', $title_power_ok, 1 ),
        array( 'Number in title (1pt)', (bool) $title_has_number, 1 ),
        array( 'Permalink <= 75 chars (4pts)', $path_len <= 75, 4 ),
    );

    $total   = 0;
    $earned  = 0;
    $checks  = array();
    $issues  = array();
    foreach ( $tests as $idx => $test ) {
        $pass = (bool) $test[1];
        $max  = (int) $test[2];
        // Item 14 (assets) and item 9 (length) carry partial earned values.
        if ( 14 === $idx ) { $earn = $assets; }
        elseif ( 9 === $idx ) { $earn = $len_score; }
        elseif ( 8 === $idx ) { $earn = $density_score; }
        else { $earn = $pass ? $max : 0; }
        $earned += $earn;
        $total  += $max;
        $checks[] = array( 'check' => $test[0], 'pass' => $pass, 'earned' => $earn, 'max' => $max, 'note' => isset( $test[3] ) ? (string) $test[3] : '' );
        if ( ! $pass ) {
            $issues[] = $test[0];
        }
    }

    return array(
        'score'   => (int) round( $earned * 100 / max( 1, $total ) ),
        'checks'  => $checks,
        'issues'  => $issues,
        'stats'   => array(
            'words'    => $words,
            'density'  => $density,
            'kw_hits'  => $kw_hits,
            'images'   => $images_n,
            'internal' => $internal,
            'external' => $external,
            'title_len' => mb_strlen( $seo_t ),
            'desc_len'  => mb_strlen( $seo_d ),
            'path_len'  => $path_len,
        ),
    );
}

/**
 * Endpoint callback: apply meta/content, then verify + score.
 */
function morimoflix_seo_update_endpoint( $request ) {
    $post_type = $request['objectType'];
    $post_id   = (int) $request['objectID'];
    $apply     = $request['apply'];
    $post      = get_post( $post_id );

    if ( ! $post || 'trash' === $post->post_status ) {
        return new WP_Error( 'morimoflix_seo_no_post', 'Post not found.', array( 'status' => 404 ) );
    }
    if ( $post->post_type !== $post_type ) {
        return new WP_Error( 'morimoflix_seo_type_mismatch', 'Requested objectType does not match post.', array( 'status' => 400 ) );
    }

    $kind    = 'tv' === $post_type ? 'Series' : 'Movie';
    $kind_lc = 'tv' === $post_type ? 'series' : 'movie';
    $meta    = morimoflix_seo_default_meta( $post, $kind, $kind_lc );

    if ( ! empty( $request['title'] ) )         { $meta['rank_math_title'] = trim( (string) $request['title'] ); }
    if ( ! empty( $request['description'] ) )  { $meta['rank_math_description'] = trim( (string) $request['description'] ); }
    if ( ! empty( $request['focus_keyword'] ) ){ $meta['rank_math_focus_keyword'] = trim( (string) $request['focus_keyword'] ); }
    if ( ! empty( $request['canonical_url'] ) ){ $meta['rank_math_canonical_url'] = trim( (string) $request['canonical_url'] ); }

    if ( 'check' === $apply ) {
        $result = morimoflix_seo_checklist( $post->post_content, $meta, get_the_title( $post ), get_permalink( $post ) );
        return rest_ensure_response( array(
            'status'  => 'ok',
            'mode'    => 'check',
            'post_id' => $post_id,
            'score'   => $result,
        ) );
    }

    $content_marker = 'mfseo-h1';
    $already        = false !== strpos( $post->post_content, $content_marker );
    $applied        = array( 'content' => false, 'meta' => false, 'skipped_content' => false );

    if ( in_array( $apply, array( 'full', 'content' ), true ) ) {
        if ( $already && empty( $request['force'] ) ) {
            $applied['skipped_content'] = true;
        } else {
            $update = wp_update_post(
                array(
                    'ID'           => $post_id,
                    'post_content' => morimoflix_seo_build_content( $post_id ),
                ),
                true
            );
            if ( is_wp_error( $update ) ) {
                return $update;
            }
            $applied['content'] = true;
        }
    }

    if ( in_array( $apply, array( 'full', 'meta' ), true ) ) {
        foreach ( $meta as $key => $value ) {
            update_post_meta( $post_id, $key, $value );
        }
        $applied['meta'] = true;
    }

    $fresh    = get_post( $post_id );
    $fresh_meta = array();
    foreach ( $meta as $key => $default ) {
        $fresh_meta[ $key ] = (string) get_post_meta( $post_id, $key, true );
    }
    $result = morimoflix_seo_checklist( $fresh->post_content, $fresh_meta, get_the_title( $fresh ), get_permalink( $fresh ) );

    $verify = array( 'checked' => false );
    if ( ! empty( $request['verify'] ) ) {
        $response = wp_remote_get( get_permalink( $fresh ), array( 'timeout' => 10, 'user-agent' => 'Mozilla/5.0 (FlixGarden SEO Verify)' ) );
        if ( ! is_wp_error( $response ) ) {
            $body   = (string) wp_remote_retrieve_body( $response );
            $v_title = preg_match( '/<title>(.*?)<\/title>/is', $body, $vm ) ? html_entity_decode( $vm[1], ENT_QUOTES | ENT_HTML5 ) : '';
            $verify = array(
                'checked'    => true,
                'title'      => $v_title,
                'title_ok'   => '' !== $fresh_meta['rank_math_title'] && false !== mb_stripos( $v_title, $fresh_meta['rank_math_title'] ),
            );
        }
    }

    return rest_ensure_response( array(
        'status'       => 'ok',
        'mode'         => $apply,
        'post_id'      => $post_id,
        'permalink'    => get_permalink( $fresh ),
        'applied'      => $applied,
        'meta_saved'   => $fresh_meta,
        'score'        => $result,
        'verify'       => $verify,
    ) );
}
