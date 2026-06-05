<?php
/**
 * The main template file.
 *
 * Used as home.php, archive.php, and search.php to reduce template count
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

global $posterity_a13;

$ajax_call = !empty($_REQUEST['a13-ajax-get']);

if ( $ajax_call ) {
    if ( function_exists('posterity_display_items_from_query_post_list') ) {
        posterity_display_items_from_query_post_list();
    }

    the_posts_pagination();

    if ( function_exists('posterity_result_count') ) {
        posterity_result_count();
    }

} else {

    $_title = '';

    // Determine page title
    if ( is_search() ) {
        $search_term = (string) get_search_query();
        $all_search  = new WP_Query( [ 's' => $search_term, 'posts_per_page' => -1 ] );
        $count       = $all_search->post_count;

        $_title = sprintf(
            esc_html( _n( '%1$d search result for "%2$s"', '%1$d search results for "%2$s"', $count, 'posterity' ) ),
            $count,
            $search_term
        );

    } elseif ( is_archive() ) {
        if ( is_author() ) {
            $_title = sprintf(
                esc_html__( 'Author Archives: %s', 'posterity' ),
                "<span class='vcard'>" . get_the_author() . "</span>"
            );
        } elseif ( is_category() ) {
            $_title = sprintf(
                esc_html__( 'Category Archives: %s', 'posterity' ),
                '<span>' . single_cat_title( '', false ) . '</span>'
            );
        } elseif ( is_tag() ) {
            $_title = sprintf(
                esc_html__( 'Tag Archives: %s', 'posterity' ),
                '<span>' . single_tag_title( '', false ) . '</span>'
            );
        } elseif ( is_day() ) {
            $_title = sprintf(
                esc_html__( 'Daily Archives: %s', 'posterity' ),
                '<span>' . get_the_date() . '</span>'
            );
        } elseif ( is_month() ) {
            $_title = sprintf(
                esc_html__( 'Monthly Archives: %s', 'posterity' ),
                '<span>' . get_the_date( 'F Y' ) . '</span>'
            );
        } elseif ( is_year() ) {
            $_title = sprintf(
                esc_html__( 'Yearly Archives: %s', 'posterity' ),
                '<span>' . get_the_date( 'Y' ) . '</span>'
            );
        } else {
            $_title = esc_html__( 'Blog Archives', 'posterity' );
        }
    }

    $lazy_load        = $posterity_a13->get_option('blog_lazy_load') === 'on';
    $pagination_class = $lazy_load ? ' lazy-load-on' : '';

    get_header();

    if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'archive' ) ) {

        posterity_title_bar( 'outside', $_title );
        ?>
        <article id="content" class="clearfix">
            <div class="content-limiter">
                <div id="col-mask">
                    <div class="content-box<?php echo esc_attr( $pagination_class ); ?>">
                        <?php
                        // Safe display of posts
                        global $post;
                        if ( function_exists('posterity_display_items_from_query_post_list') ) {
                            posterity_display_items_from_query_post_list();
                        }
                        ?>
                        <div class="clear"></div>

                        <?php
                        the_posts_pagination();

                        if ( function_exists('posterity_result_count') ) {
                            posterity_result_count();
                        }
                        ?>
                    </div>
                    <?php get_sidebar(); ?>
                </div>
            </div>
        </article>
        <?php
    }

    get_footer();
}