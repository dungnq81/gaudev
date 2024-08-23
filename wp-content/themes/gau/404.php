<?php
/**
 * The template for displaying 404 pages (Not Found).
 *
 * @package HD
 */

\defined( 'ABSPATH' ) || die;

wp_safe_redirect( \Cores\Helper::home() );
