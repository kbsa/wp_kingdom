<?php

/*
  Plugin Name: Ultimate media cleaner
  Version: 1.1
  Plugin URI: http://www.nicearma.com/
  Author: Nicearma
  Author URI: http://www.nicearma.com/
  Text Domain: ultimate-media-cleaner
  Description: Search all unused files uploaded and delete them all, cleaning your server and database
 */

/*
  Copyright (c) 2017 http://www.nicearma.com
  Released under the GPL license
  http://www.gnu.org/licenses/gpl.txt
 */

if (is_admin()) {

    /*     * ******************* CONST ************************************ */

    include(plugin_dir_path(__FILE__) . 'php/consts/logger-type.const.php');
    include(plugin_dir_path(__FILE__) . 'php/consts/response-code.const.php');
    include(plugin_dir_path(__FILE__) . 'php/consts/file-type.const.php');
    include(plugin_dir_path(__FILE__) . 'php/consts/file-status.const.php');

    /*     * ******************* MODELS ************************************ */
    include(plugin_dir_path(__FILE__) . 'php/models/count.model.php');

    include(plugin_dir_path(__FILE__) . 'php/models/file.model.php');
    include(plugin_dir_path(__FILE__) . 'php/models/directory-files.model.php');

    include(plugin_dir_path(__FILE__) . 'php/models/directory.model.php');

    include(plugin_dir_path(__FILE__) . 'php/models/response.model.php');
    include(plugin_dir_path(__FILE__) . 'php/models/verification.model.php');

    include(plugin_dir_path(__FILE__) . 'php/models/options/backup.model.php');
    include(plugin_dir_path(__FILE__) . 'php/models/options/check.model.php');
    include(plugin_dir_path(__FILE__) . 'php/models/options/show.model.php');
    include(plugin_dir_path(__FILE__) . 'php/models/options/ignore.model.php');

    include(plugin_dir_path(__FILE__) . 'php/models/option.model.php');

    include(plugin_dir_path(__FILE__) . 'php/models/status.model.php');
    /*     * ******************* SQL ************************************ */

    include(plugin_dir_path(__FILE__) . 'php/sql/file.sql.php');
    include(plugin_dir_path(__FILE__) . 'php/sql/front.sql.php');
    include(plugin_dir_path(__FILE__) . 'php/sql/image.sql.php');
    include(plugin_dir_path(__FILE__) . 'php/sql/backup.sql.php');

    /*     * ******************* CHECKER ************************************ */

    include(plugin_dir_path(__FILE__) . 'php/sql/checker/checkAbstract.sql.php');
    include(plugin_dir_path(__FILE__) . 'php/sql/checker/checkExcerptAll.sql.php');
    include(plugin_dir_path(__FILE__) . 'php/sql/checker/checkExcerptBestLuck.sql.php');
    include(plugin_dir_path(__FILE__) . 'php/sql/checker/checkPostAndPageAll.sql.php');
    include(plugin_dir_path(__FILE__) . 'php/sql/checker/checkPostAndPageBestLuck.sql.php');
    include(plugin_dir_path(__FILE__) . 'php/sql/checker/checkPostMeta.sql.php');
    include(plugin_dir_path(__FILE__) . 'php/sql/checker/checkers.php');

    /*     * ******************* SERVICES ************************************ */

    include(plugin_dir_path(__FILE__) . 'php/services/logger.service.php');

    include(plugin_dir_path(__FILE__) . 'php/services/file.service.php');
    include(plugin_dir_path(__FILE__) . 'php/services/directory.service.php');

    include(plugin_dir_path(__FILE__) . 'php/services/verify.service.php');
    include(plugin_dir_path(__FILE__) . 'php/services/verify-front.service.php');

    include(plugin_dir_path(__FILE__) . 'php/services/option.service.php');

    include(plugin_dir_path(__FILE__) . 'php/services/backup.service.php');

    /*     * ******************* REST ************************************ */

    include(plugin_dir_path(__FILE__) . 'php/rest/help.php');

    include(plugin_dir_path(__FILE__) . 'php/rest/file.rest.php');
    include(plugin_dir_path(__FILE__) . 'php/rest/directory.rest.php');

    include(plugin_dir_path(__FILE__) . 'php/rest/verify.rest.php');
    include(plugin_dir_path(__FILE__) . 'php/rest/verify-front.rest.php');

    include(plugin_dir_path(__FILE__) . 'php/rest/option.rest.php');

    include(plugin_dir_path(__FILE__) . 'php/rest/backup.rest.php');
}


add_action('admin_menu', 'umc_admin_menu');

function umc_admin_menu() {

    /* Add our plugin submenu and administration screen */
    $page_hook_suffix = add_submenu_page('tools.php', // The parent page of this submenu
            __('Ultimate media cleaner', 'umc'), // The submenu title
            __('Ultimate media cleaner', 'umc'), // The screen title
            'activate_plugins', // The capability required for access to this submenu
            'umc', // The slug to use in the URL of the screen
            'umc_display_menu' // The function to call to display the screen
    );

    add_action('admin_enqueue_scripts', 'ucm_load_scripts');
}

function ucm_load_scripts($hook) {

    if ($hook != 'tools_page_umc') {
        return;
    }

    wp_register_style('umc-styles', plugins_url('js/styles.bundle.css', __FILE__));

    wp_register_script('umc-inline', plugins_url('js/inline.bundle.js', __FILE__));
    wp_register_script('umc-polyfills', plugins_url('js/polyfills.bundle.js', __FILE__));
    wp_register_script('umc-main', plugins_url('js/main.bundle.js', __FILE__));

  
    wp_register_script('umc-verdor', plugins_url('js/vendor.bundle.js', __FILE__));
    //only dev
    //wp_register_script('umc-styles', plugins_url('js/styles.bundle.js', __FILE__));
   
    wp_enqueue_style('umc-styles');

    /* Link our already registered script to a page */
    wp_enqueue_script('umc-inline', '', array(), null, true);
    wp_enqueue_script('umc-polyfills', array(), '', null, true);
    
    //only dev
    //wp_enqueue_script('umc-styles', '', array(), null, true);
    
    wp_enqueue_script('umc-verdor', '', array(), null, true);
    
    wp_enqueue_script('umc-main', '', array(), null, true);
    
}

function umc_display_menu() {

    echo '<umc-root></umc-root>';
}

?>