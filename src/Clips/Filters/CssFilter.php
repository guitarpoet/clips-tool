<?php namespace Clips\Filters; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

use Clips\AbstractFilter;

class CssFilter extends AbstractFilter {
	public function filter_after($chain, $controller, $method, $args, $request, $controller_ret) {
		$css = \Clips\clips_context('css');
		if($css) {
			if(!is_array($css)) {
				$css = array($css);
			}

			\Clips\clips_context('css', \Clips\clips_out("string://{{#.}}<link rel=\"stylesheet\" href=\"{{.}}.css\">\n{{/.}}", $css, false));
		}
	}
}
