<?php namespace Clips\Widgets\Jquery; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

/**
 * This filter will construct the jquery initialize function using jquery_init context
 */
class InitFilter extends \Clips\AbstractFilter {
	public function filter_after($chain, $controller, $method, $args, $request, $controller_ret) {
		$init = \Clips\clips_context('jquery_init');
		if($init) {
			if(!is_array($init)) {
				$init = array($init);
			}

			$init []= "if(typeof initialize === 'function') initialize();";

			\Clips\add_init_js('jQuery(function($){'.implode("\n", $init).'});');
		}
	}
}
