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
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'dauqsryoab_decobelo' );

/** MySQL database username */
define( 'DB_USER', 'dauqsryoab_decobelo' );

/** MySQL database password */
define( 'DB_PASSWORD', 'U-SeCE+u0x-3+2[j' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

define( 'FS_METHOD', 'direct' );

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
define( 'AUTH_KEY',         'QX%gll&=2l0eQK!-nYwba/s~ZzCpb1N!jRks&On},ne3kMJ=5t8_8C$fEL@3G>&;' );
define( 'SECURE_AUTH_KEY',  '$1KP._%yXQ?>l*s^q<n0<7}}jL4F@@hD0u1V9|f}W3&k%<U&t5v:1FG1bX@>5dc0' );
define( 'LOGGED_IN_KEY',    '37]`bvj{AU$4;NJg]?5w57h;AU)>vW@3&2/Dlt{IL#& LtW7NEEwF>&NJw&1+BKr' );
define( 'NONCE_KEY',        ']V.k=Kv%kdc~1c(I&#*8)aWVwNU1|oF^,Gu1x-8++8YvI*5t27VP{TX<^hu&Lo^d' );
define( 'AUTH_SALT',        'C9 ~;8KTov@w=u,TiJQp|*E&/KH;+IQo#2qqs.A!&%Q#?F,si|n2+iDV52.bb,zp' );
define( 'SECURE_AUTH_SALT', 'o/3PX%FDMGBu.Gp8;!VNt-j&9&]}|bJ%7pI)*gYqSRf#qC? ~?_ZNOqA&irH@V]_' );
define( 'LOGGED_IN_SALT',   'bVy6ceU f7r^ppX-_0forv`z<R?)t#( MK(Hpnpfz61bCE6AK8D]ba3|Ksckd+UZ' );
define( 'NONCE_SALT',       '`t_R=mp`=HWN/Zd2){es+;)NwHi78/G4T&d!Z]E%[2l&DPXN4LB*<BQkt/8yH}]d' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'db_';

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
define( 'UPLOADS', 'wp-content/uploads' );
require_once ABSPATH . 'wp-settings.php';
