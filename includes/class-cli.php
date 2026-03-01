<?php
/**
 * WP-CLI Commands
 *
 * @package Zsoogi_Clipper
 */

namespace Zsoogi;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * WP-CLI commands for Zsoogi Clipper.
 *
 * Usage: wp zsoogi <command>
 *
 * @package Zsoogi\CLI
 */
class CLI {

	/**
	 * Generate PWA static files (manifest.json and sw.js) at the WordPress root.
	 *
	 * Writes physical files so nginx can serve them directly without passing
	 * through PHP. Required on hosts (e.g. SiteGround) where the nginx layer
	 * intercepts requests before WordPress rewrite rules run.
	 *
	 * Run this command after every plugin deployment.
	 *
	 * ## EXAMPLES
	 *
	 *     wp zsoogi pwa-files
	 *
	 * @subcommand pwa-files
	 * @when after_wp_load
	 *
	 * @param string[] $args       Positional arguments (unused).
	 * @param string[] $assoc_args Associative arguments (unused).
	 * @return void
	 */
	public function pwa_files( $args, $assoc_args ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
		if ( ! class_exists( 'Zsoogi\PWA' ) ) {
			\WP_CLI::error( 'Zsoogi\PWA class not found — is the plugin active?' );
		}

		\WP_CLI::log( 'Writing PWA static files to ' . ABSPATH . '...' );

		$result = PWA::write_static_files();

		if ( is_wp_error( $result ) ) {
			\WP_CLI::error( $result->get_error_message() );
		}

		\WP_CLI::success( sprintf(
			'manifest.json and sw.js written to %s (plugin v%s)',
			ABSPATH,
			ZSOOGI_CLIPPER_VERSION
		) );
	}
}
