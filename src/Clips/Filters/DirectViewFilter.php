<?php namespace Clips\Filters; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

/**
 * This is only a test view filter, will log the render request, and only echo the template out
 */
class DirectViewFilter extends ViewFilter {
	public function doRender($controller, $method, $args, $request, $template, $data, $controller_ret) {
		Clips\clips_log($template, $data);
		echo $this->template;
	}
}
