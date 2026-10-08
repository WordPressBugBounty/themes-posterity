<?php
/**
 * Theme dashboard 
 *
 * @package Posterity
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* -------------------------------------------------------------------------
 * Point 3 - Top-level menu named after the active theme
 * ---------------------------------------------------------------------- */
function posterity_ti_dashboard_menu() {
	$name = posterity_ti_theme_name();
	add_menu_page(
		$name,
		$name,
		'manage_options',
		posterity_ti_page_slug(),
		'posterity_ti_page_display',
		get_template_directory_uri() . '/images/theme-info/menu-icon.svg',
		59
	);
}
add_action( 'admin_menu', 'posterity_ti_dashboard_menu' );

/**
 * Styles + scripts for notices, menu and the dashboard page.
 */
function posterity_ti_admin_assets() {
	$ver = wp_get_theme( get_template() )->get( 'Version' );

	wp_enqueue_style( 'posterity-theme-info', get_template_directory_uri() . '/css/theme-info-admin.css', array(), $ver );

	// Highlight the menu item (its id depends on the active theme slug).
	$id = '#toplevel_page_' . posterity_ti_page_slug();
	$css = "#adminmenu li{$id}>a{background:linear-gradient(90deg,#4f43d1 0,#19b7f0 100%) !important;color:#fff !important;}"
	 . "#adminmenu li{$id}>a:hover,#adminmenu li{$id}.current>a,#adminmenu li{$id}>a:focus{background:linear-gradient(90deg,#19b7f0 0,#4f43d1 100%) !important;color:#fff !important;}"
	 . "#adminmenu li{$id} .wp-menu-image img{width:18px;height:18px;opacity:1;padding-top:7px;}";
	wp_add_inline_style( 'posterity-theme-info', $css );

	wp_enqueue_script( 'posterity-theme-info', get_template_directory_uri() . '/js/theme-info-admin.js', array( 'jquery' ), $ver, true );
	wp_localize_script(
		'posterity-theme-info',
		'posterityTI',
		array(
			'ajaxurl'    => admin_url( 'admin-ajax.php' ),
			'nonce'      => wp_create_nonce( 'posterity_ti_nonce' ),
			'installing' => esc_html__( 'Installing & Activating...', 'posterity' ),
			'activated'  => esc_html__( 'Activated', 'posterity' ),
			'tryAgain'   => esc_html__( 'Try Again', 'posterity' ),
			'failed'     => esc_html__( 'Failed to install or activate the plugin.', 'posterity' ),
		)
	);
}
add_action( 'admin_enqueue_scripts', 'posterity_ti_admin_assets' );

/**
 * Free vs Premium comparison rows. Values: true = yes, false = no, string = text.
 */
function posterity_ti_feature_rows() {
	return apply_filters(
		'posterity_ti_feature_rows',
		array(
			array( __( 'Logo Upload', 'posterity' ), true, true ),
			array( __( 'Color Changes', 'posterity' ), false, true ),
			array( __( 'Font Family', 'posterity' ), false, __( '650 Google Fonts', 'posterity' ) ),
			array( __( 'Font Size Options', 'posterity' ), false, true ),
			array( __( 'Layout Settings', 'posterity' ), false, true ),
			array( __( 'Shortcodes', 'posterity' ), false, true ),
			array( __( 'Slider Settings', 'posterity' ), false, true ),
			array( __( 'More Widgets', 'posterity' ), false, true ),
			array( __( 'Page Templates', 'posterity' ), '3', '8' ),
			array( __( 'Better Responsive', 'posterity' ), false, true ),
			array( __( 'More Homepage Section', 'posterity' ), false, true ),
			array( __( 'Inner Page Header', 'posterity' ), false, true ),
			array( __( 'Default Contact Form', 'posterity' ), false, true ),
			array( __( 'Default Gallery', 'posterity' ), false, true ),
			array( __( 'Blog Layouts', 'posterity' ), false, true ),
			array( __( 'Header/Footer Widgets', 'posterity' ), false, true ),
			array( __( 'Icons Pack', 'posterity' ), false, true ),
			array( __( 'Full Documentation', 'posterity' ), false, true ),
			array( __( 'Email/ Support', 'posterity' ), false, true ),
		)
	);
}

/* -------------------------------------------------------------------------
 * Point 4 - Dashboard page
 * ---------------------------------------------------------------------- */
function posterity_ti_page_display() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$links      = posterity_ti_links();
	$theme      = wp_get_theme();
	$theme_name = $theme->get( 'Name' );
	$img        = get_template_directory_uri() . '/images/theme-info/';
	$shot       = get_template_directory_uri() . '/screenshot.png';

	include_once ABSPATH . 'wp-admin/includes/plugin.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
	$companion_active = is_plugin_active( $links['companion_file'] );

	// Premium-only features (free = false) for the Pro box.
	$pro_only = array();
	foreach ( posterity_ti_feature_rows() as $row ) {
		if ( false === $row[1] && true === $row[2] ) {
			$pro_only[] = $row[0];
		}
	}
	$pro_only = array_slice( $pro_only, 0, 4 );

	$tabs = array(
		'lite_theme' => array( 'dashicons-admin-home', __( 'Getting Started', 'posterity' ) ),
		'theme_pro'  => array( 'dashicons-star-filled', __( 'Premium Version', 'posterity' ) ),
		'free_pro'   => array( 'dashicons-editor-table', __( 'Free vs Pro', 'posterity' ) ),
		'get_bundle' => array( 'dashicons-screenoptions', __( 'WordPress Themes Bundle', 'posterity' ) ),
	);
	?>
<div class="wrap posterity-ti-wrap">
	<h1 class="screen-reader-text"><?php echo esc_html( $theme_name ); ?></h1>

	<!-- Top bar -->
	<header class="pti-topbar">
		<div class="pti-topbar__id">			 
			<div>
				<h2 class="pti-topbar__name"><?php echo esc_html( $theme_name ); ?></h2>
				<span class="pti-badge"><?php
					/* translators: %s: theme version */
					printf( esc_html__( 'v%s', 'posterity' ), esc_html( $theme->get( 'Version' ) ) );
				?></span>
			</div>
		</div>
		<nav class="pti-topbar__nav" aria-label="<?php esc_attr_e( 'Quick links', 'posterity' ); ?>">
			<a href="<?php echo esc_url( $links['free_doc'] ); ?>" target="_blank" rel="noopener"><span class="dashicons dashicons-book-alt" aria-hidden="true"></span><?php esc_html_e( 'Docs', 'posterity' ); ?></a>
			<a href="<?php echo esc_url( $links['support'] ); ?>" target="_blank" rel="noopener"><span class="dashicons dashicons-sos" aria-hidden="true"></span><?php esc_html_e( 'Support', 'posterity' ); ?></a>
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank"><span class="dashicons dashicons-external" aria-hidden="true"></span><?php esc_html_e( 'View site', 'posterity' ); ?></a>
			<a href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>" class="pti-btn pti-btn--primary pti-btn--sm"><?php esc_html_e( 'Customize', 'posterity' ); ?></a>
		</nav>
	</header>

	<div class="pti-layout">

		<!-- Left: tabs -->
		<div class="pti-layout__main">
			<!-- Tabbed content -->
			<div class="wrapper-info pti-card">
				<div class="tab-sec">
					<div class="tab pti-tabs" role="tablist" aria-label="<?php esc_attr_e( 'Theme dashboard sections', 'posterity' ); ?>">
						<?php $first = true; foreach ( $tabs as $tab_id => $tab ) : ?>
							<button type="button" role="tab" class="tablinks<?php echo $first ? ' active' : ''; ?>" data-tab="<?php echo esc_attr( $tab_id ); ?>" aria-controls="<?php echo esc_attr( $tab_id ); ?>">
								<span class="dashicons <?php echo esc_attr( $tab[0] ); ?>" aria-hidden="true"></span><?php echo esc_html( $tab[1] ); ?>
							</button>
						<?php $first = false; endforeach; ?>
					</div>

					<!-- Getting started -->
					<section id="lite_theme" class="tabcontent open" role="tabpanel">
						<div class="pti-cols">
							<div class="pti-cols__main">
								<h3 class="pti-h"><?php
									/* translators: %s: theme name */
									printf( esc_html__( 'Getting started with %s', 'posterity' ), esc_html( $theme_name ) );
								?></h3>
								<p class="pti-prose"><?php
									/* translators: %s: theme name */
									printf( esc_html__( '%s is a next generation multipurpose theme which comes with loads of options. It provides a multilingual and RTL experience and is compatible with WooCommerce, contact form, SEO and page builder plugins like Gutenberg, Elementor, Brizy, Beaver Builder, SiteOrigin and others. It comes with 150+ ready to import templates through the free SKT Templates plugin, which means you have unlimited possibilities to create your next website in any industry such as charity, construction, computer, software, agency, portfolio or business. The theme options in the Customizer let you change colors, typography, header, footer, blog and layout settings without touching code. Simple, flexible, easy to use and fully documented.', 'posterity' ), esc_html( $theme_name ) );
								?></p>
							</div>
							<div class="pti-cols__side">
								<h4 class="pti-label"><?php esc_html_e( 'Quick actions', 'posterity' ); ?></h4>
								<ul class="pti-steps">
									<li>
										<a href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>" target="_blank">
											<span class="dashicons dashicons-admin-customizer" aria-hidden="true"></span>
											<span><strong><?php esc_html_e( 'Edit your site', 'posterity' ); ?></strong><small><?php esc_html_e( 'Colors, typography, header and footer', 'posterity' ); ?></small></span>
										</a>
									</li>
									<?php if ( $companion_active ) : ?>
									<li>
										<a href="<?php echo esc_url( admin_url( 'admin.php?page=skt_template_directory' ) ); ?>">
											<span class="dashicons dashicons-download" aria-hidden="true"></span>
											<span><strong><?php esc_html_e( 'Import templates', 'posterity' ); ?></strong><small><?php esc_html_e( 'Start from a ready-made design', 'posterity' ); ?></small></span>
										</a>
									</li>
									<?php endif; ?>
									<li>
										<a href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank">
											<span class="dashicons dashicons-visibility" aria-hidden="true"></span>
											<span><strong><?php esc_html_e( 'Visit your site', 'posterity' ); ?></strong><small><?php esc_html_e( 'See how it looks right now', 'posterity' ); ?></small></span>
										</a>
									</li>
								</ul>
							</div>
						</div>

						<div class="pti-help">
							<div class="pti-help__item">
								<span class="pti-help__icon dashicons dashicons-sos" aria-hidden="true"></span>
								<h4><?php esc_html_e( 'Need support?', 'posterity' ); ?></h4>
								<p><?php esc_html_e( 'Our team is ready to help with any questions about the theme.', 'posterity' ); ?></p>
								<a href="<?php echo esc_url( $links['support'] ); ?>" class="pti-link" target="_blank" rel="noopener"><?php esc_html_e( 'Support forum', 'posterity' ); ?></a>
							</div>							 
							<div class="pti-help__item">
								<span class="pti-help__icon dashicons dashicons-book-alt" aria-hidden="true"></span>
								<h4><?php esc_html_e( 'Documentation', 'posterity' ); ?></h4>
								<p><?php esc_html_e( 'Step-by-step guides for setting up and configuring the theme.', 'posterity' ); ?></p>
								<a href="<?php echo esc_url( $links['free_doc'] ); ?>" class="pti-link" target="_blank" rel="noopener"><?php esc_html_e( 'Read the docs', 'posterity' ); ?></a>
							</div>
						</div>
					</section>

					<!-- Premium -->
					<section id="theme_pro" class="tabcontent" role="tabpanel">
						<div class="pti-cols">
							<div class="pti-cols__main">
								<h3 class="pti-h"><?php esc_html_e( 'Premium theme information', 'posterity' ); ?></h3>
								<p class="pti-prose"><?php esc_html_e( 'Take your website further with the premium version. It is built for agencies, businesses, freelancers, startups, portfolios and online stores that need a polished and professional look. Showcase your services, team, testimonials, pricing and projects with a clean and engaging design, and sell products with full WooCommerce compatibility. The responsive layout works flawlessly across desktops, tablets and smartphones, while SEO-friendly, lightweight code helps your search engine visibility and page speed. With one click demo import, page builder compatibility, extended theme options and dedicated premium support, you can build a powerful website without writing a single line of code.', 'posterity' ); ?></p>
								<div class="pti-actions">
									<a href="<?php echo esc_url( $links['pro_buy'] ); ?>" target="_blank" rel="noopener" class="pti-btn pti-btn--primary"><?php esc_html_e( 'Buy Premium', 'posterity' ); ?></a>
									<a href="<?php echo esc_url( $links['live_demo'] ); ?>" target="_blank" rel="noopener" class="pti-btn pti-btn--secondary"><?php esc_html_e( 'Live Demo', 'posterity' ); ?></a>
								</div>
							</div>
							<div class="pti-cols__side">
								<div class="pti-shot"><img src="<?php echo esc_url( $shot ); ?>" alt="" /></div>
							</div>
						</div>
					</section>

					<!-- Free vs Premium -->
					<section id="free_pro" class="tabcontent" role="tabpanel">
						<h3 class="pti-h"><?php esc_html_e( 'Free vs Pro', 'posterity' ); ?></h3>
						<div class="pti-table-wrap">
							<table class="pti-table">
								<thead>
									<tr>
										<th scope="col"><?php esc_html_e( 'Feature', 'posterity' ); ?></th>
										<th scope="col"><?php esc_html_e( 'Free', 'posterity' ); ?></th>
										<th scope="col" class="is-pro"><?php esc_html_e( 'Pro', 'posterity' ); ?></th>
									</tr>
								</thead>
								<tbody>
								<?php
								foreach ( posterity_ti_feature_rows() as $row ) {
									echo '<tr><th scope="row">' . esc_html( $row[0] ) . '</th>';
									for ( $c = 1; $c <= 2; $c++ ) {
										echo '<td class="' . ( 2 === $c ? 'is-pro' : '' ) . '">';
										if ( true === $row[ $c ] ) {
											echo '<span class="pti-yes dashicons dashicons-yes-alt" aria-hidden="true"></span><span class="screen-reader-text">' . esc_html__( 'Yes', 'posterity' ) . '</span>';
										} elseif ( false === $row[ $c ] ) {
											echo '<span class="pti-no" aria-hidden="true"></span><span class="screen-reader-text">' . esc_html__( 'No', 'posterity' ) . '</span>';
										} else {
											echo esc_html( $row[ $c ] );
										}
										echo '</td>';
									}
									echo '</tr>';
								}
								?>
								</tbody>
								<tfoot>
									<tr>
										<td></td>
										<td></td>
										<td class="is-pro"><a href="<?php echo esc_url( $links['pro_buy'] ); ?>" target="_blank" rel="noopener" class="pti-btn pti-btn--primary pti-btn--block"><?php esc_html_e( 'Upgrade to Pro', 'posterity' ); ?></a></td>
									</tr>
								</tfoot>
							</table>
						</div>
					</section>

					<!-- Bundle -->
					<section id="get_bundle" class="tabcontent" role="tabpanel">
						<div class="pti-cols">
							<div class="pti-cols__main">
								<h3 class="pti-h"><?php esc_html_e( 'WordPress Themes Bundle', 'posterity' ); ?></h3>
								<p class="pti-prose"><?php
									/* translators: %s: number of themes */
									printf( esc_html__( 'Enhance your website effortlessly with our WordPress Themes Bundle. Get access to %s premium WordPress themes, all designed to meet diverse business needs. Enjoy seamless integration with popular plugins, ultimate customization flexibility, and regular updates to keep your site current and secure. Plus, benefit from our dedicated customer support, ensuring a smooth and professional web experience.', 'posterity' ), esc_html( $links['theme_count'] ) );
								?></p>
								<div class="pti-actions">
									<a href="<?php echo esc_url( $links['bundle_buy'] ); ?>" target="_blank" rel="noopener" class="pti-btn pti-btn--primary"><?php
										/* translators: %s: price */
										printf( esc_html__( 'Get the bundle for %s', 'posterity' ), esc_html( $links['bundle_price'] ) );
									?></a>
									<a href="<?php echo esc_url( $links['bundle_doc'] ); ?>" target="_blank" rel="noopener" class="pti-btn pti-btn--secondary"><?php esc_html_e( 'Documentation', 'posterity' ); ?></a>
								</div>
							</div>
							<div class="pti-cols__side">
								<div class="pti-shot pti-scroll" style="background-image:url('<?php echo esc_url( $img . 'bundle-preview.jpg' ); ?>');" role="img" aria-label="<?php esc_attr_e( 'WordPress Themes Bundle preview', 'posterity' ); ?>"></div>
							</div>
						</div>
					</section>
				</div>
			</div>
		</div>

		<!-- Right: bundle + pro -->
		<aside class="pti-layout__side">
            <!-- WordPress Themes Bundle -->
            <section class="pti-hero" aria-labelledby="pti-hero-title">
				<div class="pti-hero__content">
					<span class="pti-hero__pill"><?php
						/* translators: %s: number of themes */
						printf( esc_html__( '%s premium themes', 'posterity' ), esc_html( $links['theme_count'] ) );
					?></span>
					<h2 id="pti-hero-title" class="pti-hero__title"><?php esc_html_e( 'WordPress Themes Bundle', 'posterity' ); ?></h2>
					<p class="pti-hero__lede"><?php
						/* translators: %s: number of themes */
						printf( esc_html__( 'Get access to %s premium WordPress themes for every kind of business, with regular updates and dedicated support.', 'posterity' ), esc_html( $links['theme_count'] ) );
					?></p>

					<ul class="pti-hero__points">
						<li><?php esc_html_e( 'Customization flexibility', 'posterity' ); ?></li>
						<li><?php esc_html_e( 'Dedicated support', 'posterity' ); ?></li>
					</ul>

					<div class="pti-plans">
						<div class="pti-plan">
							<div class="pti-plan__head">
								<span class="pti-plan__name"><?php esc_html_e( 'All Themes', 'posterity' ); ?></span>
								<span class="pti-plan__price"><?php echo esc_html( $links['bundle_price'] ); ?></span>
							</div>
							<p class="pti-plan__term"><?php esc_html_e( '1 year of updates & support', 'posterity' ); ?></p>
							<a href="<?php echo esc_url( $links['bundle_buy'] ); ?>" target="_blank" rel="noopener" class="pti-btn pti-btn--primary pti-btn--block"><?php esc_html_e( 'Buy Now', 'posterity' ); ?></a>
						</div>
						<div class="pti-plan">
							<div class="pti-plan__head">
								<span class="pti-plan__name"><?php esc_html_e( 'Lifetime', 'posterity' ); ?></span>
								<span class="pti-plan__price"><?php echo esc_html( $links['lifetime_price'] ); ?></span>
							</div>
							<p class="pti-plan__term"><?php esc_html_e( 'Lifetime updates & support', 'posterity' ); ?></p>
							<a href="<?php echo esc_url( $links['bundle_lifetime'] ); ?>" target="_blank" rel="noopener" class="pti-btn pti-btn--primary pti-btn--block"><?php esc_html_e( 'Buy Now', 'posterity' ); ?></a>
						</div>
					</div>
				</div>
				<div class="pti-hero__media">
					<img src="<?php echo esc_url( $img . 'bundle-notice.png' ); ?>" alt="<?php esc_attr_e( 'WordPress Themes Bundle', 'posterity' ); ?>" />
				</div>
			</section>
            <!-- Pro theme box -->
            <section class="pti-pro" aria-labelledby="pti-pro-title">
				<div class="pti-pro__media">
					<img src="<?php echo esc_url( $shot ); ?>" alt="<?php
						/* translators: %s: theme name */
						echo esc_attr( sprintf( __( '%s Pro preview', 'posterity' ), $theme_name ) );
					?>" />
				</div>
				<div class="pti-pro__body">
					<h2 id="pti-pro-title" class="pti-pro__title"><?php
						/* translators: %s: theme name */
						printf( esc_html__( '%s Pro', 'posterity' ), esc_html( $theme_name ) );
					?></h2>
					<p class="pti-pro__text"><?php esc_html_e( 'Everything in the free theme, plus premium layouts, extended theme options and priority support from the team that built it.', 'posterity' ); ?></p>
					<?php if ( $pro_only ) : ?>
						<ul class="pti-pro__list">
							<?php foreach ( $pro_only as $feature ) : ?>
								<li><?php echo esc_html( $feature ); ?></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
					<div class="pti-actions">
						<a href="<?php echo esc_url( $links['pro_buy'] ); ?>" target="_blank" rel="noopener" class="pti-btn pti-btn--primary"><?php esc_html_e( 'Buy Premium', 'posterity' ); ?></a>
						<a href="<?php echo esc_url( $links['live_demo'] ); ?>" target="_blank" rel="noopener" class="pti-btn pti-btn--secondary"><?php esc_html_e( 'Live Demo', 'posterity' ); ?></a>
						<a href="<?php echo esc_url( $links['pro_doc'] ); ?>" target="_blank" rel="noopener" class="pti-btn pti-btn--secondary"><?php esc_html_e( 'Documentation', 'posterity' ); ?></a>
					</div>
				</div>
			</section>
		</aside>


	</div>
</div>
	<?php
}