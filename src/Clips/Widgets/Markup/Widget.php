<?php namespace Clips\Widgets\Markup; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

class Widget extends \Clips\Widget {
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
