<?php

//Begin Really Simple SSL session cookie settings
@ini_set('session.cookie_httponly', true);
@ini_set('session.cookie_secure', true);
@ini_set('session.use_only_cookies', true);
//END Really Simple SSL

 // Added by WP Rocket




# BEGIN WP Cache by 10Web
define( 'WP_CACHE', true ); // Added by WP Rocket
define( 'TWO_PLUGIN_DIR_CACHE', '/home/chaudama/dongthanhcong.vn/wp-content/plugins/tenweb-speed-optimizer/' );
# END WP Cache by 10Web
 // Added by WP Rocket

 // Added by WP Rocket

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
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'dongthanhcong' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'Opentechiz' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

define('DISABLE_WP_CRON', true);

define('FORCE_SSL_ADMIN', false);

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
define( 'AUTH_KEY',         'agxwtqxetcx1ua4ebhsxcbcxcuxl7wyuhrvftoigblcslz04ilu808yuklpauakm' );
define( 'SECURE_AUTH_KEY',  'sxcgdpll6nseazxhfncpqltf6wxxnrdtn8euhkrixxcqzfihrxg7stehcqpgf87y' );
define( 'LOGGED_IN_KEY',    'gvnmzhfrwdm9noqohbp4eihied6vog1bypafvao7atwpeurniiqvjhjv8esncnie' );
define( 'NONCE_KEY',        'xs0nku5mdw4e6byatf83puy5eqtmnp9lmsucjlvlhhlx2m1bk6a7bwpmlbjpciwr' );
define( 'AUTH_SALT',        'di4xhvhozlbeprtgwelgwxptg85uzxp0fgno7uy96wwvh6pifzzjqtpan90pex7a' );
define( 'SECURE_AUTH_SALT', 'puhacf2bcnspasofizluppgzt0rso6vpneatf4dgcjwkkkpnjywqu6sxlxf9hun6' );
define( 'LOGGED_IN_SALT',   'ew7smfakuued43un8a3asijavi7lcdkxiniecoquklc1bp1jkp0wyn4vz44zngny' );
define( 'NONCE_SALT',       'sudtkialclsydxqoinn0bneichyd6gmtvt7hpyvrii2fq4rzgispkcgz4kn3ssx0' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wpjd_';

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
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';


