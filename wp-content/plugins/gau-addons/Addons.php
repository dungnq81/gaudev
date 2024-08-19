<?php

use Addons\Base\Singleton;

use Addons\Activity_Log\Activity_Log;
use Addons\Editor\Editor;
use Addons\Option_Page\Option_Page;
use Addons\Third_Party;

\defined( 'ABSPATH' ) || die;

/**
 * Addons Class
 *
 * @author Gaudev Team
 */
final class Addons {

	use Singleton;

	private Option_Page $option_page;

	/** ----------------------------------------------- */

	private function init(): void {
		add_action( 'plugins_loaded', [ &$this, 'i18n' ], 1 );
		add_action( 'plugins_loaded', [ &$this, 'plugins_loaded' ], 11 );

		add_action( 'admin_enqueue_scripts', [ &$this, 'admin_enqueue_scripts' ], 39 );

		add_action( 'admin_menu', [ &$this, 'admin_menu' ] );
		add_filter( 'menu_order', [ &$this, 'options_reorder_submenu' ] );

		add_filter( 'custom_menu_order', '__return_true' );
	}

	/** ----------------------------------------------- */

	/**
	 * Load localization file
	 *
	 * @return void
	 */
	public function i18n(): void {
		load_plugin_textdomain( ADDONS_TEXT_DOMAIN );
		load_plugin_textdomain( ADDONS_TEXT_DOMAIN, false, ADDONS_PATH . 'languages' );
	}

	/** ----------------------------------------------- */

	/**
	 * @return void
	 */
	public function plugins_loaded(): void {

		$this->option_page = ( Option_Page::get_instance() );

		( Editor::get_instance() );
		( Activity_Log::get_instance() );


		//( Third_Party\Faker::get_instance() );

		check_plugin_active( 'wp-rocket/wp-rocket.php' ) && Third_Party\WpRocket::get_instance();
		check_plugin_active( 'seo-by-rank-math/rank-math.php' ) && Third_Party\RankMath::get_instance();
	}

	/** ----------------------------------------------- */

	/**
	 * @return void
	 */
	public function admin_enqueue_scripts(): void {
		if ( ! wp_style_is( 'select2-style' ) ) {
			wp_enqueue_style( 'select2-style', ADDONS_URL . 'assets/css/plugins/select2.min.css' );
		}

		if ( ! wp_script_is( 'select2', 'registered' ) ) {
			wp_register_script( 'select2', ADDONS_URL . 'assets/js/plugins/select2.full.min.js', [ "jquery-core" ], false, true );
			wp_script_add_data( "select2", "defer", true );
		}

		wp_enqueue_style( 'admin-addons-style', ADDONS_URL . 'assets/css/admin_addons.css', [], ADDONS_VERSION );
		wp_enqueue_script( 'admin-addons', ADDONS_URL . 'assets/js/admin_addons.js', [ 'select2' ], ADDONS_VERSION, true );
	}

	/** ----------------------------------------------- */

	/**
	 * @return void
	 */
	public function admin_menu(): void {
		$menu_setting = add_menu_page(
			__( 'Gau Settings', ADDONS_TEXT_DOMAIN ),
			__( 'Gau', ADDONS_TEXT_DOMAIN ),
			'manage_options',
			'gau-settings',
			[ &$this->option_page, '_gau_menu_callback' ],
			'dashicons-admin-settings',
			80
		);

		$submenu_customize = add_submenu_page(
			'gau-settings',
			__( 'Advanced', ADDONS_TEXT_DOMAIN ),
			__( 'Advanced', ADDONS_TEXT_DOMAIN ),
			'manage_options',
			'customize.php'
		);

		$submenu_info = add_submenu_page(
			'gau-settings',
			__( 'Server Info', ADDONS_TEXT_DOMAIN ),
			__( 'Server Info', ADDONS_TEXT_DOMAIN ),
			'manage_options',
			'server-info',
			[ &$this->option_page, '_gau_server_info_callback', ]
		);
	}

	/** ----------------------------------------------- */

	/**
	 * Reorder the submenu pages.
	 *
	 * @param array $menu_order The WP menu order.
	 */
	public function options_reorder_submenu( array $menu_order ): array {

		// Load the global submenu.
		global $submenu;

		if ( empty( $submenu['gau-settings'] ) ) {
			return $menu_order;
		}

		// Change menu title
		$submenu['gau-settings'][0][0] = __( 'Settings', ADDONS_TEXT_DOMAIN );

		return $menu_order;
	}

	/** ----------------------------------------------- */
}
