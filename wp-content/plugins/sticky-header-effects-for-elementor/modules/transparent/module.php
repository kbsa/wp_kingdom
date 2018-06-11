<?php
namespace SheHeader\Modules\Transparent;

use Elementor\Controls_Manager;
use Elementor\Controls_Stack;
use SheHeader\Base\Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Module_Base {

	public function __construct() {
		parent::__construct();

		$this->add_actions();
	}

	public function get_name() {
		return 'transparent';
	}

	public function register_controls( Controls_Stack $element ) {
		$element->start_controls_section(
			'section_sticky_header_effect',
			[
				'label' => __( 'Sticky Header Effects', 'she-header' ),
				'tab' => Controls_Manager::TAB_ADVANCED,
				'condition' => [
					'stretch_section' => ''					
				],
			]
		);

		$element->add_control(
			'transparent',
			[
				'label' => __( 'Enable', 'she-header' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'On', 'she-header' ),
				'label_off' => __( 'Off', 'bew-header' ),
				'return_value' => 'yes',
				'default' => '',
				'frontend_available' => true,
				'prefix_class'  => 'she-header-'
			]
		);

		$element->add_control(
			'transparent_on',
			[
				'label' => __( 'Enable On', 'she-header' ),
				'type' => Controls_Manager::SELECT2,
				'multiple' => true,
				'label_block' => 'true',
				'default' => [ 'desktop', 'tablet', 'mobile' ],
				'options' => [
					'desktop' => __( 'Desktop', 'she-header' ),
					'tablet' => __( 'Tablet', 'she-header' ),
					'mobile' => __( 'Mobile', 'she-header' ),
				],
				'condition' => [
					'stretch_section' => '',
					'transparent!' => ''
				],
				'render_type' => 'none',
				'frontend_available' => true,
			]
		);
				
		$element->add_responsive_control(
			'scroll_distance',
			[
				'label' => __( 'Scroll Distance (px)', 'she-header' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 60,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'size_units' => [ 'px'],				
				'description' => __( 'Choose the scroll distance to enabled the transparent effect', 'she-header' ),
				'condition' => [
					'transparent!' => '',
				],
			]
		);
		
		
		$element->add_control(
			'background',
			[
				'label' => __( 'Background Color', 'she-header' ),
				'type' => Controls_Manager::COLOR,				
				'label_block' => 'true',				
				'condition' => [
					'stretch_section' => '',
					'transparent!' => ''
				],
				'render_type' => 'none',
				'frontend_available' => true,				
			]
		);
		
		$element->add_control(
			'shrink_header',
			[
				'label' => __( 'Shrink', 'she-header' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'On', 'she-header' ),
				'label_off' => __( 'Off', 'she-header' ),
				'return_value' => 'yes',
				'default' => '',
				'frontend_available' => true,
				'condition' => [
					'transparent!' => '',
				],
			]
		);
				
		$element->add_responsive_control(
			'custom_height_header',
			[
				'label' => __( 'Height (px)', 'she-header' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 70,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'size_units' => [ 'px'],
				'description' => __( 'Choose the header height after scrolling', 'she-header' ),
				'condition' => [
					'shrink_header' => 'yes',
					'transparent!' => '',
				],
				'frontend_available' => true,
			]
		);
		

		$element->end_controls_section();
	}

	private function add_actions() {
		if( !function_exists('is_plugin_active') ) {
			
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			
		}
		
		if( is_plugin_active( 'elementor-pro/elementor-pro.php' ) ) {
		add_action( 'elementor/element/section/section_scrolling_effect/after_section_end', [ $this, 'register_controls' ] );
		} else {
		add_action( 'elementor/element/section/section_advanced/after_section_end', [ $this, 'register_controls' ] );	
		}
		
		add_action( 'elementor/frontend/element/after_render', [ $this, 'after_render'], 10, 1 );
		
		add_action( 'elementor/frontend/after_enqueue_styles', [ $this, 'enqueue_styles' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
	}
	
	public function after_render($element) {
		$settings 		= $element->get_settings(); 		
		if( $element->get_settings( 'transparent' ) == 'yes' ) { 
		$background				= $settings['background'];
		$scroll_distance 		= $settings['scroll_distance'];
		$scroll_distance_size	= $scroll_distance['size']
		
		?>			
			<script type="text/javascript">	
				( function( $ ) {
					"use strict";
					var sheTransparentElementorFront = {
						init: function() {
							elementorFrontend.hooks.addAction('frontend/element_ready/global', sheTransparentElementorFront.initWidget);
						},
						initWidget: function( $scope ) {
							
						var header = $('.she-header-yes');
						var container = $('.she-header-yes .elementor-container');
						
						var data_settings = header.data('settings');
						var background = data_settings["background"];
						var data_height = data_settings["custom_height_header"];
						var data_height_tablet = data_settings["custom_height_header_tablet"];
						var data_height_mobile = data_settings["custom_height_header_mobile"];						
						var width = $(window).width();
						
						if( typeof data_height != 'undefined' && data_height) {		
						if( width > 768 ) {
						var shrink_height = data_height["size"];
						}else if (width  > 480 && width < 768  ) {
						var shrink_height = data_height_tablet["size"];						
						}else if (width < 480 ) {
						var shrink_height = data_height_mobile["size"];	
						}
						}
						
						$(window).scroll(function() {    
							var scroll = $(window).scrollTop();
						
							if (scroll >= <?php echo $scroll_distance_size; ?>) {
								header.removeClass('header').addClass("she-header");
								header.css("background-color", background);
								container.css({"min-height": shrink_height, "transition": "all 0.4s ease-in-out", "-webkit-transition": "all 0.4s ease-in-out", "-moz-transition": "all 0.4s ease-in-out"});
								
							} else {
								header.removeClass("she-header").addClass('header');
								header.css("background-color", "");
								container.css("min-height", "");
								
							}
						});

						}
					};
					$(window).on('elementor/frontend/init', sheTransparentElementorFront.init);
				}( jQuery ) );
			</script>
			
		<?php }
	}
	
	public function enqueue_styles() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
						
		wp_enqueue_style(
			'she-header-style',
			SHE_HEADER_ASSETS_URL  . 'css/she-header-style' . '.css',
			[],
			SHE_HEADER_VERSION
		);
		
	}
	
	public function enqueue_scripts() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_script(
			'she-header',
			SHE_HEADER_URL . 'assets/js/she-header.js',
			[
				'jquery',
			],
			SHE_HEADER_VERSION,
			false
		);
	}
}
