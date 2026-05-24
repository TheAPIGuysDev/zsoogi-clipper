<?php
/**
 * Uninstall Zsoogi Clipper
 *
 * Runs when the plugin is deleted via WordPress admin.
 * Removes all plugin data from the database.
 *
 * @package Zsoogi_Clipper
 * @since 0.9.0
 */

// Exit if accessed directly or not called by WordPress.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

/**
 * Delete plugin options.
 *
 * @since 0.9.0
 *
 * @return void
 */
function zsoogi_clipper_delete_options() {
	delete_option( 'zsoogi_clipper_auto_featured_image' );
	delete_option( 'zsoogi_clipper_include_metadata' );
}

/**
 * Delete plugin data for multisite.
 *
 * @since 0.9.0
 *
 * @return void
 */
function zsoogi_clipper_multisite_uninstall() {
	global $wpdb;

	if ( is_multisite() ) {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Direct query acceptable for uninstall.
		$blog_ids = $wpdb->get_col( "SELECT blog_id FROM {$wpdb->blogs}" );

		foreach ( $blog_ids as $blog_id ) {
			switch_to_blog( $blog_id );
			zsoogi_clipper_delete_options();
			restore_current_blog();
		}
	} else {
		zsoogi_clipper_delete_options();
	}
}

// Run uninstall.
zsoogi_clipper_multisite_uninstall();
