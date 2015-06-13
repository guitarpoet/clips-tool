<?php namespace Clips\Widgets\Image; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

use Clips\WidgetV2;

class Widget extends WidgetV2 {
	public function doInit() {
		\Clips\context('jquery_init',<<<TEXT

	//====================================
	// Initializing Images
	//====================================
	setTimeout(function(){
		\$('figure,picture').picture();
		\$('div.responsive > img').responsiveImage();
	}, 450);
TEXT
 ,true);
	}
}
