<?php
/**
 * PWA - Progressive Web App Support
 *
 * @package Zsoogi_Clipper
 */

namespace Zsoogi;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Progressive Web App support for Zsoogi Clipper.
 *
 * Serves /manifest.json and /sw.js via WordPress rewrite rules and
 * injects the required <head> meta tags on every page (frontend,
 * wp-admin, and the login screen) so the browser can offer an
 * "Add to Home Screen" prompt and keep the admin session persistent
 * in the standalone PWA context on iOS and Android.
 *
 * @package Zsoogi\PWA
 */
class PWA {

	/**
	 * Query var used to route manifest / service-worker requests.
	 *
	 * @var string
	 */
	const QUERY_VAR = 'zsoogi_pwa_file';

	/**
	 * Initialize PWA functionality.
	 *
	 * Called for all users — manifest.json and sw.js must be publicly
	 * accessible so browsers can fetch them without authentication.
	 *
	 * @since 0.9.2
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_endpoints' ) );
		add_filter( 'query_vars', array( __CLASS__, 'add_query_vars' ) );
		add_action( 'template_redirect', array( __CLASS__, 'serve_files' ) );

		// Output <link rel="manifest"> on every page type.
		add_action( 'wp_head', array( __CLASS__, 'output_head_tags' ) );
		add_action( 'admin_head', array( __CLASS__, 'output_head_tags' ) );
		add_action( 'login_head', array( __CLASS__, 'output_head_tags' ) );
	}

	/**
	 * Register rewrite rules for /manifest.json and /sw.js.
	 *
	 * Added with 'top' priority so they run before any theme rules.
	 * Must be followed by flush_rewrite_rules() — handled in the
	 * plugin activation hook.
	 *
	 * @since 0.9.2
	 *
	 * @return void
	 */
	public static function register_endpoints() {
		add_rewrite_rule( '^manifest\.json$', 'index.php?' . self::QUERY_VAR . '=manifest', 'top' );
		add_rewrite_rule( '^sw\.js$', 'index.php?' . self::QUERY_VAR . '=sw', 'top' );
	}

	/**
	 * Register the PWA query variable with WordPress.
	 *
	 * @since 0.9.2
	 *
	 * @param string[] $vars Existing registered query variables.
	 * @return string[] Modified query variables.
	 */
	public static function add_query_vars( $vars ) {
		$vars[] = self::QUERY_VAR;
		return $vars;
	}

	/**
	 * Serve manifest.json or sw.js when the rewrite rule matches.
	 *
	 * @since 0.9.2
	 *
	 * @return void
	 */
	public static function serve_files() {
		$file = get_query_var( self::QUERY_VAR );

		if ( ! $file ) {
			return;
		}

		switch ( $file ) {
			case 'manifest':
				self::serve_manifest();
				break;
			case 'sw':
				self::serve_service_worker();
				break;
		}
	}

	/**
	 * Build and serve the Web App Manifest JSON.
	 *
	 * Icons fall back to zsoogi.png if the correctly-sized PNGs
	 * (icon-192.png / icon-512.png) have not yet been generated.
	 *
	 * @since 0.9.2
	 *
	 * @return void
	 */
	private static function serve_manifest() {
		$plugin_url = ZSOOGI_CLIPPER_PLUGIN_URL;
		$site_name  = get_bloginfo( 'name' );

		// Build icon list, falling back to zsoogi.png if sized icons are missing.
		$icons = array();
		foreach ( array( '192', '512' ) as $size ) {
			$icon_file = ZSOOGI_CLIPPER_PLUGIN_DIR . 'assets/images/icon-' . $size . '.png';
			$icon_url  = file_exists( $icon_file )
				? $plugin_url . 'assets/images/icon-' . $size . '.png'
				: $plugin_url . 'assets/images/zsoogi.png';

			$icons[] = array(
				'src'     => $icon_url,
				'sizes'   => $size . 'x' . $size,
				'type'    => 'image/png',
				'purpose' => 'any maskable',
			);
		}

		$manifest = array(
			'name'             => $site_name . ' — Zsoogi Clips',
			'short_name'       => 'Zsoogi',
			'description'      => 'Admin-only research clips for ' . $site_name,
			'start_url'        => '/wp-admin/edit.php?post_type=zsoogiclips',
			'display'          => 'standalone',
			'orientation'      => 'portrait',
			'background_color' => '#1d2327',
			'theme_color'      => '#2271b1',
			'scope'            => '/',
			'icons'            => $icons,
		);

		header( 'Content-Type: application/manifest+json; charset=utf-8' );
		header( 'Cache-Control: public, max-age=86400' );
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo wp_json_encode( $manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
		exit;
	}

	/**
	 * Serve the service worker JavaScript from assets/js/sw.js.
	 *
	 * Replaces the __VERSION__ placeholder at serve-time so the cache
	 * name stays in sync with the plugin version without a build step.
	 *
	 * The Service-Worker-Allowed: / header extends the scope beyond the
	 * plugin's subdirectory, which is required when serving from a path
	 * that differs from the intended scope.
	 *
	 * @since 0.9.2
	 *
	 * @return void
	 */
	private static function serve_service_worker() {
		$js_file = ZSOOGI_CLIPPER_PLUGIN_DIR . 'assets/js/sw.js';

		if ( ! file_exists( $js_file ) ) {
			status_header( 404 );
			exit;
		}

		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$js = file_get_contents( $js_file );
		$js = str_replace( '__VERSION__', ZSOOGI_CLIPPER_VERSION, $js );

		header( 'Content-Type: application/javascript; charset=utf-8' );
		header( 'Service-Worker-Allowed: /' );
		header( 'Cache-Control: no-cache, no-store, must-revalidate' );
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $js;
		exit;
	}

	/**
	 * Output PWA <head> meta tags and service worker registration script.
	 *
	 * Attached to wp_head, admin_head, and login_head so the manifest
	 * link is present on every page, including the WP login screen and
	 * the wp-admin clips list (the PWA start_url).
	 *
	 * @since 0.9.2
	 *
	 * @return void
	 */
	public static function output_head_tags() {
		$manifest_url = esc_url( home_url( '/manifest.json' ) );
		$apple_icon   = esc_url( ZSOOGI_CLIPPER_PLUGIN_URL . 'assets/images/icon-192.png' );
		?>
		<link rel="manifest" href="<?php echo $manifest_url; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>">
		<meta name="theme-color" content="#2271b1">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
		<meta name="apple-mobile-web-app-title" content="Zsoogi">
		<link rel="apple-touch-icon" href="<?php echo $apple_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>">
		<script>
		if ( 'serviceWorker' in navigator ) {
			navigator.serviceWorker.register( '/sw.js', { scope: '/' } ).catch( function( e ) {
				console.warn( 'Zsoogi SW registration failed:', e );
			} );
		}
		</script>
		<?php
	}
}
