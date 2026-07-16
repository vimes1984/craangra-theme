<?php
/**
 * Crann Grá Theme functions and definitions
 *
 * @package Crann Grá
 */

// -------------------------------------------------------------
// 1. Enqueue theme style.css
// -------------------------------------------------------------
// Enqueue styles for front-end and block editor
function crann_gra_enqueue_assets() {
    // Enqueue FontAwesome
    wp_enqueue_style( 'font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css', [], '6.4.0' );
    // Enqueue main style.css with cache busting version
    $style_path = get_template_directory() . '/style.css';
    $version = file_exists( $style_path ) ? filemtime( $style_path ) : '1.0.0';
    wp_enqueue_style( 'crann-gra-style', get_stylesheet_uri(), [], $version );

    // Enqueue Isotope & Infinite Scroll on shop pages
    if ( ! is_admin() && class_exists( 'WooCommerce' ) && ( is_shop() || is_product_category() || is_product_taxonomy() ) ) {
        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'isotope', 'https://cdnjs.cloudflare.com/ajax/libs/jquery.isotope/3.0.6/isotope.pkgd.min.js', array( 'jquery' ), '3.0.6', true );
        wp_enqueue_script( 'imagesloaded', 'https://cdnjs.cloudflare.com/ajax/libs/jquery.imagesloaded/5.0.0/imagesloaded.pkgd.min.js', array( 'jquery' ), '5.0.0', true );

        $js_path = get_template_directory() . '/assets/js/shop-filter.js';
        $js_ver = file_exists( $js_path ) ? filemtime( $js_path ) : '1.0.0';
        wp_enqueue_script( 'crann-gra-shop-filter', get_template_directory_uri() . '/assets/js/shop-filter.js', array( 'jquery', 'isotope', 'imagesloaded' ), $js_ver, true );

        $categories = get_terms( array(
            'taxonomy' => 'product_cat',
            'hide_empty' => true,
        ) );
        $categories_data = array();
        if ( ! is_wp_error( $categories ) ) {
            foreach ( $categories as $cat ) {
                if ( strtolower( $cat->slug ) === 'uncategorized' ) {
                    continue;
                }
                $categories_data[] = array(
                    'name' => html_entity_decode( $cat->name ),
                    'slug' => $cat->slug,
                );
            }
        }
        wp_localize_script( 'crann-gra-shop-filter', 'shop_filter_params', array(
            'categories' => $categories_data,
        ) );
    }
}
add_action( 'wp_enqueue_scripts', 'crann_gra_enqueue_assets' );
add_action( 'enqueue_block_assets', 'crann_gra_enqueue_assets' );

// -------------------------------------------------------------
// 2. Make all enqueued URLs domain-agnostic (Relative Paths)
//    This prevents browser CORS blocking when accessing the local
//    site via localhost vs 127.0.0.1 or other IP addresses.
// -------------------------------------------------------------
function crann_gra_make_url_relative( $src ) {
    if ( ! $src ) {
        return $src;
    }
    // Only convert URLs that belong to our local WordPress site
    $site_url = site_url();
    if ( strpos( $src, $site_url ) === 0 ) {
        return '/' . ltrim( substr( $src, strlen( $site_url ) ), '/' );
    }
    return $src;
}
add_filter( 'script_loader_src', 'crann_gra_make_url_relative', 999 );
add_filter( 'style_loader_src', 'crann_gra_make_url_relative', 999 );
add_filter( 'script_module_loader_src', 'crann_gra_make_url_relative', 999 );

// -------------------------------------------------------------
// 3. Enable CORS Headers for REST API & HTTP requests
//    This allows WooCommerce block actions (like Cart / Checkout)
//    to successfully communicate with the database.
// -------------------------------------------------------------
function crann_gra_enable_local_cors() {
    $origin = isset( $_SERVER['HTTP_ORIGIN'] ) ? $_SERVER['HTTP_ORIGIN'] : '';
    
    // If origin is local, send headers
    if ( $origin && (strpos( $origin, 'localhost' ) !== false || strpos( $origin, '127.0.0.1' ) !== false) ) {
        header( 'Access-Control-Allow-Origin: ' . $origin );
        header( 'Access-Control-Allow-Credentials: true' );
        header( 'Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE' );
        header( 'Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization, X-WP-Nonce' );
        
        // Handle OPTIONS preflight requests
        if ( isset( $_SERVER['REQUEST_METHOD'] ) && $_SERVER['REQUEST_METHOD'] === 'OPTIONS' ) {
            status_header( 200 );
            exit;
        }
    }
}
add_action( 'init', 'crann_gra_enable_local_cors', 1 );

// Filter REST API CORS response headers specifically
add_action( 'rest_api_init', function() {
    remove_filter( 'rest_pre_serve_request', 'rest_send_cors_headers' );
    add_filter( 'rest_pre_serve_request', function( $value ) {
        $origin = isset( $_SERVER['HTTP_ORIGIN'] ) ? $_SERVER['HTTP_ORIGIN'] : '';
        if ( $origin && (strpos( $origin, 'localhost' ) !== false || strpos( $origin, '127.0.0.1' ) !== false) ) {
            header( 'Access-Control-Allow-Origin: ' . $origin );
            header( 'Access-Control-Allow-Credentials: true' );
            header( 'Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE' );
            header( 'Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization, X-WP-Nonce' );
        }
        return $value;
    }, 15 );
}, 15 );

// Allow local origins in WordPress HTTP Origin checks
function crann_gra_allow_http_origin( $origin ) {
    if ( strpos( $origin, 'localhost' ) !== false || strpos( $origin, '127.0.0.1' ) !== false ) {
        return $origin;
    }
    return $origin;
}
add_filter( 'allowed_http_origin', 'crann_gra_allow_http_origin', 999 );

// -------------------------------------------------------------
// 4. Automatically upload and set Site Logo on theme activation
// -------------------------------------------------------------
function crann_gra_setup_theme() {
    // Add theme support
    add_theme_support( 'custom-logo' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'editor-styles' );
    
    // Check if site logo is already set
    $logo_id = get_option( 'site_logo' );
    if ( ! $logo_id ) {
        // Path to theme's logo
        $logo_path = get_template_directory() . '/images/logo.jpg';
        if ( file_exists( $logo_path ) ) {
            require_once( ABSPATH . 'wp-admin/includes/image.php' );
            require_once( ABSPATH . 'wp-admin/includes/file.php' );
            require_once( ABSPATH . 'wp-admin/includes/media.php' );
            
            // Check if logo is already uploaded
            $query = new WP_Query( [
                'post_type'      => 'attachment',
                'post_status'    => 'any',
                'post_mime_type' => 'image/jpeg',
                'title'          => 'Crann Grá Logo',
                'posts_per_page' => 1,
            ] );
            
            if ( $query->have_posts() ) {
                $logo_id = $query->posts[0]->ID;
            } else {
                // Copy theme logo to uploads
                $upload_dir = wp_upload_dir();
                $filename = 'logo.jpg';
                $filepath = $upload_dir['path'] . '/' . $filename;
                
                if ( copy( $logo_path, $filepath ) ) {
                    $filetype = wp_check_filetype( $filename, null );
                    $attachment = [
                        'post_mime_type' => $filetype['type'],
                        'post_title'     => 'Crann Grá Logo',
                        'post_content'   => '',
                        'post_status'    => 'inherit'
                    ];
                    
                    $logo_id = wp_insert_attachment( $attachment, $filepath );
                    if ( ! is_wp_error( $logo_id ) ) {
                        $attach_data = wp_generate_attachment_metadata( $logo_id, $filepath );
                        wp_update_attachment_metadata( $logo_id, $attach_data );
                    }
                }
            }
            
            if ( $logo_id && ! is_wp_error( $logo_id ) ) {
                update_option( 'site_logo', $logo_id );
                set_theme_mod( 'custom_logo', $logo_id );
            }
        }
    }
}
add_action( 'after_setup_theme', 'crann_gra_setup_theme' );

// -------------------------------------------------------------
// 5. Register Custom Taxonomies (Family, Genus, Species)
// -------------------------------------------------------------
function crann_gra_register_taxonomies() {
    $taxonomies = array(
        'plant_family'  => array( 'singular' => 'Family', 'plural' => 'Families' ),
        'plant_genus'   => array( 'singular' => 'Genus', 'plural' => 'Genera' ),
        'plant_species' => array( 'singular' => 'Species', 'plural' => 'Species' ),
    );

    foreach ( $taxonomies as $slug => $labels ) {
        register_taxonomy( $slug, 'product', array(
            'labels'             => array(
                'name'              => $labels['plural'],
                'singular_name'     => $labels['singular'],
                'search_items'      => 'Search ' . $labels['plural'],
                'all_items'         => 'All ' . $labels['plural'],
                'parent_item'       => 'Parent ' . $labels['singular'],
                'parent_item_colon' => 'Parent ' . $labels['singular'] . ':',
                'edit_item'         => 'Edit ' . $labels['singular'],
                'update_item'       => 'Update ' . $labels['singular'],
                'add_new_item'      => 'Add New ' . $labels['singular'],
                'new_item_name'     => 'New ' . $labels['singular'] . ' Name',
                'menu_name'         => $labels['singular'],
            ),
            'hierarchical'      => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array( 'slug' => str_replace( 'plant_', '', $slug ) ),
            'show_in_rest'      => true,
        ) );
    }
}
add_action( 'init', 'crann_gra_register_taxonomies' );

// -------------------------------------------------------------
// 6. Get Plant Taxonomy Hierarchy HTML
// -------------------------------------------------------------
function crann_gra_get_taxonomy_hierarchy( $product_id ) {
    $family_terms  = get_the_terms( $product_id, 'plant_family' );
    $genus_terms   = get_the_terms( $product_id, 'plant_genus' );
    $species_terms = get_the_terms( $product_id, 'plant_species' );

    $family  = ( ! empty( $family_terms ) && ! is_wp_error( $family_terms ) ) ? $family_terms[0]->name : '';
    $genus   = ( ! empty( $genus_terms ) && ! is_wp_error( $genus_terms ) ) ? $genus_terms[0]->name : '';
    $species = ( ! empty( $species_terms ) && ! is_wp_error( $species_terms ) ) ? $species_terms[0]->name : '';

    if ( ! $family && ! $genus && ! $species ) {
        return '';
    }

    $output = '<div class="plant-taxonomy-hierarchy">';
    if ( $family ) {
        $output .= '<span class="tax-item family"><span class="tax-label">Family:</span> <span class="tax-value">' . esc_html( $family ) . '</span></span>';
    }
    if ( $genus ) {
        if ( $family ) {
            $output .= '<span class="tax-sep">&rarr;</span>';
        }
        $output .= '<span class="tax-item genus"><span class="tax-label">Genus:</span> <span class="tax-value">' . esc_html( $genus ) . '</span></span>';
    }
    if ( $species ) {
        if ( $family || $genus ) {
            $output .= '<span class="tax-sep">&rarr;</span>';
        }
        // Scientific species name is usually italicized
        $output .= '<span class="tax-item species"><span class="tax-label">Species:</span> <span class="tax-value"><em>' . esc_html( $species ) . '</em></span></span>';
    }
    $output .= '</div>';

    return $output;
}

// -------------------------------------------------------------
// 7. Inject Taxonomy Hierarchy into Product Title Blocks
// -------------------------------------------------------------
function crann_gra_render_product_title_taxonomy( $block_content, $block ) {
    if ( 'core/post-title' === $block['blockName'] ) {
        $post_id = isset( $block['context']['postId'] ) ? $block['context']['postId'] : get_the_ID();
        if ( $post_id && 'product' === get_post_type( $post_id ) ) {
            $hierarchy = crann_gra_get_taxonomy_hierarchy( $post_id );
            if ( $hierarchy ) {
                $block_content .= $hierarchy;
            }
        }
    }
    return $block_content;
}
add_filter( 'render_block', 'crann_gra_render_product_title_taxonomy', 10, 2 );

// =============================================================
// SEO & GEO IMPLEMENTATION ACTIONS
// =============================================================

// 1. Force index, follow on production environments (Lifting noindex blocks)
function crann_gra_force_robots_indexation( $robots ) {
    $robots['index'] = true;
    $robots['follow'] = true;
    if ( isset( $robots['noindex'] ) ) {
        unset( $robots['noindex'] );
    }
    if ( isset( $robots['nofollow'] ) ) {
        unset( $robots['nofollow'] );
    }
    return $robots;
}
add_filter( 'wp_robots', 'crann_gra_force_robots_indexation', 999 );

// 2. Set Custom Metadata and descriptions for the Shop Catalog page
function crann_gra_custom_seo_meta() {
    if ( ! class_exists( 'WooCommerce' ) ) {
        return;
    }

    if ( is_shop() ) {
        echo '<meta name="description" content="Browse our collection of clay-tolerant perennials, native Irish ferns, and cottage garden plants propagated at Bumble Cottage, County Leitrim. Shop online at Crann Grá." />' . "\n";
    }
}
add_action( 'wp_head', 'crann_gra_custom_seo_meta', 1 );

// Customize Document Title for Shop
function crann_gra_custom_shop_title( $title_parts ) {
    if ( class_exists( 'WooCommerce' ) && is_shop() ) {
        $title_parts['title'] = 'Buy Hardy Perennials & Garden Plants Online';
        $title_parts['site']  = 'Crann Grá';
    }
    return $title_parts;
}
add_filter( 'document_title_parts', 'crann_gra_custom_shop_title', 999 );

// 3. Deploy LocalBusiness & GardenStore JSON-LD Schema
function crann_gra_inject_json_ld_schema() {
    if ( is_front_page() || ( class_exists( 'WooCommerce' ) && is_shop() ) ) {
        $schema = array(
            "@context" => "https://schema.org",
            "@graph" => array(
                array(
                    "@type" => "OnlineBusiness",
                    "@id" => "https://cranngra.com/#store",
                    "name" => "Crann Grá",
                    "url" => "https://cranngra.com/shop/",
                    "logo" => "https://cranngra.com/wp-content/themes/crann-gra-theme/images/logo.jpg",
                    "image" => "https://cranngra.com/wp-content/themes/crann-gra-theme/images/hero.jpg",
                    "priceRange" => "€€",
                    "sameAs" => array(
                        "https://www.facebook.com/cranngra",
                        "https://www.instagram.com/cranngra",
                        "https://www.linkedin.com/company/cranngra"
                    ),
                    "description" => "Sustainably grown, peat-free wildflowers, native ferns, and garden herbs acclimated to the West of Ireland climate."
                )
            )
        );
        echo '<script type="application/ld+json">' . json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) . '</script>' . "\n";
    }
}
add_action( 'wp_head', 'crann_gra_inject_json_ld_schema', 100 );

// 4. Optimize WooCommerce Blocks Stylesheet Delivery by dequeueing redundant micro-files
function crann_gra_optimize_wc_block_styles() {
    if ( is_admin() ) {
        return;
    }
    
    $handles = array(
        'wc-blocks-style-breadcrumbs',
        'wc-blocks-style-store-notices',
        'wc-blocks-style-product-results-count',
        'wc-blocks-style-product-image',
        'woocommerce-product-price-style',
        'woocommerce-product-button-style',
        'woocommerce-product-template-style',
        'woocommerce-product-collection-style',
    );
    
    foreach ( $handles as $handle ) {
        wp_dequeue_style( $handle );
        wp_deregister_style( $handle );
    }
}
add_action( 'wp_enqueue_scripts', 'crann_gra_optimize_wc_block_styles', 100 );


