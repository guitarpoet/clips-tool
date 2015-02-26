<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

function smarty_function_input($params, $template) {
	$default = array('class' => array('form-input', 'form-control'));
	$f = Clips\clips_context('current_field');
	if($f) {
		$default = $f->getDefault($default);
	}

	$data = Clips\context('current_form_field_data');
	if($data) {
		$default['value'] = $data;
	}
	return Clips\create_tag('input', $params, $default);
}
