<?php namespace Clips\Filters; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

use Clips\AbstractFilter;

class FormFilter extends AbstractFilter {
	public function accept($chain, $controller, $method, $args, $request, $controller_ret = null) {
		return $this->method == 'post';
	}

	public function filter_before($chain, $controller, $method, $args, $request) {
	}
}
