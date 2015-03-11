<?php in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

function smarty_block_textarea($params, $content = '', $template, &$repeat) {
	if($repeat) {
		Clips\clips_context('indent_level', 1, true);
		return;
	}

	$default = array('class' => array('form-input', 'form-control'));

	$f = Clips\clips_context('current_field');
	if($f) {
		$default = $f->getDefault($default);
	}

	$data = Clips\context('current_form_field_data');
	if($data) {
		$content = $data;
	}

	$state = Clips\context('current_form_field_state');
	if($state && $state == 'readonly') {
		 return "<p>$content</p>";
	}

	Clips\context_pop('indent_level');
	return Clips\create_tag_with_content('textarea', $content, $params, $default, true);
}
