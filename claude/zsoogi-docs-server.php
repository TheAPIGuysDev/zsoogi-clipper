<?php
/**
 * Plugin Name: Zsoogi Clipper Documentation Server
 * Plugin URI: https://theapiguys.com/zsoogi-clipper
 * Description: Serves MkDocs documentation from /zsoogi-clipper/ URL path
 * Version: 1.0.0
 * Author: The API Guys
 * Author URI: https://theapiguys.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: zsoogi-docs-server
 *
 * @package Zsoogi_Docs_Server
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main plugin class.
 */
class Zsoogi_Docs_Server {

	/**
	 * Documentation directory name (relative to WP_CONTENT_DIR).
	 *
	 * @var string
	 */
	const DOCS_DIR_NAME = 'zsoogi-clipper-docs';

	/**
	 * URL base for documentation.
	 *
	 * @var string
	 */
	const DOCS_URL_BASE = 'documentation';

	/**
	 * Enable debug mode (shows helpful info when rules don't work).
	 *
	 * @var bool
	 */
	const DEBUG_MODE = true;

	/**
	 * Initialize the plugin.
	 */
	public static function init() {
		// Register shortcode.
		add_shortcode( 'zsoogi_docs', array( __CLASS__, 'docs_shortcode' ) );

		// Override template for pages with shortcode.
		add_filter( 'template_include', array( __CLASS__, 'use_blank_template' ) );

		// Activation hook.
		register_activation_hook( __FILE__, array( __CLASS__, 'activate' ) );
		register_deactivation_hook( __FILE__, array( __CLASS__, 'deactivate' ) );
	}

	/**
	 * Use blank template for pages with docs shortcode.
	 *
	 * @param string $template The current template path.
	 * @return string Modified template path.
	 */
	public static function use_blank_template( $template ) {
		global $post;

		// Only for pages.
		if ( ! is_page() || ! $post ) {
			return $template;
		}

		// Check if page content has the shortcode.
		if ( has_shortcode( $post->post_content, 'zsoogi_docs' ) ) {
			// Return our blank template from the plugin.
			$blank_template = dirname( __FILE__ ) . '/template-blank-docs.php';
			if ( file_exists( $blank_template ) ) {
				return $blank_template;
			}
		}

		return $template;
	}

	/**
	 * Shortcode to display documentation.
	 *
	 * Usage: [zsoogi_docs page="index.html"]
	 * Or just: [zsoogi_docs] for the homepage
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string HTML output.
	 */
	public static function docs_shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'page' => 'index.html',
				'height' => '100vh',
			),
			$atts
		);

		// Get the docs directory URL.
		$docs_url = plugins_url( 'docs-built/' . $atts['page'], __FILE__ );

		// Return iframe with the docs.
		$output = sprintf(
			'<iframe src="%s" width="100%%" height="%s" frameborder="0" style="border: none; display: block;"></iframe>',
			esc_url( $docs_url ),
			esc_attr( $atts['height'] )
		);

		return $output;
	}

	/**
	 * Get the documentation directory path.
	 *
	 * Tries multiple locations to find the docs directory.
	 *
	 * @return string|false Full path to docs directory, or false if not found.
	 */
	private static function get_docs_directory() {
		// Allow custom path via filter.
		$custom_path = apply_filters( 'zsoogi_docs_directory', false );
		if ( $custom_path && file_exists( $custom_path ) ) {
			return trailingslashit( $custom_path );
		}

		// Try multiple possible locations.
		$possible_paths = array(
			// 1. Plugin directory (easiest - no separate upload needed)
			dirname( __FILE__ ) . '/docs-built',
			// 2. wp-content/uploads (WordPress uploads directory)
			WP_CONTENT_DIR . '/uploads/' . self::DOCS_DIR_NAME,
			// 3. wp-content directory (common location)
			WP_CONTENT_DIR . '/' . self::DOCS_DIR_NAME,
			// 4. wp-content/plugins (alternative location)
			WP_CONTENT_DIR . '/plugins/' . self::DOCS_DIR_NAME,
			// 5. Relative to ABSPATH (fallback for standard installs)
			ABSPATH . 'wp-content/' . self::DOCS_DIR_NAME,
		);

		// Try each path and return the first that exists.
		foreach ( $possible_paths as $path ) {
			if ( file_exists( $path ) ) {
				return trailingslashit( $path );
			}
		}

		// If none found, return the default (first option).
		return trailingslashit( WP_CONTENT_DIR . '/' . self::DOCS_DIR_NAME );
	}

	/**
	 * Add custom rewrite rules.
	 */
	public static function add_rewrite_rules() {
		// Match /zsoogi-clipper or /zsoogi-clipper/anything
		// For multisite, we need to be more specific about the pattern
		add_rewrite_rule(
			'^' . self::DOCS_URL_BASE . '(/.*)?$',
			'index.php?zsoogi_docs_page=$matches[1]',
			'top'
		);

		// Additional rule for root path
		add_rewrite_rule(
			'^' . self::DOCS_URL_BASE . '$',
			'index.php?zsoogi_docs_page=/',
			'top'
		);
	}

	/**
	 * Add custom query vars.
	 *
	 * @param array $vars Existing query vars.
	 * @return array Modified query vars.
	 */
	public static function add_query_vars( $vars ) {
		$vars[] = 'zsoogi_docs_page';
		return $vars;
	}

	/**
	 * Serve documentation files.
	 */
	public static function serve_documentation() {
		// Check if this is a docs request
		$page = get_query_var( 'zsoogi_docs_page', null );

		// Debug logging (if debug mode enabled).
		if ( self::DEBUG_MODE && $page !== null ) {
			error_log( '[Zsoogi Docs] Request URI: ' . $_SERVER['REQUEST_URI'] );
			error_log( '[Zsoogi Docs] Query var page: ' . var_export( $page, true ) );
		}

		// Only process if this is a docs request
		// Note: empty string is valid (means root page)
		if ( $page === null || $page === false ) {
			return;
		}

		// Get the documentation directory.
		$docs_dir = self::get_docs_directory();

		// Default to index.html if no page specified.
		if ( empty( $page ) || '/' === $page ) {
			$page = 'index.html';
		}

		// Build full file path.
		$file_path = $docs_dir . $page;

		// Debug logging.
		if ( self::DEBUG_MODE ) {
			error_log( '[Zsoogi Docs] Docs dir: ' . $docs_dir );
			error_log( '[Zsoogi Docs] File path: ' . $file_path );
			error_log( '[Zsoogi Docs] File exists: ' . ( file_exists( $file_path ) ? 'YES' : 'NO' ) );
		}

		// Security: Get real path and verify it's within docs directory.
		$real_path = realpath( $file_path );
		$real_docs_dir = realpath( $docs_dir );

		// If path traversal detected or file doesn't exist.
		if ( ! $real_path || strpos( $real_path, $real_docs_dir ) !== 0 ) {
			// Try with /index.html appended (for directory URLs).
			$file_path_with_index = rtrim( $file_path, '/' ) . '/index.html';
			$real_path = realpath( $file_path_with_index );

			if ( ! $real_path || strpos( $real_path, $real_docs_dir ) !== 0 ) {
				status_header( 404 );
				get_template_part( 404 );
				exit;
			}

			$file_path = $file_path_with_index;
		}

		// Serve the file.
		self::serve_file( $file_path );
	}

	/**
	 * Serve a static file with appropriate headers.
	 *
	 * @param string $file_path Full path to the file.
	 */
	private static function serve_file( $file_path ) {
		// Get file extension.
		$extension = strtolower( pathinfo( $file_path, PATHINFO_EXTENSION ) );

		// Set appropriate content type.
		$mime_types = array(
			'html' => 'text/html',
			'htm'  => 'text/html',
			'css'  => 'text/css',
			'js'   => 'application/javascript',
			'json' => 'application/json',
			'xml'  => 'application/xml',
			'png'  => 'image/png',
			'jpg'  => 'image/jpeg',
			'jpeg' => 'image/jpeg',
			'gif'  => 'image/gif',
			'svg'  => 'image/svg+xml',
			'ico'  => 'image/x-icon',
			'woff' => 'font/woff',
			'woff2' => 'font/woff2',
			'ttf'  => 'font/ttf',
			'eot'  => 'application/vnd.ms-fontobject',
		);

		$content_type = isset( $mime_types[ $extension ] ) ? $mime_types[ $extension ] : 'application/octet-stream';

		// Set headers.
		header( 'Content-Type: ' . $content_type );

		// Cache control for static assets.
		if ( in_array( $extension, array( 'css', 'js', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'woff', 'woff2', 'ttf' ), true ) ) {
			// Cache for 30 days.
			header( 'Cache-Control: public, max-age=2592000' );
			header( 'Expires: ' . gmdate( 'D, d M Y H:i:s', time() + 2592000 ) . ' GMT' );
		} else {
			// Cache HTML for 1 hour.
			header( 'Cache-Control: public, max-age=3600' );
		}

		// Output file.
		readfile( $file_path );
		exit;
	}

	/**
	 * Plugin activation.
	 */
	public static function activate() {
		// For multisite, we might need to activate on each site
		if ( is_multisite() ) {
			// Get current blog ID
			$current_blog = get_current_blog_id();

			// If network activating, activate on all sites
			if ( isset( $_GET['networkwide'] ) && ( $_GET['networkwide'] == 1 ) ) {
				$blog_ids = get_sites( array( 'fields' => 'ids' ) );
				foreach ( $blog_ids as $blog_id ) {
					switch_to_blog( $blog_id );
					self::activate_single_site();
					restore_current_blog();
				}
			} else {
				self::activate_single_site();
			}
		} else {
			self::activate_single_site();
		}
	}

	/**
	 * Activate on a single site.
	 */
	private static function activate_single_site() {
		// Add rewrite rules.
		self::add_rewrite_rules();

		// Flush rewrite rules.
		flush_rewrite_rules();

		// Check if docs directory exists.
		$docs_dir = self::get_docs_directory();
		if ( ! file_exists( $docs_dir ) ) {
			// Show admin notice.
			set_transient( 'zsoogi_docs_missing_notice', true, 300 );
		}

		// Save activation time for debugging.
		update_option( 'zsoogi_docs_activated', time() );
	}

	/**
	 * Plugin deactivation.
	 */
	public static function deactivate() {
		// Flush rewrite rules to remove our custom rules.
		flush_rewrite_rules();
	}

	/**
	 * Show admin notice if docs directory is missing.
	 */
	public static function admin_notices() {
		if ( get_transient( 'zsoogi_docs_missing_notice' ) ) {
			$docs_dir = self::get_docs_directory();
			?>
			<div class="notice notice-warning is-dismissible">
				<p>
					<strong>Zsoogi Docs Server:</strong>
					Documentation directory not found at <code><?php echo esc_html( $docs_dir ); ?></code>
				</p>
				<p>
					Please upload your MkDocs site to this directory.
					Build with <code>mkdocs build</code> and copy contents of <code>./site/</code> to this location.
				</p>
			</div>
			<?php
			delete_transient( 'zsoogi_docs_missing_notice' );
		}

		// Debug mode notice.
		if ( self::DEBUG_MODE && current_user_can( 'manage_options' ) ) {
			$screen = get_current_screen();
			if ( $screen && $screen->id === 'plugins' ) {
				$docs_url = home_url( '/' . self::DOCS_URL_BASE . '/' );
				$docs_dir = self::get_docs_directory();
				$docs_exist = file_exists( $docs_dir );

				// Get all possible paths for debugging.
				$possible_paths = array(
					'WP_CONTENT_DIR/uploads' => WP_CONTENT_DIR . '/uploads/' . self::DOCS_DIR_NAME,
					'WP_CONTENT_DIR' => WP_CONTENT_DIR . '/' . self::DOCS_DIR_NAME,
					'WP_CONTENT_DIR/plugins' => WP_CONTENT_DIR . '/plugins/' . self::DOCS_DIR_NAME,
					'ABSPATH/wp-content' => ABSPATH . 'wp-content/' . self::DOCS_DIR_NAME,
				);
				?>
				<div class="notice notice-info">
					<p><strong>Zsoogi Docs Server - Debug Info:</strong></p>
					<ul style="list-style: disc; margin-left: 20px;">
						<li>Docs URL: <code><?php echo esc_html( $docs_url ); ?></code></li>
						<li>Active Docs Path: <code><?php echo esc_html( $docs_dir ); ?></code></li>
						<li>Directory Exists: <?php echo $docs_exist ? '✅ Yes' : '❌ No'; ?></li>
						<?php if ( $docs_exist ) : ?>
							<li>Index File: <?php echo file_exists( $docs_dir . 'index.html' ) ? '✅ Found' : '❌ Missing'; ?></li>
						<?php endif; ?>
						<li>Multisite: <?php echo is_multisite() ? 'Yes' : 'No'; ?></li>
					</ul>

					<?php if ( ! $docs_exist ) : ?>
						<p><strong>Checked these locations:</strong></p>
						<ul style="list-style: disc; margin-left: 20px; font-size: 0.9em;">
							<?php foreach ( $possible_paths as $label => $path ) : ?>
								<li>
									<?php echo esc_html( $label ); ?>: <code><?php echo esc_html( $path ); ?></code>
									<?php echo file_exists( $path ) ? '✅' : '❌'; ?>
								</li>
							<?php endforeach; ?>
						</ul>
						<p style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 10px;">
							<strong>📁 Upload your docs to one of these locations:</strong><br>
							Recommended: <code><?php echo esc_html( WP_CONTENT_DIR . '/' . self::DOCS_DIR_NAME ); ?></code>
						</p>
					<?php endif; ?>

					<p>
						<strong>Troubleshooting:</strong>
						If docs aren't loading, go to <a href="<?php echo esc_url( admin_url( 'options-permalink.php' ) ); ?>">Settings → Permalinks</a>
						and click "Save Changes" to flush rewrite rules.
					</p>
					<p style="font-size: 0.9em; color: #666;">
						To disable this notice, set <code>DEBUG_MODE = false</code> in the plugin file.
					</p>
				</div>
				<?php
			}
		}
	}
}

// Initialize the plugin.
add_action( 'plugins_loaded', array( 'Zsoogi_Docs_Server', 'init' ) );
add_action( 'admin_notices', array( 'Zsoogi_Docs_Server', 'admin_notices' ) );
