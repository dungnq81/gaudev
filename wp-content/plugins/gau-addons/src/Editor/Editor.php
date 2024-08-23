<?php

namespace Addons\Editor;

use Addons\Base\Singleton;

\defined( 'ABSPATH' ) || die;

final class Editor {

	use Singleton;

	// ------------------------------------------------------

	private function init(): void {

		( new TinyMCE() );

		add_action( 'admin_init', [ &$this, 'editor_admin_init' ], 11 );
		add_action( 'wp_enqueue_scripts', [ &$this, 'editor_enqueue_scripts' ], 98 );
	}

	// ------------------------------------------------------

	/**
	 * @return void
	 */
	public function editor_admin_init(): void {
		$block_editor_options = get_option( 'editor__options' );

		$use_widgets_block_editor_off           = $block_editor_options['use_widgets_block_editor_off'] ?? '';
		$gutenberg_use_widgets_block_editor_off = $block_editor_options['gutenberg_use_widgets_block_editor_off'] ?? '';
		$use_block_editor_for_post_type_off     = $block_editor_options['use_block_editor_for_post_type_off'] ?? '';

		// Disables the block editor from managing widgets.
		if ( $use_widgets_block_editor_off ) {
			add_filter( 'use_widgets_block_editor', '__return_false' );
		}

		// Disables the block editor from managing widgets in the Gutenberg plugin.
		if ( $gutenberg_use_widgets_block_editor_off ) {
			add_filter( 'gutenberg_use_widgets_block_editor', '__return_false' );
		}

		// Use Classic Editor - Disable Gutenberg Editor
		if ( $use_block_editor_for_post_type_off ) {
			add_filter( 'use_block_editor_for_post_type', '__return_false' );
		}
	}

	// ------------------------------------------------------

	/**
	 * @return void
	 */
	public function editor_enqueue_scripts(): void {
		$block_editor_options = get_option( 'editor__options' );
		$block_style_off      = $block_editor_options['block_style_off'] ?? '';

		/** Remove block CSS */
		if ( $block_style_off ) {
			wp_dequeue_style( 'global-styles' );

			wp_dequeue_style( 'wp-block-library' );
			wp_dequeue_style( 'wp-block-library-theme' );

			// Remove WooCommerce block CSS
			if ( check_plugin_active( 'woocommerce/woocommerce.php' ) ) {
				wp_deregister_style( 'wc-blocks-vendors-style' );
				wp_deregister_style( 'wc-block-style' );
			}
		}
	}
}
