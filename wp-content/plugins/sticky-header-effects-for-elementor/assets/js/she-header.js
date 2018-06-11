var $j = jQuery.noConflict();

$j( window ).on( 'load', function() {
	"use strict";
	// She header
	sheHeader();
} );

// Make sure you run this code under Elementor..
$j( window ).on( 'elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/she_header.default', function() {
		"use strict";
	// She header
	sheHeader();
	});
	} );
	

/* ==============================================
TRANSPARENT EFFECT
============================================== */	


function sheHeader() {
	
	// Add header class to body after pass add to cart buttom
	

};