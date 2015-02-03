<?php namespace Clips\Filters; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

class MustacheViewFilter extends ViewFilter {
	protected function doRender($controller, $method, $args, $request, $template, $data, $controller_ret) {
		clips_out($template, $data);
	}
}
