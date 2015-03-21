<?php namespace Clips\Widgets\Rules; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

class Widget extends \Clips\Widget {
	public function doInit() {
		\Clips\context('jquery_init', 
			"
	//====================================
	// Initializing Clips Rules
	//====================================
	Clips.rule = new Clips.RuleEngine('".\Clips\site_url('clips/commands')."');\n", 
			true);
	}
}
