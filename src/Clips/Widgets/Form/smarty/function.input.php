<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

function smarty_function_input($params, $template) {
	$default = array('class' => 'form-input');
	$f = clips_context('current_field');
	if($f) {
		// If we can find the field definition
		$default['id'] = $f->getId();
		$default['name'] = $f->name;
		$default['placeholder'] = $f->placeholder;
		if(isset($f->defaultValue))
			$default['value'] = $f->defaultValue;
		if(isset($f->value))
			$default['value'] = $f->value;
	}
	return Clips\create_tag('input', $params, $default);
}
