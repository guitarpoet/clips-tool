<?php namespace Clips\Filters; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

use Clips\AbstractFilter;

/**
 * The css filter for processing css configurations.
 *
 * This filter should put before the view filters(since the view filters will use the result of
 * this filter).
 *
 * This filter will render all the css as link files
 *
 * @author Jack
 * @date Fri Feb 20 21:03:14 2015
 */
class CssFilter extends AbstractFilter {
	public function filter_after($chain, $controller, $method, $args, $request, $controller_ret) {
		$css = \Clips\clips_context('css');
		if($css) {
			if(!is_array($css)) {
				$css = array($css);
			}

			$css = array_map(function($item) { return '<link rel="stylesheet" href="'.\Clips\safe_add_extension($item, 'css').'">'; }, $css);
			\Clips\clips_context('css', implode("\n\t\t", $css), false);
		}
	}
}
