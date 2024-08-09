<?php

namespace Libs\Login_Security;

use Cores\Helper;
use Cores\Traits\Singleton;

\defined( 'ABSPATH' ) || die;

final class Custom_Login {

	use Singleton;

	// --------------------------------------------------

	private function init(): void {
		add_action( 'login_init', [ &$this, 'login_ips_access' ], PHP_INT_MIN );
	}

	// --------------------------------------------------

	/**
	 * Check blocked & restrict login
	 *
	 * @return true|void
	 */
	public function login_ips_access() {
		$_login_security          = Helper::filterSettingOptions( 'login_security', false );
		$_custom_security_options = Helper::getOption( 'login_security__options', false );

		$custom_restrict_ips = $_custom_security_options['login_ips_access'] ?? [];
		$custom_blocked_ips  = $_custom_security_options['disable_ips_access'] ?? [];

		$allowed_ips = array_filter( array_merge( $_login_security['allowlist_ips_login_access'], (array) $custom_restrict_ips ) );
		$blocked_ips = array_filter( array_merge( $_login_security['blocked_ips_login_access'], (array) $custom_blocked_ips ) );

		unset( $custom_restrict_ips, $custom_blocked_ips );

		// Bail if the allowed ip list is empty.
		if ( empty( $allowed_ips ) && empty( $blocked_ips ) ) {
			return true;
		}

		// Check if the current IP is in the allowed list, block all other IPs not in the list.
		if ( ! empty( $allowed_ips ) ) {
			foreach ( $allowed_ips as $allowed_ip ) {
				if ( Helper::ipInRange( Helper::getIpAddress(), $allowed_ip ) ) {
					return true;
				}
			}

			// Update the total blocked logins counter.
			update_option( '_security_total_blocked_logins', get_option( '_security_total_blocked_logins', 0 ) + 1 );

			error_log( 'Restricted login page: access currently not permitted - ' . Helper::getIpAddress() );
			wp_die(
				esc_html__( 'You don’t have access to this page. Please contact the administrator of this website for further assistance.', TEXT_DOMAIN ),
				esc_html__( 'Restricted access', TEXT_DOMAIN ),
				[
					'hd_error'      => true,
					'response'      => 403,
					'blocked_login' => true,
				]
			);
		}

		// Block all IPs in the list.
		if ( ! empty( $blocked_ips ) ) {
			foreach ( $blocked_ips as $blocked_ip ) {
				if ( Helper::ipInRange( Helper::getIpAddress(), $blocked_ip ) ) {

					// Update the total blocked logins counter.
					update_option( '_security_total_blocked_logins', get_option( '_security_total_blocked_logins', 0 ) + 1 );

					error_log( 'Restricted login page: access currently not permitted - ' . Helper::getIpAddress() );
					wp_die(
						esc_html__( 'You don’t have access to this page. Please contact the administrator of this website for further assistance.', TEXT_DOMAIN ),
						esc_html__( 'Restricted access', TEXT_DOMAIN ),
						[
							'hd_error'      => true,
							'response'      => 403,
							'blocked_login' => true,
						]
					);
				}
			}
		}
	}
}
