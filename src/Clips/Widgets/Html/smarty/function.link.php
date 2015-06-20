<?php in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

function smarty_function_link($params, $template) {
	return Clips\create_tag('link', $params);
}
