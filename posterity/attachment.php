<?php
/**
 * The template for displaying attachments
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

global $post;

// Only proceed if there is a post to show
if ( have_posts() ) {
    the_post(); // Safely advance the global $post
} else {
    echo '<p>' . esc_html__( 'No attachment found.', 'posterity' ) . '</p>';
    get_footer();
    exit;
}

get_header();
?>

<article id="content" class="clearfix">
    <div class="content-limiter">
        <div id="col-mask">

            <div id="post-<?php the_ID(); ?>" <?php post_class( 'content-box' ); ?>>

                <div class="formatter">
                    <?php
                    // Safe wrapper for title bar to avoid array_reduce on null
                    if ( function_exists('posterity_title_bar') ) {
                        posterity_title_bar( 'inside' );
                    }
                    ?>
                    
                    <div class="real-content">

                        <?php
                        if ( wp_attachment_is_image( $post->ID ) ) {
                            echo '<p class="attachment">' . wp_get_attachment_image( $post->ID, 'large' ) . '</p>';
                        } else {
                            echo wp_kses_post( prepend_attachment('') );
                            the_content();
                        }
                        ?>

                        <div class="attachment-info">

                            <?php if ( ! empty( $post->post_parent ) ) : ?>
                                <span>
                                    <a href="<?php echo esc_url( get_permalink( $post->post_parent ) ); ?>" title="<?php echo esc_attr( sprintf( esc_html__( 'Return to %s', 'posterity' ), get_the_title( $post->post_parent ) ) ); ?>" rel="gallery">
                                        <?php echo esc_html( sprintf( esc_html__( 'Return to %s', 'posterity' ), get_the_title( $post->post_parent ) ) ); ?>
                                    </a>
                                </span>
                            <?php endif; ?>

                            <span>
                                <?php
                                printf(
                                    esc_html__( 'By %1$s', 'posterity' ),
                                    sprintf(
                                        '<a class="author" href="%1$s" title="%2$s" rel="author">%3$s</a>',
                                        esc_url( get_author_posts_url( get_the_author_meta( 'ID', $post->post_author ) ) ),
                                        sprintf( esc_attr__( 'View all posts by %s', 'posterity' ), get_the_author() ),
                                        get_the_author()
                                    )
                                );
                                ?>
                            </span>

                            <span>
                                <?php
                                printf(
                                    esc_html__( 'Published %1$s', 'posterity' ),
                                    sprintf(
                                        '<abbr class="published" title="%1$s">%2$s</abbr>',
                                        esc_attr( get_the_time() ),
                                        get_the_date()
                                    )
                                );
                                ?>
                            </span>

                            <?php
                            if ( wp_attachment_is_image( $post->ID ) ) {
                                $metadata = wp_get_attachment_metadata( $post->ID );
                                if ( is_array( $metadata ) && ! empty( $metadata['width'] ) && ! empty( $metadata['height'] ) ) {
                                    echo '<span>';
                                    printf(
                                        esc_html__( 'Full size is %s pixels', 'posterity' ),
                                        sprintf(
                                            '<a href="%1$s" title="%2$s">%3$s &times; %4$s</a>',
                                            esc_url( wp_get_attachment_url( $post->ID ) ),
                                            esc_attr__( 'Link to full-size image', 'posterity' ),
                                            esc_html( $metadata['width'] ),
                                            esc_html( $metadata['height'] )
                                        )
                                    );
                                    echo '</span>';
                                }
                            }

                            if ( function_exists('edit_post_link') ) {
                                edit_post_link( esc_html__( 'Edit', 'posterity' ), '' );
                            }
                            ?>
                        </div>

                        <div class="clear"></div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</article>

<?php get_footer(); ?>