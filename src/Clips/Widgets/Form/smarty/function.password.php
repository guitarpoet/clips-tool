<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

function smarty_function_password($params, $template) {
	$params['type'] = 'password';
	Clips\require_widget_smarty_plugin('Form', 'input');	
	return smarty_function_input($params, $template);
}
