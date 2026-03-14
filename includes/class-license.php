<?php
/**
 * License - Premium feature gating
 *
 * @package Zsoogi_Clipper
 */

namespace Zsoogi;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Handles premium license detection and feature gating.
 *
 * ## Usage
 *
 *   License::is_premium()                   // true if a valid license is active
 *   License::has_feature( 'transcripts' )   // true if a specific feature is unlocked
 *
 * ## Local development
 *
 *   Add to wp-config.php to bypass license checks:
 *   define( 'ZSOOGI_PREMIUM_LICENSE', true );
 *
 * @package Zsoogi\License
 */
class License {

	/**
	 * WordPress option key for the stored license key.
	 *
	 * @var string
	 */
	const OPTION_KEY = 'zsoogi_clipper_license_key';

	/**
	 * WordPress option key for the license validation status.
	 *
	 * @var string
	 */
	const OPTION_STATUS = 'zsoogi_clipper_license_status';

	/**
	 * Map of feature slugs to the tier required.
	 *
	 * Add new premium features here. All currently map to 'premium'
	 * but the structure supports future 'enterprise' tier gating.
	 *
	 * @var array<string, string>
	 */
	private static $features = array(
		'citation_formats' => 'premium', // detailed + academic citation styles
		'transcripts'      => 'premium', // YouTube transcript capture
		'pdf_extraction'   => 'premium', // PDF content extraction (future)
	);

	/**
	 * Returns true if a valid premium license is active.
	 *
	 * Checks (in order):
	 *   1. ZSOOGI_PREMIUM_LICENSE constant (for local dev / staging)
	 *   2. Stored license status in wp_options
	 *
	 * @return bool
	 */
	public static function is_premium(): bool {
		if ( defined( 'ZSOOGI_PREMIUM_LICENSE' ) && ZSOOGI_PREMIUM_LICENSE ) {
			return true;
		}

		return 'valid' === get_option( self::OPTION_STATUS, '' );
	}

	/**
	 * Returns true if a specific premium feature is available.
	 *
	 * @param string $feature Feature key — must exist in self::$features.
	 * @return bool
	 */
	public static function has_feature( string $feature ): bool {
		if ( ! isset( self::$features[ $feature ] ) ) {
			return false;
		}

		return self::is_premium();
	}

	/**
	 * Activate a license key.
	 *
	 * Currently a stub — replace the TODO block with a real API call
	 * to your license server (EDD Software Licensing, Freemius, etc.).
	 *
	 * @since 0.9.2
	 *
	 * @param string $key Raw license key from the settings form.
	 * @return bool True on success, false on failure.
	 */
	public static function activate( string $key ): bool {
		$key = sanitize_text_field( $key );

		if ( empty( $key ) ) {
			return false;
		}

		// TODO: validate against license server.
		// Example with EDD Software Licensing:
		// $response = wp_remote_post( 'https://theapiguys.com', [
		//     'body' => [
		//         'edd_action'  => 'activate_license',
		//         'license'     => $key,
		//         'item_name'   => urlencode( 'Zsoogi Clipper Premium' ),
		//         'url'         => home_url(),
		//     ],
		// ] );
		// $license_data = json_decode( wp_remote_retrieve_body( $response ) );
		// $status = $license_data->license; // 'valid' | 'invalid' | 'expired' | ...

		update_option( self::OPTION_KEY, $key );
		update_option( self::OPTION_STATUS, 'valid' ); // replace with $status from API.

		return true;
	}

	/**
	 * Deactivate the current license.
	 *
	 * @since 0.9.2
	 *
	 * @return void
	 */
	public static function deactivate(): void {
		// TODO: notify license server to free the activation slot.
		update_option( self::OPTION_STATUS, 'inactive' );
	}

	/**
	 * Return the stored license key, redacted for safe display.
	 *
	 * e.g. "ABCD****************************WXYZ"
	 *
	 * @return string
	 */
	public static function get_display_key(): string {
		$key = get_option( self::OPTION_KEY, '' );

		if ( strlen( $key ) <= 8 ) {
			return $key;
		}

		return substr( $key, 0, 4 )
			. str_repeat( '*', max( 0, strlen( $key ) - 8 ) )
			. substr( $key, -4 );
	}

	/**
	 * Return the current license status string.
	 *
	 * @return string 'valid' | 'inactive' | '' (never activated)
	 */
	public static function get_status(): string {
		if ( defined( 'ZSOOGI_PREMIUM_LICENSE' ) && ZSOOGI_PREMIUM_LICENSE ) {
			return 'valid';
		}

		return get_option( self::OPTION_STATUS, '' );
	}
}
