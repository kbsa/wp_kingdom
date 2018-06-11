<?php
/*
 * Followig class handling date input control and their
* dependencies. Do not make changes in code
* Create on: 9 November, 2013
*/

class NM_Date_wooproduct extends PPOM_Inputs{
	
	/*
	 * input control settings
	 */
	var $title, $desc, $settings;
	
	/*
	 * this var is pouplated with current plugin meta
	*/
	var $plugin_meta;
	
	function __construct(){
		
		$this -> plugin_meta = ppom_get_plugin_meta();
		
		$this -> title 		= __ ( 'Date Input', 'ppom' );
		$this -> desc		= __ ( 'regular date input', 'ppom' );
		$this -> settings	= self::get_settings();
		
	}
	
	
	
	
	private function get_settings(){
		
		return array (
		'title' => array (
				'type' => 'text',
				'title' => __ ( 'Title', 'ppom' ),
				'desc' => __ ( 'It will be shown as field label', 'ppom' ) 
		),
		'data_name' => array (
				'type' => 'text',
				'title' => __ ( 'Data name', 'ppom' ),
				'desc' => __ ( 'REQUIRED: The identification name of this field, that you can insert into body email configuration. Note:Use only lowercase characters and underscores.', 'ppom' ) 
		),
		'description' => array (
				'type' => 'text',
				'title' => __ ( 'Description', 'ppom' ),
				'desc' => __ ( 'Small description, it will be diplay near name title.', 'ppom' ) 
		),
		'error_message' => array (
				'type' => 'text',
				'title' => __ ( 'Error message', 'ppom' ),
				'desc' => __ ( 'Insert the error message for validation.', 'ppom' ) 
		),
		
		'required' => array (
				'type' => 'checkbox',
				'title' => __ ( 'Required', 'ppom' ),
				'desc' => __ ( 'Select this if it must be required.', 'ppom' ) 
		),
		'class' => array (
				'type' => 'text',
				'title' => __ ( 'Class', 'ppom' ),
				'desc' => __ ( 'Insert an additional class(es) (separateb by comma) for more personalization.', 'ppom' ) 
		),
		'default_value' => array (
				'type' => 'text',
				'title' => __ ( 'Default Date', 'ppom' ),
				'desc' => __ ( 'User format YYYY-MM-DD e.g: 2017-05-25.', 'ppom' ),
		),
		'width' => array (
				'type' => 'select',
				'title' => __ ( 'Width', 'ppom' ),
				'desc' => __ ( 'Type field width in % e.g: 50%', 'ppom'),
				'options'	=> ppom_get_input_cols(),
				'default'	=> 12,
		),
		'visibility' => array (
				'type' => 'select',
				'title' => __ ( 'Visibility', 'ppom' ),
				'desc' => __ ( 'Set field visibility based on user.', 'ppom'),
				'options'	=> ppom_field_visibility_options(),
				'default'	=> 'everyone',
		),
		'visibility_role' => array (
				'type' => 'text',
				'title' => __ ( 'User Roles', 'ppom' ),
				'desc' => __ ( 'Role separated by comma.', 'ppom'),
				'hidden' => true,
		),
		'jquery_dp' => array (
				'type' => 'checkbox',
				'title' => __ ( 'Use jQuery Datepicker', 'ppom' ),
				'desc' => __ ( 'It will load jQuery fancy datepicker.', 'ppom' ) 
		),
		'date_formats' => array (
				'type' => 'select',
				'title' => __ ( 'Date formats', 'ppom' ),
				'desc' => __ ( 'Select date format. (if jQuery enabled above)', 'ppom' ),
				'options' => array (
						'mm/dd/yy' => 'Default - mm/dd/yyyy',
						'dd/mm/yy' => 'dd/mm/yyyy',
						'yy-mm-dd' => 'ISO 8601 - yy-mm-dd',
						'd M, y' => 'Short - d M, y',
						'd MM, y' => 'Medium - d MM, y',
						'DD, d MM, yy' => 'Full - DD, d MM, yy',
						'\'day\' d \'of\' MM \'in the year\' yy' => 'With text - \'day\' d \'of\' MM \'in the year\' yy',
						'\'Month\' MM \'day\' d \'in the year\' yy' => 'With text - \'Month\' January \'day\' 7 \'in the year\' yy'
				) 
		),
		'year_range' => array (
				'type' => 'text',
				'title' => __ ( 'Year Range', 'ppom' ),
				'desc' => __ ( 'e.g: 1950:2016. (if jQuery enabled above) Set star/end year like <a target="_blank" href="http://api.jqueryui.com/datepicker/#option-yearRange">Here</a>', 'ppom' ) 
		),
		'past_dates' => array (
				'type' => 'checkbox',
				'title' => __ ( 'Disable Past Dates?', 'ppom' ),
				'desc' => __ ( 'It will disable past dates.', 'ppom' ) 
		),
		'no_weekends' => array (
				'type' => 'checkbox',
				'title' => __ ( 'Disable Weekends?', 'ppom' ),
				'desc' => __ ( 'It will disable Weekends.', 'ppom' ) 
		),
		'logic' => array (
				'type' => 'checkbox',
				'title' => __ ( 'Enable Conditions', 'ppom' ),
				'desc' => __ ( 'Tick it to turn conditional logic to work below', 'ppom' )
		),
		'conditions' => array (
				'type' => 'html-conditions',
				'title' => __ ( 'Conditions', 'ppom' ),
				'desc' => __ ( 'Tick it to turn conditional logic to work below', 'ppom' )
		),
		);
	}
	
	
	/*
	 * @params: args
	*/
	function render_input($args, $content=""){
		
		$_html = '<input type="text" ';
		
		foreach ($args as $attr => $value){
			
			$_html .= $attr.'="'.stripslashes( $value ).'"';
		}
		
		if($content)
			$_html .= 'value="' . stripslashes($content	) . '"';
		
		$_html .= ' />';
		
		echo $_html;
		
		$this -> get_input_js($args);
	}
	
	/*
	 * following function is rendering JS needed for input
	*/
	function get_input_js($args){
	?>
		
				<script type="text/javascript">	
				<!--
				jQuery(function($){

					$("#<?php echo $args['id'];?>").datepicker("destroy");
					
					$("#<?php echo $args['id'];?>").datepicker({ 	
						changeMonth: true,
						changeYear: true,
						dateFormat: $("#<?php echo $args['id'];?>").attr('data-format'),
						defaultDate: "01-01-1964"
						});
				});
				
				//--></script>
				<?php
		}
}