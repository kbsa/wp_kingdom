<?php
define( 'WPCACHEHOME', 'C:\xampp\htdocs\kingdom\wp-content\plugins\wp-super-cache/' );
define('WP_CACHE', true);
/** 
 * Configuración básica de WordPress.
 *
 * Este archivo contiene las siguientes configuraciones: ajustes de MySQL, prefijo de tablas,
 * claves secretas, idioma de WordPress y ABSPATH. Para obtener más información,
 * visita la página del Codex{@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} . Los ajustes de MySQL te los proporcionará tu proveedor de alojamiento web.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** Ajustes de MySQL. Solicita estos datos a tu proveedor de alojamiento web. ** //
/** El nombre de tu base de datos de WordPress */
define('DB_NAME', 'kingdom');

/** Tu nombre de usuario de MySQL */
define('DB_USER', 'root');

/** Tu contraseña de MySQL */
define('DB_PASSWORD', '');

/** Host de MySQL (es muy probable que no necesites cambiarlo) */
define('DB_HOST', 'localhost');

/** Codificación de caracteres para la base de datos. */
define('DB_CHARSET', 'utf8mb4');

/** Cotejamiento de la base de datos. No lo modifiques si tienes dudas. */
define('DB_COLLATE', '');

/**#@+
 * Claves únicas de autentificación.
 *
 * Define cada clave secreta con una frase aleatoria distinta.
 * Puedes generarlas usando el {@link https://api.wordpress.org/secret-key/1.1/salt/ servicio de claves secretas de WordPress}
 * Puedes cambiar las claves en cualquier momento para invalidar todas las cookies existentes. Esto forzará a todos los usuarios a volver a hacer login.
 *
 * @since 2.6.0
 */
define('AUTH_KEY', 'e;EQ`Eu#/QVgUpU6Ae7`nb[M2rCez,./A%O<?5/1Ej/xS7>8*R}Xu@ik:>*D]`D ');
define('SECURE_AUTH_KEY', ';5SoUW+7M?,XhRc|havZQZ fyD&-E?pf`eA`V{r743.RZ7M<3M#T9t_)|U=>P55(');
define('LOGGED_IN_KEY', '}]$I:6=4||]j%5)87`MB>-,le6#<w4<fM`B!(5vPw^2itmS < cGjDj2V-f+0;8r');
define('NONCE_KEY', 'E*+Uy_9L:RSUa.$X?iNu%I*<QBa*K3M#*N/~JweXMNovKh0M&#N%=J3l<x4(f^F7');
define('AUTH_SALT', 'T87)J9/*$*lGH1/aQ#PE[hG^_tqBamyuu~ls#-xECI2gAusP}YK1pI~/%XSQZZp6');
define('SECURE_AUTH_SALT', 'OX~`bA+?9Ox|un,P`p?~R2U4byzKpbr?iA;znNFs1<ypx-xj9SrIiiBPA/[EaZpd');
define('LOGGED_IN_SALT', '4XS9^^+Be3!.?9S}G]C-s<9@p{kxu*_f/5K(o@UKrL@c]lvgn:O#sg$m8|we9jBl');
define('NONCE_SALT', 'Vmxd+1y$z$rzfA![V6FRjq2*p.Fmr>lf/CBw(yxo<1-BT7(.~uh~X88M- #n|{9~');

/**#@-*/

/**
 * Prefijo de la base de datos de WordPress.
 *
 * Cambia el prefijo si deseas instalar multiples blogs en una sola base de datos.
 * Emplea solo números, letras y guión bajo.
 */
$table_prefix  = 'wp_';


/**
 * Para desarrolladores: modo debug de WordPress.
 *
 * Cambia esto a true para activar la muestra de avisos durante el desarrollo.
 * Se recomienda encarecidamente a los desarrolladores de temas y plugins que usen WP_DEBUG
 * en sus entornos de desarrollo.
 */
define('WP_DEBUG', false);

/* ¡Eso es todo, deja de editar! Feliz blogging */

/** WordPress absolute path to the Wordpress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');

