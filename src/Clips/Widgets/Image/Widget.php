<?php namespace Clips\Widgets\Image; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

class Widget extends \Clips\Widget {
	public function doInit() {
		\Clips\context('jquery_init',<<<TEXT

	//====================================
	// Initializing Images
	//====================================
	setTimeout(function(){\$('figure,picture').picture()}, 450);
TEXT
 ,true);
	}
}
