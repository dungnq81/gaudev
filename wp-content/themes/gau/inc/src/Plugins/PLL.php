<?php

namespace Plugins;

use Cores\Helper;
use Cores\Traits\Singleton;

\defined( 'ABSPATH' ) || die;

final class PLL {

	use Singleton;

	// --------------------------------------------------

	/**
	 * @return void
	 */
	private function init(): void {
		add_action( 'init', [ &$this, 'register_and_translate_wc_attributes' ] );
		add_action( 'wp_loaded', [ &$this, 'language_switch_empty_cart' ] );
		add_filter( 'woocommerce_attribute_label', [ &$this, 'translate_product_attribute_label' ], 10, 3 );
		add_filter( 'woocommerce_variation_option_name', [ &$this, 'translate_product_attribute_option_name' ] );

		// custom filters
		add_filter( 'gau_home_url', [ &$this, 'gau_home_url' ] );
		add_filter( 'gau_get_shop_url', [ &$this, 'gau_get_shop_url' ], 10, 1 );
	}

	// --------------------------------------------------

	/**
	 * @return mixed
	 */
	public function gau_home_url(): mixed {
		return \pll_home_url();
	}

	// --------------------------------------------------

	/**
	 * @param $url
	 *
	 * @return string
	 */
	public function gau_get_shop_url( $url ): string {
		if ( Helper::checkPluginActive( 'polylang-wc/polylang-wc.php' ) ) {
			return $url;
		}

		$shop_page_id = wc_get_page_id( 'shop' );
		$shop_slug    = get_post_field( 'post_name', $shop_page_id );

		return \pll_home_url() . $shop_slug;
	}

	// --------------------------------------------------

	/**
	 * @return void
	 */
	public function language_switch_empty_cart(): void {
		if ( ! Helper::isWoocommerceActive() ) {
			return;
		}

		$current_lang  = \pll_current_language();
		$previous_lang = isset( $_COOKIE['wc_language'] ) ? sanitize_text_field( $_COOKIE['wc_language'] ) : '';

		if ( $current_lang &&
		     $previous_lang &&
		     $current_lang !== $previous_lang &&
		     WC()->cart && ! WC()->cart->is_empty()
		) {
			WC()->cart->empty_cart();
		}

		// 7 days - 10080 minutes
		setcookie( 'wc_language', $current_lang, time() + 10080 * MINUTE_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN, ini_get( 'session.cookie_secure' ), ini_get( 'session.cookie_httponly' ) );
	}

	// --------------------------------------------------

	/**
	 * @param $label
	 * @param $name
	 * @param $product
	 *
	 * @return string
	 */
	public function translate_product_attribute_label( $label, $name, $product ): string {
		return \pll__( $label );
	}

	// --------------------------------------------------

	/**
	 * @param $term_name
	 *
	 * @return string
	 */
	public function translate_product_attribute_option_name( $term_name ): string {
		return \pll__( $term_name );
	}

	// --------------------------------------------------

	/**
	 * @return void
	 */
	public function register_and_translate_wc_attributes(): void {
		if ( ! Helper::isWoocommerceActive() ) {
			return;
		}

		// Get all product attributes
		foreach ( wc_get_attribute_taxonomies() as $attribute ) {

			// Register attribute label with Polylang
			\pll_register_string( $attribute->attribute_name, $attribute->attribute_label, TEXT_DOMAIN );

			// Get all terms (options) of the attribute
			$terms = get_terms( [ 'taxonomy' => 'pa_' . $attribute->attribute_name, 'hide_empty' => false ] );

			foreach ( $terms as $term ) {

				// Register each attribute option with Polylang
				\pll_register_string( $term->name, $term->name, TEXT_DOMAIN );
			}
		}
	}
}
