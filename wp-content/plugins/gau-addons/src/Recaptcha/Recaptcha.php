<?php

namespace Addons\Recaptcha;

use Addons\Base\Singleton;

\defined( 'ABSPATH' ) || die;

$recaptcha_options = get_option( 'recaptcha__options' );

define( "GOOGLE_CAPTCHA_V2_SITE_KEY", $recaptcha_options['recaptcha_v2_site_key'] ?? '' );
define( "GOOGLE_CAPTCHA_V2_SECRET_KEY", $recaptcha_options['recaptcha_v2_secret_key'] ?? '' );

define( "GOOGLE_CAPTCHA_V3_SITE_KEY", $recaptcha_options['recaptcha_v3_site_key'] ?? '' );
define( "GOOGLE_CAPTCHA_V3_SECRET_KEY", $recaptcha_options['recaptcha_v3_secret_key'] ?? '' );
define( "GOOGLE_CAPTCHA_V3_SCORE", $recaptcha_options['recaptcha_v3_score'] ?? '0.5' );

define( "GOOGLE_CAPTCHA_GLOBAL", $recaptcha_options['recaptcha_global'] ?? false );

final class Recaptcha {

	use Singleton;

	// --------------------------------------------------

	public array $forms = [];

	private function init(): void {
		$default_forms = [

			// default
			'login_form' => [ 'form_name' => __( 'Login Form', ADDONS_TEXT_DOMAIN ) ],
//			'registration_form'         => [ 'form_name' => __( 'Registration Form', ADDONS_TEXT_DOMAIN ) ],
//			'reset_pwd_form'            => [ 'form_name' => __( 'Reset Password Form', ADDONS_TEXT_DOMAIN ) ],
//			'password_form'             => [ 'form_name' => __( 'Protected Post Password Form', ADDONS_TEXT_DOMAIN ) ],
//			'comments_form'             => [ 'form_name' => __( 'Comments Form', ADDONS_TEXT_DOMAIN ) ],
//
//			// woocommerce
//			'woocommerce_login'         => [ 'form_name' => __( 'WooCommerce Login Form', ADDONS_TEXT_DOMAIN ) ],
//			'woocommerce_register'      => [ 'form_name' => __( 'WooCommerce Registration Form', ADDONS_TEXT_DOMAIN ) ],
//			'woocommerce_lost_password' => [ 'form_name' => __( 'WooCommerce Reset Password Form', ADDONS_TEXT_DOMAIN ) ],
//			'woocommerce_checkout'      => [ 'form_name' => __( 'WooCommerce Checkout Form', ADDONS_TEXT_DOMAIN ) ],
//
//			// other
//			'cf7'                       => [ 'form_name' => __( 'Contact Form 7', ADDONS_TEXT_DOMAIN ) ],
//			'wpforms'                   => [ 'form_name' => __( 'WPForms', ADDONS_TEXT_DOMAIN ) ],
//			'jetpack_contact_form'      => [ 'form_name' => __( 'Jetpack Contact Form', ADDONS_TEXT_DOMAIN ) ],
//			'mailchimp'                 => [ 'form_name' => __( 'MailChimp for Wordpress', ADDONS_TEXT_DOMAIN ) ],
//			'elementor_contact_form'    => [ 'form_name' => __( 'Elementor Contact Form', ADDONS_TEXT_DOMAIN ) ],
		];

		$custom_forms = apply_filters( 'recaptcha_custom_forms', [] );
		$this->forms  = apply_filters( 'recaptcha_forms', array_merge( $default_forms, $custom_forms ) );
	}

	// ------------------------------------------------------

	/**
	 * @param $render
	 *
	 * @return string
	 */
	public function get_api_url( $render ): string {
		$use_globally = GOOGLE_CAPTCHA_GLOBAL ? 'recaptcha.net' : 'google.com';

		if ( ! empty( $render ) ) {
			return sprintf( 'https://www.' . $use_globally . '/recaptcha/api.js?render=%s', $render );
		}

		return 'https://www.' . $use_globally . '/recaptcha/api.js';
	}

	// ------------------------------------------------------


}
