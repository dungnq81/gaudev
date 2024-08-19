<?php

defined( 'ABSPATH' ) || die;

/** ----------------------------------------------- */

if ( ! function_exists( 'redirect' ) ) {
	/**
	 * @param string $uri
	 * @param int $status
	 *
	 * @return true|void
	 */
	function redirect( string $uri = '', int $status = 301 ) {
		if ( ! preg_match( '#^(\w+:)?//#', $uri ) ) {
			$uri = trailingslashit( esc_url( network_home_url( $uri ) ) );
		}

		if ( ! headers_sent() ) {
			wp_safe_redirect( $uri, $status );
		} else {
			echo '<script>';
			echo 'window.location.href="' . $uri . '";';
			echo '</script>';
			echo '<noscript>';
			echo '<meta http-equiv="refresh" content="0;url=' . $uri . '" />';
			echo '</noscript>';

			return true;
		}
	}
}

/** ----------------------------------------------- */

if ( ! function_exists( 'get_custom_post_option_content' ) ) {
	/**
	 * @param string $post_type - max 20 characters
	 * @param bool $encode
	 *
	 * @return array|string
	 */
	function get_custom_post_option_content( string $post_type, bool $encode = false ): array|string {
		if ( empty( $post_type ) ) {
			return '';
		}

		$post = get_custom_post_option( $post_type );
		if ( isset( $post->post_content ) ) {
			$post_content = wp_unslash( $post->post_content );
			if ( $encode ) {
				$post_content = wp_unslash( base64_decode( $post->post_content ) );
			}

			return $post_content;
		}

		return '';
	}
}

/** ----------------------------------------------- */

if ( ! function_exists( 'get_custom_post_option' ) ) {
	/**
	 * @param string $post_type - max 20 characters
	 *
	 * @return array|WP_Post|null
	 */
	function get_custom_post_option( string $post_type ): array|WP_Post|null {
		if ( empty( $post_type ) ) {
			return null;
		}

		$custom_query_vars = [
			'post_type'              => $post_type,
			'post_status'            => get_post_stati(),
			'posts_per_page'         => 1,
			'no_found_rows'          => true,
			'cache_results'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'lazy_load_term_meta'    => false,
		];

		$post    = null;
		$post_id = get_theme_mod( $post_type . '_option_id' );

		if ( $post_id > 0 && get_post( $post_id ) ) {
			$post = get_post( $post_id );
		}

		// `-1` indicates no post exists; no query necessary.
		if ( ! $post && - 1 !== $post_id ) {
			$post = ( new \WP_Query( $custom_query_vars ) )->post;

			set_theme_mod( $post_type . '_option_id', $post->ID ?? - 1 );
		}

		return $post;
	}
}

/** ----------------------------------------------- */

if ( ! function_exists( 'filter_setting_options' ) ) {
	/**
	 * @param $name
	 * @param mixed $default
	 *
	 * @return array|mixed
	 */
	function filter_setting_options( $name, mixed $default = [] ): mixed {
		$filters = apply_filters( 'gau_theme_setting_options', [] );

		if ( isset( $filters[ $name ] ) ) {
			return $filters[ $name ] ?: $default;
		}

		return [];
	}
}

/** ----------------------------------------------- */

if ( ! function_exists( 'capitalized_slug' ) ) {
	/**
	 * @param $slug
	 *
	 * @return string
	 */
	function capitalized_slug( $slug ): string {
		$words            = preg_split( '/[_-]/', $slug );
		$capitalizedWords = array_map( 'ucfirst', $words );

		if ( str_contains( $slug, '_' ) ) {
			return implode( '_', $capitalizedWords );
		}

		return implode( '-', $capitalizedWords );
	}
}

/** ----------------------------------------------- */

/**
 * @param string $str
 * @param string|null $encoding
 *
 * @return string
 */
function mb_ucfirst( string $str, string $encoding = null ): string {
	if ( is_null( $encoding ) ) {
		$encoding = mb_internal_encoding();
	}

	return mb_strtoupper( mb_substr( $str, 0, 1, $encoding ), $encoding ) . mb_substr( $str, 1, null, $encoding );
}

/** ----------------------------------------------- */

if ( ! function_exists( 'ht_access' ) ) {
	/**
	 * @return bool
	 */
	function ht_access(): bool {
		global $is_apache;

		if ( $is_apache ) {
			return true;
		}

		// ?
		if ( isset( $_SERVER['HTACCESS'] ) && 'on' === $_SERVER['HTACCESS'] ) {
			return true;
		}

		return false;
	}
}

/** ----------------------------------------------- */

if ( ! function_exists( 'ip_address' ) ) {
	/**
	 * Get the IP address from which the user is viewing the current page.
	 *
	 * @return string
	 */
	function ip_address(): string {

		// Get real visitor IP behind CloudFlare network
		if ( isset( $_SERVER["HTTP_CF_CONNECTING_IP"] ) ) {
			$_SERVER['REMOTE_ADDR']    = $_SERVER["HTTP_CF_CONNECTING_IP"];
			$_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
		}

		$client  = $_SERVER['HTTP_CLIENT_IP'] ?? '';
		$forward = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '';
		$remote  = $_SERVER['REMOTE_ADDR'] ?? '';

		if ( filter_var( $client, FILTER_VALIDATE_IP ) ) {
			return $client;
		}

		if ( filter_var( $forward, FILTER_VALIDATE_IP ) ) {
			return $forward;
		}

		if ( filter_var( $remote, FILTER_VALIDATE_IP ) ) {
			return $remote;
		}

		// Fallback local ip.
		return '127.0.0.1';
	}
}

/** ----------------------------------------------- */

if ( ! function_exists( 'check_plugin_active' ) ) {
	/**
	 * Check if the plugin is installed
	 *
	 * @param $plugin_slug
	 *
	 * @return bool
	 */
	function check_plugin_active( $plugin_slug ): bool {
		return check_plugin_installed( $plugin_slug ) && \is_plugin_active( $plugin_slug );
	}
}

/** ----------------------------------------------- */

if ( ! function_exists( 'check_plugin_installed' ) ) {
	/**
	 * Check if plugin is installed by getting all plugins from the plugins dir
	 *
	 * @param $plugin_slug
	 *
	 * @return bool
	 */
	function check_plugin_installed( $plugin_slug ): bool {

		// Check if needed functions exist - if not, require them
		if ( ! function_exists( 'get_plugins' ) || ! function_exists( 'is_plugin_active' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$installed_plugins = \get_plugins();

		return array_key_exists( $plugin_slug, $installed_plugins ) || in_array( $plugin_slug, $installed_plugins, false );
	}
}
