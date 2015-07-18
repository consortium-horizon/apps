<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, and ABSPATH. You can find more information by visiting
 * {@link https://codex.wordpress.org/Editing_wp-config.php Editing wp-config.php}
 * Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'wordpress');

/** MySQL database username */
define('DB_USER', 'wordpress');

/** MySQL database password */
define('DB_PASSWORD', 'wordpress');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

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
define('AUTH_KEY',         'tn0om+@e 8Ffl(GX3Vk-yoML?(,[o!~{C8(w_5||V8JudFf(%%YSnb6n;Faf`2jc');
define('SECURE_AUTH_KEY',  'Nk^t@WuE4%ZWQL}0zAa^@<|6xrLZ|s0XC5dsJwjrvP*uJs7nk?*YPu,LVye5H:l?');
define('LOGGED_IN_KEY',    '4.9s:Ut0?gFf[/,1g/#<smqlUo/)(#+fRJ.bY$c.kyu86.m-6:SW$vc-dsiKn]Pz');
define('NONCE_KEY',        '$$hE|{K~WVt![?lyB]mxjTx-vNG[-7SZ[stgkA2UJSff]$.qy;+&f4/s=xR|jNk0');
define('AUTH_SALT',        '*=!6xBsr?fA>nUuB!2ZC!%-A2.AV3D?e;{_raDk|B4G$Pmxc-ZTpl(z*W:_2Zn>]');
define('SECURE_AUTH_SALT', 'wgWeD`%HXp7er:Qy|bU*P-HYZXTd<_<:Vd;+6gd_[i~ -F}^n58H;Mh_#Z}.tst>');
define('LOGGED_IN_SALT',   'qDM-:TU}ZnE rRo#3 w~?lLJAJzU(D?}ZT1I:hM -:+w<+{VwL_fXh~^`RJf%#][');
define('NONCE_SALT',       'OLS-y6#W5.Tk(,c _y|OkKFG]Mx7w~tZ~86c${OazZ7,Qd26mD:^)SDi?(]{@`Ik');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

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
