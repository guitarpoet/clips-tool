<?php namespace Clips\Filters; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

use Clips\AbstractFilter;

class JsFilter extends AbstractFilter {
	public function filter_after($chain, $controller, $method, $args, $request, $controller_ret) {
		$js = \Clips\clips_context('js');
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
			\Clips\clips_context('js', implode("\n\t\t", $js), false);
		}
	}
}
