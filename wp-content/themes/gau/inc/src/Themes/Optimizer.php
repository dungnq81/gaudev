<?php

namespace Themes;

use Cores\Helper;
use Cores\Traits\Singleton;

/**
 * Optimizer Class
 *
 * @author Gaudev
 */

\defined( 'ABSPATH' ) || die;

final class Optimizer {

	use Singleton;

	// ------------------------------------------------------

	private function init(): void {

		$this->_cleanup();
		$this->_optimizer();
	}

	// ------------------------------------------------------
	// ------------------------------------------------------

	/**
	 * @return void
	 */
	private function _optimizer(): void {

		add_filter( 'script_loader_tag', [ &$this, 'script_loader_tag' ], 12, 3 );
		add_filter( 'style_loader_tag', [ &$this, 'style_loader_tag' ], 12, 2 );

		// Adding Shortcode in WordPress Using Custom HTML Widget
		add_filter( 'widget_text', 'do_shortcode' );
		add_filter( 'widget_text', 'shortcode_unautop' );

		add_action( 'wp_enqueue_scripts', [ &$this, 'enqueue' ], 11 );
		add_filter( 'posts_search', [ &$this, 'post_search_by_title' ], 500, 2 );
		//add_filter( 'posts_where', [ &$this, 'posts_title_filter' ], 499, 2 );

		// if not admin page
		if ( ! is_admin() ) {
			add_action( 'pre_get_posts', [ &$this, 'set_posts_per_page' ] );

			// only front-end
			if ( ! Helper::isLogin() ) {
				add_action( 'wp_print_footer_scripts', [ &$this, 'print_footer_scripts' ], 999 );
				add_action( 'wp_footer', [ &$this, 'deferred_scripts' ], 1000 );
			}
		}

		// Filters the rel values that are added to links with `target` attribute.
		add_filter( 'wp_targeted_link_rel', static function ( $rel, $link_target ) {
			$rel .= ' nofollow';
			return $rel;
		}, 999, 2 );

		// excerpt_more
		add_filter( 'excerpt_more', static function () {
			return ' ' . '&hellip;';
		} );

		// Remove logo admin bar
		add_action( 'wp_before_admin_bar_render', static function () {
			global $wp_admin_bar;
			$wp_admin_bar->remove_menu( 'wp-logo' );
		} );

		// Normalize upload filename
		add_filter( 'sanitize_file_name', static function ( $filename ) {
			return remove_accents( $filename );
		}, 10, 1 );

		// query_vars
		add_filter( 'query_vars', static function ( $vars ) {
			$vars[] = 'page';
			$vars[] = 'paged';

			return $vars;
		} );

		// Remove archive title prefix
		add_filter( 'get_the_archive_title_prefix', static function ( $prefix ) {
			return __return_empty_string();
		} );
	}

	// ------------------------------------------------------

	/**
	 * @return void
	 */
	public function enqueue(): void {
		wp_dequeue_style( 'classic-theme-styles' );
	}

	// ------------------------------------------------------

	/**
	 * Search only in post title or excerpt
	 *
	 * @param $search
	 * @param $wp_query
	 *
	 * @return mixed|string
	 */
	public function post_search_by_title( $search, $wp_query ): mixed {
		global $wpdb;

		if ( empty( $search ) ) {
			return $search; // skip processing – no search term in a query
		}

		$q = $wp_query->query_vars;
		$n = ! empty( $q['exact'] ) ? '' : '%';

		$search = $search_and = '';

		foreach ( (array) $q['search_terms'] as $term ) {
			$term = mb_strtolower( esc_sql( $wpdb->esc_like( $term ) ) );

			$like       = "LIKE CONCAT('{$n}', CONVERT('{$term}', BINARY), '{$n}')";
			$search     .= "{$search_and}(LOWER($wpdb->posts.post_title) {$like} OR LOWER($wpdb->posts.post_excerpt) {$like})";
			$search_and = " AND ";
		}

		if ( ! empty( $search ) ) {
			$search = " AND ({$search}) ";
			if ( ! is_user_logged_in() ) {
				$search .= " AND ($wpdb->posts.post_password = '') ";
			}
		}

		return $search;
	}

	// ------------------------------------------------------

	/**
	 * Search only in post-title - wp_query
	 *
	 * @param $where
	 * @param $wp_query
	 *
	 * @return mixed|string
	 */
//	public function posts_title_filter( $where, $wp_query ) {
//		global $wpdb;
//
//		if ( $search_term = $wp_query->get( 'title_filter' ) ) {
//			$term = esc_sql( $wpdb->esc_like( $search_term ) );
//			$where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . $term . '%\'';
//		}
//
//		return $where;
//	}

	// ------------------------------------------------------

	/**
	 * @param $query
	 */
	public function set_posts_per_page( $query ): void {
		if ( ! is_admin() ) {

			// get default value
			$posts_per_page_default = $posts_per_page = Helper::getOption( 'posts_per_page' );
			$posts_num_per_page_arr = Helper::filterSettingOptions( 'posts_num_per_page', [] );

			if ( ! empty( $posts_num_per_page_arr ) ) {
				$posts_per_page = min( $posts_num_per_page_arr );

				if ( isset( $_GET['pagenum'] ) ) {
					$pagenum = esc_sql( $_GET['pagenum'] );

					if ( in_array( $pagenum, $posts_num_per_page_arr, false ) ) {
						$posts_per_page = $pagenum;
					}

					if ( $pagenum > max( $posts_num_per_page_arr ) ) {
						$posts_per_page = max( $posts_num_per_page_arr );
					}
				}
			}

			if ( (int) $posts_per_page_default < (int) $posts_per_page ) {
				$query->set( 'posts_per_page', $posts_per_page );
			}
		}
	}

	// ------------------------------------------------------

	/**
	 * @return void
	 */
	public function print_footer_scripts(): void { ?>
		<script>document.documentElement.classList.remove('no-js');if (-1 !== navigator.userAgent.toLowerCase().indexOf('msie') || -1 !== navigator.userAgent.toLowerCase().indexOf('trident/')) {document.documentElement.classList.add('is-IE');}</script>
		<?php

		if ( is_file( $skip_link = THEME_PATH . 'assets/js/plugins/skip-link-focus.js' ) ) {
			echo '<script>';
			include $skip_link;
			echo '</script>';
		}

		if ( is_file( $flex_gap = THEME_PATH . 'assets/js/plugins/flex-gap.js' ) ) {
			echo '<script>';
			include $flex_gap;
			echo '</script>';
		}

		$str_parsed = Helper::filterSettingOptions( 'defer_script', [] );
		if ( Helper::hasDelayScriptTag( $str_parsed ) && is_file( $load_scripts = THEME_PATH . 'assets/js/plugins/load-scripts.js' ) ) {
			echo '<script>';
			include $load_scripts;
			echo '</script>';
		}
	}

	// ------------------------------------------------------

	/**
	 * @param string $tag
	 * @param string $handle
	 * @param string $src
	 *
	 * @return string
	 */
	public function script_loader_tag( string $tag, string $handle, string $src ): string {

		// Adds `async`, `defer` and attribute support for scripts registered or enqueued by the theme.
		foreach ( [ 'async', 'defer' ] as $attr ) {
			if ( ! wp_scripts()->get_data( $handle, $attr ) ) {
				continue;
			}

			// Prevent adding attribute when already added in #12009.
			if ( ! preg_match( ":\s$attr(=|>|\s):", $tag ) ) {
				$tag = preg_replace( ':(?=></script>):', " $attr", $tag, 1 );
			}

			// Only allow async or defer, not both.
			break;
		}

		// Custom filter which adds proper attributes

		// Fontawesome kit
		if ( ( 'fontawesome-kit' === $handle ) && ! preg_match( ':\scrossorigin([=>\s]):', $tag ) ) {
			$tag = preg_replace( ':(?=></script>):', " crossorigin='anonymous'", $tag, 1 );
		}

		// Add script handles to the array
		$str_parsed = Helper::filterSettingOptions( 'defer_script', [] );

		return Helper::lazyScriptTag( $str_parsed, $tag, $handle, $src );
	}

	// ------------------------------------------------------

	/**
	 * Add style handles to the array below
	 *
	 * @param string $html
	 * @param string $handle
	 *
	 * @return string
	 */
	public function style_loader_tag( string $html, string $handle ): string {
		$styles = Helper::filterSettingOptions( 'defer_style', [] );

		return Helper::lazyStyleTag( $styles, $html, $handle );
	}

	// ------------------------------------------------------

	/**
	 * @return void
	 */
	public function deferred_scripts(): void {}

	// ------------------------------------------------------
	// ------------------------------------------------------

	/**
	 * Launching operation cleanup
	 *
	 * @return void
	 */
	private function _cleanup(): void {

		// wp_head
		remove_action( 'wp_head', 'rsd_link' );
		remove_action( 'wp_head', 'wlwmanifest_link' );
		remove_action( 'wp_head', 'wp_shortlink_wp_head' );
		remove_action( 'wp_head', 'wp_generator' );
		remove_action( 'wp_head', 'feed_links_extra', 3 );
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );

		// All actions related to emojis
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_action( 'admin_print_styles', 'print_emoji_styles' );
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );

		// Staticize emoji
		remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
		remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
		remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );

		/**
		 * Remove wp-json header from WordPress
		 * Note that the REST API functionality will still be working as it used to;
		 * this only removes the header code that is being inserted.
		 */
		remove_action( 'wp_head', 'rest_output_link_wp_head' );
		remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
		remove_action( 'template_redirect', 'rest_output_link_header', 11 );

		// Remove id li navigation
		add_filter( 'nav_menu_item_id', '__return_null', 10, 3 );
	}
}
