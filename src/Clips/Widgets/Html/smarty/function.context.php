<?php in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

function smarty_function_context($params, $template) {
	$key = Clips\get_default($params, 'key', null);
	if($key) {
		$formatter = Clips\get_default($params, 'formatter', null);
		$obj = Clips\clips_context($key);
		if($formatter)
			return Clips\format($obj, $formatter);	
		return $obj;
	}
	return '';
}
