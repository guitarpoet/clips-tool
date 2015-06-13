<?php namespace Clips\Widgets\Html; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

use Clips\WidgetV2;

class Widget extends WidgetV2 {
	protected function doInit() {
		// Added the clips object to JavaScript
		$router = \Clips\context('router');
		if($router) 
			\Clips\add_init_js(\Clips\clips_out('clips_js', array('base' => $router->staticUrl('/'), 'site' => $router->baseUrl('/')), false));
	}
}
