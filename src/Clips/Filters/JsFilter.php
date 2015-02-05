<?php namespace Clips\Filters; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

use Clips\AbstractFilter;

class JsFilter extends AbstractFilter {
	public function filter_after($chain, $controller, $method, $args, $request, $controller_ret) {
		$js = clips_context('js');
		if($js) {
			if(!is_array($js)) {
				$js = array($js);
			}
		}

		clips_context('js', clips_out("string://{{#.}}<script type=\"text/javascript\" {{^init}}src=\"{{.}}.js\"{{/init}}>{{script}}</script>\n{{/.}}", $js, false));
	}
}
