<?php namespace Clips\Filters; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

/**
 * This filter will render the data using mustache template(using clips_out utilities)
 *
 * @author Jack
 * @date Fri Feb 20 21:28:27 2015
 */
class MustacheViewFilter extends ViewFilter {
	protected function doRender($controller, $method, $args, $request, $template, $data, $controller_ret) {
		\Clips\clips_out($template, $data);
	}
}
