<?php
/**
 * Zsoogi Clipper Admin Menu
 *
 * Handles the admin menu and settings page for the Zsoogi Clipper plugin.
 *
 * @package Zsoogi_Clipper
 * @since 2.1.1
 */

namespace Zsoogi;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Zsoogi Clipper Admin Menu - Settings page administration.
 *
 * This class handles the admin menu and settings page for the Zsoogi Clipper plugin,
 * providing configuration options and plugin management interface.
 *
 * @package Zsoogi\Admin_Menu
 */
class Admin_Menu {

	/**
	 * Settings page slug.
	 *
	 * @var string
	 */
	const PAGE_SLUG = 'zsoogi-clipper-settings';

	/**
	 * Settings option group.
	 *
	 * @var string
	 */
	const OPTION_GROUP = 'zsoogi_clipper_settings';

	/**
	 * Bookmarklet installation page slug.
	 *
	 * @var string
	 */
	const BOOKMARKLET_SLUG = 'zsoogi-clipper-bookmarklet';

	/**
	 * Hook suffixes returned by add_submenu_page(), keyed by page slug.
	 *
	 * Used to scope asset enqueueing to this plugin's own screens.
	 *
	 * @var array
	 */
	private static $page_hooks = array();

	/**
	 * Initialize the Admin Menu functionality.
	 *
	 * Hooks into WordPress admin_menu action to add the settings page.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'add_admin_menu' ) );
		add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_admin_assets' ) );
	}

	/**
	 * Add admin menu page under Zsoogi Clipper.
	 *
	 * Creates a submenu page under the Zsoogi Clipper custom post type.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function add_admin_menu() {
		$plugin_version                             = ZSOOGI_CLIPS_VERSION;
		$label_name                                 = get_option( 'zsoogi_clips_label', 'Zsoogi Clips' );
		self::$page_hooks[ self::BOOKMARKLET_SLUG ] = add_submenu_page(
			'edit.php?post_type=' . Zsoogi_Clips::POST_TYPE,
			sprintf(
				/* translators: %s: Post type label name */
				__( '%s - Install Bookmarklet', 'zsoogi-clipper' ),
				$label_name
			),
			__( 'Grab Zsoogi', 'zsoogi-clipper' ),
			'manage_options',
			self::BOOKMARKLET_SLUG,
			array( __CLASS__, 'render_bookmarklet_page' )
		);
		self::$page_hooks[ self::PAGE_SLUG ] = add_submenu_page(
			'edit.php?post_type=' . Zsoogi_Clips::POST_TYPE,
			sprintf(
				/* translators: 1: Post type label name, 2: Plugin version */
				__( '%1$s Settings - v%2$s', 'zsoogi-clipper' ),
				$label_name,
				$plugin_version
			),
			__( 'Settings', 'zsoogi-clipper' ),
			'manage_options',
			self::PAGE_SLUG,
			array( __CLASS__, 'render_settings_page' )
		);
	}

	/**
	 * Register plugin settings.
	 *
	 * Registers settings, sections, and fields for the settings page.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function register_settings() {
		// Register General Settings section.
		add_settings_section(
			'zsoogi_clipper_general',
			__( 'General Settings', 'zsoogi-clipper' ),
			array( __CLASS__, 'render_general_section' ),
			self::PAGE_SLUG
		);

		// Custom label name.
		register_setting(
			self::OPTION_GROUP,
			'zsoogi_clips_label',
			array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'default'           => 'Zsoogi Clips',
			)
		);

		add_settings_field(
			'zsoogi_clips_label',
			__( 'Menu Label', 'zsoogi-clipper' ),
			array( __CLASS__, 'render_label_field' ),
			self::PAGE_SLUG,
			'zsoogi_clipper_general'
		);

		// Register Zsoogi Clipper settings section.
		add_settings_section(
			'zsoogi_clipper',
			__( 'Zsoogi Clipper Settings', 'zsoogi-clipper' ),
			array( __CLASS__, 'render_clipper_section' ),
			self::PAGE_SLUG
		);

		// Auto-set featured image.
		register_setting(
			self::OPTION_GROUP,
			'zsoogi_clipper_auto_featured_image',
			array(
				'type'              => 'boolean',
				'sanitize_callback' => array( __CLASS__, 'sanitize_checkbox' ),
				'default'           => true,
			)
		);

		add_settings_field(
			'zsoogi_clipper_auto_featured_image',
			__( 'Auto-Set Featured Image', 'zsoogi-clipper' ),
			array( __CLASS__, 'render_auto_featured_image_field' ),
			self::PAGE_SLUG,
			'zsoogi_clipper'
		);

		// Include metadata.
		register_setting(
			self::OPTION_GROUP,
			'zsoogi_clipper_include_metadata',
			array(
				'type'              => 'boolean',
				'sanitize_callback' => array( __CLASS__, 'sanitize_checkbox' ),
				'default'           => false,
			)
		);

		add_settings_field(
			'zsoogi_clipper_include_metadata',
			__( 'Include Page Metadata', 'zsoogi-clipper' ),
			array( __CLASS__, 'render_include_metadata_field' ),
			self::PAGE_SLUG,
			'zsoogi_clipper'
		);

		/**
		 * Allow add-on plugins to register their own settings sections.
		 *
		 * @param string $page_slug The settings page slug.
		 */
		do_action( 'zsoogi_clipper/settings_sections', self::PAGE_SLUG );
	}

	/**
	 * Sanitize checkbox input.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $value The input value to sanitize.
	 * @return bool Sanitized boolean value.
	 */
	public static function sanitize_checkbox( $value ) {
		return ! empty( $value ) ? true : false;
	}

	/**
	 * Render the settings page.
	 *
	 * Outputs the HTML for the settings page including form and fields.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function render_settings_page() {
		// Check user capabilities.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Add settings saved message.
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- WordPress Settings API handles nonce verification.
		if ( isset( $_GET['settings-updated'] ) ) {
			add_settings_error(
				'zsoogi_clipper_messages',
				'zsoogi_clipper_message',
				__( 'Settings saved successfully.', 'zsoogi-clipper' ),
				'updated'
			);
		}

		// Show error/update messages.
		settings_errors( 'zsoogi_clipper_messages' );
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

			<form action="options.php" method="post">
				<?php
				// Output security fields for the registered setting.
				settings_fields( self::OPTION_GROUP );

				// Output setting sections and their fields.
				do_settings_sections( self::PAGE_SLUG );

				// Output save settings button.
				submit_button( __( 'Save Settings', 'zsoogi-clipper' ) );
				?>
			</form>

			<hr>

			<div class="zsoogi-clipper-info">
				<h2><?php esc_html_e( 'Plugin Information', 'zsoogi-clipper' ); ?></h2>
				<p>
					<strong><?php esc_html_e( 'Version:', 'zsoogi-clipper' ); ?></strong>
					<?php echo esc_html( ZSOOGI_CLIPS_VERSION ); ?>
				</p>
				<p>
					<strong><?php esc_html_e( 'Post Type:', 'zsoogi-clipper' ); ?></strong>
					<code><?php echo esc_html( Zsoogi_Clips::POST_TYPE ); ?></code>
				</p>
				<p>
					<strong><?php esc_html_e( 'Taxonomy:', 'zsoogi-clipper' ); ?></strong>
					<code><?php echo esc_html( Zsoogi_Clips::TAXONOMY ); ?></code>
				</p>
			</div>
		</div>
		<?php
	}

	/**
	 * Render the General settings section description.
	 *
	 * @since 2.2.0
	 *
	 * @return void
	 */
	public static function render_general_section() {
		?>
		<p><?php esc_html_e( 'Configure the display name and general settings for your Zsoogi Clip post type.', 'zsoogi-clipper' ); ?></p>
		<?php
	}

	/**
	 * Render the label field.
	 *
	 * @since 2.2.0
	 *
	 * @return void
	 */
	public static function render_label_field() {
		$value = get_option( 'zsoogi_clips_label', 'Zsoogi Clips' );
		?>
		<input
			type="text"
			name="zsoogi_clips_label"
			value="<?php echo esc_attr( $value ); ?>"
			class="regular-text"
		/>
		<p class="description">
			<?php esc_html_e( 'This name will appear in the admin menu and post type labels (e.g., "Zsoogi Clips" becomes "Zsoogi Clipper").', 'zsoogi-clipper' ); ?>
		</p>
		<?php
	}

	/**
	 * Render the Zsoogi Clipper settings section description.
	 *
	 * @since 2.1.4
	 *
	 * @return void
	 */
	public static function render_clipper_section() {
		$bookmarklet_url = admin_url( 'edit.php?post_type=' . Zsoogi_Clips::POST_TYPE . '&page=zsoogi-clipper-bookmarklet' );
		?>
		<p><?php esc_html_e( 'Configure how the Zsoogi Clipper bookmarklet formats captured content.', 'zsoogi-clipper' ); ?></p>

		<div class="zsoogi-clipper-callout">
			<h4>📚 <?php esc_html_e( 'Install the Zsoogi Clipper Bookmarklet', 'zsoogi-clipper' ); ?></h4>
			<p><?php esc_html_e( 'The Zsoogi Clipper bookmarklet lets you capture content from any webpage directly into your Zsoogi Clips.', 'zsoogi-clipper' ); ?></p>

			<p class="zsoogi-clipper-callout-lead">
				<strong><?php esc_html_e( 'To install:', 'zsoogi-clipper' ); ?></strong>
			</p>
			<ol class="zsoogi-clipper-callout-steps">
				<li>
				Visit the <a href="<?php echo esc_url( $bookmarklet_url ); ?>">
						<?php esc_html_e( 'Grab Zsoogi bookmarklet installation page', 'zsoogi-clipper' ); ?>
					</a>
				</li>
				<li><?php esc_html_e( 'Drag the "Grab Zsoogi" button to your browser\'s bookmarks bar', 'zsoogi-clipper' ); ?></li>
				<li><?php esc_html_e( 'Click the bookmarklet while viewing any webpage to capture content', 'zsoogi-clipper' ); ?></li>
			</ol>
			<p class="zsoogi-clipper-callout-footnote">
				<strong><?php esc_html_e( 'What it captures:', 'zsoogi-clipper' ); ?></strong>
				<?php esc_html_e( 'Page URL, title, selected text, and images - all automatically formatted based on your settings below.', 'zsoogi-clipper' ); ?>
			</p>
		</div>
		<?php
	}

	/**
	 * Render the auto-set featured image field.
	 *
	 * @since 2.1.4
	 *
	 * @return void
	 */
	public static function render_auto_featured_image_field() {
		$value = get_option( 'zsoogi_clipper_auto_featured_image', true );
		?>
		<label>
			<input
				type="checkbox"
				name="zsoogi_clipper_auto_featured_image"
				value="1"
				<?php checked( 1, $value ); ?>
			/>
			<?php esc_html_e( 'Automatically set captured image as featured image', 'zsoogi-clipper' ); ?>
		</label>
		<p class="description">
			<?php esc_html_e( 'When enabled, the first image captured by the bookmarklet will be set as the post\'s featured image.', 'zsoogi-clipper' ); ?>
		</p>
		<?php
	}

	/**
	 * Render the include metadata field.
	 *
	 * @since 2.1.4
	 *
	 * @return void
	 */
	public static function render_include_metadata_field() {
		$value = get_option( 'zsoogi_clipper_include_metadata', false );
		?>
		<label>
			<input
				type="checkbox"
				name="zsoogi_clipper_include_metadata"
				value="1"
				<?php checked( 1, $value ); ?>
			/>
			<?php esc_html_e( 'Include capture date and metadata in post content', 'zsoogi-clipper' ); ?>
		</label>
		<p class="description">
			<?php esc_html_e( 'Adds capture date and time to help track when research was gathered.', 'zsoogi-clipper' ); ?>
		</p>
		<?php
	}

	/**
	 * Enqueue admin styles and scripts for this plugin's own screens.
	 *
	 * Replaces the inline <style> and <script> blocks that previously lived in
	 * render_bookmarklet_page(), per the WordPress.org plugin guidelines.
	 *
	 * @since 1.0.0
	 *
	 * @param string $hook_suffix Current admin page hook suffix.
	 * @return void
	 */
	public static function enqueue_admin_assets( $hook_suffix ) {
		$bookmarklet_hook = isset( self::$page_hooks[ self::BOOKMARKLET_SLUG ] ) ? self::$page_hooks[ self::BOOKMARKLET_SLUG ] : '';
		$settings_hook    = isset( self::$page_hooks[ self::PAGE_SLUG ] ) ? self::$page_hooks[ self::PAGE_SLUG ] : '';

		// Only load on this plugin's own admin screens.
		if ( $hook_suffix !== $bookmarklet_hook && $hook_suffix !== $settings_hook ) {
			return;
		}

		wp_enqueue_style(
			'zsoogi-clipper-admin',
			ZSOOGI_CLIPS_PLUGIN_URL . 'assets/css/admin.css',
			array(),
			ZSOOGI_CLIPS_VERSION
		);

		// The bookmarklet payload is only needed on the installation page.
		if ( $hook_suffix !== $bookmarklet_hook ) {
			return;
		}

		wp_enqueue_script(
			'zsoogi-clipper-bookmarklet',
			ZSOOGI_CLIPS_PLUGIN_URL . 'assets/js/admin-bookmarklet.js',
			array(),
			ZSOOGI_CLIPS_VERSION,
			true
		);

		// wp_add_inline_script() rather than wp_localize_script(): the latter runs
		// every string through html_entity_decode(), which is unsafe for a raw JS
		// payload. wp_json_encode() here matches the previous inline behaviour.
		wp_add_inline_script(
			'zsoogi-clipper-bookmarklet',
			'var zsoogiClipperBookmarklet = { code: '
				. wp_json_encode( self::get_bookmarklet_code( self::get_site_base_url(), ZSOOGI_CLIPS_VERSION ) )
				. ' };',
			'before'
		);
	}

	/**
	 * Get the site base URL used when building the bookmarklet payload.
	 *
	 * @since 1.0.0
	 *
	 * @return string Site base URL with no trailing admin path.
	 */
	private static function get_site_base_url() {
		$site_url = esc_url( admin_url( 'post-new.php' ) );

		return str_replace( '/wp-admin/post-new.php', '', $site_url );
	}

	/**
	 * Build the minified bookmarklet JavaScript payload.
	 *
	 * @since 1.0.0
	 *
	 * @param string $site_url       Site base URL.
	 * @param string $plugin_version Plugin version.
	 * @return string Minified bookmarklet code.
	 */
	private static function get_bookmarklet_code( $site_url, $plugin_version ) {
		// Read the bookmarklet JavaScript file.
		$js_file = ZSOOGI_CLIPS_PLUGIN_DIR . 'assets/js/bookmarklet.js';

		if ( ! file_exists( $js_file ) ) {
			return '';
		}

		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Reading local plugin file, not remote URL.
		$js_code = file_get_contents( $js_file );

		// Replace placeholders with actual values.
		$js_code = str_replace( '__VERSION__', $plugin_version, $js_code );
		$js_code = str_replace( '__SITE_URL__', $site_url, $js_code );

		// Remove comments and extra whitespace to minify.
		// NOTE: strip comment-only lines (^\s*//) NOT all occurrences of // — the latter
		// would destroy https:// and other URLs embedded in string literals.
		$js_code = preg_replace( '/\/\*[\s\S]*?\*\//', '', $js_code ); // Remove block comments.
		$js_code = preg_replace( '/^\s*\/\/.*$/m', '', $js_code );      // Remove comment-only lines.
		$js_code = preg_replace( '/\s+/', ' ', $js_code );              // Collapse whitespace.
		$js_code = preg_replace( '/\s*([{}();,:])\s*/', '$1', $js_code ); // Remove spaces around operators.
		$js_code = trim( $js_code );

		return $js_code;
	}

	/**
	 * Render the bookmarklet installation page.
	 *
	 * @since 2.2.3
	 *
	 * @return void
	 */
	public static function render_bookmarklet_page() {
		// Check user capabilities.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$label_name     = get_option( 'zsoogi_clips_label', 'Zsoogi Clips' );
		$site_url       = self::get_site_base_url();
		$plugin_version = ZSOOGI_CLIPS_VERSION;
		?>
		<div class="wrap">

			<div class="zsoogi-clipper-page">
				<h1>📚 <?php echo esc_html( $label_name ); ?> - v<?php echo esc_html( $plugin_version ); ?></h1>

				<p>
				<?php
				printf(
					/* translators: %s: Post type label name (e.g. "Zsoogi Clips") */
					esc_html__( 'A modern, jQuery-free bookmarklet for capturing web research into your %s, an admin-only post-type. Others trying to view will be redirected to the homepage, keeping your research private.', 'zsoogi-clipper' ),
					esc_html( $label_name )
				);
				?>
			</p>

				<h2><?php esc_html_e( 'Installation', 'zsoogi-clipper' ); ?></h2>
				<div class="zsoogi-clipper-instructions">
					<ol>
						<li><?php esc_html_e( 'Drag the button below to your bookmarks bar (or right-click and "Bookmark This Link")', 'zsoogi-clipper' ); ?></li>
						<li><?php esc_html_e( 'If your bookmarks bar isn\'t visible, press Ctrl+Shift+B (Windows) or Cmd+Shift+B (Mac)', 'zsoogi-clipper' ); ?></li>
					</ol>
				</div>

				<div class="zsoogi-clipper-link-wrap">
					<a id="zsoogi-clipper-bookmarklet-link" href="#" class="zsoogi-clipper-link">
						🔖 ZsoogiClips v<?php echo esc_html( $plugin_version ); ?>
					</a>
				</div>

				<h2><?php esc_html_e( 'How to Use', 'zsoogi-clipper' ); ?></h2>
				<div class="zsoogi-clipper-instructions">
					<ol>
						<li><strong><?php esc_html_e( 'On any webpage:', 'zsoogi-clipper' ); ?></strong> <?php esc_html_e( 'Select text you want to capture (optional)', 'zsoogi-clipper' ); ?></li>
						<li><strong><?php esc_html_e( 'Click the bookmarklet', 'zsoogi-clipper' ); ?></strong> <?php esc_html_e( 'in your bookmarks bar', 'zsoogi-clipper' ); ?></li>
						<li><strong><?php esc_html_e( 'A new window opens', 'zsoogi-clipper' ); ?></strong> <?php esc_html_e( 'with a new Zsoogi Clip pre-filled with:', 'zsoogi-clipper' ); ?>
							<ul>
								<li><?php esc_html_e( 'The page title', 'zsoogi-clipper' ); ?></li>
								<li><?php esc_html_e( 'Your selected text (as a blockquote)', 'zsoogi-clipper' ); ?></li>
								<li><?php esc_html_e( 'A link to the source', 'zsoogi-clipper' ); ?></li>
							</ul>
						</li>
						<li><strong><?php esc_html_e( 'Add your notes', 'zsoogi-clipper' ); ?></strong> <?php esc_html_e( 'and publish!', 'zsoogi-clipper' ); ?></li>
					</ol>
				</div>

				<h2><?php esc_html_e( 'Features', 'zsoogi-clipper' ); ?></h2>
				<ul>
					<li>✅ <?php esc_html_e( 'No jQuery required - pure vanilla JavaScript', 'zsoogi-clipper' ); ?></li>
					<li>✅ <?php esc_html_e( 'Captures page URL, title, selected text, and images', 'zsoogi-clipper' ); ?></li>
					<li>✅ <?php esc_html_e( 'Smart image detection (filters out icons, logos, avatars)', 'zsoogi-clipper' ); ?></li>
					<li>✅ <?php esc_html_e( 'Auto-set featured image (configurable in settings)', 'zsoogi-clipper' ); ?></li>
					<li>✅ <?php esc_html_e( 'Customizable blockquote and citation formatting', 'zsoogi-clipper' ); ?></li>
					<li>✅ <?php esc_html_e( 'YouTube transcript capture (when transcript panel is open)', 'zsoogi-clipper' ); ?></li>
					<li>✅ <?php esc_html_e( 'Works with the 2025 theme (or any theme)', 'zsoogi-clipper' ); ?></li>
					<li>✅ <?php esc_html_e( 'Creates posts as zsoogiclips post type', 'zsoogi-clipper' ); ?></li>
					<li>✅ <?php esc_html_e( 'Perfect for web research and note-taking', 'zsoogi-clipper' ); ?></li>
					<li>✅ <?php esc_html_e( 'Secure HTTPS connection', 'zsoogi-clipper' ); ?></li>
				</ul>

				<div class="zsoogi-clipper-note">
					<strong><?php esc_html_e( 'Note:', 'zsoogi-clipper' ); ?></strong>
					<?php
					printf(
						/* translators: %s: Site URL */
						esc_html__( 'The bookmarklet is automatically configured for your current site (%s). It will work on this domain in any environment (local, staging, production).', 'zsoogi-clipper' ),
						'<code>' . esc_html( $site_url ) . '</code>'
					);
					?>
				</div>

				<h2><?php esc_html_e( 'How It Works', 'zsoogi-clipper' ); ?></h2>
				<p><?php esc_html_e( 'The bookmarklet captures:', 'zsoogi-clipper' ); ?></p>
				<ul>
					<li><strong><?php esc_html_e( 'URL:', 'zsoogi-clipper' ); ?></strong> <?php esc_html_e( 'The current page URL', 'zsoogi-clipper' ); ?></li>
					<li><strong><?php esc_html_e( 'Title:', 'zsoogi-clipper' ); ?></strong> <?php esc_html_e( 'The page title (used as your Zsoogi Clip title)', 'zsoogi-clipper' ); ?></li>
					<li><strong><?php esc_html_e( 'Selection:', 'zsoogi-clipper' ); ?></strong> <?php esc_html_e( 'Any text you\'ve selected on the page', 'zsoogi-clipper' ); ?></li>
					<li><strong><?php esc_html_e( 'Image:', 'zsoogi-clipper' ); ?></strong> <?php esc_html_e( 'First meaningful image (>200px, excludes icons/logos)', 'zsoogi-clipper' ); ?></li>
					<li><strong><?php esc_html_e( 'YouTube Transcripts:', 'zsoogi-clipper' ); ?></strong> <?php esc_html_e( 'For YouTube videos, captures transcript text when the transcript panel is open (enable in settings)', 'zsoogi-clipper' ); ?></li>
				</ul>
				<p><?php esc_html_e( 'Then it opens a new window to create a Zsoogi Clip with these details. For large data (like transcripts), the window.name bridge is used to avoid URL length limits. The Zsoogi Clipper plugin processes everything with your custom formatting settings.', 'zsoogi-clipper' ); ?></p>

				<h2><?php esc_html_e( 'Troubleshooting', 'zsoogi-clipper' ); ?></h2>
				<h3><?php esc_html_e( 'Popup Blocked?', 'zsoogi-clipper' ); ?></h3>
				<p><?php esc_html_e( 'If the bookmarklet doesn\'t open a new window, your browser is blocking popups. Allow popups for the sites you want to clip from.', 'zsoogi-clipper' ); ?></p>

				<h3><?php esc_html_e( 'Content Not Pre-filled?', 'zsoogi-clipper' ); ?></h3>
				<p>
					<?php
					printf(
						/* translators: %s: Link to settings page */
						esc_html__( 'Make sure the Zsoogi Clipper is properly configured in your %s.', 'zsoogi-clipper' ),
						'<a href="' . esc_url( admin_url( 'edit.php?post_type=' . Zsoogi_Clips::POST_TYPE . '&page=' . self::PAGE_SLUG ) ) . '">' . esc_html__( 'settings', 'zsoogi-clipper' ) . '</a>'
					);
					?>
				</p>
			</div>
		</div>
		<?php
	}
}
