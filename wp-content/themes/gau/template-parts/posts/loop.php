<?php

use Cores\Helper;

\defined( 'ABSPATH' ) || die;

global $post;

$post_title = get_the_title( $post->ID );
$post_title = ( ! empty( $post_title ) ) ? $post_title : __( '(no title)', TEXT_DOMAIN );
