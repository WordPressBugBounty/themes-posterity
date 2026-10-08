<?php
/**
 * SKT Themes welcome notice, bundle notice and shared helpers
 * for the theme dashboard page.
 *
 * @package Posterity
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function posterity_ti_links() {
	$links = array(
		'brand'           => 'Let Us Build with SKT Themes',
		'pro_buy'         => 'https://www.sktthemes.org/shop/flexible-wordpress-theme/',
		'live_demo'       => 'https://sktperfectdemo.com/demos/posteritypro/',
		'pro_doc'         => 'https://sktthemesdemo.net/documentation/posterity-pro-doc/',
		'free_doc'        => 'https://www.sktthemesdemo.net/documentation/posterity-doc/',
		'support'         => 'https://wordpress.org/support/theme/posterity/',
		'bundle_buy'      => 'https://www.sktthemes.org/shop/all-themes/',
		'bundle_lifetime' => 'https://www.sktthemes.org/shop/lifetime-access-wordpress-themes/',
		'bundle_doc'      => 'https://sktthemesdemo.net/documentation/',
		'theme_count'     => '420+',
		'bundle_price'    => '$69',
		'lifetime_price'  => '$199',
		'companion_slug'  => 'skt-templates',
		'companion_file'  => 'skt-templates/skt-templates.php',
	);
	return apply_filters( 'posterity_ti_links', $links );
}

/**
 * Admin page slug: "{active-theme-slug}-info", e.g. admin.php?page=posterity-info
 */
function posterity_ti_page_slug() {
	return sanitize_key( get_stylesheet() ) . '-info';
}

/**
 * Name of the currently active theme (child theme name when a child theme is active).
 */
function posterity_ti_theme_name() {
	return wp_get_theme()->get( 'Name' );
}

/**
 * Are we on the theme dashboard page?
 */
function posterity_ti_is_info_page() {
	return isset( $_GET['page'] ) && sanitize_key( wp_unslash( $_GET['page'] ) ) === posterity_ti_page_slug(); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
}

/**
 * Common checks before printing a notice.
 */
function posterity_ti_can_show_notice() {
	if ( is_network_admin() || ! current_user_can( 'manage_options' ) || posterity_ti_is_info_page() ) {
		return false;
	}
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( $screen && method_exists( $screen, 'is_block_editor' ) && $screen->is_block_editor() ) {
		return false;
	}
	return true;
}

/* -------------------------------------------------------------------------
 * Point 1 - Welcome notice
 * ---------------------------------------------------------------------- */
function posterity_ti_welcome_notice() {
	if ( get_option( 'posterity_ti_welcome_dismissed' ) || ! posterity_ti_can_show_notice() ) {
		return;
	}
	$links      = posterity_ti_links();
	$theme_name = posterity_ti_theme_name();
	$shot       = get_template_directory_uri() . '/screenshot.png';

	// Real screenshot size so the browser can reserve the space before it loads.
	$shot_w    = 1200;
	$shot_h    = 900;
	$shot_path = get_template_directory() . '/screenshot.png';
	if ( file_exists( $shot_path ) ) {
		$size = wp_getimagesize( $shot_path );
		if ( ! empty( $size[0] ) && ! empty( $size[1] ) ) {
			$shot_w = (int) $size[0];
			$shot_h = (int) $size[1];
		}
	}
	?>
	<div class="notice notice-success is-dismissible welcome-notice posterity-ti-welcome">
		<div class="pti-welcome">
			<div class="pti-welcome__body">
				<h2 class="pti-welcome__title"><?php
					/* translators: %s: theme name */
					printf( esc_html__( '%s is active and ready to use', 'posterity' ), esc_html( $theme_name ) );
				?></h2>
				<p class="pti-welcome__text"><?php
					/* translators: %s: theme name */
					printf( esc_html__( 'You are now using %s, a modern theme to build your website. Preview the demo, or upgrade to unlock every premium feature.', 'posterity' ), esc_html( $theme_name ) );
				?></p>

				<div class="pti-welcome__actions">
					<a href="<?php echo esc_url( $links['live_demo'] ); ?>" class="pti-btn pti-btn--pti-link" target="_blank" rel="noopener"><?php esc_html_e( 'View demo', 'posterity' ); ?></a>
					<a href="<?php echo esc_url( $links['pro_buy'] ); ?>" class="pti-btn pti-btn--primary" target="_blank" rel="noopener"><?php esc_html_e( 'Upgrade to Pro', 'posterity' ); ?></a>
					<a href="<?php echo esc_url( $links['bundle_buy'] ); ?>" class="pti-link" target="_blank" rel="noopener"><?php
						/* translators: %s: number of themes, e.g. 420+ */
						printf( esc_html__( 'Get all %s themes in one bundle', 'posterity' ), esc_html( $links['theme_count'] ) );
					?></a>
				</div>
			</div>

			<div class="pti-welcome__art" style="aspect-ratio: <?php echo esc_attr( $shot_w . ' / ' . $shot_h ); ?>;">
				<img
					src="<?php echo esc_url( $shot ); ?>"
					alt="<?php echo esc_attr( $theme_name ); ?>"
					width="<?php echo esc_attr( $shot_w ); ?>"
					height="<?php echo esc_attr( $shot_h ); ?>"
					decoding="async"
					onload="this.classList.add('is-loaded')"
				/>
			</div>
		</div>
	</div>
	<?php
}
add_action( 'admin_notices', 'posterity_ti_welcome_notice' );

/* -------------------------------------------------------------------------
 * Point 2 - WordPress Themes Bundle notice
 * ---------------------------------------------------------------------- */
function posterity_ti_bundle_notice() {
	if ( get_option( 'posterity_ti_bundle_dismissed' ) || ! posterity_ti_can_show_notice() ) {
		return;
	}
	$links = posterity_ti_links();
	?>
	<div class="notice posterity-ti-bundle-notice">
		<button type="button" class="posterity-ti-bundle-dismiss" aria-label="<?php esc_attr_e( 'Dismiss this notice.', 'posterity' ); ?>"></button>
		<div class="pti-bnotice">
			<div class="pti-bnotice__art">
				<img src="<?php echo esc_url( get_template_directory_uri() . '/images/theme-info/bundle-notice.png' ); ?>" alt="<?php esc_attr_e( 'WordPress Themes Bundle', 'posterity' ); ?>" />
			</div>

			<div class="pti-bnotice__copy">
				<h2 class="pti-bnotice__title"><?php esc_html_e( 'WordPress Themes Bundle', 'posterity' ); ?></h2>
				<p class="pti-bnotice__sub"><?php
					/* translators: %s: number of themes */
					printf( esc_html__( 'Get access to %s premium WordPress themes in a single bundle.', 'posterity' ), esc_html( $links['theme_count'] ) );
				?></p>
					<p class="pti-bnotice__note"><?php esc_html_e( 'Best price guaranteed', 'posterity' ); ?></p>
			</div>

			<div class="pti-bnotice__plans">
				<a href="<?php echo esc_url( $links['bundle_buy'] ); ?>" class="pti-bplan" target="_blank" rel="noopener">
					<span class="pti-bplan__name"><?php esc_html_e( 'Yearly', 'posterity' ); ?></span>
					<span class="pti-bplan__price"><?php echo esc_html( $links['bundle_price'] ); ?></span>
					<span class="pti-bplan__term"><?php esc_html_e( '1 year updates & support', 'posterity' ); ?></span>
                    <span class="pti-bplan__buy"><?php esc_html_e( 'Buy Now', 'posterity' ); ?></span>
				</a>
				<a href="<?php echo esc_url( $links['bundle_lifetime'] ); ?>" class="pti-bplan" target="_blank" rel="noopener">
					<span class="pti-bplan__name"><?php esc_html_e( 'Lifetime', 'posterity' ); ?></span>
					<span class="pti-bplan__price"><?php echo esc_html( $links['lifetime_price'] ); ?></span>
					<span class="pti-bplan__term"><?php esc_html_e( 'Lifetime updates & support', 'posterity' ); ?></span>
                    <span class="pti-bplan__buy"><?php esc_html_e( 'Buy Now', 'posterity' ); ?></span>
				</a>
			</div>
		</div>
	</div>
	<?php
}
add_action( 'admin_notices', 'posterity_ti_bundle_notice' );

/* -------------------------------------------------------------------------
 * AJAX handlers
 * ---------------------------------------------------------------------- */

/** Permanently dismiss the welcome or bundle notice. */
function posterity_ti_dismiss_notice() {
	check_ajax_referer( 'posterity_ti_nonce', 'nonce' );
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error();
	}
	$which = isset( $_POST['notice'] ) ? sanitize_key( wp_unslash( $_POST['notice'] ) ) : '';
	if ( 'welcome' === $which ) {
		update_option( 'posterity_ti_welcome_dismissed', 1 );
	} elseif ( 'bundle' === $which ) {
		update_option( 'posterity_ti_bundle_dismissed', 1 );
	}
	wp_send_json_success();
}
add_action( 'wp_ajax_posterity_ti_dismiss_notice', 'posterity_ti_dismiss_notice' );

/** GET STARTED: install (if needed) and activate the SKT Templates companion plugin. */
function posterity_ti_install_companion() {
	check_ajax_referer( 'posterity_ti_nonce', 'nonce' );
	if ( ! current_user_can( 'install_plugins' ) || ! current_user_can( 'activate_plugins' ) ) {
		wp_send_json_error( array( 'message' => esc_html__( 'You are not allowed to install plugins.', 'posterity' ) ) );
	}

	$links = posterity_ti_links();
	$file  = $links['companion_file'];

	include_once ABSPATH . 'wp-admin/includes/plugin.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound

	if ( ! is_plugin_active( $file ) ) {
		$installed = get_plugins();

		if ( ! isset( $installed[ $file ] ) ) {
			include_once ABSPATH . 'wp-admin/includes/file.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
			include_once ABSPATH . 'wp-admin/includes/misc.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
			include_once ABSPATH . 'wp-admin/includes/plugin-install.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
			include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound

			$api = plugins_api( 'plugin_information', array( 'slug' => $links['companion_slug'], 'fields' => array( 'sections' => false ) ) );
			if ( is_wp_error( $api ) || empty( $api->download_link ) ) {
				wp_send_json_error( array( 'message' => esc_html__( 'Could not reach WordPress.org to download SKT Templates.', 'posterity' ) ) );
			}

			ob_start();
			$upgrader = new Plugin_Upgrader( new Automatic_Upgrader_Skin() );
			$result   = $upgrader->install( $api->download_link );
			ob_end_clean();

			if ( is_wp_error( $result ) || ! $result ) {
				wp_send_json_error( array( 'message' => esc_html__( 'Failed to install SKT Templates.', 'posterity' ) ) );
			}
		}

		ob_start();
		$activated = activate_plugin( $file );
		ob_end_clean();

		if ( is_wp_error( $activated ) && 'unexpected_output' !== $activated->get_error_code() ) {
			wp_send_json_error( array( 'message' => $activated->get_error_message() ) );
		}
	}

	update_option( 'posterity_ti_welcome_dismissed', 1 );
	wp_send_json_success( array( 'redirect' => admin_url( 'admin.php?page=' . posterity_ti_page_slug() ) ) );
}
add_action( 'wp_ajax_posterity_ti_install_companion', 'posterity_ti_install_companion' );

/** Show the notices again every time the theme is (re)activated. */
function posterity_ti_reset_notices() {
	delete_option( 'posterity_ti_welcome_dismissed' );
	delete_option( 'posterity_ti_bundle_dismissed' );
}
add_action( 'after_switch_theme', 'posterity_ti_reset_notices' );
