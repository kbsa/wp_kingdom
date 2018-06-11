<?php
/*
 * this file contains pluing meta information and then shared
 * between pluging and admin classes
 * * [1]
 */

ppom_direct_access_not_allowed();

function ppom_direct_access_not_allowed() {
    if( ! defined('ABSPATH') ) die('Not Allowed.');
}


function ppom_pa($arr){
	
	echo '<pre>';
	print_r($arr);
	echo '</pre>';
}

// Get field column
function ppom_get_field_colum( $meta ) {
	
	$field_column = '';
	if( empty($meta['width']) ) return 12;
	
	// Check width has old settings
	if( strpos( $meta['width'], '%' ) !== false ) {
		
		$field_column = 12;
	} elseif( intval($meta['width']) > 12 ) {
		$field_column = 12;
	} else {
		$field_column = $meta['width'];
	}
	
	return apply_filters('ppom_field_col', $meta['width'], $meta);
}

function ppom_translation_options( $option ) {
	
	$option['option'] = ppom_wpml_translate($option['option'], 'PPOM');
	return $option;
}

/**
 * some WC functions wrapper
 * */
 

if( !function_exists('ppom_wc_add_notice')){
function ppom_wc_add_notice($string, $type="error"){
 	
 	global $woocommerce;
 	if( version_compare( $woocommerce->version, 2.1, ">=" ) ) {
 		wc_add_notice( $string, $type );
	    // Use new, updated functions
	} else {
	   $woocommerce->add_error ( $string );
	}
 }
}

if( !function_exists('ppom_add_order_item_meta') ){
	
	function ppom_add_order_item_meta($item_id, $key, $val){
		
		wc_add_order_item_meta( $item_id, $key, $val );
	}
}

/**
 * WPML
 * registering and translating strings input by users
 */
if( ! function_exists('nm_wpml_register') ) {
	

	function nm_wpml_register($field_value, $domain) {
		
		if ( ! function_exists ( 'icl_register_string' )) 
			return $field_value;
		
		$field_name = $domain . ' - ' . sanitize_key($field_value);
		//WMPL
	    /**
	     * register strings for translation
	     * source: https://wpml.org/wpml-hook/wpml_register_single_string/
	     */
	     
	     do_action( 'wpml_register_single_string', $domain, $field_name, $field_value );
	     
	    //WMPL
		}
}

if( ! function_exists('ppom_wpml_translate') ) {
	

	function ppom_wpml_translate($field_value, $domain) {
		
		$field_name = $domain . ' - ' . sanitize_key($field_value);
		//WMPL
	    /**
	     * register strings for translation
	     * source: https://wpml.org/wpml-hook/wpml_translate_single_string/
	     */
	    
		return apply_filters('wpml_translate_single_string', $field_value, $domain, $field_name );
		//WMPL
	}
}

/**
 * returning order id 
 * 
 * @since 7.9
 */
if ( ! function_exists('nm_get_order_id') ) {
	function nm_get_order_id( $order ) {
		
		$class_name = get_class ($order);
		if( $class_name != 'WC_Order' ) 
			return $order -> ID;
		
		if ( version_compare( WC_VERSION, '2.7', '<' ) ) {  
		
			// vesion less then 2.7
			return $order -> id;
		} else {
			
			return $order -> get_id();
		}
	}
}

/**
 * returning product id 
 * 
 * @since 7.9
 */

function ppom_get_product_id( $product ) {
		
	$product_id = '';
	if ( version_compare( WC_VERSION, '2.7', '<' ) ) {  
	
		// vesion less then 2.7
		$product_id = $product -> ID;
	} else {
		
		$product_id = $product -> get_id();
	}

	
	// WPML Check, if product is translated
	if( function_exists('icl_object_id') && apply_filters('ppom_use_parent_product_ml', false) ) {
		
		$wpml_default_lang = apply_filters( 'wpml_default_language', NULL );
		$product_id = apply_filters( 'wpml_object_id', $product_id, 'product', false, $wpml_default_lang);
	}
	
	return $product_id;
}

// Get product price after some filters like currency switcher
function ppom_get_product_price( $product ) {
	
	$product_price = $product->get_price();
	if( has_filter('woocs_exchange_value') ) {
		$product_price = apply_filters('woocs_exchange_value', $product->get_price());
	}
	
	return apply_filters('ppom_product_price', $product_price, $product);
}

/**
 * check wheather ppom setting allow to send file as attachment
 * @since 8.1
 **/
function ppom_send_file_in_attachment($product_id) {
	
	$product_meta = get_post_meta ( $product_id, '_product_meta_id', true );
	
	if ($product_meta == 0 || $product_meta == 'None')
		return false;
	
	$single_form = PPOM()-> get_product_meta ( $product_meta );
	if( $single_form -> send_file_attachment == 'yes'){
		return true;
	} else {
		return false;
	}
	
}

/**
 * adding cart items to order
 * @since 8.2
 **/
function ppom_make_meta_data( $cart_item, $context="cart" ){
	
	// ppom_pa($cart_item['ppom']);
	
	if( ! isset($cart_item['ppom']['fields']) ) return $cart_item;
		
	// removing id field
	if (isset( $cart_item ['ppom'] ['fields']['id'] )) {
		unset( $cart_item ['ppom'] ['fields']['id']);
	}
	
	$ppom_meta = array(); 
	
	foreach($cart_item['ppom']['fields'] as $key => $value) {
		
		// if no value
		if( $value == '' ) continue;
		
		
		$product_id = $cart_item['data'] ->post_type == 'product' ? $cart_item['data']->get_id() : $cart_item['data']->get_parent_id();
		$field_meta = ppom_get_field_meta_by_dataname( $product_id, $key );
		
		// ppom_pa($field_meta);
		// If field deleted while it's in cart
		if( empty($field_meta) ) continue;
		
		$field_type = isset($field_meta['type']) ? $field_meta['type'] : '';
		$field_title= isset($field_meta['title']) ? $field_meta['title'] : '';
		
		// third party plugin for different fields types
		$field_type = apply_filters('ppom_make_meta_data_field_type', $field_type, $field_meta);
		
		$meta_data = array();
		
		switch( $field_type ) {
			
			case 'quantities':
				$total_qty = 0;
				$qty_values = array();
				foreach($value as $label => $qty) {
					if( !empty($qty) ) {
						$qty_values[] = "{$label} = {$qty}";
						// $ppom_meta[$label] = $qty;
						$total_qty += $qty;	
					}
				}
				$qty_values[] = __('Total','ppom').' = '.$total_qty;
				$meta_data = array('name'=>$field_title, 'value'=>implode(",",$qty_values));
				// A placeholder key to handle qunantity display in item meta data under myaccount
				$ppom_meta['ppom_has_quantities'] = $total_qty;
				break;
				
			case 'file':
				if( $context == 'order') {
					$uploaded_filenames = array();
					foreach($value as $file_id => $file_uploaded) {
						$uploaded_filenames[] = $file_uploaded['org'];
					}
					$meta_data = array('name'=>$field_title, 'value'=>implode(',',$uploaded_filenames));
				} else {
					$file_thumbs_html = '';
					foreach($value as $file_id => $file_uploaded) {
						$file_name = $file_uploaded['org'];
						$file_thumbs_html .= ppom_create_thumb_for_meta($file_name, $product_id);
					}
					$ppom_meta['ppom_has_files'][$key] = $value;
					$meta_data = array('name'=>$field_title, 'value'=>$file_thumbs_html);
					// $ppom_meta[$field_title] = $file_thumbs_html;
				}
				break;
				
			case 'cropper':
				if( $context == 'order') {
					$uploaded_filenames = array();
					foreach($value as $file_id => $file_cropped) {
						$uploaded_filenames[] = $file_cropped['org'];
					}
					$meta_data = array('name'=>$field_title, 'value'=>implode(',',$uploaded_filenames));
				} else {
					$file_thumbs_html = '';
					// ppom_pa($value);
					foreach($value as $file_id => $file_cropped) {
						
						$file_name = $file_cropped['org'];
						$file_thumbs_html = ppom_create_thumb_for_meta($file_name, $product_id, true);
					}
					// $ppom_meta['ppom_has_files'][$key] = $value;
					// $ppom_meta['ppom_cropped_data'][$key] = $value;
					// $ppom_meta[$field_title] = $file_thumbs_html;
					$meta_data = array('name'=>$field_title, 'value'=>$file_thumbs_html);
				}
				break;
				
			case 'image':
				if($value) {
					
					$meta_data = array('name'=>$field_title, 'value'=>ppom_generate_html_for_images($value));
				}
				break;
				
			case 'audio':
				if($value) {
					$ppom_file_count = 1;
					foreach($value as $id => $audio_meta) {
						$audio_meta = json_decode(stripslashes($audio_meta), true);
						$audio_url	= stripslashes($audio_meta['link']);
						$audio_html = '<a href="'.esc_url($audio_url).'" title="'.esc_attr($audio_meta['title']).'">'.$audio_meta['title'].'</a>';
						$meta_lable	= $field_title.': '.$ppom_file_count++;
						// $ppom_meta[$meta_lable] = $audio_html;
						$meta_data = array('name'=>$meta_lable, 'value'=>$audio_html);
					}
				}
				break;
				
			case 'bulkquantity':
				
				$bq_value = $value['option'].' ('.$value['qty'].')';
				// $ppom_meta[$key] = $value['option'].' ('.$value['qty'].')';
				$meta_data = array('name'=>$key, 'value'=>$bq_value);
				// A placeholder key to handle qunantity display in item meta data under myaccount
				$ppom_meta['ppom_has_quantities'] = $value['qty'];
				break;
				
			// NOTE: We have DISABLE this due to REST API values
			/*case 'checkbox':
				
				
				$option_posted = $value;
				
				$option_value_array = array();
				
				$product = new WC_Product($product_id);
				$options_filter	 = ppom_convert_options_to_key_val($field_meta['options'], $field_meta, $product);
				
				foreach($option_posted as $posted_value) {
					foreach($options_filter as $option_key => $option) {
	                    
	                    $option_value = stripslashes($option['raw']);
	                    
	                    if(  $posted_value == $option_value ) {
	                        $option_value_array[] = $option['label'];
	                    }
	                }
				}
				
				$meta_data = array('name'=>$field_title, 'value'=> implode(',',$option_value_array));
				break;
				
			case 'select':
			case 'radio':
				
				$posted_value = stripslashes($value);
				
				$option_price = '';
				
				$product = new WC_Product($product_id);
				$options_filter	 = ppom_convert_options_to_key_val($field_meta['options'], $field_meta, $product);
			
				foreach($options_filter as $option_key => $option) {
	                    
                    $option_value = stripslashes($option['raw']);
                    
                    if(  $posted_value == $option_value ) {
                        $option_price = $option['label'];
                        break;
                    }
                }
				
				$meta_data = array('name'=>$field_title, 'value'=> $option_price);
				break;*/
				
			default:
				$value = is_array($value) ? implode(",", $value) : $value;
				// $ppom_meta[$field_title] = stripcslashes($value);
				$meta_data = array('name'=>$field_title, 'value'=>stripcslashes($value));
				break;
		}
		
		
		// Getting option price if field have
		$option_price = ppom_get_field_option_price( $field_meta, $value );
		if( $option_price != 0 ) {
			$meta_data['price'] = $option_price;
		}
		
		$ppom_meta[$key] = $meta_data;
	}
	
	
	// ppom_pa($ppom_meta);
	return apply_filters('ppom_meta_data', $ppom_meta, $cart_item);
}

/**
* hiding prices for variable product
* only when priced options are used
* 
* @since 8.2
**/
function ppom_meta_priced_options( $the_meta ) {
	
	$has_priced_option = false;
	foreach ( $the_meta as $key => $meta ) {
	
		$options		= ( isset($meta['options'] ) ? $meta['options'] : array());
		foreach($options as $opt)
		{
				
			if( isset($opt['price']) && $opt['price'] != '') {
				$has_priced_option = true;
			}
		}
	}
	
	return apply_filters('ppom_meta_priced_options', $has_priced_option, $the_meta);
}

/**
 * check if browser is IE
 **/
function ppom_if_browser_is_ie()
{
	//print_r($_SERVER['HTTP_USER_AGENT']);
	
	if(!(isset($_SERVER['HTTP_USER_AGENT']) && (strpos($_SERVER['HTTP_USER_AGENT'], 'Trident') !== false || strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false))){
		return false;
	}else{
		return true;
	}
}

// parsing viary tools to array notation
function ppom_get_editing_tools( $editing_tools ){

	parse_str ( $editing_tools, $tools );
	if (isset( $tools['editing_tools'] ) && $tools['editing_tools'])
		return implode(',', $tools['editing_tools']);
}

/**
 * Check if selected meta as input type included
 * return input: data_name
 * 
 **/
function ppom_has_posted_field_value( $posted_fields, $field ) {
	
	$has_value = false;
	
	$data_name = $field['data_name'];
	
	if( !empty($posted_fields) ) {
		foreach($posted_fields as $field_key => $value){
			
			
			if( $field_key == $data_name) {
				
				if( $value != '' ) {
					$has_value = true;
				}
				
				if( $has_value ) break;
			}
		}
	}
	
	return apply_filters('ppom_has_posted_field_value', $has_value, $posted_fields, $field);
}

function ppom_load_file_upload_js($product) {
	
	$product_meta_id = get_post_meta ( $product -> ID, '_product_meta_id', true );
	
	if ($product_meta_id == "" || $product_meta_id == 'None')
		return;
		
	$product_meta = PPOM() -> get_product_meta($product_meta_id);
	
	$ppom_fields = json_decode ( $product_meta->the_meta );
	
	$file_inputs = array();
	
	
	foreach($ppom_fields as $field){
		
		if( $field->type == 'file' ){
			
			$file_cost = $field -> file_cost == '' ? array('') : array(sprintf(__("File Charges (%s)", "nm_personalizedproduct"), $field->title) => array('fee' => $field -> file_cost, 'taxable' => $field->onetime_taxable));
			$file_cost = json_encode($file_cost);
			
			$field -> editing_tools = ppom_get_editing_tools($field -> editing_tools);
			$field -> file_cost = $file_cost;
			$field -> cropping_ratio = $field ->cropping_ratio == '' ? NULL : explode("\n", $field ->cropping_ratio);
			
			// aviary crop preset
			if( $field -> cropping_ratio != '') {
				
				$crop_preset = $field -> cropping_ratio;
				$js_crop_preset = '';
				if($crop_preset){
					$js_crop_preset = '[';
					foreach($crop_preset as $preset){
						$js_crop_preset .= "'".str_replace('/', 'x', $preset)."',";
					}
					$js_crop_preset = rtrim($js_crop_preset, ',');
					$js_crop_preset .= ']';
				}
				
				$field -> aviary_crop_preset = $js_crop_preset;
			}
			
			$file_inputs[] = $field;
		}
	}
	
	if( empty( $file_inputs ) ) return;
	
	// ppom_pa($file_inputs);
	
	wp_enqueue_script( 'ppom-file-upload', PPOM()->plugin_meta['url'].'/js/file-upload.js', array('jquery', 'plupload'), '8.4', true);
	$ppom_file_vars = array('file_inputs'		=> $file_inputs,
							'delete_file_msg'	=> __("Are you sure?", "ppom"),
							'aviary_api_key'	=> $product_meta -> aviary_api_key,
							'plupload_runtime'	=> (ppom_if_browser_is_ie()) ? 'html5,html4' : 'html5,silverlight,html4,browserplus,gear');
	wp_localize_script( 'ppom-file-upload', 'ppom_file_vars', $ppom_file_vars);
	
	// if Aviary Editor is used
	if($product_meta -> aviary_api_key != ''){
		
		if(is_ssl()){
			wp_enqueue_script( 'aviary-api', '//dme0ih8comzn4.cloudfront.net/js/feather.js');	
		}else{
			wp_enqueue_script( 'aviary-api', '//feather.aviary.com/imaging/v1/editor.js');	
		}
	}
}


function ppom_is_aviary_installed() {

	if( is_plugin_active('nm-aviary-photo-editing-addon/index.php') ){
		return true;
	}else{
		return false;
	}
	
}

// Return ammount after apply percent
function ppom_get_amount_after_percentage($base_amount, $percent) {
	
	$base_amount = floatval($base_amount);
	$percent_amount = 0;
	$percent		= substr( $percent, 0, -1 );
	$percent_amount	= wc_format_decimal( (floatval($percent) / 100) * $base_amount, wc_get_price_decimals());
	
	return $percent_amount;
}

function ppom_settings_link($links) {
	
	$quote_url = "https://najeebmedia.com/get-quote/";
	$ppom_setting_url = admin_url( 'admin.php?page=ppom');
	
	$ppom_links = array();
	$ppom_links[] = sprintf(__('<a href="%s">Add Fields</a>', 'ppom'), esc_url($ppom_setting_url) );
	$ppom_links[] = sprintf(__('<a href="%s">Request for Customized Solution</a>', 'ppom'), esc_url($quote_url) );
	
	foreach($ppom_links as $link) {
		
  		array_push( $links, $link );
	}
	
  	return $links;
}

// check if product has meta Returns Meta ID if true otherwise null
function ppom_has_product_meta( $product_id ) {
	
	$ppom_meta_id = get_post_meta ( $product_id, PPOM_PRODUCT_META_KEY, true );
	
	if( $ppom_meta_id == 0 || $ppom_meta_id == 'None' ) {
		
		$ppom_meta_id = null;
	}
	
	return $ppom_meta_id;
}

// Get field type by data_name
function ppom_get_field_meta_by_dataname( $product_id, $data_name ) {
	
	if( ! $selected_meta_id = ppom_has_product_meta($product_id) ) return '';
	
	$ppom_settings = PPOM() -> get_product_meta ( $selected_meta_id );
	if( empty($ppom_settings) ) return '';
	
	$fields_meta = json_decode ( $ppom_settings->the_meta, true );
	$field_meta = '';
	foreach($fields_meta as $field) {
	
		if( !empty($field['data_name']) && sanitize_key($field['data_name']) == $data_name) {
			$field_meta = $field;
			break;
		}
	}
	
	return $field_meta;
}

// Is PPOM meta has field of specific type
function ppom_has_field_by_type( $product_id, $field_type ) {
	
	if( ! $selected_meta_id = ppom_has_product_meta($product_id) ) return '';
	$ppom_settings = PPOM() -> get_product_meta ( $selected_meta_id );
	
	if( !$ppom_settings ) return '';
	
	$fields_meta = json_decode ( $ppom_settings->the_meta, true );
	
	$fields_found = array();
	foreach($fields_meta as $field) {
		
		if( !empty($field['type']) && $field['type'] == $field_type ) {
			$fields_found[] = $field;
		}
	}
	
	return $fields_found;
}

function ppom_load_template($file_name, $variables=array('')){

	if( is_array($variables))
    extract( $variables );
    
   $file_path =  PPOM_PATH . '/templates/'.$file_name;
   if( file_exists($file_path))
   	include ($file_path);
   else
   	die('File not found'.$file_path);
}

// load file from full given path
function ppom_load_file($file_path, $variables=array('')){

	if( is_array($variables))
    extract( $variables );
    
   if( file_exists($file_path))
   	include ($file_path);
   else
   	die('File not found'.$file_path);
}

function ppom_load_bootstrap_css() {
	
	$return = true;
	if( defined('NMOYE_THEME_VERSION') ) $return = false;
	
	return apply_filters('ppom_bootstrap_css', $return);
}

function ppom_has_category_meta( $product_id ) {
		
	$p_categories = get_the_terms($product_id, 'product_cat');
	$meta_found = false;
	 if($p_categories){
	 	
	 	global $wpdb;
		$ppom_table = $wpdb->prefix . PPOM_TABLE_META;
		
		$qry = "SELECT * FROM {$ppom_table}";
		$qry .= " WHERE productmeta_categories != ''";
		$meta_with_cats = $wpdb->get_results ( $qry );
		
		
		foreach($meta_with_cats as $meta_cats){
			
			if( $meta_found )	//if we found any meta so dont need to loop again
				continue;
			
			if( $meta_cats->productmeta_categories == 'All' ) {
				$meta_found = $meta_cats->productmeta_id;
			}else{
				//making array of meta cats
				$meta_cat_array = explode("\n", $meta_cats->productmeta_categories);
				
				//Now iterating the p_categories to check it's slug in meta cats
				foreach($p_categories as $cat) {
					
					if( in_array($cat->slug, $meta_cat_array) ) {
						$meta_found = $meta_cats->productmeta_id;
					}
				}
			}

		}
	 }
	 
	 return $meta_found;
}

function ppom_convert_options_to_key_val($options, $meta, $product) {
	
	if( empty($options) ) return $options;
	
	// ppom_pa($options);
	
	$ppom_new_option = array();
	foreach($options as $option) {
		
		if( isset($option['option']) ) {
			
			$option_price	= '';
			$option_price_without_tax	= '';
			$option_label	= $option['option'];
			$option_percent = '';
			
			$show_price		= isset($meta['show_price']) ? $meta['show_price'] : '';
			$data_name		= isset($meta['data_name']) ? $meta['data_name'] : '';
			
			// Price matrix discount
			$discount	= isset($meta['discount']) && $meta['discount'] == 'on' ? true : false;
			$discount_type	= isset($meta['discount_type']) ? $meta['discount_type'] : 'base';
			
			// $show_option_price = apply_filters('ppom_show_option_price', $show_price, $meta);
			if( !empty($option['price'])) {
				
				$option_price = $option['price'];
				
				// check if price in percent
				if(strpos($option['price'],'%') !== false){
					$option_price = ppom_get_amount_after_percentage($product->get_price(), $option['price']);
					// check if price is fixed and taxable
					if(isset($meta['onetime']) && $meta['onetime'] == 'on' && $meta['onetime_taxable'] == 'on') {
						$option_price_without_tax = $option_price;
						$option_price = ppom_get_price_including_tax($option_price, $product);
					}
					
					$option_label = $option['option'] . ' ('.wc_price($option_price).')';
					$option_percent = $option['price'];
				} else {
					
					// check if price is fixed and taxable
					if(isset($meta['onetime']) && $meta['onetime'] == 'on' && $meta['onetime_taxable'] == 'on') {
						$option_price_without_tax = $option_price;
						$option_price = ppom_get_price_including_tax($option_price, $product);
					}
					$option_label = $option['option'] . ' ('.wc_price($option_price).')';
				}
				
			} else {
				
				$option_label = $option['option'];
				$option_price = $option['price'];
				// $ppom_new_option[$option['option']] = $option['option'];
			}
			
			$the_option = stripslashes($option['option']);
			
			$option_id = ppom_get_option_id($option, $data_name);
			
			$ppom_new_option[$the_option] = array('label'	=> apply_filters('ppom_option_label', stripcslashes($option_label), $option, $meta, $product), 
														'price'	=> apply_filters('ppom_option_price', $option_price, $option, $meta, $product),
														'raw'	=> $the_option,
														'without_tax'=>$option_price_without_tax,
														'percent'=> $option_percent,
														'option_id' => $option_id);
														
			if( $discount ) {
				$ppom_new_option[$the_option]['discount'] = $discount_type;
			}
			
		}
	}
	
	if( !empty($meta['first_option']) ) {
		$ppom_new_option[''] = array('label'=>sprintf(__("%s","ppom"),$meta['first_option']), 
										'price'	=> '',
										'raw'	=> '',
										'without_tax' => '');
	}
	
	// ppom_pa($ppom_new_option);
	return apply_filters('ppom_options_after_changes', $ppom_new_option, $options, $meta, $product);
}


// Retrun unique option ID
function ppom_get_option_id($option, $data_name=null) {
	
	$default_option = is_null($data_name) ? $option['option'] : $data_name.'_'.$option['option'];
	
	$option_id = empty($option['id']) ? $default_option : $option['id'];

	return apply_filters('ppom_option_id', sanitize_key( $option_id ), $option, $data_name );
}

function ppom_get_price_including_tax( $price, $product ) {
	
	if(  'incl' !== get_option( 'woocommerce_tax_display_shop' ) ) return $price;
	
	$line_price   = $price;
	$return_price = $line_price;

	$tax_rates    = WC_Tax::get_rates( $product->get_tax_class() );
	$taxes        = WC_Tax::calc_tax( $line_price, $tax_rates, false );
	$tax_amount   = WC_Tax::get_tax_total( $taxes );
	$return_price = round( $line_price + $tax_amount, wc_get_price_decimals() );
	return $return_price;
	
	if ( $product->is_taxable() ) {
		if ( ! wc_prices_include_tax() ) {
			$tax_rates    = WC_Tax::get_rates( $product->get_tax_class() );
			$taxes        = WC_Tax::calc_tax( $line_price, $tax_rates, false );
			$tax_amount   = WC_Tax::get_tax_total( $taxes );
			$return_price = round( $line_price + $tax_amount, wc_get_price_decimals() );
		} else {
			$tax_rates      = WC_Tax::get_rates( $product->get_tax_class() );
			$base_tax_rates = WC_Tax::get_base_tax_rates( $product->get_tax_class( 'unfiltered' ) );

			/**
			 * If the customer is excempt from VAT, remove the taxes here.
			 * Either remove the base or the user taxes depending on woocommerce_adjust_non_base_location_prices setting.
			 */
			if ( ! empty( WC()->customer ) && WC()->customer->get_is_vat_exempt() ) {
				$remove_taxes = apply_filters( 'woocommerce_adjust_non_base_location_prices', true ) ? WC_Tax::calc_tax( $line_price, $base_tax_rates, true ) : WC_Tax::calc_tax( $line_price, $tax_rates, true );
				$remove_tax   = array_sum( $remove_taxes );
				$return_price = round( $line_price - $remove_tax, wc_get_price_decimals() );

			/**
			 * The woocommerce_adjust_non_base_location_prices filter can stop base taxes being taken off when dealing with out of base locations.
			 * e.g. If a product costs 10 including tax, all users will pay 10 regardless of location and taxes.
			 * This feature is experimental @since 2.4.7 and may change in the future. Use at your risk.
			 */
			} elseif ( $tax_rates !== $base_tax_rates && apply_filters( 'woocommerce_adjust_non_base_location_prices', true ) ) {
				$base_taxes   = WC_Tax::calc_tax( $line_price, $base_tax_rates, true );
				$modded_taxes = WC_Tax::calc_tax( $line_price - array_sum( $base_taxes ), $tax_rates, false );
				$return_price = round( $line_price - array_sum( $base_taxes ) + wc_round_tax_total( array_sum( $modded_taxes ), wc_get_price_decimals() ), wc_get_price_decimals() );
			}
		}
	}
	return apply_filters( 'ppom_get_price_including_tax', $return_price, $product);
}

// Check if field conditionally hidden
function ppom_is_field_hidden_by_condition( $field_name ) {
	
	if( !isset($_POST['ppom']['conditionally_hidden']) ) return false;
	
	$ppom_is_hidden = false;
	
	$ppom_hidden_fields = explode(",", $_POST['ppom']['conditionally_hidden']);
	// Remove duplicates
	$ppom_hidden_fields = array_unique( $ppom_hidden_fields );
	
	if( in_array($field_name, $ppom_hidden_fields) ) {
		
		$ppom_is_hidden = true;
	}
	
	return apply_filters('ppom_is_field_hidden_by_condition', $ppom_is_hidden);
}

// Get cart item max quantity for matrix
function ppom_get_cart_item_max_quantity( $cart_item ) {
	
	$max_quantity = null;
	if( isset($cart_item['ppom']['ppom_pricematrix']) ) {
		$matrix 	= json_decode( stripslashes($cart_item['ppom']['ppom_pricematrix']) );
		$last_range = end($matrix);
		$qty_ranges = explode('-', $last_range->raw);
		$max_quantity	= $qty_ranges[1];
	}
	
	return $max_quantity;
}

// Get price matrix by quantity
function ppom_get_price_matrix_by_quantity($product, $quantity) {
	
	$quantities_field = ppom_has_field_by_type($product->get_id(), 'pricematrix');
	if( ! isset($quantities_field[0]) ) return '';
	
	$quantities_field = $quantities_field[0];
	
	$matrix_found = ppom_extract_matrix_by_quantity($quantities_field, $product, $quantity);
	
	return $matrix_found;
}

// Get Discount matrix by quantity
function ppom_get_discount_matrix_by_quantity($product, $quantity) {
	
	$quantities_field = ppom_has_field_by_type($product->get_id(), 'pricematrix');
	if( ! isset($quantities_field[0]) || $quantities_field[0]['discount'] == '' ) return '';
	
	$quantities_field = $quantities_field[0];
	
	$matrix_found = ppom_extract_matrix_by_quantity($quantities_field, $product, $quantity);
	
	return $matrix_found;
}

// Extract relevant matrix from Matrix Range given by quantity
function ppom_extract_matrix_by_quantity($quantities_field, $product, $quantity) {
	
	$options	= $quantities_field['options'];
	$ranges	 = ppom_convert_options_to_key_val($options, $quantities_field, $product);
	
	$matrix = '';
	foreach ($ranges as $range => $data) {
		
		$range_array	= explode('-', $range);
		$range_start	= $range_array[0];
		$range_end		= $range_array[1];
		
		$quantity = intval($quantity);
		if( $quantity >= $range_start && $quantity <= $range_end ) {
			$matrix = $data;
			break;
		}
	}
	
	return $matrix;
}

// Return thumbs size
function ppom_get_thumbs_size() {
	
	return apply_filters('ppom_thumbs_size', '75px');
}

// Return file size in kb
function ppom_get_filesize_in_kb( $file_name ) {
		
	$base_dir = ppom_get_dir_path();
	$file_path = $base_dir . 'confirmed/' . $file_name;
	
	if (file_exists($file_path)) {
		$size = filesize ( $file_path );
		return round ( $size / 1024, 2 ) . ' KB';
	}elseif(file_exists( $base_dir . '/' . $file_name ) ){
		$size = filesize ( $base_dir . '/' . $file_name );
		return round ( $size / 1024, 2 ) . ' KB';
	}
	
}


// Generating html for file input and cropper in order meta from filename
function ppom_generate_html_for_files( $file_names, $input_type, $item ) {
	
	$file_name_array = explode(',', $file_names);
	
	$order_html = '<table>';
	foreach($file_name_array as $file_name) {
		
			$file_edit_path = ppom_get_dir_path('edits').ppom_file_get_name($file_name, $item->get_product_id());
			
			// Making file thumb download with new path
			$ppom_file_url = ppom_get_file_download_url( $file_name, $item->get_order_id(), $item->get_product_id());
			$ppom_file_thumb_url = ppom_get_dir_url(true) . $file_name;
			$order_html .= '<tr><td><a href="'.esc_url($ppom_file_url).'">';
			$order_html .= '<img class="img-thumbnail" style="width:'.esc_attr(ppom_get_thumbs_size()).'" src="'.esc_url($ppom_file_thumb_url).'">';
			$order_html .= '</a></td>';
			$order_html .= '<td><a class="button" href="'.esc_url($ppom_file_url).'">';
			$order_html .= __('Download File', 'ppom');
			$order_html .= '</a></td></tr>';
			
			if( $input_type == 'cropper' ) {
				
					$cropped_file_name = ppom_file_get_name($file_name, $item->get_product_id());
					$cropped_url = ppom_get_dir_url() . 'cropped/' . $cropped_file_name;
					$order_html .= '<tr><td><a href="'.esc_url($cropped_url).'">';
					$order_html .= '<img style="width:'.esc_attr(ppom_get_thumbs_size()).'" class="img-thumbnail" src="'.esc_url($cropped_url).'">';
					$order_html .= '</a></td>';
					$order_html .= '<td><a class="button" href="'.esc_url($cropped_url).'">';
					$order_html .= __('Cropped', 'ppom');
					$order_html .= '</a></td></tr>';
			} elseif( file_exists($file_edit_path) ) {
				
				$edit_file_name = ppom_file_get_name($file_name, $item->get_product_id());
				$edit_url = ppom_get_dir_url() . 'edits/' . $edit_file_name;
				$edit_thumb_url = ppom_get_dir_url() . 'edits/thumbs/' . $file_name;
				$order_html .= '<tr><td><a href="'.esc_url($edit_url).'">';
				$order_html .= '<img style="width:'.esc_attr(ppom_get_thumbs_size()).'" class="img-thumbnail" src="'.esc_url($edit_thumb_url).'">';
				$order_html .= '</a></td>';
				$order_html .= '<td><a class="button" href="'.esc_url($edit_url).'">';
				$order_html .= __('Edited', 'ppom');
				$order_html .= '</a></td></tr>';
			}
	}
	$order_html .= '</table>';
	
	return apply_filters('ppom_order_files_html', $order_html, $file_names, $input_type, $item);
}


// return html for images selected
function ppom_generate_html_for_images( $images ) {
	
	
	$ppom_html	=  '<table class="table table-bordered">';
	foreach($images as $id => $images_meta) {
		
		$images_meta	= json_decode(stripslashes($images_meta), true);
		$image_url		= stripslashes($images_meta['link']);
		$image_label	= $images_meta['title'];
		$image_html 	= '<img class="img-thumbnail" style="width:'.esc_attr(ppom_get_thumbs_size()).'" src="'.esc_url($image_url).'" title="'.esc_attr($image_label).'">';
		
		$ppom_html	.= '<tr><td><a href="'.esc_url($image_url).'" class="lightbox" itemprop="image" title="'.esc_attr($image_label).'">' . $image_html . '</a></td>';
		$ppom_html	.= '<td>' .esc_attr(ppom_files_trim_name( $image_label )) . '</td>';
		$ppom_html	.= '</tr>';
		
	}
	
	$ppom_html .= '</table>';
	
	return apply_filters('ppom_images_html', $ppom_html);
}

// Getting field option price
function ppom_get_field_option_price( $field_meta, $option_label ) {
	
	// var_dump($field_meta['options']);
	if( ! isset( $field_meta['options']) || $field_meta['type'] == 'bulkquantity' ) return 0;
	
	$option_price = 0;
	foreach( $field_meta['options'] as $option ) {
		
		if( $option['option'] == $option_label && isset($option['price']) && $option['price'] != '' ) {
			
			$option_price = $option['price'];
		}
	}
	
	return apply_filters("ppom_field_option_price", intval($option_price), $field_meta, $option_label);
}

// check if PPOM PRO is installed
function ppom_pro_is_installed() {
	
	$return = false;
	    
    if( class_exists('PPOM_PRO') ) 
        $return = true;
    return $return;
}

// Check if field is visible
function ppom_is_field_visible( $field ) {
	
	if( ! ppom_pro_is_installed() ) return true;
	
	$visibility = isset($field['visibility']) ? $field['visibility'] : 'everyone';
	$visibility_role = isset($field['visibility_role']) ? $field['visibility_role'] : '';
	
	$is_visible = false;
	switch( $visibility ) {
		
		case 'everyone':
			$is_visible = true;
			break;
			
		case 'members':
			if( is_user_logged_in() ) {
				$is_visible = true;
			}
			break;
			
		case 'guests':
			if( ! is_user_logged_in() ) {
				$is_visible = true;
			}
			break;
			
		case 'roles':
			$role = ppom_get_current_user_role();
			$allowed_roles = explode(',', $visibility_role);
			
			if( in_array($role, $allowed_roles) ) {
				$is_visible = true;
			}
			break;
	}
	
	return apply_filters('ppom_is_field_visible', $is_visible, $field);
	
}

// Get logged in user role
function ppom_get_current_user_role() {
  
	if( is_user_logged_in() ) {
		$user = wp_get_current_user();
		$role = ( array ) $user->roles;
		return $role[0];
	} else {
		return false;
	}
}