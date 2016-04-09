<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// Dynamically set the Site URL based on the incoming request protocol/host.
// The Docker host could be anything, so we don't want to make any assumptions.
 define('WP_SITEURL', ($_SERVER['HTTPS'] || $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' ? 'https' : 'http') .  '://' .
$_SERVER['HTTP_HOST'] . '/');
define('WP_HOME', ($_SERVER['HTTPS'] || $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' ? 'https' : 'http') .  '://' .
$_SERVER['HTTP_HOST'] . '/');

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', getenv('WORDPRESS_DB_NAME'));

/** MySQL database username */
define('DB_USER', getenv('WORDPRESS_DB_USER'));

/** MySQL database password */
define('DB_PASSWORD', getenv('WORDPRESS_DB_PASS'));

/** MySQL hostname */
define('DB_HOST', 'mysql');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '4a3890f29f15d13e563156f7a663f78363dc636b');
define('SECURE_AUTH_KEY',  '95f84d09068dc243679f09e850428bf772e0d2c9');
define('LOGGED_IN_KEY',    '9d638031fe41f44e0f882471896b4e7468d43767');
define('NONCE_KEY',        '2bc19eeb0642fe6ba5894afdac0ea021cc263838');
define('AUTH_SALT',        '67b1819c17905ebfb33b6e39fe9d965cd34fd81b');
define('SECURE_AUTH_SALT', '460e5122833e34b0f2d60aa6787bbb903c2df7b9');
define('LOGGED_IN_SALT',   '36e675950a3d097521deaaf24548b8ce8fb42884');
define('NONCE_SALT',       '22f8c8b1e7d0c817d5b825a4c566c3a0a170711c');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);

// If we're behind a proxy server and using HTTPS, we need to alert Wordpress of that fact
// see also http://codex.wordpress.org/Administration_Over_SSL#Using_a_Reverse_Proxy
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
	$_SERVER['HTTPS'] = 'on';
}

/* Multisite */
define('WP_ALLOW_MULTISITE', true);
/*define('MULTISITE', true);
define('SUBDOMAIN_INSTALL', false);
define('DOMAIN_CURRENT_SITE', $_SERVER['HTTP_HOST']);
define('PATH_CURRENT_SITE', '/');
define('SITE_ID_CURRENT_SITE', 1);
define('BLOG_ID_CURRENT_SITE', 1);*/

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
