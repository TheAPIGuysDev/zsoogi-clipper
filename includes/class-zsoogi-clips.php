<?php

namespace Zsoogi;

/**
 * Zsoogi - Custom Post Type and Taxonomy Registration.
 *
 * This class handles the registration of the Zsoogi custom post type
 * and its associated taxonomy for organizing Zsoogi Clip content.
 *
 * @package Zsoogi\Zsoogi_Clips
 */
class Zsoogi_Clips {

	/**
	 * Post type slug.
	 *
	 * @var string
	 */
	const POST_TYPE = 'zsoogiclips';

	/**
	 * Taxonomy slug.
	 *
	 * @var string
	 */
	const TAXONOMY = 'zsoogi_type';

	/**
	 * Text domain for translations.
	 *
	 * @var string
	 */
	const TEXT_DOMAIN = 'zsoogi-clipper';

	/**
	 * Initialize the Zsoogi functionality.
	 *
	 * Hooks into WordPress init action to register the custom post type
	 * and taxonomy.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_post_type' ), 0 );
		add_action( 'init', array( __CLASS__, 'register_taxonomy' ), 0 );
		add_action( 'init', array( __CLASS__, 'register_builtin_taxonomies' ), 10 );
		add_action( 'init', array( __CLASS__, 'ensure_default_term' ), 20 );
		add_action( 'save_post_' . self::POST_TYPE, array( __CLASS__, 'set_default_term' ), 10, 2 );
		add_action( 'template_redirect', array( __CLASS__, 'restrict_frontend_access' ) );
		add_filter( 'template_include', array( __CLASS__, 'load_custom_template' ) );
	}

	/**
	 * Register the Zsoogi custom post type.
	 *
	 * Creates a custom post type for Zsoogi Clip articles with support for
	 * title, editor, thumbnail, comments, and revisions.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function register_post_type() {

		$labels = array(
			'name'                  => _x( 'Zsoogi Clips', 'Post Type General Name', self::TEXT_DOMAIN ),
			'singular_name'         => _x( 'Zsoogi Clip', 'Post Type Singular Name', self::TEXT_DOMAIN ),
			'menu_name'             => __( 'Zsoogi Clips', self::TEXT_DOMAIN ),
			'name_admin_bar'        => __( 'Zsoogi Clip', self::TEXT_DOMAIN ),
			'archives'              => __( 'Zsoogi Clip Archives', self::TEXT_DOMAIN ),
			'attributes'            => __( 'Zsoogi Clip Attributes', self::TEXT_DOMAIN ),
			'parent_item_colon'     => __( 'Parent Zsoogi Clip:', self::TEXT_DOMAIN ),
			'all_items'             => __( 'All Zsoogi Clips', self::TEXT_DOMAIN ),
			'add_new_item'          => __( 'Add New Zsoogi Clip', self::TEXT_DOMAIN ),
			'add_new'               => __( 'Add New Zsoogi Clip', self::TEXT_DOMAIN ),
			'new_item'              => __( 'New Zsoogi Clip', self::TEXT_DOMAIN ),
			'edit_item'             => __( 'Edit Zsoogi Clip', self::TEXT_DOMAIN ),
			'update_item'           => __( 'Update Zsoogi Clip', self::TEXT_DOMAIN ),
			'view_item'             => __( 'View Zsoogi Clip', self::TEXT_DOMAIN ),
			'view_items'            => __( 'View Zsoogi Clips', self::TEXT_DOMAIN ),
			'search_items'          => __( 'Search Zsoogi Clips', self::TEXT_DOMAIN ),
			'not_found'             => __( 'Zsoogi Clip not found', self::TEXT_DOMAIN ),
			'not_found_in_trash'    => __( 'Zsoogi Clip not found in Trash', self::TEXT_DOMAIN ),
			'featured_image'        => __( 'Featured Image', self::TEXT_DOMAIN ),
			'set_featured_image'    => __( 'Set featured image', self::TEXT_DOMAIN ),
			'remove_featured_image' => __( 'Remove featured image', self::TEXT_DOMAIN ),
			'use_featured_image'    => __( 'Use as featured image', self::TEXT_DOMAIN ),
			'insert_into_item'      => __( 'Insert into Zsoogi Clip', self::TEXT_DOMAIN ),
			'uploaded_to_this_item' => __( 'Uploaded to this Zsoogi Clip', self::TEXT_DOMAIN ),
			'items_list'            => __( 'Zsoogi Clips list', self::TEXT_DOMAIN ),
			'items_list_navigation' => __( 'Zsoogi Clips list navigation', self::TEXT_DOMAIN ),
			'filter_items_list'     => __( 'Filter Zsoogi clips list', self::TEXT_DOMAIN ),
		);

		$args = array(
			'label'               => __( 'Zsoogi Clips', self::TEXT_DOMAIN ),
			'description'         => __( 'Zsoogi Clips.', self::TEXT_DOMAIN ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor', 'thumbnail', 'author', 'comments', 'revisions' ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_icon'           => 'dashicons-rest-api',
			'menu_position'       => 5,
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'capability_type'     => 'page',
			'show_in_rest'        => true,
		);

		register_post_type( self::POST_TYPE, $args );
	}

	/**
	 * Register the ZsoogiType taxonomy.
	 *
	 * Creates a hierarchical taxonomy for categorizing Zsoogi Clip articles by type.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function register_taxonomy() {

		$labels = array(
			'name'                       => _x( 'Zsoogi Type', 'Taxonomy General Name', self::TEXT_DOMAIN ),
			'singular_name'              => _x( 'Zsoogi Type', 'Taxonomy Singular Name', self::TEXT_DOMAIN ),
			'menu_name'                  => __( 'Zsoogi Type', self::TEXT_DOMAIN ),
			'all_items'                  => __( 'All Types', self::TEXT_DOMAIN ),
			'parent_item'                => __( 'Parent Type', self::TEXT_DOMAIN ),
			'parent_item_colon'          => __( 'Parent Type:', self::TEXT_DOMAIN ),
			'new_item_name'              => __( 'New Type Name', self::TEXT_DOMAIN ),
			'add_new_item'               => __( 'Add New Type', self::TEXT_DOMAIN ),
			'edit_item'                  => __( 'Edit Type', self::TEXT_DOMAIN ),
			'update_item'                => __( 'Update Type', self::TEXT_DOMAIN ),
			'view_item'                  => __( 'View Type', self::TEXT_DOMAIN ),
			'separate_items_with_commas' => __( 'Separate types with commas', self::TEXT_DOMAIN ),
			'add_or_remove_items'        => __( 'Add or remove types', self::TEXT_DOMAIN ),
			'choose_from_most_used'      => __( 'Choose from the most used types', self::TEXT_DOMAIN ),
			'popular_items'              => __( 'Popular Types', self::TEXT_DOMAIN ),
			'search_items'               => __( 'Search Types', self::TEXT_DOMAIN ),
			'not_found'                  => __( 'Type Not Found', self::TEXT_DOMAIN ),
			'no_terms'                   => __( 'No items', self::TEXT_DOMAIN ),
			'items_list'                 => __( 'Types list', self::TEXT_DOMAIN ),
			'items_list_navigation'      => __( 'Types list navigation', self::TEXT_DOMAIN ),
		);

		$args = array(
			'labels'            => $labels,
			'hierarchical'      => true,
			'public'            => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_nav_menus' => true,
			'show_tagcloud'     => true,
			'show_in_rest'      => true,
		);

		register_taxonomy( self::TAXONOMY, array( self::POST_TYPE ), $args );
	}

	/**
	 * Register built-in taxonomies for the Zsoogi Clip post type.
	 *
	 * Adds support for categories and tags to Zsoogi Clips. This is done
	 * after post type registration to avoid race conditions on Multisite.
	 *
	 * @since 2.4.1
	 *
	 * @return void
	 */
	public static function register_builtin_taxonomies() {
		register_taxonomy_for_object_type( 'category', self::POST_TYPE );
		register_taxonomy_for_object_type( 'post_tag', self::POST_TYPE );
	}

	/**
	 * Restrict frontend access to Zsoogi Clips.
	 *
	 * Redirects non-administrator users to the homepage when attempting to
	 * view Zsoogi Clips, archives, or taxonomy pages on the frontend.
	 *
	 * @since 2.3.0
	 *
	 * @return void
	 */
	public static function restrict_frontend_access() {
		// Check if we're viewing a Zsoogi Clip post (single, archive, or taxonomy).
		if ( is_singular( self::POST_TYPE ) || is_post_type_archive( self::POST_TYPE ) || is_tax( self::TAXONOMY ) ) {
			// Allow access only for logged-in administrators.
			if ( ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
				// Redirect to homepage.
				wp_safe_redirect( home_url() );
				exit;
			}
		}
	}

	/**
	 * Load custom template for Zsoogi Clips.
	 *
	 * Provides a custom single post template for Zsoogi Clips that integrates
	 * with modern WordPress themes.
	 *
	 * @since 2.3.0
	 *
	 * @param string $template The path to the template file.
	 *
	 * @return string The modified template path.
	 */
	public static function load_custom_template( $template ) {
		if ( is_singular( self::POST_TYPE ) ) {
			$plugin_template = ZSOOGI_CLIPS_PLUGIN_DIR . 'templates/single-zsoogiclips.php';
			if ( file_exists( $plugin_template ) ) {
				return $plugin_template;
			}
		}
		return $template;
	}

	/**
	 * Ensure the default "Research" term exists.
	 *
	 * Creates the "Research" term in the Zsoogi type taxonomy if it doesn't exist.
	 * Runs on init with priority 20 to ensure taxonomy is registered first.
	 *
	 * @since 2.2.0
	 *
	 * @return void
	 */
	public static function ensure_default_term() {
		// Check if the term already exists.
		$term = term_exists( 'Research', self::TAXONOMY );

		// If term doesn't exist, create it.
		if ( ! $term ) {
			wp_insert_term(
				'Research',
				self::TAXONOMY,
				array(
					'description' => __( 'Research and documentation articles', self::TEXT_DOMAIN ),
					'slug'        => 'research',
				)
			);
		}
	}

	/**
	 * Set default term for new Zsoogi Clips.
	 *
	 * Automatically assigns the "Research" term to new Zsoogi Clips that don't
	 * have any terms set in the Zsoogi type taxonomy.
	 *
	 * @since 2.2.0
	 *
	 * @param int     $post_id The post ID.
	 * @param WP_Post $post    The post object.
	 * @return void
	 */
	public static function set_default_term( $post_id, $post ) {
		// Skip if this is an autosave or revision.
		if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
			return;
		}

		// Check if post already has terms.
		$terms = wp_get_object_terms( $post_id, self::TAXONOMY );

		// If no terms are set, assign the default "Research" term.
		if ( empty( $terms ) || is_wp_error( $terms ) ) {
			$research_term = get_term_by( 'slug', 'research', self::TAXONOMY );
			if ( $research_term ) {
				wp_set_object_terms( $post_id, $research_term->term_id, self::TAXONOMY );
			}
		}
	}
}
