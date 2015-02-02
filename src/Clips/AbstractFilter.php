<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

use Clips\Interfaces\Filter;

/**
 * The abstract filter to extend
 *
 * @author Jack
 * @date Mon Feb  2 20:50:49 2015
 */
class AbstractFilter implements Filter {
	public function accept($chain, $controller, $method, $args, $request, $controller_ret = null) {
		return true;
	}

	public function filter_before($chain, $controller, $method, $args, $request) {
	}

	public function filter_after($chain, $controller, $method, $args, $request, $controller_ret) {
	}

	public function filter_render($chain, $controller, $method, $args, $request, $view, $view_context, $controller_ret) {
	}
}
