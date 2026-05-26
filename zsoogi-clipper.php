<?php
/**
 * Plugin Name: Zsoogi Clipper
 * Plugin URI: https://theapiguys.com/zsoogi-clipper
 * Description: Create admin-only Zsoogi Clips with a modern jQuery-free bookmarklet for web research.
 * Version: 1.0.0
 * Author: The API Guys
 * Author URI: https://theapiguys.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: zsoogi-clipper
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.4
 *
 * @package Zsoogi_Clipper
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Conflict check: bail if the pro version is in the active plugins list.
// We check the option directly because pro hasn't loaded yet at this point
// (free loads first alphabetically), so its functions/constants aren't defined.
$zsoogi_active = (array) get_option( 'active_plugins', array() );
if ( in_array( 'zsoogi-clipper-pro/zsoogi-clipper-pro.php', $zsoogi_active, true ) ) {
	add_action(
		'admin_notices',
		function () {
			echo '<div class="notice notice-warning is-dismissible"><p>';
			echo wp_kses(
				__( '<strong>Zsoogi Clipper (free)</strong> is inactive because <strong>Zsoogi Clipper Pro</strong> is already active. You do not need both plugins.', 'zsoogi-clipper' ),
				array( 'strong' => array() )
			);
			echo '</p></div>';
		}
	);
	return;
}
unset( $zsoogi_active );

// Define plugin constants.
if ( ! defined( 'ZSOOGI_CLIPS_VERSION' ) ) {
	define( 'ZSOOGI_CLIPS_VERSION', '1.0.0' );
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
		'includes/class-zsoogi-clips.php',
		'includes/class-admin-menu.php',
		'includes/class-zsoogi-clipper.php',
	);

	foreach ( $includes as $file ) {
		$file_path = ZSOOGI_CLIPS_PLUGIN_DIR . $file;

		if ( file_exists( $file_path ) ) {
			require_once $file_path;
		} elseif ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			// Log error only if WP_DEBUG is enabled.
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			error_log( 'Zsoogi Clipper: Required file not found - ' . $file );
		}
	}
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

// Initialize plugin on 'plugins_loaded' hook.
add_action( 'plugins_loaded', 'zsoogi_clipper_init' );

// Register activation and deactivation hooks.
register_activation_hook( ZSOOGI_CLIPS_PLUGIN_FILE, 'zsoogi_clipper_activate' );
register_deactivation_hook( ZSOOGI_CLIPS_PLUGIN_FILE, 'zsoogi_clipper_deactivate' );
