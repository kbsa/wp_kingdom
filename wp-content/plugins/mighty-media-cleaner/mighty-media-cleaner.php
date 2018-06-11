<?php

/**
 *
 * @link              http://hibit.co/mighty-media-cleaner
 * @since             0.1.0
 * @package           Mighty_MC
 *
 * @wordpress-plugin
 * Plugin Name:       Mighty Media Cleaner
 * Plugin URI:        http://hibit.co/mighty-media-cleaner/
 * Description:       A powerful & smart plugin to scan and detect unused media files on your WordPress site.
 * Version:           0.1.0
 * Author:            Hibit
 * Author URI:        http://hibit.co/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       mighty-media-cleaner
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if(!defined('WPINC')) {
	die;
}

function mmc_activate_mighty_mc() {
	require_once plugin_dir_path(__FILE__) . 'includes/class-mightyMC-activator.php';
	Mighty_MC_Activator::activate();
}

function mmc_deactivate_mighty_mc() {
	require_once plugin_dir_path(__FILE__) . 'includes/class-mightyMC-deactivator.php';
	Mighty_MC_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'mmc_activate_mighty_mc');
register_deactivation_hook(__FILE__, 'mmc_deactivate_mighty_mc');


/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-mightyMC.php';

/**
 * Begins execution of the plugin.
 *
 * @since    0.1.0
 */
function mmc_run_mighty_mc() {
	
	if (!defined('MIGHTY_ADMIN_MEDIA')) {
		define('MIGHTY_ADMIN_MEDIA', plugin_dir_url(__FILE__) . "admin/media/img/");
	}
	$plugin = new Mighty_MC();
	$plugin->run();
	
}

mmc_run_mighty_mc();
