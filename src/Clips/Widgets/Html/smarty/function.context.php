<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

function smarty_function_context($params, $template) {
	$key = get_default($params, 'key', null);
	if($key) {
		$formatter = get_default($params, 'formatter', null);
		$obj = clips_context($key);
		if($formatter)
			return format($obj, $formatter);	
		return $obj;
	}
	return '';
}
