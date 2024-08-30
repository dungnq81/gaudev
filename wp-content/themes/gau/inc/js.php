<?php
/**
 * JS Output functions
 *
 * @author   Gaudev
 */

\defined( 'ABSPATH' ) || die;

// --------------------------------------------------
// Custom JS
// --------------------------------------------------

add_action( 'wp_footer', '__custom_js', 999 );

/**
 * @return void
 */
function __custom_js(): void {
	ob_start();
	?>
	<script></script>
	<?php
	$content = ob_get_clean();

	echo \Cores\Helper::JSMinify( $content, true );
}
