<?php namespace Clips\Widgets\ListView; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

use Clips\WidgetV2;

class Widget extends WidgetV2 {
	protected function doInit() {
		$js = 
<<<TEXT

	//====================================
	// Initializing List View 
	//====================================
	$('.listview.clips-listview').on('list.init', function(){
		var self = $(this);
		if($.isFunction($.fn.selectBoxIt)){
			self.parents('.listview_wrapper').find('select:not([data-no-selectBoxIt])').each(function(){
				$(this).selectBoxIt({});
			});
		}
	});
TEXT;
		\Clips\context('jquery_init', $js, true);
	}
}
