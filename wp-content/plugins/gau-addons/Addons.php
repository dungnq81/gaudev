<?php

use Addons\Base\Singleton;

use Addons\Aspect_Ratio\Aspect_Ratio;
use Addons\Smtp\SMTP;
use Addons\Base_Slug\Base_Slug;
use Addons\Custom_Css\Custom_Css;
use Addons\Custom_Email\Custom_Email;
use Addons\Custom_Script\Custom_Script;
use Addons\Custom_Sorting\Custom_Sorting;
use Addons\Editor\Editor;
use Addons\Login_Security\Login_Security;
use Addons\Optimizer\Font\Font;
use Addons\Optimizer\Minifier\Minify_Html;
use Addons\Optimizer\Optimizer;
use Addons\Option_Page\Option_Page;
use Addons\Security\Security;
use Addons\Woocommerce\WooCommerce;
use Addons\Third_Party\RankMath;
use Addons\Third_Party\WpRocket;

\defined( 'ABSPATH' ) || die;

/**
 * Addons Class
 *
 * @author Gaudev Team
 */
final class Addons {

	use Singleton;

	/**
	 * @var mixed|false|null
	 */
	public mixed $optimizer_options;

	/** ----------------------------------------------- */

	private function init(): void {
		add_action( 'plugins_loaded', [ &$this, 'i18n' ], 1 );
		add_action( 'plugins_loaded', [ &$this, 'plugins_loaded' ], 11 );

		add_action( 'admin_enqueue_scripts', [ &$this, 'admin_enqueue_scripts' ], 39, 1 );

		// Parser
		$this->optimizer_options = get_option( 'optimizer__options', [] );
		$this->_output_parser();
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

		( Option_Page::get_instance() );
		( Custom_Css::get_instance() );
		( Custom_Script::get_instance() );
		( WooCommerce::get_instance() );
		( Custom_Sorting::get_instance() );
		( Custom_Email::get_instance() );
		( Base_Slug::get_instance() );
		( Login_Security::get_instance() );
		( Security::get_instance() );
		( Optimizer::get_instance() );
		( Editor::get_instance() );
		( SMTP::get_instance() );
		( Aspect_Ratio::get_instance() );

		check_plugin_active( 'wp-rocket/wp-rocket.php' ) && WpRocket::get_instance();
		check_plugin_active( 'seo-by-rank-math/rank-math.php' ) && RankMath::get_instance();

		//( Faker::get_instance() );
	}

	/** ----------------------------------------------- */

	/**
	 * @param $hook
	 *
	 * @return void
	 */
	public function admin_enqueue_scripts( $hook ): void {
		$version = ADDONS_VERSION;
		if ( WP_DEBUG ) {
			$version = date( 'YmdHis', current_time( 'U', 0 ) );
		}

		wp_enqueue_style( 'admin-addons-style', ADDONS_URL . 'assets/css/admin_addons.css', [], $version );
		wp_enqueue_script( 'admin-addons', ADDONS_URL . 'assets/js/admin_addons.js', [ 'jquery-core' ], $version, true );
		wp_script_add_data( 'admin-addons', 'defer', true );

		// options_enqueue_assets
		$allowed_pages = 'toplevel_page_gau-settings';
		if ( $allowed_pages === $hook ) {

			if ( wp_style_is( 'select2-style' ) ) {
				wp_dequeue_style( 'select2-style' );
			}
			if ( wp_script_is( 'select2', 'registered' ) ) {
				wp_deregister_script( 'select2' );
			}

			wp_enqueue_style( 'select2-style', ADDONS_URL . 'assets/css/plugins/select2.min.css', [], $version );
			wp_enqueue_script( 'select2', ADDONS_URL . 'assets/js/plugins/select2.js', [ 'jquery-core' ], $version, true );
			wp_script_add_data( 'select2', 'defer', true );

			$codemirror_settings = [
				'codemirror_css'  => wp_enqueue_code_editor( [ 'type' => 'text/css' ] ),
				'codemirror_html' => wp_enqueue_code_editor( [ 'type' => 'text/html' ] ),
			];

			wp_enqueue_style( 'wp-codemirror' );
			wp_localize_script( 'admin-addons', 'codemirror_settings', $codemirror_settings );
		}
	}

	/** ----------------------------------------------- */

	/**
	 * @return void
	 */
	private function _output_parser(): void {
		if ( defined( 'WP_CLI' ) || is_admin() ) {
			return;
		}

		$minify_html   = $this->optimizer_options['minify_html'] ?? 0;
		$font_optimize = $this->optimizer_options['font_optimize'] ?? 0;
		$font_preload  = isset( $this->optimizer_options['font_preload'] ) ? implode( PHP_EOL, $this->optimizer_options['font_preload'] ) : '';
		$dns_prefetch  = isset( $this->optimizer_options['dns_prefetch'] ) ? implode( PHP_EOL, $this->optimizer_options['dns_prefetch'] ) : '';

		if ( ! empty( $minify_html ) ||
		     ! empty( $font_optimize ) ||
		     ! empty( $font_preload ) ||
		     ! empty( $dns_prefetch )
		) {
			add_action( 'wp_loaded', [ &$this, 'start_bufffer' ] );
			add_action( 'shutdown', [ &$this, 'end_buffer' ] );
		}
	}

	/** ----------------------------------------------- */

	/**
	 * @return void
	 */
	public function start_bufffer(): void {
		ob_start( [ &$this, 'run' ] );
	}

	/** ----------------------------------------------- */

	/**
	 * @return void
	 */
	public function end_buffer(): void {
		if ( ob_get_length() ) {
			ob_end_flush();
		}
	}

	/** ----------------------------------------------- */

	/**
	 * @param string $html
	 *
	 * @return string
	 */
	public function run( string $html ): string {
		if ( ! preg_match( '/<\/html>/i', $html ) ) {
			return $html;
		}

		// Do not run optimization if amp is active, the page is an xml or feed.
		if ( is_amp_enabled( $html ) ||
		     is_xml( $html ) ||
		     is_feed()
		) {
			return $html;
		}

		return $this->_optimize_for_visitors( $html );
	}

	/** ----------------------------------------------- */

	/**
	 * @param $html
	 *
	 * @return string
	 */
	private function _optimize_for_visitors( $html ): string {

		$html = ( Font::get_instance() )->run( $html );
		$html = $this->_dns_prefetch( $html );

		$minify_html = $this->optimizer_options['minify_html'] ?? 0;
		if ( ! empty( $minify_html ) ) {
			$html = Minify_Html::minify( $html );
		}

		return $html;
	}

	/** ----------------------------------------------- */

	/**
	 * @param $html
	 *
	 * @return array|mixed|string|string[]
	 */
	private function _dns_prefetch( $html ): mixed {

		// Check if there are any urls inserted by the user.
		$urls = $this->optimizer_options['dns_prefetch'] ?? false;

		// Return, if no url's are set by the user.
		if ( empty( $urls ) ) {
			return $html;
		}

		$new_html = '';
		foreach ( $urls as $url ) {

			// Replace the protocol with //.
			$url_without_protocol = preg_replace( '~(?:(?:https?:)?(?:\/\/)(?:www\.|(?!www)))?((?:.*?)\.(?:.*))~', '//$1', $url );

			$new_html .= '<link rel="dns-prefetch" href="' . $url_without_protocol . '" />';
		}

		return str_replace( '</head>', $new_html . '</head>', $html );
	}
}
