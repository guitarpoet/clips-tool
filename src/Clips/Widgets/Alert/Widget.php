<?php namespace Clips\Widgets\Alert; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

use Clips\WidgetV2;

class Widget extends WidgetV2 {

	protected function doInit() {
		$js =
			<<<TEXT
			
	//====================================
	// Initializing Alert 
	//====================================
	$(".alert").each(function(){
		$(this).message();
	});
	
	$('.owl-carousel[data-role="alertgroup"]').each(function(){
		$(this).alertgroup();
	});
TEXT;
		\Clips\context('jquery_init', $js, true);
	}
}
