<?php namespace Clips\Filters; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

use Clips\AbstractFilter;

class CssFilter extends AbstractFilter {
	public function filter_after($chain, $controller, $method, $args, $request, $controller_ret) {
		$css = clips_context('css');
		if($css) {
			if(!is_array($css)) {
				$css = array($css);
			}

			clips_context('css', clips_out("string://{{#.}}<link rel=\"stylesheet\" href=\"{{.}}\">\n{{/.}}", $css, false));
		}
	}
}
