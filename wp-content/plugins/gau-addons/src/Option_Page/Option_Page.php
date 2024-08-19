<?php

namespace Addons\Option_Page;

use Addons\Base\Singleton;

\defined( 'ABSPATH' ) || die;

final class Option_Page {

	use Singleton;

	/** ----------------------------------------------- */

	/**
	 * @return void
	 */
	public function _gau_menu_callback(): void {
		?>
        <div class="wrap" id="_container">
            <form id="_settings_form" method="post" enctype="multipart/form-data">

				<?php $nonce_field = wp_nonce_field( '_wpnonce_settings_form_' . get_current_user_id() ); ?>

                <div id="main" class="filter-tabs clearfix">

					<?php include __DIR__ . '/options_menu.php'; ?>
					<?php include __DIR__ . '/options_content.php'; ?>

                </div>
            </form>
        </div>
		<?php
    }

	/** ----------------------------------------------- */

	/**
	 * @return void
	 */
	public function _gau_server_info_callback(): void {
		?>
		<div class="wrap">
			<div id="main">
				<h2 class="hide-text"></h2>
				<div class="server-info-body">
					<h2><?php echo __( 'Server info', ADDONS_TEXT_DOMAIN ) ?></h2>
					<p class="desc"><?php echo __( 'System configuration information', ADDONS_TEXT_DOMAIN ) ?></p>
					<div class="server-info-inner code">
						<ul>
							<li><?php echo sprintf( '<span>Platform:</span> %s', php_uname() ); ?></li>

							<?php if ( $server_software = $_SERVER['SERVER_SOFTWARE'] ?? null ) : ?>
								<li><?php echo sprintf( '<span>SERVER:</span> %s', $server_software ); ?></li>
							<?php endif; ?>

							<li><?php echo sprintf( '<span>PHP version:</span> %s', PHP_VERSION ); ?></li>
							<li><?php echo sprintf( '<span>WordPress version:</span> %s', get_bloginfo( 'version' ) ); ?></li>
							<li><?php echo sprintf( '<span>WordPress multisite:</span> %s', ( is_multisite() ? 'Yes' : 'No' ) ); ?></li>
							<?php
							$openssl_status = __( 'Available', ADDONS_TEXT_DOMAIN );
							$openssl_text   = '';
							if ( ! defined( 'OPENSSL_ALGO_SHA1' ) && ! extension_loaded( 'openssl' ) ) {
								$openssl_status = __( 'Not available', ADDONS_TEXT_DOMAIN );
								$openssl_text   = __( ' (openssl extension is required in order to use any kind of encryption like TLS or SSL)', ADDONS_TEXT_DOMAIN );
							}
							?>
							<li><?php echo sprintf( '<span>openssl:</span> %s%s', $openssl_status, $openssl_text ); ?></li>
							<li><?php echo sprintf( '<span>allow_url_fopen:</span> %s', ( ini_get( 'allow_url_fopen' ) ? __( 'Enabled', ADDONS_TEXT_DOMAIN ) : __( 'Disabled', ADDONS_TEXT_DOMAIN ) ) ); ?></li>
							<?php
							$stream_socket_client_status = __( 'Not Available', ADDONS_TEXT_DOMAIN );
							$fsockopen_status            = __( 'Not Available', ADDONS_TEXT_DOMAIN );
							$socket_enabled              = false;

							if ( function_exists( 'stream_socket_client' ) ) {
								$stream_socket_client_status = __( 'Available', ADDONS_TEXT_DOMAIN );
								$socket_enabled              = true;
							}
							if ( function_exists( 'fsockopen' ) ) {
								$fsockopen_status = __( 'Available', ADDONS_TEXT_DOMAIN );
								$socket_enabled   = true;
							}

							$socket_text = '';
							if ( ! $socket_enabled ) {
								$socket_text = __( ' (In order to make a SMTP connection your server needs to have either stream_socket_client or fsockopen)', ADDONS_TEXT_DOMAIN );
							}
							?>
							<li><?php echo sprintf( '<span>stream_socket_client:</span> %s', $stream_socket_client_status ); ?></li>
							<li><?php echo sprintf( '<span>fsockopen:</span> %s%s', $fsockopen_status, $socket_text ); ?></li>

							<?php if ( $agent = $_SERVER['HTTP_USER_AGENT'] ?? null ) : ?>
								<li><?php echo sprintf( '<span>User agent:</span> %s', $agent ); ?></li>
							<?php endif; ?>

							<li><?php echo sprintf( '<span>IP:</span> %s', \ip_address() ); ?></li>
						</ul>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}
