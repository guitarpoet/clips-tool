<?php namespace Clips\Widgets\Markup; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

use Clips\WidgetV2;

class Widget extends WidgetV2 {
	protected function doInit() {
		$js = <<<TEXT
	$('[data-role=menu]').ztree_toc({
		is_auto_number: true,
		documment_selector: '.markup',
		ztreeStyle: {
		},
		callback: {
		}
	});
TEXT;
		\Clips\context('jquery_init', $js, true);
	}
}
