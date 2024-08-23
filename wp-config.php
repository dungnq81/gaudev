<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
const DB_NAME     = 'gaudevxyz_NzMyMWMwZDQ3MGM1OD_dbname';
const DB_USER     = 'gaudevxyz_NzMyMWMwZDQ3MGM1OD_username';
const DB_PASSWORD = 'MWFlZGM3ZmIxMjMyZjFjZTY1NmM2MDA5Mzk0';

const DB_HOST    = 'localhost';
const DB_CHARSET = 'utf8mb4';
const DB_COLLATE = 'utf8mb4_unicode_520_ci';

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY', 'xT)uKACG4mA)p;D+`I$Ww{ItRshci_vEEElxvpxLBW/}kPsjJm0%4Q={bqQ ?u/J' );
define( 'SECURE_AUTH_KEY', 'I48]I(`48UY-=;t3QmuK{_bp2b9EN9q]~=}J-v}X>r-LW^TCdg1&3T cnkVaeYqz' );
define( 'LOGGED_IN_KEY', '`NMEFkM[PM+-!RX#i}0 @JpdCeiTDnHk*KE6(!0JvNWDoRA|I$=n6_bqW!sUchC?' );
define( 'NONCE_KEY', 'Z),O|r*h_hK1{5drK?xr`%aUG4ce2h*PNmUQBwS-(7iy_qufjcWiT4B+VH5 aYpi' );
define( 'AUTH_SALT', '2E=~+rHACSc7#B25YMOMwdiWqq;Ox#HjL({S+prGO:OVsRAZPBr Q#LzvHz$nLpy' );
define( 'SECURE_AUTH_SALT', 'Pn(QlX/93]il`EZ&CKtzTCebrgL046Oke+&Dd.Ahn/LqkC9x-FIcQv}trX/7cmo1' );
define( 'LOGGED_IN_SALT', 'o@!zG}/aD0Jg+j, V&a=v9J_~j2 E8g%^Fo8{28WO>T^8QyDfqnqKBt64r)Im;p3' );
define( 'NONCE_SALT', 'ga,t|aYL:ZqUgI{zLW]aN$p+95lO0,##YzkZP$p9{T:LCIfkp_eg]#NRy0F@5Q3a' );
define( 'WP_CACHE_KEY_SALT', 'En:Ge$R`m4Q^!$5BBf`V@i^`[oVfA4)?+{uQiW,L5sQ[e.5}W`?8l^y;whB=z7,*' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'MTRlZG_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
const WP_DEBUG = true;
//const WP_DEBUG_LOG     = true;
//const WP_DEBUG_DISPLAY = true;

/* Add any custom values between this line and the "stop editing" line. */

if ( ! empty( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && 'https' === $_SERVER['HTTP_X_FORWARDED_PROTO'] ) {
	$_SERVER['HTTPS'] = 'on';
}

//if ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] === 'on' ) {
//	@ini_set( 'session.cookie_secure', '1' );
//}

const TRACKING = false;
//if ( ! defined( 'FS_METHOD' ) ) {
//	define( 'FS_METHOD', 'direct' );
//}

/** PHP Memory */
const WP_MEMORY_LIMIT     = '512M';
const WP_MAX_MEMORY_LIMIT = '512M';

const DISALLOW_FILE_EDIT = true;
const DISALLOW_FILE_MODS = false;

/* SSL */
const FORCE_SSL_LOGIN = true;
const FORCE_SSL_ADMIN = true;

const WP_POST_REVISIONS = 2;
const EMPTY_TRASH_DAYS  = 15;
const AUTOSAVE_INTERVAL = 120;

/** WordPress core auto-update, */
const WP_AUTO_UPDATE_CORE = true;

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
