<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

function smarty_function_password($params, $template) {
	$default = array('class' => array('form-input', 'form-control'), 'type' => 'password');
	$f = Clips\clips_context('current_field');
	if($f) {
		$default = $f->getDefault($default);
	}
	return Clips\create_tag('input', $params, $default);
}
