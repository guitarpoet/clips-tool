<?php namespace Clips\Widgets\Lang; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

class Widget extends \Clips\Widget {
	protected function doInit() {
		$current_bundle = \Clips\context('current_bundle');
		if(!$current_bundle)
			$current_bundle = '';
		\Clips\context('jquery_init', <<<TEXT

	//====================================
	// Initializing lang support
	//====================================
	$.get(Clips.siteUrl('bundle/show/$current_bundle'), function(data) { Clips.bundle = data;}, 'json');
TEXT
, true);
	}
}
