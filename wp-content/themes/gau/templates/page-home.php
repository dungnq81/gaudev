<?php
/**
 * The template for displaying homepage
 * Template Name: Home
 * Template Post Type: page
 */

\defined( 'ABSPATH' ) || die;

// header
get_header( 'homepage' );

if ( have_posts() ) {
	the_post();
}

if ( post_password_required() ) :
	echo get_the_password_form(); // WPCS: XSS ok.

	return;
endif;

// homepage widget
if ( is_active_sidebar( 'home-sidebar' ) ) :
	dynamic_sidebar( 'home-sidebar' );
endif;

// custom page
try {
	$ACF = \Cores\Helper::getFields( get_the_ID() );
} catch ( JsonException $e ) {}

$home_list_sections = ! empty( $ACF->home_list_sections ) ? (array) $ACF->home_list_sections : [];
foreach ( $home_list_sections as $section ) {

	$acf_fc_layout = $section['acf_fc_layout'] ?? '';

	if ( $acf_fc_layout ) {
		get_template_part( 'inc/blocks/home/' . $acf_fc_layout, null, $section );
	}
}

// footer
get_footer( 'homepage' );
