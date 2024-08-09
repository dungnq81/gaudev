<?php

namespace Themes;

use Cores\CSS;
use Cores\Helper;
use Cores\Traits\Singleton;

use Libs\Login_Security\Login_Security;

use Plugins\ACF;
use Plugins\CF7;
use Plugins\Elementor;
use Plugins\PLL;
use Plugins\WooCommerce;

\defined( 'ABSPATH' ) || die;

/**
 * Theme Class
 *
 * @author HD
 */
final class Theme {

	use Singleton;

	// --------------------------------------------------

	private function init(): void {

		// plugins_loaded -> after_setup_theme -> init -> widgets_init -> wp_loaded -> admin_menu -> admin_init ...

		// Login
		$this->_admin_login();

		add_action( 'after_setup_theme', [ &$this, 'i18n' ], 1 );
		add_action( 'after_setup_theme', [ &$this, 'after_setup_theme' ], 10 );
		add_action( 'after_setup_theme', [ &$this, 'setup' ], 10 );
		add_action( 'after_setup_theme', [ &$this, 'plugins_setup' ], 10 );

		/** Enqueue Scripts */
		add_action( 'wp_enqueue_scripts', [ &$this, 'wp_enqueue_scripts' ], 10 );

		/** Restrict admin install plugin */
		add_filter( 'user_has_cap', [ &$this, 'restrict_admin_plugin_install' ], 10, 3 );

		/** Widgets WordPress */
		add_action( 'widgets_init', [ &$this, 'unregister_widgets' ], 12 );
		add_action( 'widgets_init', [ &$this, 'register_widgets' ], 12 );
	}

	// --------------------------------------------------

	/**
	 * Load localization file
	 *
	 * @return void
	 */
	public function i18n(): void {

		/**
		 * Make theme available for translation.
		 * Translations can be filed at WordPress.org.
		 * See: https://translate.wordpress.org/projects/wp-themes/hello-elementor
		 */
		load_theme_textdomain( TEXT_DOMAIN, trailingslashit( WP_LANG_DIR ) . 'themes/' );
		load_theme_textdomain( TEXT_DOMAIN, get_template_directory() . '/languages' );
		load_theme_textdomain( TEXT_DOMAIN, get_stylesheet_directory() . '/languages' );
	}

	// --------------------------------------------------

	/**
	 * Sets up theme defaults and register support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook.
	 * The init hook is too late for some features, such
	 * as indicating support for post-thumbnails.
	 */
	public function after_setup_theme(): void {

		/** Add theme support for various features. */
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'title-tag' );
		add_theme_support( 'html5', [
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'script',
		] );

		add_theme_support( 'customize-selective-refresh-widgets' );
		add_theme_support( 'align-wide' );
		add_theme_support( 'wp-block-styles' );

		/** This theme styles the visual editor to resemble the theme style. */
		add_editor_style();

		/** Remove Template Editor support until WP 5.9 since more Theme Blocks are going to be introduced. */
		remove_theme_support( 'block-templates' );

		/** Enable excerpt to page, page-attributes to post */
		add_post_type_support( 'page', [ 'excerpt' ] );
		add_post_type_support( 'post', [ 'page-attributes' ] );

		/** Set default values for the upload media box */
		update_option( 'image_default_align', 'center' );
		update_option( 'image_default_size', 'large' );

		/**
		 * Add support for core custom logo.
		 *
		 * @link https://codex.wordpress.org/Theme_Logo
		 */
		$logo_height = 240;
		$logo_width  = 240;

		add_theme_support(
			'custom-logo',
			apply_filters(
				'custom_logo_args',
				[
					'height'               => $logo_height,
					'width'                => $logo_width,
					'flex-height'          => true,
					'flex-width'           => true,
					'header-text'          => '',
					'unlink-homepage-logo' => true,
				]
			)
		);
	}

	// --------------------------------------------------

	/**
	 * Init function
	 *
	 * @return void
	 */
	public function setup(): void {
		if ( is_admin() ) {
			( Admin::get_instance() );
		}

		( Login_Security::get_instance() );
		( Customizer::get_instance() );
		( Optimizer::get_instance() );
		( Shortcode::get_instance() );

		/** template hooks */
		$this->_hooks();

		// folders
		$dirs = [
			'template_structures' => THEME_PATH . 'template-structures',
			'templates'           => THEME_PATH . 'templates',
			'template_parts'      => THEME_PATH . 'template-parts',
			'storage'             => THEME_PATH . 'storage',
			'languages'           => THEME_PATH . 'languages',

			'inc_ajax'   => INC_PATH . 'ajax',
			'inc_blocks' => INC_PATH . 'blocks',
		];

		foreach ( $dirs as $dir => $path ) {
			Helper::createDirectory( $path );

			// autoload template_structures & ajax files
			if ( in_array( $dir, [ 'template_structures', 'inc_ajax' ] ) ) {
				Helper::FQN_Load( $path, true );
			}
		}
	}

	// --------------------------------------------------

	/**
	 * @return void
	 */
	public function plugins_setup(): void {

		Helper::isWoocommerceActive() && WooCommerce\WooCommerce::get_instance();
		Helper::isAcfActive() && ACF\ACF::get_instance();
		Helper::isCf7Active() && CF7::get_instance();
		Helper::isPolylangActive() && PLL::get_instance();
	}

	// --------------------------------------------------

	/**
	 * Registers a WP_Widget widget
	 *
	 * @return void
	 */
	public function register_widgets(): void {
		$widgets_dir = INC_PATH . 'src/Widgets';
		$FQN         = '\\Widgets\\';

		Helper::createDirectory( $widgets_dir );
		Helper::FQN_Load( $widgets_dir, false, true, $FQN, true );
	}

	// --------------------------------------------------

	/**
	 * Unregisters a WP_Widget widget
	 *
	 * @return void
	 */
	public function unregister_widgets(): void {
		unregister_widget( 'WP_Widget_Search' );
		unregister_widget( 'WP_Widget_Recent_Posts' );

		// Removes the styling added to the header for recent comments
		global $wp_widget_factory;

		remove_action( 'wp_head', [
			$wp_widget_factory->widgets['WP_Widget_Recent_Comments'],
			'recent_comments_style'
		] );
	}

	// --------------------------------------------------

	/**
	 * Enqueue scripts and styles
	 *
	 * @return void
	 */
	public function wp_enqueue_scripts(): void {

		$version = THEME_VERSION;
		if ( WP_DEBUG ) {
			$version = date( 'YmdHi', current_time( 'U', 0 ) );
		}

		// swiper-js
		if ( ! wp_style_is( 'swiper-style' ) ) {
			wp_enqueue_style( "swiper-style", ASSETS_URL . "css/plugins/swiper.css", [], $version );
		}
		if ( ! wp_script_is( 'swiper', 'registered' ) ) {
			wp_register_script( "swiper", ASSETS_URL . "js/plugins/swiper.js", [], $version, true );
			wp_script_add_data( "swiper", "defer", true );
		}

		// select 2
		if ( ! wp_style_is( 'select2-style' ) ) {
			wp_enqueue_style( 'select2-style', ASSETS_URL . 'css/plugins/select2.min.css' );
		}

		if ( ! wp_script_is( 'select2', 'registered' ) ) {
			wp_register_script( 'select2', ASSETS_URL . 'js/plugins/select2.full.min.js', [ "jquery-core" ], false, true );
			wp_script_add_data( "select2", "defer", true );
		}

		// --------------------------------------------------

		/** Stylesheet */
		wp_register_style( "plugins-style", ASSETS_URL . "css/plugins.css", [], $version );
		wp_enqueue_style( "app-style", ASSETS_URL . "css/app.css", [ "plugins-style" ], $version );

		/** Scripts */
		wp_enqueue_script( "app", ASSETS_URL . "js/app.js", [ "jquery-core", "swiper", "select2" ], $version, true );
		wp_script_add_data( "app", "defer", true );

		wp_enqueue_script( "back-to-top", ASSETS_URL . "js/plugins/back-to-top.js", [], false, true );
		wp_enqueue_script( "social-share", ASSETS_URL . "js/plugins/social-share.js", [], '0.0.3', true );

		wp_enqueue_style( "responsive-style", ASSETS_URL . "css/responsive.css", [ "app-style" ], $version );
		wp_enqueue_style( "chart-style", ASSETS_URL . "css/plugins/chart.css", [ "app-style" ], $version );
		wp_enqueue_script( "chart", ASSETS_URL . "js/plugins/chart.js", [ "app" ], $version, true );

		//wp_enqueue_style( "fonts-style", ASSETS_URL . "css/fonts.css", [], $version );

		/** Inline Js */
		$recaptcha_options = Helper::getOption( 'recaptcha__options' );

		$recaptcha_v2_site_key   = $recaptcha_options['recaptcha_v2_site_key'] ?? '';
		$recaptcha_v2_secret_key = $recaptcha_options['recaptcha_v2_secret_key'] ?? '';

		$recaptcha_v3_site_key   = $recaptcha_options['recaptcha_v3_site_key'] ?? '';
		$recaptcha_v3_secret_key = $recaptcha_options['recaptcha_v3_secret_key'] ?? '';

		$l10n = [
			'ajaxUrl'               => esc_js( admin_url( 'admin-ajax.php', 'relative' ) ),
			'baseUrl'               => esc_js( untrailingslashit( site_url() ) . '/' ),
			'themeUrl'              => esc_js( THEME_URL ),
			'wpnonce_ajax_csrf'     => wp_create_nonce( '_wpnonce_ajax_csrf' ),
			'tracking'              => ( defined( 'TRACKING' ) && TRACKING ) ? 1 : 0,
			'recaptcha_v2_site_key' => ( $recaptcha_v2_site_key && $recaptcha_v2_secret_key ) ? esc_js( $recaptcha_v2_site_key ) : '',
			'recaptcha_v3_site_key' => ( $recaptcha_v3_site_key && $recaptcha_v3_secret_key ) ? esc_js( $recaptcha_v3_site_key ) : '',
			'locale'                => esc_js( get_locale() ),
			'lang'                  => esc_js( Helper::getLang() ),
			'lg'                    => [
				'view_more'      => __( 'View more', TEXT_DOMAIN ),
				'view_detail'    => __( 'Detail', TEXT_DOMAIN ),
				'added_to_cart'  => __( 'Added to cart', TEXT_DOMAIN ),
				'confirm_remove' => __( 'Bạn muốn xóa sản phẩm này?', TEXT_DOMAIN ),
			],
		];
		wp_localize_script( 'jquery-core', TEXT_DOMAIN, $l10n );

		/** Comments */
		if ( is_singular() && comments_open() && Helper::getOption( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		} else {
			wp_dequeue_script( 'comment-reply' );
		}
	}

	// --------------------------------------------------

	/**
	 * @param $allcaps
	 * @param $caps
	 * @param $args
	 *
	 * @return mixed
	 */
	public function restrict_admin_plugin_install( $allcaps, $caps, $args ): mixed {
		$allowed_users_ids_install_plugins = Helper::filter_setting_options( 'allowed_users_ids_install_plugins', [] );

		// Get the current user ID
		$user_id = get_current_user_id();

		// Check if the current user is in the allowed users list
		if ( $user_id && in_array( $user_id, $allowed_users_ids_install_plugins, false ) ) {
			return $allcaps;
		}

		// If user is not allowed, remove the capability to install plugins
		if ( isset( $allcaps['activate_plugins'] ) ) {
			unset( $allcaps['install_plugins'] );
		}

		return $allcaps;
	}

	// --------------------------------------------------

	private function _hooks(): void {
		/**
		 * Use the is-active class of ZURB Foundation on wp_list_pages output.
		 * From required+ Foundation http://themes.required.ch.
		 */
		add_filter( 'wp_list_pages', static function ( $input ) {
			return str_replace( 'current_page_item', 'current_page_item is-active', $input );
		}, 10, 2 );

		/** Add support for buttons in the top-bar menu */
		add_filter( 'wp_nav_menu', static function ( $ul_class ) {
			$find    = [ '/<a rel="button"/', '/<a title=".*?" rel="button"/' ];
			$replace = [ '<a rel="button" class="button"', '<a rel="button" class="button"' ];

			return preg_replace( $find, $replace, $ul_class, 1 );
		} );

		// -------------------------------------------------------------
		// images sizes
		// -------------------------------------------------------------

		/**
		 * thumbnail (576x0)
		 * medium (768x0)
		 * large (1024x0)
		 *
		 * small-thumbnail (150x150)
		 * widescreen (1920x9999)
		 * post-thumbnail (1280x9999)
		 */

		/** Custom thumb */
		add_image_size( 'small-thumbnail', 150, 150, true );
		add_image_size( 'widescreen', 1920, 9999, false );
		add_image_size( 'post-thumbnail', 1200, 9999, false );

		/** Disable unwanted image sizes */
		add_filter( 'intermediate_image_sizes_advanced', static function ( $sizes ) {
			unset( $sizes['medium_large'], $sizes['1536x1536'], $sizes['2048x2048'] );

			// disable 2x medium-large size
			// disable 2x large size

			return $sizes;
		} );

		/** Disable scaled */
		//add_filter( 'big_image_size_threshold', '__return_false' );

		/** Disable other sizes */
		add_action( 'init', static function () {
			remove_image_size( '1536x1536' ); // disable 2x medium-large size
			remove_image_size( '2048x2048' ); // disable 2x large size
		} );

		// ------------------------------------------

		add_filter( 'post_thumbnail_html', static function ( $html ) {
			return preg_replace( '/(<img[^>]+)(style=\"[^\"]+\")([^>]+)(>)/', '${1}${3}${4}', $html );
		}, 10, 1 );

//		add_filter( 'image_send_to_editor', function ( $html ) {
//			return preg_replace( '/(<img[^>]+)(style=\"[^\"]+\")([^>]+)(>)/', '${1}${3}${4}', $html );
//		}, 10, 1 );

		add_filter( 'the_content', static function ( $html ) {
			return preg_replace( '/(<img[^>]+)(style=\"[^\"]+\")([^>]+)(>)/', '${1}${3}${4}', $html );
		}, 10, 1 );
	}

	// --------------------------------------------------

	/**
	 * @return void
	 */
	private function _admin_login(): void {
		add_action( 'login_enqueue_scripts', [ &$this, 'login_enqueue_script' ], 31 );

		// Changing the alt text on the logo to show your site name
		add_filter( 'login_headertext', static function () {
			$headertext = Helper::getThemeMod( 'login_page_headertext_setting' );

			return $headertext ?: get_bloginfo( 'name' );
		} );

		// Changing the logo link from WordPress.org to your site
		add_filter( 'login_headerurl', static function () {
			$headerurl = Helper::getThemeMod( 'login_page_headerurl_setting' );

			return $headerurl ?: Helper::home();
		} );
	}

	// --------------------------------------------------

	/**
	 * @retun void
	 */
	public function login_enqueue_script(): void {
		wp_enqueue_style( "login-style", THEME_URL . "assets/css/admin.css", [], THEME_VERSION );
		wp_enqueue_script( "login", THEME_URL . "assets/js/login.js", [ "jquery" ], THEME_VERSION, true );

		//$default_logo    = THEME_URL . "storage/img/logo.png";
		//$default_logo_bg = THEME_URL . "storage/img/login-bg.jpg";

		$default_logo    = '';
		$default_logo_bg = '';

		// script/style
		$logo          = ! empty( $logo = Helper::getThemeMod( 'login_page_logo_setting' ) ) ? $logo : $default_logo;
		$logo_bg       = ! empty( $logo_bg = Helper::getThemeMod( 'login_page_bgimage_setting' ) ) ? $logo_bg : $default_logo_bg;
		$logo_bg_color = Helper::getThemeMod( 'login_page_bgcolor_setting' );

		$css = CSS::get_instance();

		if ( $logo_bg ) {
			$css->set_selector( 'body.login' );
			$css->add_property( 'background-image', 'url(' . $logo_bg . ')' );
		}

		if ( $logo_bg_color ) {
			$css->set_selector( 'body.login' );
			$css->add_property( 'background-color', $logo_bg_color );

			$css->set_selector( 'body.login:before' );
			$css->add_property( 'background', 'none' );
			$css->add_property( 'opacity', 1 );
		}

		$css->set_selector( 'body.login #login h1 a' );
		if ( $logo ) {
			$css->add_property( 'background-image', 'url(' . $logo . ')' );
		} //else {
		//$css->add_property( 'background-image', 'unset' );
		//}

		if ( $css->css_output() ) {
			wp_add_inline_style( 'login-style', $css->css_output() );
		}
	}
}
