<?php

namespace Addons\Third_Party;

use Addons\Base\Singleton;

\defined( 'ABSPATH' ) || die;

final class Faker {

	use Singleton;

	// -------------------------------------------------------------

	private function init(): void {
		$this->_acf_faker();
	}

	// -------------------------------------------------------------

	/**
	 * @return void
	 */
	private function _acf_faker(): void {
		if ( check_plugin_active( 'advanced-custom-fields-pro/acf.php' ) ) {
			$lic_data = base64_encode(
				maybe_serialize(
					[
						'key' => '********',
						'url' => home_url(),
					]
				)
			);

			update_option( 'acf_pro_license', $lic_data );
			update_option( 'acf_pro_license_status', [ 'status' => 'active', 'next_check' => time() * 9 ] );
			add_action( 'init', static function () {
				add_filter( 'pre_http_request', static function ( $pre, $url, $request_args ) {
					if ( is_string( $url ) && str_contains( $url, 'https://connect.advancedcustomfields.com/' ) ) {
						return [ 'response' => [ 'code' => 200, 'message' => 'OK' ] ];
					}

					return $pre;
				}, 10, 3 );
			} );
		}
	}
}
