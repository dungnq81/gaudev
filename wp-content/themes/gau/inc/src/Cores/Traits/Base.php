<?php

namespace Cores\Traits;

use Detection\Exception\MobileDetectException;
use Detection\MobileDetect;

\defined( 'ABSPATH' ) || die;

trait Base {

	// --------------------------------------------------

	/**
	 * @param $url
	 *
	 * @return bool
	 */
	public static function isUrl( $url ): bool {

		// Basic URL validation using filter_var
		if ( ! filter_var( $url, FILTER_VALIDATE_URL ) ) {
			return false;
		}

		$parsed_url = parse_url( $url );

		// Validate scheme
		$valid_schemes = [ 'http', 'https' ];
		if ( ! isset( $parsed_url['scheme'] ) || ! in_array( $parsed_url['scheme'], $valid_schemes, false ) ) {
			return false;
		}

		// Validate host
		if ( ! isset( $parsed_url['host'] ) || ! filter_var( $parsed_url['host'], FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME ) ) {
			return false;
		}

		// Validate DNS resolution for the host
		if ( ! checkdnsrr( $parsed_url['host'], 'A' ) && ! checkdnsrr( $parsed_url['host'], 'AAAA' ) ) {
			return false;
		}

		return true;
	}

	// --------------------------------------------------

	/**
	 * Test if the current browser runs on a mobile device (smartphone, tablet, etc.)
	 *
	 * @return boolean
	 * @throws MobileDetectException
	 */
	public static function isMobile(): bool {

		if ( class_exists( MobileDetect::class ) ) {
			return ( new MobileDetect() )->isMobile();
		}

		return wp_is_mobile();
	}

	// --------------------------------------------------

	/**
	 * @param string $version
	 *
	 * @return  bool
	 */
	public static function isPhp( string $version = '7.4' ): bool {
		static $phpVer;
		if ( ! isset( $phpVer[ $version ] ) ) {
			$phpVer[ $version ] = ! ( ( version_compare( PHP_VERSION, $version ) < 0 ) );
		}

		return $phpVer[ $version ];
	}

	// --------------------------------------------------

	/**
	 * @param $input
	 *
	 * @return bool
	 */
	public static function isInteger( $input ): bool {
		return filter_var( $input, FILTER_VALIDATE_INT ) !== false;
	}

	// --------------------------------------------------

	/**
	 * @param $value
	 *
	 * @return bool
	 */
	public static function isEmpty( $value ): bool {
		if ( is_string( $value ) ) {
			return trim( $value ) === '';
		}

		return ! is_numeric( $value ) && ! is_bool( $value ) && empty( $value );
	}

	// -------------------------------------------------------------

	/**
	 * Whether the current request is a WP CLI request.
	 *
	 * @return bool
	 */
	public static function isWpCli(): bool {
		return defined( 'WP_CLI' ) && \WP_CLI;
	}

	// -------------------------------------------------------------

	/**
	 * @return bool
	 */
	public static function isAdmin(): bool {
		return is_admin();
	}

	// -------------------------------------------------------------

	/**
	 * @return bool
	 */
	public static function isLogin(): bool {
		return in_array( $GLOBALS['pagenow'], [ 'wp-login.php', 'wp-register.php' ] );
	}

	// -------------------------------------------------------------

	/**
	 * Check if plugin is installed by getting all plugins from the plugins dir
	 *
	 * @param $plugin_slug
	 *
	 * @return bool
	 */
	public static function checkPluginInstalled( $plugin_slug ): bool {

		// Check if needed functions exist - if not, require them
		if ( ! function_exists( 'get_plugins' ) || ! function_exists( 'is_plugin_active' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$installed_plugins = get_plugins();

		return array_key_exists( $plugin_slug, $installed_plugins ) || in_array( $plugin_slug, $installed_plugins, false );
	}

	// -------------------------------------------------------------

	/**
	 * Check if the plugin is installed
	 *
	 * @param $plugin_slug
	 *
	 * @return bool
	 */
	public static function checkPluginActive( $plugin_slug ): bool {
		return self::checkPluginInstalled( $plugin_slug ) && is_plugin_active( $plugin_slug );
	}

	// -------------------------------------------------------------

	/**
	 * @return bool
	 */
	public static function isAcfActive(): bool {
		return self::checkPluginActive( 'advanced-custom-fields/acf.php' ) ||
		       self::checkPluginActive( 'advanced-custom-fields-pro/acf.php' );
	}

	// -------------------------------------------------------------

	/**
	 * @return bool
	 */
	public static function isPolylangActive(): bool {
		return self::checkPluginActive( 'polylang/polylang.php' ) ||
		       self::checkPluginActive( 'polylang-pro/polylang.php' );
	}

	// -------------------------------------------------------------

	/**
	 * @return bool
	 */
	public static function isCf7Active(): bool {
		return self::checkPluginActive( 'contact-form-7/wp-contact-form-7.php' );
	}

	// -------------------------------------------------------------

	/**
	 * @return bool
	 */
	public static function isWoocommerceActive(): bool {
		return self::checkPluginActive( 'woocommerce/woocommerce.php' );
	}
}
