<?php
/**
 * Template used for displaying content of "chat" format posts on archive page.
 * It is used only on page with posts list: blog, archive, search
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly
?>

<div class="formatter">
    <?php posterity_post_meta_data(); ?>
    <h2 class="post-title"><a href="<?php echo esc_url(get_permalink()); ?>"<?php posterity_schema_args('headline'); ?>><?php the_title(); ?></a></h2>
    <div class="real-content"<?php posterity_schema_args('text'); ?>>
        <?php
		// Fixed PHP 8.1 Error
        global $post;
		if (!empty($post) && isset($post->post_content)) {
			echo wp_kses_post(posterity_daoon_chat_post($post->post_content));
		}
		?> 
        <div class="clear"></div>
    </div>
</div>