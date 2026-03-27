<?php
/**
 * Abilities - WordPress Abilities API integration for MCP
 *
 * Registers Zsoogi Clipper capabilities as WordPress Abilities so AI clients
 * (Claude Desktop, Claude Code, Cursor, VS Code via the WP MCP Adapter) can
 * discover and execute them via natural language.
 *
 * Requires WordPress 6.9+ (Abilities API) and the MCP Adapter plugin.
 * Gracefully does nothing on older WordPress versions.
 *
 * @package Zsoogi_Clipper
 * @since   2.5.0
 */

namespace Zsoogi;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Registers Zsoogi Clipper WordPress Abilities for AI / MCP integration.
 *
 * ## Registered Abilities
 *
 *   zsoogi/create-clip    — Create a clip from URL, title, and optional excerpt
 *   zsoogi/search-clips   — Search clips by keyword, domain, or tag
 *   zsoogi/get-transcript — Retrieve stored YouTube transcript for a clip (transcripts feature required)
 *   zsoogi/export-clips   — Export filtered clips to Markdown or JSON
 *
 * ## Usage
 *
 *   Ask your MCP-connected AI:
 *   "Create a clip from https://example.com with title 'Market Research Q2'"
 *   "Search my clips for anything about competitor pricing"
 *   "Export all clips tagged 'research' as Markdown"
 *
 * @package Zsoogi\Abilities
 * @since   2.5.0
 */
class Abilities {

	/**
	 * Hook into WordPress.
	 *
	 * @since 2.5.0
	 *
	 * @return void
	 */
	public static function init(): void {
		if ( ! License::has_feature( 'abilities' ) ) {
			return;
		}

		add_action( 'wp_abilities_api_init', array( __CLASS__, 'register_abilities' ) );
	}

	/**
	 * Register all Zsoogi Clipper abilities.
	 *
	 * Bails silently on WordPress versions that pre-date the Abilities API.
	 *
	 * @since 2.5.0
	 *
	 * @return void
	 */
	public static function register_abilities(): void {
		if ( ! function_exists( 'wp_register_ability' ) ) {
			return;
		}

		self::register_create_clip();
		self::register_search_clips();
		self::register_get_transcript();
		self::register_export_clips();
	}

	// ── Registration ─────────────────────────────────────────────────────────────

	/**
	 * Register the zsoogi/create-clip ability.
	 *
	 * @since 2.5.0
	 *
	 * @return void
	 */
	private static function register_create_clip(): void {
		wp_register_ability(
			'zsoogi/create-clip',
			array(
				'label'       => __( 'Create Clip', 'zsoogi-clipper' ),
				'description' => __( 'Create a new Zsoogi Clip from a URL, title, and optional excerpt.', 'zsoogi-clipper' ),
				'category'    => 'content',

				'input_schema' => array(
					'type'       => 'object',
					'properties' => array(
						'url'     => array(
							'type'        => 'string',
							'description' => 'The source URL to clip.',
						),
						'title'   => array(
							'type'        => 'string',
							'description' => 'Title for the clip.',
						),
						'excerpt' => array(
							'type'        => 'string',
							'description' => 'Optional selected text or excerpt from the source.',
						),
						'tags'    => array(
							'type'        => 'array',
							'items'       => array( 'type' => 'string' ),
							'description' => 'Optional zsoogi_type taxonomy term slugs to assign.',
						),
					),
					'required' => array( 'url', 'title' ),
				),

				'output_schema' => array(
					'type'       => 'object',
					'properties' => array(
						'post_id'  => array( 'type' => 'integer' ),
						'edit_url' => array( 'type' => 'string' ),
						'status'   => array( 'type' => 'string' ),
					),
				),

				'execute_callback'    => array( __CLASS__, 'execute_create_clip' ),
				'permission_callback' => static function () {
					return current_user_can( 'edit_posts' );
				},
				'meta' => array(
					'mcp' => array( 'public' => true ),
				),
			)
		);
	}

	/**
	 * Register the zsoogi/search-clips ability.
	 *
	 * @since 2.5.0
	 *
	 * @return void
	 */
	private static function register_search_clips(): void {
		wp_register_ability(
			'zsoogi/search-clips',
			array(
				'label'       => __( 'Search Clips', 'zsoogi-clipper' ),
				'description' => __( 'Search Zsoogi Clips by keyword, source domain, or tag.', 'zsoogi-clipper' ),
				'category'    => 'content',

				'input_schema' => array(
					'type'       => 'object',
					'properties' => array(
						'query'  => array(
							'type'        => 'string',
							'description' => 'Keyword to search clip titles and content.',
						),
						'domain' => array(
							'type'        => 'string',
							'description' => 'Filter by source domain, e.g. "youtube.com".',
						),
						'tag'    => array(
							'type'        => 'string',
							'description' => 'Filter by zsoogi_type taxonomy term slug.',
						),
						'limit'  => array(
							'type'        => 'integer',
							'description' => 'Maximum results to return. Default 10, max 50.',
						),
					),
				),

				'output_schema' => array(
					'type'  => 'array',
					'items' => array(
						'type'       => 'object',
						'properties' => array(
							'post_id' => array( 'type' => 'integer' ),
							'title'   => array( 'type' => 'string' ),
							'url'     => array( 'type' => 'string' ),
							'excerpt' => array( 'type' => 'string' ),
							'date'    => array( 'type' => 'string' ),
						),
					),
				),

				'execute_callback'    => array( __CLASS__, 'execute_search_clips' ),
				'permission_callback' => static function () {
					return current_user_can( 'edit_posts' );
				},
				'meta' => array(
					'mcp' => array( 'public' => true ),
				),
			)
		);
	}

	/**
	 * Register the zsoogi/get-transcript ability.
	 *
	 * Only registered when the transcripts feature is also active.
	 *
	 * @since 2.5.0
	 *
	 * @return void
	 */
	private static function register_get_transcript(): void {
		if ( ! License::has_feature( 'transcripts' ) ) {
			return;
		}

		wp_register_ability(
			'zsoogi/get-transcript',
			array(
				'label'       => __( 'Get YouTube Transcript', 'zsoogi-clipper' ),
				'description' => __( 'Retrieve the stored YouTube transcript for a Zsoogi Clip.', 'zsoogi-clipper' ),
				'category'    => 'content',

				'input_schema' => array(
					'type'       => 'object',
					'properties' => array(
						'post_id' => array(
							'type'        => 'integer',
							'description' => 'The clip post ID to retrieve the transcript from.',
						),
					),
					'required' => array( 'post_id' ),
				),

				'output_schema' => array(
					'type'       => 'object',
					'properties' => array(
						'post_id'    => array( 'type' => 'integer' ),
						'title'      => array( 'type' => 'string' ),
						'transcript' => array( 'type' => 'string' ),
						'language'   => array( 'type' => 'string' ),
					),
				),

				'execute_callback'    => array( __CLASS__, 'execute_get_transcript' ),
				'permission_callback' => static function () {
					return current_user_can( 'edit_posts' );
				},
				'meta' => array(
					'mcp' => array( 'public' => true ),
				),
			)
		);
	}

	/**
	 * Register the zsoogi/export-clips ability.
	 *
	 * @since 2.5.0
	 *
	 * @return void
	 */
	private static function register_export_clips(): void {
		wp_register_ability(
			'zsoogi/export-clips',
			array(
				'label'       => __( 'Export Clips', 'zsoogi-clipper' ),
				'description' => __( 'Export a filtered set of Zsoogi Clips to Markdown or JSON.', 'zsoogi-clipper' ),
				'category'    => 'content',

				'input_schema' => array(
					'type'       => 'object',
					'properties' => array(
						'format' => array(
							'type'        => 'string',
							'enum'        => array( 'markdown', 'json' ),
							'description' => 'Export format: "markdown" or "json".',
						),
						'tag'    => array(
							'type'        => 'string',
							'description' => 'Filter by zsoogi_type taxonomy term slug.',
						),
						'limit'  => array(
							'type'        => 'integer',
							'description' => 'Maximum clips to export. Default 20, max 100.',
						),
					),
					'required' => array( 'format' ),
				),

				'output_schema' => array(
					'type'       => 'object',
					'properties' => array(
						'format'  => array( 'type' => 'string' ),
						'count'   => array( 'type' => 'integer' ),
						'content' => array( 'type' => 'string' ),
					),
				),

				'execute_callback'    => array( __CLASS__, 'execute_export_clips' ),
				'permission_callback' => static function () {
					return current_user_can( 'edit_posts' );
				},
				'meta' => array(
					'mcp' => array( 'public' => true ),
				),
			)
		);
	}

	// ── Execute callbacks ─────────────────────────────────────────────────────────

	/**
	 * Execute zsoogi/create-clip.
	 *
	 * @since 2.5.0
	 *
	 * @param array $args Validated input matching input_schema.
	 * @return array|\WP_Error
	 */
	public static function execute_create_clip( array $args ) {
		$url     = esc_url_raw( $args['url'] );
		$title   = sanitize_text_field( $args['title'] );
		$excerpt = isset( $args['excerpt'] ) ? sanitize_textarea_field( $args['excerpt'] ) : '';
		$tags    = isset( $args['tags'] ) ? array_map( 'sanitize_text_field', (array) $args['tags'] ) : array();

		$post_id = wp_insert_post(
			array(
				'post_type'    => 'zsoogiclips',
				'post_title'   => $title,
				'post_content' => $excerpt,
				'post_status'  => 'draft',
				'meta_input'   => array(
					'_source_url' => $url,
				),
			),
			true
		);

		if ( is_wp_error( $post_id ) ) {
			return $post_id;
		}

		if ( ! empty( $tags ) ) {
			wp_set_object_terms( $post_id, $tags, 'zsoogi_type' );
		}

		return array(
			'post_id'  => $post_id,
			'edit_url' => get_edit_post_link( $post_id, 'raw' ),
			'status'   => 'draft',
		);
	}

	/**
	 * Execute zsoogi/search-clips.
	 *
	 * @since 2.5.0
	 *
	 * @param array $args Validated input matching input_schema.
	 * @return array
	 */
	public static function execute_search_clips( array $args ): array {
		$limit = min( 50, max( 1, (int) ( $args['limit'] ?? 10 ) ) );

		$query_args = array(
			'post_type'      => 'zsoogiclips',
			'post_status'    => array( 'publish', 'draft', 'private' ),
			'posts_per_page' => $limit,
		);

		if ( ! empty( $args['query'] ) ) {
			$query_args['s'] = sanitize_text_field( $args['query'] );
		}

		if ( ! empty( $args['tag'] ) ) {
			$query_args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				array(
					'taxonomy' => 'zsoogi_type',
					'field'    => 'slug',
					'terms'    => sanitize_key( $args['tag'] ),
				),
			);
		}

		if ( ! empty( $args['domain'] ) ) {
			$query_args['meta_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				array(
					'key'     => '_source_url',
					'value'   => sanitize_text_field( $args['domain'] ),
					'compare' => 'LIKE',
				),
			);
		}

		$posts   = get_posts( $query_args );
		$results = array();

		foreach ( $posts as $post ) {
			$results[] = array(
				'post_id' => $post->ID,
				'title'   => $post->post_title,
				'url'     => get_post_meta( $post->ID, '_source_url', true ),
				'excerpt' => wp_trim_words( $post->post_content, 30 ),
				'date'    => $post->post_date,
			);
		}

		return $results;
	}

	/**
	 * Execute zsoogi/get-transcript.
	 *
	 * @since 2.5.0
	 *
	 * @param array $args Validated input matching input_schema.
	 * @return array|\WP_Error
	 */
	public static function execute_get_transcript( array $args ) {
		$post_id = absint( $args['post_id'] );
		$post    = get_post( $post_id );

		if ( ! $post || 'zsoogiclips' !== $post->post_type ) {
			return new \WP_Error(
				'not_found',
				__( 'Clip not found.', 'zsoogi-clipper' )
			);
		}

		$transcript = get_post_meta( $post_id, '_youtube_transcript', true );

		if ( empty( $transcript ) ) {
			return new \WP_Error(
				'no_transcript',
				__( 'No transcript stored for this clip.', 'zsoogi-clipper' )
			);
		}

		return array(
			'post_id'    => $post_id,
			'title'      => $post->post_title,
			'transcript' => $transcript,
			'language'   => get_post_meta( $post_id, '_youtube_transcript_language', true ) ?: 'en',
		);
	}

	/**
	 * Execute zsoogi/export-clips.
	 *
	 * @since 2.5.0
	 *
	 * @param array $args Validated input matching input_schema.
	 * @return array
	 */
	public static function execute_export_clips( array $args ): array {
		$format = in_array( $args['format'], array( 'markdown', 'json' ), true ) ? $args['format'] : 'markdown';
		$limit  = min( 100, max( 1, (int) ( $args['limit'] ?? 20 ) ) );

		$query_args = array(
			'post_type'      => 'zsoogiclips',
			'post_status'    => array( 'publish', 'draft', 'private' ),
			'posts_per_page' => $limit,
			'orderby'        => 'date',
			'order'          => 'DESC',
		);

		if ( ! empty( $args['tag'] ) ) {
			$query_args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				array(
					'taxonomy' => 'zsoogi_type',
					'field'    => 'slug',
					'terms'    => sanitize_key( $args['tag'] ),
				),
			);
		}

		$posts = get_posts( $query_args );

		if ( 'json' === $format ) {
			$data = array();
			foreach ( $posts as $post ) {
				$data[] = array(
					'id'      => $post->ID,
					'title'   => $post->post_title,
					'url'     => get_post_meta( $post->ID, '_source_url', true ),
					'content' => $post->post_content,
					'date'    => $post->post_date,
				);
			}
			$content = wp_json_encode( $data, JSON_PRETTY_PRINT );
		} else {
			$lines = array();
			foreach ( $posts as $post ) {
				$url     = get_post_meta( $post->ID, '_source_url', true );
				$lines[] = '## ' . $post->post_title;
				if ( $url ) {
					$lines[] = '**Source:** ' . $url . '  ';
				}
				$lines[] = '';
				if ( $post->post_content ) {
					$lines[] = wp_strip_all_tags( $post->post_content );
					$lines[] = '';
				}
				$lines[] = '---';
				$lines[] = '';
			}
			$content = implode( "\n", $lines );
		}

		return array(
			'format'  => $format,
			'count'   => count( $posts ),
			'content' => $content,
		);
	}
}
