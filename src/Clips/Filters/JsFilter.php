<?php namespace Clips\Filters; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

use Clips\AbstractFilter;

/**
 * The Js filter, act as CssFilter, but support JavaScript script
 *
 * To add JavaScript file, just add the file path to the Clips context, like this:
 *
 * <code>
 * Clips\add_js('a.js');
 * </code>
 *
 * If you just want to add the JavaScript script, just like this:
 *
 * <code>
 * Clips\add_init_js('alert(1);');
 * </code>
 *
 * This filter will care for the rest.
 *
 * @author Jack
 * @date Fri Feb 20 21:26:40 2015
 */
class JsFilter extends AbstractFilter {
	public function filter_after($chain, $controller, $method, $args, $request, $controller_ret) {
		$js = \Clips\context('js');
		if($js) {
			if(!is_array($js)) {
				$js = array($js);
			}

			$js = array_map(function($item) {
				if(is_array($item)) {
					return '<script type="text/javascript">'.$item['script'].'</script>';
				}
				if(is_object($item)) {
					return '<script type="text/javascript">'.$item->script.'</script>';
				}
				else {
					$path = \Clips\safe_add_extension($item, 'js');
					if(strpos($path, 'http:') === false) 
						$path = \Clips\static_url($path);
					return '<script type="text/javascript" src="'.$path.'"></script>';
				}
			} ,$js);
			\Clips\context('js', implode("\n\t\t", $js), false);
		}
		else {
			\Clips\context('js', '');
		}
	}
}
