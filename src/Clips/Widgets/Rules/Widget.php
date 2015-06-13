<?php namespace Clips\Widgets\Rules; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

use Clips\WidgetV2;

class Widget extends WidgetV2 {
	public function doInit() {
		\Clips\context('jquery_init', 
			"
	//====================================
	// Initializing Clips Rules
	//====================================
	Clips.rules = new Clips.RuleEngine('".\Clips\site_url('clips/commands')."');\n", 
			true);
	}
}
