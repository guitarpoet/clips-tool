<?php in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

function smarty_function_dump($params, $template) {
	$obj = Clips\get_default($params, 'obj');

	if($obj) {
		var_dump($obj);
	}
}
