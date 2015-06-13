<?php namespace Clips\Widgets\BootstrapDropdown; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

use Clips\WidgetV2;

class Widget extends WidgetV2 {
	protected function doInit() {
		$js = <<<TEXT
$(".dropdown-menu").each(function(){
	var self = $(this);
	self.find("li").each(function(){
		var li = $(this);
		if(li.children('.sub-menu').length > 0) {
			li.addClass('sub');
		}
	});
});
TEXT;
		\Clips\context('jquery_init', $js, true);
	}
}
