<?php in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

function smarty_function_input($params, $template) {
	$default = array('class' => array('form-input', 'form-control'));
	$f = Clips\clips_context('current_field');
	if($f) {
		$default = $f->getDefault($default);
	}

	$data = Clips\context_pop('current_form_field_data');
	if($data) {
		$default['value'] = $data;
	}

	$state = Clips\context('current_form_field_state');
	if($state && $state == 'readonly') {
		 $value = Clips\get_default($default, 'value');
		 if($value)
			 return "<span>$value</span>";

		 return "<span>".Clips\get_default($params, 'value', '')."</span>";
	}
	return Clips\create_tag('input', $params, $default);
}
