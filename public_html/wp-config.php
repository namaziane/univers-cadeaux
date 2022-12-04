<?php
define( 'WP_CACHE', true );


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
/** The name of the database for WordPress */
define( 'DB_NAME', 'u100154604_coCec' );

/** Database username */
define( 'DB_USER', 'u100154604_jrRgU' );

/** Database password */
define( 'DB_PASSWORD', 'pZZQyPsAqP' );

/** Database hostname */
define( 'DB_HOST', 'mysql' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

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
define( 'AUTH_KEY',          'dyZN mi8h/ s{Tq+(V$^,+9BL{T5/SVF7E]8S6X+v.),`wM^6yq~&]Yd~?Mfx.|_' );
define( 'SECURE_AUTH_KEY',   'H.A*3ssM9#E4r4FWqborg!TSQm&qgkRO2GPmA7b1`U._mg;=!4(M(=dS}oFw/Zwk' );
define( 'LOGGED_IN_KEY',     '7U+wgp5g*Xh7AlaR_cP)>@i%l7#(+}{>1X< c6dw[z=o8Z}-mMK3t}<MdAH x_0b' );
define( 'NONCE_KEY',         '-;iL0i/r%#q@mWI%QE9 zQ(o9Mt#8-UV#HLq|R5l[)DyQk: mT(XF4^*J-LmpV&F' );
define( 'AUTH_SALT',         'Kk!1b0Bc!j6c+vw2c-G+kZ%d$[ 0*%#KkQ7+-.5i2?9P#oz$9EwL BZbaPguVuv&' );
define( 'SECURE_AUTH_SALT',  '1~;LL)QBTS.lE,cz59/P5_yf5:5n,-c9s|P@9;5p:A{Vdd>HBvaL(3kN8xE4eB6s' );
define( 'LOGGED_IN_SALT',    '&Pr;pgTAe|UoCFqJ>H0E&]M=h=m%W#vIwprZ@m*VN?3A38k0-!;9c@],MGB_m69Z' );
define( 'NONCE_SALT',        '`8I=}(i9}VV0y80^(H<RRHx; ~xhJ5fNd#/neh_MVTOt4G05V[04!Cgg?}pGREpg' );
define( 'WP_CACHE_KEY_SALT', 'HV1<#VB+%^&{i$am*GQa#JM6 s0aPB)%@u;`pa,V{Zowo* Z*lZxilfUBu*i#CL2' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

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



define( 'WP_AUTO_UPDATE_CORE', 'minor' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
