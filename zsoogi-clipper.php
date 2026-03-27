<?php
/**
 * Plugin Name: Zsoogi Clipper
 * Plugin URI: https://github.com/TheAPIGuysDev/zsoogi-clipper
 * Description: Create admin-only Zsoogi Clips with a modern jQuery-free bookmarklet for web research.
 * Version: 2.5.0
 * Author: pbrocks
 * Author URI: https://github.com/pbrocks
 * License: GPL v3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.txt
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
if ( ! defined( 'ZSOOGI_CLIPS_VERSION' ) ) {
	define( 'ZSOOGI_CLIPS_VERSION', '2.5.0' );
}

if ( ! defined( 'ZSOOGI_CLIPS_PLUGIN_FILE' ) ) {
	define( 'ZSOOGI_CLIPS_PLUGIN_FILE', __FILE__ );
}

if ( ! defined( 'ZSOOGI_CLIPS_PLUGIN_DIR' ) ) {
	define( 'ZSOOGI_CLIPS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'ZSOOGI_CLIPS_PLUGIN_URL' ) ) {
	define( 'ZSOOGI_CLIPS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

/**
 * Load Composer autoloader if available.
 *
 * Note: Composer dependencies are optional and only needed for development
 * (PHP_CodeSniffer, WPCS). The plugin works fine in production without them.
 */
function zsoogi_clipper_load_autoloader() {
	$autoload_path = ZSOOGI_CLIPS_PLUGIN_DIR . 'vendor/autoload.php';

	if ( file_exists( $autoload_path ) ) {
		require_once $autoload_path;
		return true;
	}

	return false;
}

/**
 * Load required plugin files.
 *
 * @since 2.1.1
 *
 * @return void
 */
function zsoogi_clipper_load_includes() {
	$includes = array(
		'includes/class-license.php',
		'includes/class-zsoogi-clips.php',
		'includes/class-admin-menu.php',
		'includes/class-zsoogi-clipper.php',
		'includes/class-abilities.php',
	);

	foreach ( $includes as $file ) {
		$file_path = ZSOOGI_CLIPS_PLUGIN_DIR . $file;

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
 * Load plugin text domain for translations.
 *
 * @since 2.4.2
 *
 * @return void
 */
function zsoogi_clipper_load_textdomain() {
	load_plugin_textdomain(
		'zsoogi-clipper',
		false,
		dirname( plugin_basename( ZSOOGI_CLIPS_PLUGIN_FILE ) ) . '/languages'
	);
}

/**
 * Initialize plugin classes.
 *
 * Only loads for logged-in administrators to restrict access.
 *
 * @since 2.1.1
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

	// Only initialize Zsoogi Clipper for logged-in administrators.
	if ( ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
		return;
	}

	// Initialize Zsoogi Clipper (modern bookmarklet functionality).
	if ( class_exists( 'Zsoogi\Zsoogi_Clipper' ) ) {
		\Zsoogi\Zsoogi_Clipper::init();
	}

	// Initialize Abilities API integration (premium — WP 6.9+).
	if ( class_exists( 'Zsoogi\Abilities' ) ) {
		\Zsoogi\Abilities::init();
	}
}

/**
 * Plugin activation hook.
 *
 * @since 2.1.1
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

	// Flush rewrite rules after registration.
	flush_rewrite_rules();
}

/**
 * Plugin deactivation hook.
 *
 * @since 2.1.1
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

// Load text domain for translations.
add_action( 'plugins_loaded', 'zsoogi_clipper_load_textdomain' );

// Initialize plugin on 'plugins_loaded' hook.
add_action( 'plugins_loaded', 'zsoogi_clipper_init' );

// Register activation and deactivation hooks.
register_activation_hook( ZSOOGI_CLIPS_PLUGIN_FILE, 'zsoogi_clipper_activate' );
register_deactivation_hook( ZSOOGI_CLIPS_PLUGIN_FILE, 'zsoogi_clipper_deactivate' );
