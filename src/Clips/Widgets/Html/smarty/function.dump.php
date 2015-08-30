<?php in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

function smarty_function_dump($params, $template) {
	if($params) {
		var_dump($params);
	}
}
