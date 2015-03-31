<?php namespace Clips\Widgets\Alert; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

class Widget extends \Clips\Widget {
	protected function doInit()
	{
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