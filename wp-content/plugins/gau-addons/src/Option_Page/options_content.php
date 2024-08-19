<?php

\defined( 'ABSPATH' ) || die;

$menu_options_page = apply_filters( 'gau_menu_options_page', [] );

?>
<div id="_content" class="tabs-content">
	<h2 class="hidden-text"></h2>

	<?php
	$i = 0;
	foreach ( $menu_options_page as $slug => $value ) :
		$show_class = ( 0 === $i ) ? ' show' : '';
	?>
	<div id="<?=$slug?>_settings" class="group tabs-panel<?=$show_class?>">
		<?php

		$option_file = ADDONS_SRC_PATH . capitalized_slug( $slug ) . DIRECTORY_SEPARATOR . 'options.php';
        file_exists ( $option_file ) && include $option_file;

		?>
	</div>
	<?php $i++; endforeach; ?>

</div>
