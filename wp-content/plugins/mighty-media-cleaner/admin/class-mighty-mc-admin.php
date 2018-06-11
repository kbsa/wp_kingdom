<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://hibit.co/mighty-media-cleaner
 * @since      0.1.0
 *
 * @package    Mighty_MC
 * @subpackage Mighty_MC/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * @package    Mighty_MC
 * @subpackage Mighty_MC/admin
 * @author     Hibit <hibit.team@gmail.com>
 */
class Mighty_MC_Admin
{
    /**
     * The ID of this plugin.
     *
     * @since    0.1.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;
    /**
     * The version of this plugin.
     *
     * @since    0.1.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    0.1.0
     * @param      string $plugin_name The name of this plugin.
     * @param      string $version The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        if (!defined('MIGHTY_MEDIA_IMG')) {
            define('MIGHTY_MEDIA_IMG', plugin_dir_url(__FILE__) . "/media/img/");
        }
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    0.1.0
     */
    public function admin_enqueue_styles($hook_suffix)
    {
        if (strpos($hook_suffix, 'mighty-mc') === false) {
            return;
        }
        $query_args = array('family' => 'Raleway:300,400,600,700');
        wp_enqueue_style($this->plugin_name . '-google_fonts', add_query_arg($query_args, "//fonts.googleapis.com/css"), array(), null);
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/mighty-mc-admin.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name . "-bootstrap", plugin_dir_url(__FILE__) . 'css/bootstrap.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    0.1.0
     */
    public function admin_enqueue_scripts($hook_suffix)
    {
        if (strpos($hook_suffix, 'mighty-mc') === false) {
            return;
        }
        wp_enqueue_script($this->plugin_name . "-plugins", plugin_dir_url(__FILE__) . 'js/plugins.js', array('jquery'), $this->version, false);
        wp_enqueue_script($this->plugin_name . "-custom", plugin_dir_url(__FILE__) . 'js/mighty-mc-admin.js', array('jquery'), $this->version, false);
        wp_localize_script($this->plugin_name . "-custom", 'mighty_data', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'deSelectAll' => esc_attr__('De-select All','mighty-media-cleaner'),
            'selectAll' => esc_attr__('Select All','mighty-media-cleaner'),
            'yes' => esc_attr__('Yes','mighty-media-cleaner'),
            'no' => esc_attr__('No','mighty-media-cleaner'),
            'msg1' => esc_attr__('Do You Want to Delete Selected Image ?','mighty-media-cleaner'),
            'msg4' => esc_attr__('Do You Want To Delete The Backup File ?','mighty-media-cleaner'),
            'msg6' => esc_attr__('Do You Want To Restore The Backup File ?','mighty-media-cleaner'),
            'msg7' => esc_attr__('Backup has been restored successfully.','mighty-media-cleaner'),
            'msg5' => esc_attr__('no backup files found!','mighty-media-cleaner'),
            'moving' => esc_attr__('Moving...','mighty-media-cleaner'),
            'removing' => esc_attr__('Removing...','mighty-media-cleaner'),
            'remove' => esc_attr__('Remove','mighty-media-cleaner'),
            'removed' => esc_attr__('Removed!','mighty-media-cleaner'),
            'fileAvailable' => esc_attr__('File Available','mighty-media-cleaner'),
            'noMediaSelected' => esc_attr__('No Media Selected !','mighty-media-cleaner'),
            'restore' => esc_attr__('Restore','mighty-media-cleaner'),
            'restoring' => esc_attr__('Restoring','mighty-media-cleaner'),
            'tryAgain' => esc_attr__('Try Again!','mighty-media-cleaner'),
        ));
        wp_register_script($this->plugin_name . "-core", plugin_dir_url(__FILE__) . 'js/mighty-mc-core.js', array('jquery'), $this->version, false);
    }

    function admin_menu()
    {
        add_menu_page('Mighty Media Cleaner Dashboard', 'Mighty MC', 'manage_options', 'mighty-mc-dashboard', array($this, 'admin_home_page'), MIGHTY_ADMIN_MEDIA."/icon.png", 80);
        add_submenu_page('mighty-mc-dashboard', 'Scan Media Library', 'Scan Media', 'manage_options', 'mighty-mc-media-scan', array($this, 'admin_scan_page'));
        add_submenu_page('mighty-mc-dashboard', 'Backup', 'Backup / Restore', 'manage_options', 'mighty-mc-media-backup', array($this, 'admin_backup_page'));
        add_submenu_page('mighty-mc-dashboard', 'About', 'About', 'manage_options', 'mighty-mc-media-about', array($this, 'admin_about_page'));
        add_submenu_page(null, 'Result', 'Result', 'manage_options', 'mighty-mc-media-result', array($this, 'admin_result_page'));
    }

    function admin_home_page()
    {
        require("templates/mighty-admin-home.php");
    }

    function admin_scan_page()
    {
        require("templates/mighty-admin-scan.php");
    }

    function db_cloner()
    {
        $plugin_core = new Mighty_MC_Core();
        // Create clone from db
        $_SESSION['mc-tables'] = $plugin_core->db_files_handler();
        wp_die();
    }

    function scan_ajax()
    {
        $items = json_decode(stripcslashes($_POST['items']), true);
        $plugin_core = new Mighty_MC_Core();
        $result = $plugin_core->scan_database($items);
        foreach ($result as $key => $item) {
            $_SESSION['mc-scan'][$key] = $item;
        }
        print_r($_SESSION['mc-scan']);
        wp_die();
    }

    function admin_backup_page()
    {
        require("templates/mighty-admin-backup.php");
    }

    function admin_about_page()
    {
        require("templates/mighty-admin-about.php");
    }

    function admin_result_page()
    {
        require("templates/mighty-admin-result.php");
    }

    function add_to_media_list($actions)
    {
        $post = get_post();
        $url = admin_url() . 'admin.php?page=mighty-mc-media-scan&mode=custom&items=' . $post->ID;
        $actions['mc_scan'] = '<a href="' . $url . '">' . __('Find where it used', 'mighty-media-cleaner') . '</a>';
        return $actions;
    }

    function custom_bulk_find_usage_option()
    {
        global $pagenow;
        if ($pagenow == 'upload.php') {
            ?>
            <script type="text/javascript">
                jQuery(document).ready(function () {
                    jQuery('<option>').val('mc-scan').text('<?php _e('Find usage')?>').appendTo("select[name='action']");
                    jQuery('<option>').val('mc-scan').text('<?php _e('Find usage')?>').appendTo("select[name='action2']");
                });
            </script>
            <?php
        }
    }

    function custom_bulk_find_usage()
    {
        if (!isset($_REQUEST['detached'])) {
            // get the action
            $wp_list_table = _get_list_table('WP_Media_List_Table');
            $action = $wp_list_table->current_action();
            $allowed_actions = array("mc-scan");
            if (!in_array($action, $allowed_actions)) return;
            // security check
            check_admin_referer('bulk-media');
            // make sure ids are submitted.  depending on the resource type, this may be 'media' or 'ids'
            if (isset($_REQUEST['media'])) {
                $post_ids = array_map('intval', $_REQUEST['media']);
            }
            if (empty($post_ids)) return;
            if ('mc-scan' == $action) {
                $post_ids = implode(',', $post_ids);
                $url = admin_url() . 'admin.php?page=mighty-mc-media-scan&mode=custom&items=' . $post_ids;
                wp_redirect($url);
            }
            exit();
        }
    }

    function do_backup()
    {
        $plugin_core = new Mighty_MC_Core();
        $plugin_core->backup_all_media();
        wp_die();
    }

    function mighty_remove_backup()
    {
        $file = $_POST['file'];
        $file = MIGHTYFOLDER . '/backups/' . $file;
        if (file_exists($file)) {
            unlink($file);
            echo($file . " Deleted");
        }
        wp_die();
    }

    function mighty_restore_backup()
    {
        $file = $_POST['file'];
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        WP_Filesystem();
        global $wp_filesystem;
        $upload_dir = wp_upload_dir();
        $d_path = $upload_dir['basedir'].'/';
        $filepath = $upload_dir['basedir'].'/mighty-mc/backups/'.$file;
        $unzipfile = unzip_file( $filepath, $d_path);
        if( is_wp_error($unzipfile) ){
            define('FS_METHOD', 'direct'); //lets try direct.
            WP_Filesystem();  //WP_Filesystem() needs to be called again since now we use direct !
            $unzipfile = unzip_file( $filepath, $d_path);
        }
        if( !is_wp_error($unzipfile) ){
            /*extracted successfully*/
            global $wpdb;
            $wpdb->update(
                $wpdb->posts,
                array(
                    'post_type' => 'attachment'
                ),
                array( 'post_type' => 'mc_attachment' ),
                array(
                    '%s'
                ),
                array( '%s' )
            );
            wp_die();
        }else{
            $message = $unzipfile->get_error_message();
            $wp_filesystem->delete($d_path, true);
            return;
            echo($message.'<br/>');
            wp_die();
        }
    }

    function mighty_remove_media()
    {
        if (!is_array($_POST['media']) || !sizeof($_POST['media'])) {
            return "No Media Selected";
        }
        global $wpdb;
        foreach ($_POST['media'] as $id => $url) {
            $url = str_replace("\\\\","\\",$url);
            //Its a physical file that does not exist on media library
            if ($id == "000") {
                unlink($url);
            } else {
	            if(unlink( $url )){
		            $wpdb->update(
			            $wpdb->posts,
			            array(
				            'post_type' => 'mc_attachment'
			            ),
			            array( 'ID' => (int)$id ),
			            array(
				            '%s'
			            ),
			            array( '%d' )
		            );

		            $ext = pathinfo( $url, PATHINFO_EXTENSION );
		            $ext_length = strlen( $ext ) + 1;
		            $pattern = substr( $url, 0, -( $ext_length ) );
		            $list = glob( $pattern . "*." . $ext );
		            $file_without_ext = substr( $url, 0, -( $ext_length ) );
		            foreach ( $list as $file ) {
			            $pattern = preg_quote( $file_without_ext, '/' ) . '(-[0-9]+x[0-9]+)?[.]' . $ext;
			            $pattern = "/^.*$pattern.*\$/is";
			            if ( preg_match( $pattern, $file ) ) {
				            unlink( $file );
			            }
		            }
				}

            }
        }
        wp_die();
    }

    //Page Slug Body Class
    function mighty_custom_body_class($classes)
    {
        $classes .= " mighty-media-cleaner";
        return $classes;
    }

    function mighty_mc_load_textdomain()
    {
        load_plugin_textdomain('mighty-media-cleaner', false, plugins_url() . '/mighty-media-cleaner/languages/');
    }
}
