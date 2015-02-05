<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

function smarty_function_input($params, $template) {
	$default = array('class' => 'form-input');
	$f = clips_context('current_field');
	if($f) {
		$default = $f->getDefault($default);
	}
	return Clips\create_tag('input', $params, $default);
}
