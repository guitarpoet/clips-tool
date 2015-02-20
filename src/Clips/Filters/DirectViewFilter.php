<?php namespace Clips\Filters; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

/**
 * This is a straight view render, mainly used for simple usage, redirect for example
 */
class DirectViewFilter extends ViewFilter {
	public function doRender($controller, $method, $args, $request, $template, $data, $controller_ret) {
		echo $this->template;
	}
}
