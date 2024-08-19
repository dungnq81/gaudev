<?php

namespace Cores;

use Cores\Traits\Wp;

\defined( 'ABSPATH' ) || die;

/**
 * Helper Class
 *
 * @author Gaudev
 */
final class Helper {
	use Wp;

	// -------------------------------------------------------------

	/**
	 * @param $name
	 * @param mixed $default
	 *
	 * @return array|mixed
	 */
	public static function filterSettingOptions( $name, mixed $default = [] ): mixed {
		$filters = apply_filters( 'gau_theme_setting_options', [] );

		if ( isset( $filters[ $name ] ) ) {
			return $filters[ $name ] ?: $default;
		}

		return [];
	}

	// -------------------------------------------------------------

	/**
	 * Get lang code
	 *
	 * @return string
	 */
	public static function getLang(): string {
		return strtolower( substr( get_locale(), 0, 2 ) );
	}

	// --------------------------------------------------

	/**
	 * @return bool
	 */
	public static function Lighthouse(): bool {
		$header = $_SERVER['HTTP_USER_AGENT'];

		return mb_strpos( $header, "Lighthouse", 0, "UTF-8" ) !== false;
	}

	// --------------------------------------------------

	/**
	 * @return void
	 */
	public static function clearAllCache(): void {
		// LiteSpeed cache
		if ( class_exists( \LiteSpeed\Purge::class ) ) {
			\LiteSpeed\Purge::purge_all();
		}

		// wp-rocket cache
		if ( \defined( 'WP_ROCKET_PATH' ) && \function_exists( 'rocket_clean_domain' ) ) {
			\rocket_clean_domain();
		}

		// Clear minified CSS and JavaScript files.
		if ( function_exists( 'rocket_clean_minify' ) ) {
			\rocket_clean_minify();
		}

		// Jetpack
		if ( self::checkPluginActive( 'jetpack/jetpack.php' ) ) {
			global $wpdb;

			$wpdb->query( "DELETE FROM {$wpdb->options} WHERE `option_name` LIKE '_transient_jetpack_%'" );
			$wpdb->query( "DELETE FROM {$wpdb->options} WHERE `option_name` LIKE '_transient_timeout_jetpack_%'" );

			// Clear Photon cache locally
			if ( class_exists( \Jetpack_Photon::class ) ) {
				\Jetpack_Photon::instance()->purge_cache();
			}
		}
	}

	// --------------------------------------------------

	/**
	 * @param $value
	 * @param $min
	 * @param $max
	 *
	 * @return bool
	 */
	public static function inRange( $value, $min, $max ): bool {
		$inRange = filter_var( $value, FILTER_VALIDATE_INT, [
			'options' => [
				'min_range' => (int) $min,
				'max_range' => (int) $max,
			],
		] );

		return false !== $inRange;
	}

	// -------------------------------------------------------------

	/**
	 * Check if all values in array_b do not lie within any range in array_a.
	 *
	 * @param array $array_a The multidimensional array of ranges.
	 * @param array $array_b The array of values to check.
	 *
	 * @return bool True if all values in array_b do not lie within any range in array_a, false otherwise.
	 */
	public static function checkValuesNotInRanges( array $array_a, array $array_b ): bool {
		foreach ( $array_a as $range ) {

			// Ensure range is valid
			if ( count( $range ) !== 2 || ! is_numeric( $range[0] ) || ! is_numeric( $range[1] ) ) {
				continue;
			}

			$start = min( $range );
			$end   = max( $range );

			foreach ( $array_b as $value ) {
				if ( $value >= $start && $value < $end ) {
					return false;
				}
			}

			// Additional check for whether array_b contains the entire range of array_a
			if ( min( $array_b ) <= $start && max( $array_b ) >= $end ) {
				return false;
			}
		}

		return true;
	}

	// -------------------------------------------------------------

	/**
	 * @param $message
	 * @param bool $auto_hide
	 *
	 * @return void
	 */
	public static function messageSuccess( $message, bool $auto_hide = false ): void {
		$message = $message ?: 'Values saved';
		$message = __( $message, TEXT_DOMAIN );

		$class = 'notice notice-success is-dismissible';
		if ( $auto_hide ) {
			$class .= ' dismissible-auto';
		}

		printf( '<div class="%1$s"><p><strong>%2$s</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>', self::escAttr( $class ), $message );
	}

	// -------------------------------------------------------------

	/**
	 * @param $message
	 * @param bool $auto_hide
	 *
	 * @return void
	 */
	public static function messageError( $message, bool $auto_hide = false ): void {
		$message = $message ?: 'Values error';
		$message = __( $message, TEXT_DOMAIN );

		$class = 'notice notice-error is-dismissible';
		if ( $auto_hide ) {
			$class .= ' dismissible-auto';
		}

		printf( '<div class="%1$s"><p><strong>%2$s</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>', self::escAttr( $class ), $message );
	}

	// --------------------------------------------------

	/**
	 * A fallback when no navigation is selected by default.
	 *
	 * @param bool $container
	 *
	 * @return void
	 */
	public static function menuFallback( bool $container = false ): void {
		echo '<div class="menu-fallback">';
		if ( $container ) {
			echo '<div class="container">';
		}

		/* translators: %1$s: link to menus, %2$s: link to customize. */
		printf(
			__( 'Please assign a menu to the primary menu location under %1$s or %2$s the design.', TEXT_DOMAIN ),
			/* translators: %s: menu url */
			sprintf(
				__( '<a class="_blank" href="%s">Menus</a>', TEXT_DOMAIN ),
				get_admin_url( get_current_blog_id(), 'nav-menus.php' )
			),
			/* translators: %s: customize url */
			sprintf(
				__( '<a class="_blank" href="%s">Customize</a>', TEXT_DOMAIN ),
				get_admin_url( get_current_blog_id(), 'customize.php' )
			)
		);

		if ( $container ) {
			echo '</div>';
		}

		echo '</div>';
	}

	// --------------------------------------------------

	/**
	 * @param string $img
	 *
	 * @return string
	 */
	public static function pixelImg( string $img = '' ): string {
		if ( file_exists( $img ) ) {
			return $img;
		}

		return "data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==";
	}

	// --------------------------------------------------

	/**
	 * @param bool $img_wrap
	 * @param bool $thumb
	 *
	 * @return string
	 */
	public static function placeholderSrc( bool $img_wrap = true, bool $thumb = true ): string {
		$src = THEME_URL . 'storage/img/placeholder.png';
		if ( $thumb ) {
			$src = THEME_URL . 'storage/img/placeholder-320x320.png';
		}
		if ( $img_wrap ) {
			$src = "<img loading=\"lazy\" src=\"{$src}\" alt=\"place-holder\" class=\"wp-placeholder\">";
		}

		return $src;
	}

	// --------------------------------------------------

	/**
	 * Find an attribute and add the data as an HTML string.
	 *
	 * @param string $str The HTML string.
	 * @param string $attr The attribute to find.
	 * @param string $content_extra The content that needs to be appended.
	 * @param bool $unique Do we need to filter for unique values?
	 *
	 * @return string
	 */
	public static function appendToAttribute( string $str, string $attr, string $content_extra, bool $unique = false ): string {

		// Check if attribute has single or double quotes.
		// @codingStandardsIgnoreLine
		if ( $start = stripos( $str, $attr . '="' ) ) {
			$quote = '"';

			// @codingStandardsIgnoreLine
		} elseif ( $start = stripos( $str, $attr . "='" ) ) {
			$quote = "'";

		} else {
			// Not found
			return $str;
		}

		// Add quote (for filtering purposes).
		$attr .= '=' . $quote;

		$content_extra = trim( $content_extra );

		if ( $unique ) {

			$start += strlen( $attr );
			$end   = strpos( $str, $quote, $start );

			// Get the current content.
			$content = explode( ' ', substr( $str, $start, $end - $start ) );

			// Get our extra content.
			foreach ( explode( ' ', $content_extra ) as $class ) {
				if ( ! empty( $class ) && ! in_array( $class, $content, false ) ) {
					$content[] = $class;
				}
			}

			// Remove duplicates and empty values.
			$content = array_unique( array_filter( $content ) );
			$content = implode( ' ', $content );

			$before_content = substr( $str, 0, $start );
			$after_content  = substr( $str, $end );

			$str = $before_content . $content . $after_content;
		} else {
			$str = preg_replace(
				'/' . preg_quote( $attr, '/' ) . '/',
				$attr . $content_extra . ' ',
				$str,
				1
			);
		}

		return $str;
	}

	// --------------------------------------------------

	/**
	 * @param       $url
	 * @param int $resolution_key
	 *
	 * @return string
	 */
	public static function youtubeImage( $url, int $resolution_key = 0 ): string {
		if ( ! $url ) {
			return '';
		}

		$resolution = [
			'sddefault',
			'hqdefault',
			'mqdefault',
			'default',
			'maxresdefault',
		];

		$url_img = self::pixelImg();
		parse_str( wp_parse_url( $url, PHP_URL_QUERY ), $vars );
		if ( isset( $vars['v'] ) ) {
			$id      = $vars['v'];
			$url_img = 'https://img.youtube.com/vi/' . $id . '/' . $resolution[ $resolution_key ] . '.jpg';
		}

		return $url_img;
	}

	// --------------------------------------------------

	/**
	 * @param      $url
	 * @param int $autoplay
	 * @param bool $lazyload
	 * @param bool $control
	 *
	 * @return string|null
	 */
	public static function youtubeIframe( $url, int $autoplay = 0, bool $lazyload = true, bool $control = true ): ?string {
		$autoplay = (int) $autoplay;
		parse_str( wp_parse_url( $url, PHP_URL_QUERY ), $vars );
		$home = trailingslashit( network_home_url() );

		if ( isset( $vars['v'] ) ) {
			$idurl     = $vars['v'];
			$_size     = ' width="800px" height="450px"';
			$_autoplay = 'autoplay=' . $autoplay;
			$_auto     = ' allow="accelerometer; encrypted-media; gyroscope; picture-in-picture"';
			if ( $autoplay ) {
				$_auto = ' allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"';
			}
			$_src     = 'https://www.youtube.com/embed/' . $idurl . '?wmode=transparent&origin=' . $home . '&' . $_autoplay;
			$_control = '';
			if ( ! $control ) {
				$_control = '&modestbranding=1&controls=0&rel=0&version=3&loop=1&enablejsapi=1&iv_load_policy=3&playlist=' . $idurl . '&playerapiid=ytb_iframe_' . $idurl;
			}
			$_src  .= $_control . '&html5=1';
			$_src  = ' src="' . $_src . '"';
			$_lazy = '';
			if ( $lazyload ) {
				$_lazy = ' loading="lazy"';
			}

			return '<iframe id="ytb_iframe_' . $idurl . '" title="YouTube Video Player"' . $_lazy . $_auto . $_size . $_src . ' style="border:0"></iframe>';
		}

		return null;
	}

	// --------------------------------------------------

	/**
	 * Encoded Mailto Link
	 *
	 * Create a spam-protected mailto link written in Javascript
	 *
	 * @param string $email the email address
	 * @param string $title the link title
	 * @param array|string $attributes
	 *
	 * @return string|null
	 */
	public static function safeMailTo( string $email, string $title = '', array|string $attributes = '' ): ?string {
		if ( ! $email || ! is_email( $email ) ) {
			return null;
		}

		if ( trim( $title ) === '' ) {
			$title = $email;
		}

		$x = str_split( '<a href="mailto:', 1 );

		for ( $i = 0, $l = strlen( $email ); $i < $l; $i ++ ) {
			$x[] = '|' . ord( $email[ $i ] );
		}

		$x[] = '"';

		if ( $attributes !== '' ) {
			if ( is_array( $attributes ) ) {
				foreach ( $attributes as $key => $val ) {
					$x[] = ' ' . $key . '="';
					for ( $i = 0, $l = strlen( $val ); $i < $l; $i ++ ) {
						$x[] = '|' . ord( $val[ $i ] );
					}
					$x[] = '"';
				}
			} else {
				for ( $i = 0, $l = mb_strlen( $attributes ); $i < $l; $i ++ ) {
					$x[] = mb_substr( $attributes, $i, 1 );
				}
			}
		}

		$x[] = '>';

		$temp = [];
		for ( $i = 0, $l = strlen( $title ); $i < $l; $i ++ ) {
			$ordinal = ord( $title[ $i ] );

			if ( $ordinal < 128 ) {
				$x[] = '|' . $ordinal;
			} else {
				if ( empty( $temp ) ) {
					$count = ( $ordinal < 224 ) ? 2 : 3;
				}

				$temp[] = $ordinal;
				if ( count( $temp ) === $count ) // @phpstan-ignore-line
				{
					$number = ( $count === 3 ) ? ( ( $temp[0] % 16 ) * 4096 ) + ( ( $temp[1] % 64 ) * 64 ) + ( $temp[2] % 64 ) : ( ( $temp[0] % 32 ) * 64 ) + ( $temp[1] % 64 );
					$x[]    = '|' . $number;
					$count  = 1;
					$temp   = [];
				}
			}
		}

		$x[] = '<';
		$x[] = '/';
		$x[] = 'a';
		$x[] = '>';

		$x = array_reverse( $x );

		// improve obfuscation by eliminating newlines & whitespace
		$output = '<script>'
		          . 'let l=[];';

		foreach ( $x as $i => $value ) {
			$output .= 'l[' . $i . "] = '" . $value . "';";
		}

		return $output . ( 'for (var i = l.length-1; i >= 0; i=i-1) {'
		                   . "if (l[i].substring(0, 1) === '|') document.write(\"&#\"+unescape(l[i].substring(1))+\";\");"
		                   . 'else document.write(unescape(l[i]));'
		                   . '}'
		                   . '</script>' );
	}
}