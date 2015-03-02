<?php namespace Clips\Widgets\ListView; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

class Widget extends \Clips\Widget {
	protected function doInit() {
		$js = <<<TEXT
			$('.listview.clips-listview').on('list.loaded', function(){
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
