<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/archive-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 8.6.0
 */

/* Theme changes: added a13-ajax-get check to serve limited content for lazy load - it makes it much faster! */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Check WooCommerce is active and functions exist
if ( function_exists( 'wc_get_template' ) ) {

    // If AJAX call, load only products and pagination
    if ( isset( $_REQUEST['a13-ajax-get'] ) ) {
        while ( have_posts() ) : the_post();
            wc_get_template_part( 'content', 'product' );
        endwhile;

        // And pagination
        do_action( 'woocommerce_after_shop_loop' );

    } else {
        // Load default WooCommerce template safely
        wc_get_template( 'archive-product.php', array(), 'do-not-look-in-theme' );
    }

} else {
    // WooCommerce not active, fallback message
    echo '<p>' . esc_html__( 'WooCommerce is not active. Please activate WooCommerce plugin.', 'posterity' ) . '</p>';
}