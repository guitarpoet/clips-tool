<?php namespace Clips\Filters; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

/**
 * This is only a test view filter, will log the render request, and only echo the template out
 */
class DirectViewFilter extends ViewFilter {
	public function filter_after($chain, $controller, $method, $args, $request, $controller_ret) {
		$chain->filter_render($chain, $controller, $method, $args, $request, $this->template, $this->args, $controller_ret);
		echo $this->template;
		clips_log($this->template, $this->args);
	}
}
