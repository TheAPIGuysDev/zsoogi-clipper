<?php
/**
 * Zsoogi Clipper Handler
 *
 * Processes content captured from the Zsoogi Clipper bookmarklet.
 *
 * @package Zsoogi_Clipper
 * @since 2.1.4
 */

namespace Zsoogi;

/**
 * Zsoogi Clipper - Handles bookmarklet content capture.
 *
 * This class processes content captured from the Zsoogi Clipper bookmarklet,
 * pre-filling post titles and content with captured web research data.
 *
 * @package Zsoogi\Zsoogi_Clipper
 */
class Zsoogi_Clipper {

	/**
	 * Initialize the Zsoogi Clipper functionality.
	 *
	 * Sets up hooks to process bookmarklet data when creating new Zsoogi Clips.
	 *
	 * @since 2.1.4
	 *
	 * @return void
	 */
	public static function init() {
		// Only run on post-new.php for our custom post type.
		add_action( 'load-post-new.php', array( __CLASS__, 'process_bookmarklet' ) );

		// Pre-fill post title and content.
		add_filter( 'default_title', array( __CLASS__, 'default_title' ), 10, 2 );
		add_filter( 'default_content', array( __CLASS__, 'default_content' ), 10, 2 );

		// Set featured image after post is created.
		add_action( 'save_post_' . Zsoogi_Clips::POST_TYPE, array( __CLASS__, 'set_featured_image' ), 10, 3 );
	}

	/**
	 * Process bookmarklet data on post-new.php load.
	 *
	 * Validates and sanitizes URL parameters from the Zsoogi Clipper bookmarklet.
	 *
	 * Note: This processes GET parameters without nonce verification because
	 * bookmarklets are user-initiated actions from external sites where nonces
	 * cannot be reliably generated. Security is maintained through capability
	 * checks (manage_options) in the init function.
	 *
	 * @since 2.1.4
	 *
	 * @return void
	 */
	public static function process_bookmarklet() {
		// Verify user has administrator capabilities.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		global $typenow;

		// Only process for zsoogiclips post type.
		if ( Zsoogi_Clips::POST_TYPE !== $typenow ) {
			return;
		}

		// phpcs:disable WordPress.Security.NonceVerification.Recommended -- Bookmarklets cannot use nonces as they are user-initiated from external sites. Security maintained through capability checks.

		// Check if we have bookmarklet data.
		if ( ! isset( $_GET['url'] ) && ! isset( $_GET['title'] ) ) {
			return;
		}

		// Sanitize and store in globals for use in filters.
		if ( isset( $_GET['title'] ) ) {
			$GLOBALS['zsoogi_clipper_title'] = sanitize_text_field( wp_unslash( $_GET['title'] ) );
		}

		if ( isset( $_GET['url'] ) ) {
			$GLOBALS['zsoogi_clipper_url'] = esc_url_raw( wp_unslash( $_GET['url'] ) );
		}

		if ( isset( $_GET['selection'] ) && ! empty( $_GET['selection'] ) ) {
			$GLOBALS['zsoogi_clipper_selection'] = wp_kses_post( wp_unslash( $_GET['selection'] ) );
		}

		if ( isset( $_GET['image'] ) && ! empty( $_GET['image'] ) ) {
			$GLOBALS['zsoogi_clipper_image'] = esc_url_raw( wp_unslash( $_GET['image'] ) );
		}
		// phpcs:enable WordPress.Security.NonceVerification.Recommended
	}

	/**
	 * Set the default title for new posts from bookmarklet.
	 *
	 * @since 2.1.4
	 *
	 * @param string   $title    Default post title.
	 * @param \WP_Post $post     Post object.
	 * @return string Modified post title.
	 */
	public static function default_title( $title, $post ) {
		// Only for our post type.
		if ( Zsoogi_Clips::POST_TYPE !== $post->post_type ) {
			return $title;
		}

		// Return captured title if available.
		if ( isset( $GLOBALS['zsoogi_clipper_title'] ) ) {
			return $GLOBALS['zsoogi_clipper_title'];
		}

		return $title;
	}

	/**
	 * Set the default content for new posts from bookmarklet.
	 *
	 * Formats captured content with blockquote and citation.
	 *
	 * @since 2.1.4
	 *
	 * @param string   $content  Default post content.
	 * @param \WP_Post $post     Post object.
	 * @return string Modified post content.
	 */
	public static function default_content( $content, $post ) {
		// Only for our post type.
		if ( Zsoogi_Clips::POST_TYPE !== $post->post_type ) {
			return $content;
		}

		// Check if we have bookmarklet data.
		if ( ! isset( $GLOBALS['zsoogi_clipper_url'] ) ) {
			return $content;
		}

		$url       = $GLOBALS['zsoogi_clipper_url'];
		$title     = isset( $GLOBALS['zsoogi_clipper_title'] ) ? $GLOBALS['zsoogi_clipper_title'] : $url;
		$selection = isset( $GLOBALS['zsoogi_clipper_selection'] ) ? $GLOBALS['zsoogi_clipper_selection'] : '';

		// Get settings.
		$citation_format  = get_option( 'zsoogi_clipper_citation_format', 'detailed' );
		$include_metadata = get_option( 'zsoogi_clipper_include_metadata', false );

		// Build the content.
		$new_content = '';

		// Add selection as blockquote if available.
		if ( ! empty( $selection ) ) {
			$new_content .= "<!-- wp:quote -->\n";
			$new_content .= '<blockquote class="wp-block-quote">';
			$new_content .= '<p>' . wp_kses_post( $selection ) . '</p>';
			$new_content .= '</blockquote>';
			$new_content .= "\n<!-- /wp:quote -->\n\n";
		}

		// Add source citation based on format setting.
		$new_content .= "<!-- wp:paragraph -->\n";
		$new_content .= '<p>' . self::format_citation( $url, $title, $citation_format ) . '</p>';
		$new_content .= "\n<!-- /wp:paragraph -->\n\n";

		// Add metadata if enabled.
		if ( $include_metadata ) {
			$new_content .= "<!-- wp:paragraph -->\n";
			$new_content .= '<p><em>Captured on ' . esc_html( date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) ) ) . '</em></p>';
			$new_content .= "\n<!-- /wp:paragraph -->\n\n";
		}

		// Add notes section.
		$new_content .= "<!-- wp:heading -->\n";
		$new_content .= '<h2 class="wp-block-heading">Notes</h2>';
		$new_content .= "\n<!-- /wp:heading -->\n\n";

		$new_content .= "<!-- wp:paragraph -->\n";
		$new_content .= '<p></p>';
		$new_content .= "\n<!-- /wp:paragraph -->";

		return $new_content;
	}

	/**
	 * Format citation based on selected format.
	 *
	 * @since 2.1.4
	 *
	 * @param string $url    Source URL.
	 * @param string $title  Source title.
	 * @param string $format Citation format (simple|detailed|academic).
	 * @return string Formatted citation HTML.
	 */
	private static function format_citation( $url, $title, $format ) {
		$current_date = date_i18n( get_option( 'date_format' ) );

		switch ( $format ) {
			case 'detailed':
				return '<strong>Source:</strong> <a href="' . esc_url( $url ) . '" target="_blank" rel="noopener">' . esc_html( $title ) . '</a> - Captured on ' . esc_html( $current_date );

			case 'academic':
				return esc_html( $title ) . '. ' . esc_url( $url ) . '. Accessed: ' . esc_html( $current_date ) . '.';

			case 'simple':
			default:
				return '<strong>Source:</strong> <a href="' . esc_url( $url ) . '" target="_blank" rel="noopener">' . esc_html( $title ) . '</a>';
		}
	}

	/**
	 * Set featured image from captured image URL.
	 *
	 * Downloads the image and sets it as the post's featured image if enabled in settings.
	 *
	 * @since 2.1.4
	 *
	 * @param int      $post_id Post ID.
	 * @param \WP_Post $post    Post object (unused but required by hook signature).
	 * @param bool     $update  Whether this is an update (unused but required by hook signature).
	 * @return void
	 */
	public static function set_featured_image( $post_id, $post, $update ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
		// Note: $post and $update parameters are required by save_post hook but not used in this function.
		// Don't run on autosave or if already has featured image.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( has_post_thumbnail( $post_id ) ) {
			return;
		}

		// Check if feature is enabled.
		$auto_set_featured = get_option( 'zsoogi_clipper_auto_featured_image', true );
		if ( ! $auto_set_featured ) {
			return;
		}

		// Check if we have an image URL.
		if ( ! isset( $GLOBALS['zsoogi_clipper_image'] ) || empty( $GLOBALS['zsoogi_clipper_image'] ) ) {
			return;
		}

		$image_url = $GLOBALS['zsoogi_clipper_image'];

		// Download image and attach to post.
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$image_id = media_sideload_image( $image_url, $post_id, null, 'id' );

		if ( ! is_wp_error( $image_id ) ) {
			set_post_thumbnail( $post_id, $image_id );
			// Clear the global to prevent multiple attempts.
			unset( $GLOBALS['zsoogi_clipper_image'] );
		}
	}
}
