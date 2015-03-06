<?php namespace Clips\Widgets\DataTable; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

/**
 * The datatable widget
 */
class Widget extends \Clips\Widget {
	protected function doInit() {
		$js = <<<TEXT
			$('.datatable').each(function(){
				$(this).on('init.dt', function(){
					var self = $(this);

					self.find("tbody").selectable({
						delay: 1
					});

					self.on('click', 'tr', function(){
						$(this).addClass('ui-selected').siblings().removeClass('ui-selected');
					});

					if($.isFunction($.fn.selectBoxIt)){
						self.parents('.dataTables_wrapper').find('select:not([data-no-selectBoxIt])').each(function(){
							$(this).selectBoxIt({});
						});
					}
				});
			});
TEXT;
		\Clips\context('jquery_init', $js, true);
	}
}
