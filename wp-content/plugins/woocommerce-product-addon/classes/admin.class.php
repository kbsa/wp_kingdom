<?php
/*
 * working behind the seen
 */
class NM_PersonalizedProduct_Admin extends NM_PersonalizedProduct {
	var $menu_pages, $plugin_scripts_admin, $plugin_settings;
	function __construct() {
		
		// setting plugin meta saved in config.php
		$this->plugin_meta = ppom_get_plugin_meta();
		
		// getting saved settings
		$this->plugin_settings = get_option ( $this -> plugin_meta['shortname'] . '_settings' );
		
			
		// populating $inputs with NM_Inputs object
		// $this -> inputs = self::get_all_inputs ();
		
		/*
		 * [1] TODO: change this for plugin admin pages
		 */
		 $this->menu_pages = array ( array (
					'page_title' => __('PPOM', "ppom"),
					'menu_title' => __('PPOM', "ppom"),
					'cap' => 'manage_options',
					'slug' => "ppom",
					'callback' => 'product_meta',
					'parent_slug' => 'woocommerce' 
			),
		);
		
		/*
		 * [2] TODO: Change this for admin related scripts JS scripts and styles to loaded ADMIN
		 */
		$this->plugin_scripts_admin = array (
				array (
						'script_name' => 'scripts-table2json',
						'script_source' => '/js/admin/jquery.tabletojson.min.js',
						'localized' => false,
						'type' => 'js',
						'page_slug' => array (
								"ppom",
						),
				),				
				array (
						'script_name' => 'scripts-admin',
						'script_source' => '/js/admin/admin.js',
						'localized' => true,
						'type' => 'js',
						'page_slug' => array (
								"ppom",
						),
						'depends' => array (
								'jquery',
								'jquery-ui-accordion',
								'jquery-ui-draggable',
								'jquery-ui-droppable',
								'jquery-ui-sortable',
								'jquery-ui-slider',
								'jquery-ui-dialog',
								'jquery-ui-tabs',
						) 
				),
				array (
						'script_name' => 'ui-style',
						'script_source' => '/js/admin/ui/css/smoothness/jquery-ui-1.10.3.custom.min.css',
						'localized' => false,
						'type' => 'style',
						'page_slug' => array (
								"ppom",
								'nm-new-form' 
						) 
				),
				array (
						'script_name' => 'thickbox',
						'script_source' => 'shipped',
						'localized' => false,
						'type' => 'style',
						'page_slug' => array (
								'nm-new-form'
						)
				),				
				array (
						'script_name' => 'plugin-css',
						'script_source' => '/templates/admin/style.css',
						'localized' => false,
						'type' => 'style',
						'page_slug' => array (
								"ppom",
								'nm-new-form' 
						) 
				),
				array (
						'script_name' => 'ppom-datatable',
						'script_source' => '/js/datatable/datatables.min.css',
						'localized' => false,
						'type' => 'style',
						'page_slug' => array (
								"ppom", 
						) 
				),
				array (
						'script_name' => 'ppom-datatable',
						'script_source' => '/js/datatable/jquery.dataTables.min.js',
						'localized' => false,
						'type' => 'js',
						'page_slug' => array (
								"ppom", 
						) 
				),
		);
		
		add_action ( 'admin_menu', array (
				$this,
				'add_menu_pages' 
		) );
		
		
		/**
		 * laoding admin scripts only for plugin pages
		 * since 27 september, 2014
		 * Najeeb's 
		 */
		add_action( 'admin_enqueue_scripts', array (
						$this,
						'load_scripts_admin'
						));
						
		
		//Auto update notification
		add_action('in_plugin_update_message-nm-woocommerce-personalized-product/ppom.php', array($this, 'update_message'), 2, 10);
		
		// Get PRO version
		add_action('ppom_after_ppom_field_admin', 'ppom_admin_pro_version_notice', 10);
	}
	
	
	
	public function update_message(){
		
		echo '<div style="color:red;font:bold">';
		echo 'Download your plugin new version from <a target="_blank" href="http://clients.najeebmedia.com/">Member Area</a>';
		// echo 'Download your plugin new version from <a target="_blank" href="https://codecanyon.net/">CodeCanyon</a> and replace with existing';
		echo '</div>';
	}

	
	function load_scripts_admin($hook) {
		
		// loading script for only plugin optios pages
		// page_slug is key in $plugin_scripts_admin which determine the page
		foreach ( $this->plugin_scripts_admin as $script ) {
		
			$attach_script = false;
			if (is_array ( $script ['page_slug'] )) {
					
				foreach( $script ['page_slug'] as $page){
					$script_pages = 'woocommerce_page_'.$page;
					
					if ( $hook == $script_pages){
						$attach_script = true;
					}
				}	
			} else {
				$script_pages = 'woocommerce_page_'.$script ['page_slug'];
				
				if ($hook == $script_pages) {
					$attach_script = true;
				}
			}
				
			//echo 'script page '.$script_pages;
			if( isset($_GET['page']) && $_GET['page'] == 'ppom' ){
				// adding media upload scripts (WP 3.5+)
				wp_enqueue_media();
				
				// localized vars in js
				$arrLocalizedVars = array (
						'plugin_url' => $this->plugin_meta ['url'],
						'doing' => $this->plugin_meta ['url'] . '/images/loading.gif',
						'plugin_admin_page' => admin_url( 'admin.php?page=ppom'),
				);
				
				// checking if it is style
				if ($script ['type'] == 'js') {
					
					$depends = (isset($script['depends']) ? $script['depends'] : NULL);
					$sript_url = apply_filters('ppom_script_path', $this->plugin_meta ['url'] . $script ['script_source'], $script ['script_name'] );
					wp_enqueue_script ( "ppom" . '-' . $script ['script_name'], $sript_url, $depends );
						
					// if localized
					if ($script ['localized'])
						wp_localize_script ( "ppom" . '-' . $script ['script_name'], $this -> plugin_meta['shortname'] . '_vars', $arrLocalizedVars );
				} else {
						
					if ($script ['script_source'] == 'shipped')
						wp_enqueue_style ( $script ['script_name'] );
					else
						wp_enqueue_style ( "ppom" . '-' . $script ['script_name'], $this->plugin_meta ['url'] . $script ['script_source'] );
				}
			}
		}
		
	}
	
	/*
	 * creating menu page for this plugin
	 */
	function add_menu_pages() {
		
		if( !$this->menu_pages ) return '';
		
		foreach ( $this->menu_pages as $page ) {
			
			if ($page ['parent_slug'] == '') {
				
				$menu = add_options_page ( __ ( $page ['page_title'] . ' Settings', "ppom" ), __ ( $page ['menu_title'] . ' Settings', "ppom" ), $page ['cap'], $page ['slug'], array (
						$this,
						$page ['callback'] 
				), $this->plugin_meta ['logo'], $this->plugin_meta ['menu_position'] );
			} else {
				
				$menu = add_submenu_page ( $page ['parent_slug'], __ ( $page ['page_title'], "ppom" ), __ ( $page ['menu_title'] . ' Settings', "ppom" ), $page ['cap'], $page ['slug'], array (
						$this,
						$page ['callback'] 
				) );
			}
			
			
		}
	}
	
	
	// ====================== CALLBACKS =================================
	
	
	function product_meta() {
		echo '<div class="ppom-admin-wrap woocommerce">';
		
		if ((!isset ( $_REQUEST ['productmeta_id']))){
			echo '<h2>' . __ ( 'N-Media WooCommerce Personalized Product Option Manager', "ppom" ) . '</h2>';
			echo '<p>' . __ ( 'Create different meta groups for different products', "ppom" ) . '</p>';
			
			echo '<h2>' . __ ( 'How it works?', "ppom" ) . '</h2>';
			echo '<p>' . __ ( 'Once you create meta groups it will be displayed on product edit page on right side panel.', "ppom" ) . '</p>';
		}
		
		$action = (isset($_REQUEST ['action']) ? $_REQUEST ['action'] : '');
		if ((isset ( $_REQUEST ['productmeta_id'] ) && $_REQUEST ['do_meta'] == 'edit') || $action == 'new') {
			ppom_load_template ( 'admin/create-form.php' );
		} elseif ( isset($_REQUEST ['do_meta']) && $_REQUEST ['do_meta'] == 'clone') {
			$this -> clone_product_meta($_REQUEST ['productmeta_id']);
		}else{
			$url_add = add_query_arg(array('action' => 'new'));
			
			echo '<div class="product-meta-block">';
			echo '<a class="btn btn-success add-meta-btn" href="'.esc_url($url_add).'"><span class="dashicons dashicons-plus"></span> ' . __ ( 'Add Product Meta Group', "ppom" ) . '</a>';
			echo '<a class="btn btn-yellow import-export-btn" href=""><span class="dashicons dashicons-download"></span> ' . __ ( 'Import Meta', "ppom" ) . '</a>';
			echo '</div>';
			
		}
		
		ppom_load_template ( 'admin/existing-meta.php' );
		
		echo '</div>';
	}

	function validate_plugin(){
		
		echo '<div class="wrap">';
		echo '<h2>' . __ ( 'Provide API key below:', "ppom" ) . '</h2>';
		echo '<p>' . __ ( 'If you don\'t know your API key, please login into your: <a target="_blank" href="http://wordpresspoets.com/member-area">Member area</a>', "ppom" ) . '</p>';
		
		echo '<form onsubmit="return validate_api_wooproduct(this)">';
			echo '<p><label id="plugin_api_key">'.__('Entery API key', "ppom").':</label><br /><input type="text" name="plugin_api_key" id="plugin_api_key" /></p>';
			wp_nonce_field();
			echo '<p><input type="submit" class="button-primary button" name="plugin_api_key" /></p>';
			echo '<p id="nm-sending-api"></p>';
		echo '</form>';
		
		echo '</div>';
		
	}

}