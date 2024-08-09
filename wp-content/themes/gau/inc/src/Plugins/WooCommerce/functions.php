<?php

use Cores\Helper;

\defined( 'ABSPATH' ) || die;

// ------------------------------------------------------
// override functions
// ------------------------------------------------------

/**
 * Add default product tabs to product pages.
 *
 * @param array $tabs Array of tabs.
 *
 * @return array
 */
function woocommerce_default_product_tabs( array $tabs = [] ): array {
	global $product, $post;

	// Description tab - shows product content.
	if ( $post->post_content ) {
		$tabs['description'] = [
			'title'    => __( 'Description', 'woocommerce' ),
			'priority' => 10,
			'callback' => 'woocommerce_product_description_tab',
		];
	}

	// Additional information tab - shows attributes.
	if ( $product && ( $product->has_attributes() || apply_filters( 'wc_product_enable_dimensions_display', $product->has_weight() || $product->has_dimensions() ) ) ) {
		$tabs['additional_information'] = [
			'title'    => __( 'Additional information', 'woocommerce' ),
			'priority' => 20,
			'callback' => 'woocommerce_product_additional_information_tab',
		];
	}

	// Reviews tab - shows comments.
	if ( comments_open() ) {
		$tabs['reviews'] = [
			/* translators: %s: reviews count */
			'title'    => sprintf( __( 'Reviews (%d)', 'woocommerce' ), $product->get_review_count() ),
			'priority' => 30,
			'callback' => 'comments_template',
		];
	}

	return $tabs;
}

// ------------------------------------------------------

/**
 * Get the product thumbnail, or the placeholder if not set.
 *
 * @param string $size (default: 'woocommerce_thumbnail').
 * @param array $attr Image attributes.
 * @param bool $placeholder True to return $placeholder if no image is found, or false to return an empty string.
 *
 * @return string
 */
function woocommerce_get_product_thumbnail( string $size = 'medium', array $attr = [], bool $placeholder = true ): string {
	global $product;

	$image_size = apply_filters( 'single_product_archive_thumbnail_size', $size );

	$scale_class = '';
	$ratio_class = Helper::aspectRatioClass( 'product' );

	return $product ? '<div class="cover thumbnails"><span class="' . $scale_class . 'after-overlay res ' . $ratio_class . '">' . $product->get_image( $image_size, $attr, $placeholder ) . '</span></div>' : '';
}

// ------------------------------------------------------

/**
 * Show the product title in the product loop. By default, this is an H2.
 */
function woocommerce_template_loop_product_title(): void {
	echo '<p class="' . esc_attr( apply_filters( 'woocommerce_product_loop_title_classes', 'woocommerce-loop-product__title' ) ) . '">' . get_the_title() . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

// ------------------------------------------------------
// custom functions
// ------------------------------------------------------

if ( ! function_exists( '_wc_sale_flash_percent' ) ) {

	/**
	 * @param $product
	 *
	 * @return float|string
	 */
	function _wc_sale_flash_percent( $product ): float|string {
		global $product;
		$percent_off = '';

		if ( $product->is_on_sale() ) {

			if ( $product->is_type( 'variable' ) ) {
				$percent_off = ceil( 100 - ( $product->get_variation_sale_price() / $product->get_variation_regular_price( 'min' ) ) * 100 );
			} elseif ( $product->get_regular_price() && ! $product->is_type( 'grouped' ) ) {
				$percent_off = ceil( 100 - ( $product->get_sale_price() / $product->get_regular_price() ) * 100 );
			}
		}

		return $percent_off;
	}
}

// ------------------------------------------------------

if ( ! function_exists( '_wc_get_gallery_image_html' ) ) {

	/**
	 * @param      $attachment_id
	 * @param bool $main_image
	 * @param bool $cover
	 * @param bool $lightbox
	 *
	 * @return string
	 */
	function _wc_get_gallery_image_html( $attachment_id, bool $main_image = false, bool $cover = false, bool $lightbox = false ): string {
		$gallery_thumbnail = wc_get_image_size( 'gallery_thumbnail' );
		$thumbnail_size    = apply_filters( 'woocommerce_gallery_thumbnail_size', [
			$gallery_thumbnail['width'],
			$gallery_thumbnail['height']
		] );

		$image_size    = apply_filters( 'woocommerce_gallery_image_size', $main_image ? 'woocommerce_single' : $thumbnail_size );
		$full_size     = apply_filters( 'woocommerce_gallery_full_size', apply_filters( 'woocommerce_product_thumbnails_large_size', 'full' ) );
		$thumbnail_src = wp_get_attachment_image_src( $attachment_id, $thumbnail_size );
		$full_src      = wp_get_attachment_image_src( $attachment_id, $full_size );
		$alt_text      = Helper::escAttr( get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) );

		$image = wp_get_attachment_image(
			$attachment_id,
			$image_size,
			false,
			apply_filters(
				'woocommerce_gallery_image_html_attachment_image_params',
				[
					'title'                   => _wp_specialchars( get_post_field( 'post_title', $attachment_id ), ENT_QUOTES, 'UTF-8', true ),
					'data-caption'            => _wp_specialchars( get_post_field( 'post_excerpt', $attachment_id ), ENT_QUOTES, 'UTF-8', true ),
					'data-src'                => esc_url( $full_src[0] ),
					'data-large_image'        => esc_url( $full_src[0] ),
					'data-large_image_width'  => Helper::escAttr( $full_src[1] ),
					'data-large_image_height' => Helper::escAttr( $full_src[2] ),
					'class'                   => Helper::escAttr( $main_image ? 'wp-post-image' : '' ),
				],
				$attachment_id,
				$image_size,
				$main_image
			)
		);

		$ratio_class = Helper::aspectRatioClass( 'product' );
		$auto        = $cover ? ' ' : ' auto ';

		if ( $lightbox ) {
			$popup_image = '<span data-rel="lightbox" class="image-popup" data-src="' . esc_url( $full_src[0] ) . '" data-fa=""></span>';

			return '<div data-thumb="' . esc_url( $thumbnail_src[0] ) . '" data-thumb-alt="' . Helper::escAttr( $alt_text ) . '" class="wpg__image cover"><a class="res' . $auto . $ratio_class . '" href="' . esc_url( $full_src[0] ) . '">' . $image . '</a>' . $popup_image . '</div>';
		}

		return '<div data-thumb="' . esc_url( $thumbnail_src[0] ) . '" data-thumb-alt="' . Helper::escAttr( $alt_text ) . '" class="woocommerce-product-gallery__image wpg__thumb cover"><a class="res' . $auto . $ratio_class . '" href="' . esc_url( $full_src[0] ) . '">' . $image . '</a></div>';
	}
}
