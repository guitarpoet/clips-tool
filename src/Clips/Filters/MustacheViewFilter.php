<?php namespace Clips\Filters; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

class MustacheViewFilter extends ViewFilter {
	public function filter_after($chain, $controller, $method, $args, $request, $controller_ret) {
		$chain->filter_render($chain, $controller, $method, $args, $request, $this->template, $this->args, $controller_ret);
		clips_out($this->template, $this->args);
	}
}
