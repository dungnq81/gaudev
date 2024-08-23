<?php
/**
 * The template for displaying the header
 * This is the template that displays all the <head> section, opens the <body> tag and adds the site's header.
 *
 * @package Gaudev
 */

\defined( 'ABSPATH' ) || die;

?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>" />
	<?php

	/**
     * Head
     *
	 * @see __wp_head - 1
     * @see __external_fonts - 10
	 */
    wp_head();

    ?>
</head>
<body <?php body_class(); ?> <?php echo \Cores\Helper::microdata( 'body' ); ?>>
    <?php

    /**
     * @see Custom_Script::body_scripts_top__hook - 99
     */
    do_action( 'wp_body_open' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- core WP hook.

    /**
     * Before Header
     *
     * @see __skip_to_content_link - 2
     * @see __off_canvas_menu - 10
     */
    do_action( 'gau_before_header' );

    ?>
    <header id="masthead" class="site-header" <?php echo \Cores\Helper::microdata( 'header' ); ?>>
        <?php

        /**
         * Header
         *
         * @see __construct_header - 10
         */
        do_action( 'gau_header' );

        ?>
    </header><!-- #masthead -->
    <?php

    /**
     * After Header
     */
    do_action( 'gau_after_header' );

    ?>
    <div class="site-content" id="site-content">
        <?php

        /**
         * Before Site Content
         */
        do_action( 'gau_before_site_content' );
