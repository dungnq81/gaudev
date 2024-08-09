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
		add_filter( 'woocommerce_attribute_label', [ &$this, 'translate_product_attribute_label' ], 10, 3 );
		add_filter( 'woocommerce_variation_option_name', [ &$this, 'translate_product_attribute_option_name' ] );
		add_action( 'wp_loaded', [ &$this, 'language_switch_empty_cart' ] );
	}

	// --------------------------------------------------

	/**
	 * @return void
	 */
	public function language_switch_empty_cart(): void {
		if ( ! Helper::isWoocommerceActive() ) {
			return;
		}

		// Get the current language
		$current_lang = \pll_current_language();

		// Get the previous language from the option
		$previous_lang = Helper::getOption( 'previous_language' );

		// Check if the language has changed
//		if ( $current_lang && $previous_lang && $current_lang !== $previous_lang && WC()->cart && ! WC()->cart->is_empty() ) {
//			WC()->cart->empty_cart(); // Empty the WooCommerce cart
//		}

		// Update the previous language option
		update_option( 'previous_language', $current_lang );
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

		// Get the translated string from Polylang
		return \pll__( $label );
	}

	// --------------------------------------------------

	/**
	 * @param $term_name
	 *
	 * @return string
	 */
	public function translate_product_attribute_option_name( $term_name ): string {

		// Get the translated string from Polylang
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
			$terms = get_terms( array( 'taxonomy' => 'pa_' . $attribute->attribute_name, 'hide_empty' => false ) );

			foreach ( $terms as $term ) {

				// Register each attribute option with Polylang
				\pll_register_string( $term->name, $term->name, TEXT_DOMAIN );
			}
		}
	}
}
