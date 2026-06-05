<?php
/**
 * The Template for displaying front-page
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

global $posterity_a13;

$show_posts = get_option( 'show_on_front' );

if ( $show_posts === 'posts' ) {
    // Ensure numeric values inside index.php are cast safely
    if ( file_exists( get_template_directory() . '/index.php' ) ) {
        include get_template_directory() . '/index.php';
    }
} else {

    // Safe check if object exists
    $fp_variant = isset( $posterity_a13 ) ? $posterity_a13->get_option( 'fp_variant' ) : '';

    if ( $fp_variant === 'page' ) {
        // Use real page templates instead of front-page.php
        $page_template_path = get_page_template();
        $page_template      = $page_template_path ? basename( $page_template_path, '.php' ) : 'page';

        if ( $page_template !== 'page' && $page_template !== 'front-page' ) {
            get_template_part( $page_template );
        } else {
            get_template_part( 'page' );
        }

    } elseif ( $fp_variant === 'blog' ) {
        global $wp_query;

        // Fix for front page pagination (PHP 8.1 safe)
        $paged_str  = get_query_var( 'paged' );
        $page_str   = get_query_var( 'page' );
        $_paged     = max( 1, (int) $paged_str ?: (int) $page_str );

        $args = array(
            'post_type' => 'post',
            'paged'     => $_paged,
        );

        // Cast all numeric values inside query args to int
        $args = array_map( function( $v ) {
            return is_numeric( $v ) ? (int) $v : $v;
        }, $args );

        $wp_query->query( $args );

        // Include index.php safely, numeric casts inside posterity_result_count() are required
        if ( file_exists( get_template_directory() . '/index.php' ) ) {
            include get_template_directory() . '/index.php';
        }
    }
}