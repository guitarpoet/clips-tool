<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

function smarty_function_input($params, $template) {
	return Clips\create_tag('input', $params, array('form-input'));
}
