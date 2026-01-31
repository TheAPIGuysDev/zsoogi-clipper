<?php

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
		// Inject window.name bridge handler for bookmarklet data.
		add_action( 'admin_head-post-new.php', array( __CLASS__, 'inject_window_name_handler' ) );

		// Only run on post-new.php for our custom post type.
		add_action( 'load-post-new.php', array( __CLASS__, 'process_bookmarklet' ) );

		// Pre-fill post title and content.
		add_filter( 'default_title', array( __CLASS__, 'default_title' ), 10, 2 );
		add_filter( 'default_content', array( __CLASS__, 'default_content' ), 10, 2 );

		// Set featured image after post is created.
		add_action( 'save_post_' . Zsoogi_Clips::POST_TYPE, array( __CLASS__, 'set_featured_image' ), 10, 3 );

		// Save YouTube transcript data.
		add_action( 'save_post_' . Zsoogi_Clips::POST_TYPE, array( __CLASS__, 'save_youtube_transcript' ), 10, 3 );
	}

	/**
	 * Inject JavaScript to handle window.name bridge data from bookmarklet.
	 *
	 * When the bookmarklet passes data via window.name (to avoid GET URL length limits),
	 * this JavaScript reads it and submits it as POST data for processing.
	 *
	 * @since 2.5.0
	 *
	 * @return void
	 */
	public static function inject_window_name_handler() {
		// Only inject for our post type.
		global $typenow;
		if ( Zsoogi_Clips::POST_TYPE !== $typenow ) {
			return;
		}

		// Only inject if clipper_data parameter is present.
		if ( ! isset( $_GET['clipper_data'] ) ) {
			return;
		}

		// Inject the window.name bridge handler.
		?>
		<script type="text/javascript">
		(function() {
			// Check if window.name contains JSON data from the bookmarklet
			if (window.name && window.name.startsWith('{')) {
				try {
					// Parse the JSON data
					const data = JSON.parse(window.name);

					// Clear window.name to prevent reprocessing on page reload
					window.name = '';

					// Create a form to submit the data as POST
					const form = document.createElement('form');
					form.method = 'POST';
					form.action = window.location.href.split('?')[0] + '?post_type=<?php echo esc_js( Zsoogi_Clips::POST_TYPE ); ?>';

					// Add each data field as a hidden input
					for (const key in data) {
						if (data.hasOwnProperty(key) && data[key]) {
							const input = document.createElement('input');
							input.type = 'hidden';
							input.name = key;
							input.value = data[key];
							form.appendChild(input);
						}
					}

					// Add the form to the page and submit
					document.body.appendChild(form);
					form.submit();
				} catch (e) {
					// If JSON parsing fails, just continue normally
					console.error('Zsoogi Clipper: Failed to parse bookmarklet data', e);
				}
			}
		})();
		</script>
		<?php
	}

	/**
	 * Process bookmarklet data on post-new.php load.
	 *
	 * Validates and sanitizes URL parameters from the Zsoogi Clipper bookmarklet.
	 * Accepts data from both GET (legacy) and POST (window.name bridge).
	 *
	 * Note: This processes GET/POST parameters without nonce verification because
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

		// Merge GET and POST data (POST takes precedence for duplicate keys).
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
		$data = array_merge( $_GET, $_POST );

		// Check if we have bookmarklet data.
		if ( ! isset( $data['url'] ) && ! isset( $data['title'] ) ) {
			return;
		}

		// Sanitize and store in globals for use in filters.
		if ( isset( $data['title'] ) ) {
			$GLOBALS['zsoogi_clipper_title'] = sanitize_text_field( wp_unslash( $data['title'] ) );
		}

		if ( isset( $data['url'] ) ) {
			$GLOBALS['zsoogi_clipper_url'] = esc_url_raw( wp_unslash( $data['url'] ) );
		}

		if ( isset( $data['selection'] ) && ! empty( $data['selection'] ) ) {
			$GLOBALS['zsoogi_clipper_selection'] = wp_kses_post( wp_unslash( $data['selection'] ) );
		}

		if ( isset( $data['image'] ) && ! empty( $data['image'] ) ) {
			$GLOBALS['zsoogi_clipper_image'] = esc_url_raw( wp_unslash( $data['image'] ) );
		}

		// Process YouTube video ID.
		if ( isset( $data['youtube_video_id'] ) && ! empty( $data['youtube_video_id'] ) ) {
			// Validate YouTube video ID format (11 characters, alphanumeric plus - and _).
			$video_id = sanitize_text_field( wp_unslash( $data['youtube_video_id'] ) );
			if ( preg_match( '/^[a-zA-Z0-9_-]{11}$/', $video_id ) ) {
				$GLOBALS['zsoogi_clipper_youtube_video_id'] = $video_id;
			}
		}

		// Process YouTube transcript.
		if ( isset( $data['youtube_transcript'] ) && ! empty( $data['youtube_transcript'] ) ) {
			$GLOBALS['zsoogi_clipper_youtube_transcript_text'] = sanitize_textarea_field( wp_unslash( $data['youtube_transcript'] ) );
		}
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
		$citation_format    = get_option( 'zsoogi_clipper_citation_format', 'detailed' );
		$include_metadata   = get_option( 'zsoogi_clipper_include_metadata', false );

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

		// Process YouTube transcript if available.
		$youtube_video_id = isset( $GLOBALS['zsoogi_clipper_youtube_video_id'] ) ? $GLOBALS['zsoogi_clipper_youtube_video_id'] : '';
		$youtube_transcript = isset( $GLOBALS['zsoogi_clipper_youtube_transcript_text'] ) ? $GLOBALS['zsoogi_clipper_youtube_transcript_text'] : '';

		if ( ! empty( $youtube_video_id ) ) {
			// Check if YouTube transcripts are enabled.
			$transcripts_enabled = get_option( 'zsoogi_clipper_youtube_transcripts_enabled', false );

			if ( $transcripts_enabled && ! empty( $youtube_transcript ) ) {
				// Get excerpt length setting.
				$excerpt_length = get_option( 'zsoogi_clipper_youtube_excerpt_length', 500 );
				$excerpt_length = max( 100, min( 5000, intval( $excerpt_length ) ) );

				// Create excerpt from transcript.
				$transcript_excerpt = mb_substr( $youtube_transcript, 0, $excerpt_length );
				if ( mb_strlen( $youtube_transcript ) > $excerpt_length ) {
					$transcript_excerpt .= '...';
				}

				// Add transcript section.
				$new_content .= "<!-- wp:heading -->\n";
				$new_content .= '<h2 class="wp-block-heading">Video Transcript</h2>';
				$new_content .= "\n<!-- /wp:heading -->\n\n";

				$new_content .= "<!-- wp:quote -->\n";
				$new_content .= '<blockquote class="wp-block-quote">';
				$new_content .= '<p>' . esc_html( $transcript_excerpt ) . '</p>';
				$new_content .= '</blockquote>';
				$new_content .= "\n<!-- /wp:quote -->\n\n";

				$new_content .= "<!-- wp:paragraph -->\n";
				$new_content .= '<p><em>Full transcript stored in post metadata.</em></p>';
				$new_content .= "\n<!-- /wp:paragraph -->\n\n";

				// Store full transcript in global for save hook.
				$GLOBALS['zsoogi_clipper_youtube_transcript_for_meta'] = $youtube_transcript;
			} elseif ( empty( $youtube_transcript ) ) {
				// Video ID exists but no transcript captured.
				$new_content .= "<!-- wp:paragraph -->\n";
				$new_content .= '<p><em>To capture transcripts, open the transcript panel on YouTube before clipping.</em></p>';
				$new_content .= "\n<!-- /wp:paragraph -->\n\n";
			}
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

	/**
	 * Save YouTube transcript data to post meta.
	 *
	 * Stores the full transcript and related metadata when a YouTube video
	 * is clipped with transcript data.
	 *
	 * @since 2.5.0
	 *
	 * @param int      $post_id Post ID.
	 * @param \WP_Post $post    Post object (unused but required by hook signature).
	 * @param bool     $update  Whether this is an update (unused but required by hook signature).
	 * @return void
	 */
	public static function save_youtube_transcript( $post_id, $post, $update ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
		// Note: $post and $update parameters are required by save_post hook but not used in this function.
		// Don't run on autosave.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Check if we have transcript data to save.
		if ( ! isset( $GLOBALS['zsoogi_clipper_youtube_transcript_for_meta'] ) ) {
			return;
		}

		$transcript = $GLOBALS['zsoogi_clipper_youtube_transcript_for_meta'];
		$video_id   = isset( $GLOBALS['zsoogi_clipper_youtube_video_id'] ) ? $GLOBALS['zsoogi_clipper_youtube_video_id'] : '';

		// Only save if we have both transcript and video ID.
		if ( empty( $transcript ) || empty( $video_id ) ) {
			return;
		}

		// Save YouTube data to post meta.
		update_post_meta( $post_id, '_youtube_video_id', sanitize_text_field( $video_id ) );
		update_post_meta( $post_id, '_youtube_transcript', sanitize_textarea_field( $transcript ) );
		update_post_meta( $post_id, '_youtube_transcript_language', get_option( 'zsoogi_clipper_youtube_language', 'en' ) );
		update_post_meta( $post_id, '_youtube_transcript_fetched', current_time( 'mysql' ) );
		update_post_meta( $post_id, '_youtube_transcript_source', 'bookmarklet' );

		// Clear the globals to prevent multiple saves.
		unset( $GLOBALS['zsoogi_clipper_youtube_transcript_for_meta'] );
		unset( $GLOBALS['zsoogi_clipper_youtube_video_id'] );
		unset( $GLOBALS['zsoogi_clipper_youtube_transcript_text'] );
	}
}
