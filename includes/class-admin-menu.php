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
		$plugin_version = ZSOOGI_CLIPS_VERSION;
		$label_name     = get_option( 'zsoogi_clips_label', 'Zsoogi Clips' );
		add_submenu_page(
			'edit.php?post_type=' . Zsoogi_Clips::POST_TYPE,
			sprintf(
				/* translators: %s: Post type label name */
				__( '%s - Install Bookmarklet', 'zsoogi-clipper' ),
				$label_name
			),
			__( 'Grab Zsoogi', 'zsoogi-clipper' ),
			'manage_options',
			'zsoogi-clipper-bookmarklet',
			array( __CLASS__, 'render_bookmarklet_page' )
		);
		add_submenu_page(
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

		// Citation format.
		register_setting(
			self::OPTION_GROUP,
			'zsoogi_clipper_citation_format',
			array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'default'           => 'simple',
			)
		);

		add_settings_field(
			'zsoogi_clipper_citation_format',
			__( 'Citation Format', 'zsoogi-clipper' ),
			array( __CLASS__, 'render_citation_format_field' ),
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

			<div class="zsoogi-clipss-info">
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

		<div style="background: #f0f6fc; border: 1px solid #0c5460; border-left: 4px solid #2271b1; padding: 15px; margin: 15px 0;">
			<h4 style="margin-top: 0;">📚 <?php esc_html_e( 'Install the Zsoogi Clipper Bookmarklet', 'zsoogi-clipper' ); ?></h4>
			<p><?php esc_html_e( 'The Zsoogi Clipper bookmarklet lets you capture content from any webpage directly into your Zsoogi Clips.', 'zsoogi-clipper' ); ?></p>

			<p style="margin-bottom: 10px;">
				<strong><?php esc_html_e( 'To install:', 'zsoogi-clipper' ); ?></strong>
			</p>
			<ol style="margin-left: 20px;">
				<li>
				Visit the <a href="<?php echo esc_url( $bookmarklet_url ); ?>">
						<?php esc_html_e( 'Grab Zsoogi bookmarklet installation page', 'zsoogi-clipper' ); ?>
					</a>
				</li>
				<li><?php esc_html_e( 'Drag the "Grab Zsoogi" button to your browser\'s bookmarks bar', 'zsoogi-clipper' ); ?></li>
				<li><?php esc_html_e( 'Click the bookmarklet while viewing any webpage to capture content', 'zsoogi-clipper' ); ?></li>
			</ol>
			<p style="font-size: 0.9em; color: #666; margin-top: 10px;">
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
	 * Render the citation format field.
	 *
	 * @since 2.1.4
	 *
	 * @return void
	 */
	public static function render_citation_format_field() {
		$value = get_option( 'zsoogi_clipper_citation_format', 'simple' );
		?>
		<fieldset>
			<label>
				<input type="radio" name="zsoogi_clipper_citation_format" value="simple" <?php checked( 'simple', $value ); ?> />
				<strong><?php esc_html_e( 'Simple', 'zsoogi-clipper' ); ?></strong> -
				<?php esc_html_e( 'Source: [Title](URL)', 'zsoogi-clipper' ); ?>
			</label>
			<br />
			<label>
				<input type="radio" name="zsoogi_clipper_citation_format" value="detailed" <?php checked( 'detailed', $value ); ?> />
				<strong><?php esc_html_e( 'Detailed', 'zsoogi-clipper' ); ?></strong> -
				<?php esc_html_e( 'Source: [Title](URL) - Captured on [Date]', 'zsoogi-clipper' ); ?>
			</label>
			<br />
			<label>
				<input type="radio" name="zsoogi_clipper_citation_format" value="academic" <?php checked( 'academic', $value ); ?> />
				<strong><?php esc_html_e( 'Academic', 'zsoogi-clipper' ); ?></strong> -
				<?php esc_html_e( 'Title. URL. Accessed: [Date]', 'zsoogi-clipper' ); ?>
			</label>
		</fieldset>
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
	 * Generate the bookmarklet JavaScript code.
	 *
	 * Reads the bookmarklet.js file and replaces placeholders with actual values.
	 *
	 * @since 2.4.2
	 *
	 * @param string $site_url       The site URL.
	 * @param string $plugin_version The plugin version.
	 * @return string The minified bookmarklet code.
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
		$js_code = preg_replace( '/\/\*[\s\S]*?\*\//', '', $js_code ); // Remove multi-line comments.
		$js_code = preg_replace( '/\/\/.*$/m', '', $js_code ); // Remove single-line comments.
		$js_code = preg_replace( '/\s+/', ' ', $js_code ); // Collapse whitespace.
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
		$site_name      = get_option( 'blogname' );
		$site_url       = esc_url( admin_url( 'post-new.php' ) );
		$site_url       = str_replace( '/wp-admin/post-new.php', '', $site_url );
		$plugin_version = ZSOOGI_CLIPS_VERSION;

		// Generate the bookmarklet code.
		$bookmarklet_code = self::get_bookmarklet_code( $site_url, $plugin_version );
		?>
		<div class="wrap">
			<style>
				.bookmarklet-page {
					max-width: 800px;
				}
				.bookmarklet-link {
					display: inline-block;
					background: #2271b1;
					color: white;
					padding: 15px 30px;
					text-decoration: none;
					border-radius: 5px;
					font-size: 18px;
					font-weight: bold;
					margin: 20px 0;
				}
				.bookmarklet-link:hover {
					background: #135e96;
					color: white;
				}
				.bookmarklet-instructions {
					background: #f9f9f9;
					border-left: 4px solid #2271b1;
					padding: 15px;
					margin: 20px 0;
				}
				.bookmarklet-note {
					background: #fff3cd;
					border-left: 4px solid #ffc107;
					padding: 15px;
					margin: 20px 0;
				}
			</style>

			<div class="bookmarklet-page">
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
				<div class="bookmarklet-instructions">
					<ol>
						<li><?php esc_html_e( 'Drag the button below to your bookmarks bar (or right-click and "Bookmark This Link")', 'zsoogi-clipper' ); ?></li>
						<li><?php esc_html_e( 'If your bookmarks bar isn\'t visible, press Ctrl+Shift+B (Windows) or Cmd+Shift+B (Mac)', 'zsoogi-clipper' ); ?></li>
					</ol>
				</div>

				<div style="text-align: center; margin: 30px 0;">
					<a href="javascript:<?php echo esc_js( $bookmarklet_code ); ?>" class="bookmarklet-link">
						🔖 <?php echo esc_html( $site_name ); ?> ZsoogiClips
					</a>
				</div>

				<h2><?php esc_html_e( 'How to Use', 'zsoogi-clipper' ); ?></h2>
				<div class="bookmarklet-instructions">
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
					<li>✅ <?php esc_html_e( 'Works with the 2025 theme (or any theme)', 'zsoogi-clipper' ); ?></li>
					<li>✅ <?php esc_html_e( 'Creates posts as zsoogiclips post type', 'zsoogi-clipper' ); ?></li>
					<li>✅ <?php esc_html_e( 'Perfect for web research and note-taking', 'zsoogi-clipper' ); ?></li>
					<li>✅ <?php esc_html_e( 'Secure HTTPS connection', 'zsoogi-clipper' ); ?></li>
				</ul>

				<div class="bookmarklet-note">
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
				</ul>
				<p><?php esc_html_e( 'Then it opens a new window to create a Zsoogi Clip with these details passed as URL parameters. The Zsoogi Clipper plugin processes them with your custom formatting settings.', 'zsoogi-clipper' ); ?></p>

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
