<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */
// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'kennethl_stlawrence');

/** MySQL database username */
define('DB_USER', 'kennethl_me');

/** MySQL database password */
define('DB_PASSWORD', '?ye3B!a4');

/** MySQL hostname */
define('DB_HOST', 'localhost');

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
define('AUTH_KEY',         '5h$@Dt(TDRX>9`83GL@[v>=VF*=Zz*M<[d8$8 M8k{V.Fz|?/p|-e7=__@DzA|1|');
define('SECURE_AUTH_KEY',  '_zw?j&i.rWXx}L@S,*V@yZcwnG-m{9l~nhPRi6%!^aGtgQox#.Ju)Evr}c@/C]^E');
define('LOGGED_IN_KEY',    'DO>ca$cjO<H|_%J!l<1f+Qx//PpP_$+G<7&DE#Vy|?s}G|yH ?y@PAP}im+R+$Hx');
define('NONCE_KEY',        'kATg9(ibK&WA@;#aC|Z2x_Rix|r<QaMZ33m>w.r~:u%-b=+>FU9Im%xr`}8ys=sS');
define('AUTH_SALT',        'vr|HT~E4*x9BD[+IBmtj:/u2{kyj,~vnWU>h7PYL .cKhwO[a|x%bFWrGVn@5c6#');
define('SECURE_AUTH_SALT', 'RUN(2f}90|cBVbvS#m$3Ja8*aYcD4y)$O<(!o/W5OA!aU/`FJ_$2JnV }ZgX1|VQ');
define('LOGGED_IN_SALT',   ' xP?)IWr?)o!J@gT?%1p|}znKfTc~&I(z`!3[rG`||X&tzlp4c:Y7 .A!nntPt-!');
define('NONCE_SALT',       '%*|;+;*wp1v^.yV$ uVs6+Yz%]7!~m3`JoPN/d4pKl~z=F^kAc+B`|TVpn6-e|_4');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
