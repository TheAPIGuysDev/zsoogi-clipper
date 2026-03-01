<?php
/**
 * Plugin Name: Zsoogi Clipper
 * Plugin URI: https://github.com/TheAPIGuysDev/zsoogi-clipper
 * Description: Create admin-only Zsoogi Clips with a modern jQuery-free bookmarklet for web research.
 * Version: 0.9.2
 * Author: The API Guys
 * Author URI: https://theapiguys.com
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: zsoogi-clipper
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.4
 *
 * @package Zsoogi_Clipper
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Define plugin constants.
if ( ! defined( 'ZSOOGI_CLIPPER_VERSION' ) ) {
	define( 'ZSOOGI_CLIPPER_VERSION', '0.9.2' );
}

if ( ! defined( 'ZSOOGI_CLIPPER_PLUGIN_FILE' ) ) {
	define( 'ZSOOGI_CLIPPER_PLUGIN_FILE', __FILE__ );
}

if ( ! defined( 'ZSOOGI_CLIPPER_PLUGIN_DIR' ) ) {
	define( 'ZSOOGI_CLIPPER_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'ZSOOGI_CLIPPER_PLUGIN_URL' ) ) {
	define( 'ZSOOGI_CLIPPER_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

/**
 * Load Composer autoloader if available.
 *
 * Note: Composer dependencies are optional and only needed for development
 * (PHP_CodeSniffer, WPCS). The plugin works fine in production without them.
 *
 * @since 0.9.0
 *
 * @return bool True if autoloader was loaded, false otherwise.
 */
function zsoogi_clipper_load_autoloader() {
	$autoload_path = ZSOOGI_CLIPPER_PLUGIN_DIR . 'vendor/autoload.php';

	if ( file_exists( $autoload_path ) ) {
		require_once $autoload_path;
		return true;
	}

	return false;
}

/**
 * Load required plugin files.
 *
 * @since 0.9.0
 *
 * @return void
 */
function zsoogi_clipper_load_includes() {
	$includes = array(
		'includes/class-zsoogi-clips.php',
		'includes/class-admin-menu.php',
		'includes/class-zsoogi-clipper.php',
		'includes/class-pwa.php',
	);

	foreach ( $includes as $file ) {
		$file_path = ZSOOGI_CLIPPER_PLUGIN_DIR . $file;

		if ( file_exists( $file_path ) ) {
			require_once $file_path;
		} else {
			// Log error only if WP_DEBUG is enabled.
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
				error_log( 'Zsoogi Clipper: Required file not found - ' . $file );
			}
		}
	}
}

/**
 * Initialize plugin classes.
 *
 * Only loads for logged-in administrators to restrict access.
 *
 * @since 0.9.0
 *
 * @return void
 */
function zsoogi_clipper_init() {
	// Initialize Zsoogi custom post type and taxonomy.
	// This runs for all users so the post type is registered.
	if ( class_exists( 'Zsoogi\Zsoogi_Clips' ) ) {
		\Zsoogi\Zsoogi_Clips::init();
	}

	// Initialize Admin Menu - has its own capability checks.
	if ( class_exists( 'Zsoogi\Admin_Menu' ) ) {
		\Zsoogi\Admin_Menu::init();
	}

	// Initialize PWA support for all users (manifest.json and sw.js must be publicly accessible).
	// Only runs when the PWA setting is enabled in the settings page.
	if ( class_exists( 'Zsoogi\PWA' ) && get_option( 'zsoogi_clipper_pwa_enabled', true ) ) {
		\Zsoogi\PWA::init();
	}

	// Only initialize Zsoogi Clipper for logged-in administrators.
	if ( ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
		return;
	}

	// Initialize Zsoogi Clipper (modern bookmarklet functionality).
	if ( class_exists( 'Zsoogi\Zsoogi_Clipper' ) ) {
		\Zsoogi\Zsoogi_Clipper::init();
	}
}

/**
 * Plugin activation hook.
 *
 * @since 0.9.0
 *
 * @return void
 */
function zsoogi_clipper_activate() {
	// Load includes to register post types.
	zsoogi_clipper_load_includes();

	// Register post type and taxonomy directly (not through init hook).
	if ( class_exists( 'Zsoogi\Zsoogi_Clips' ) ) {
		\Zsoogi\Zsoogi_Clips::register_post_type();
		\Zsoogi\Zsoogi_Clips::register_taxonomy();
	}

	// Register PWA rewrite rules so they are included in the flush below.
	if ( class_exists( 'Zsoogi\PWA' ) ) {
		\Zsoogi\PWA::register_endpoints();
	}

	// Flush rewrite rules after registration.
	flush_rewrite_rules();
}

/**
 * Plugin deactivation hook.
 *
 * @since 0.9.0
 *
 * @return void
 */
function zsoogi_clipper_deactivate() {
	// Flush rewrite rules.
	flush_rewrite_rules();
}

// Load Composer autoloader.
zsoogi_clipper_load_autoloader();

// Load required files.
zsoogi_clipper_load_includes();

// Initialize plugin on 'plugins_loaded' hook.
add_action( 'plugins_loaded', 'zsoogi_clipper_init' );

// Register activation and deactivation hooks.
register_activation_hook( ZSOOGI_CLIPPER_PLUGIN_FILE, 'zsoogi_clipper_activate' );
register_deactivation_hook( ZSOOGI_CLIPPER_PLUGIN_FILE, 'zsoogi_clipper_deactivate' );
