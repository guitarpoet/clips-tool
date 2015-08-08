<?php in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

function smarty_block_form($params, $content = '', $template, &$repeat) {
	if($repeat) {
		// Set the current form to the context
		Clips\clips_context('indent_level', 1, true);
		$name = Clips\get_default($params, 'name', Clips\default_form_name());
		$data = Clips\context('form_'.$name);
		if($data) {
			Clips\context('current_form_data', $data);
		}
		Clips\clips_context('current_form', $name);
		return;
	}

	$content .= "\t".Clips\create_tag('input', array('type' => 'hidden', 'name' => Clips\Form::FORM_FIELD, 
		'value' => Clips\get_default($params, 'name', 'form')));

	$action = Clips\get_default($params, 'action');
	if($action) {
		if(strpos($action, 'http') !== 0) {
			$params['action'] = Clips\site_url($action);
		}
	}

	if(Clips\get_default($params, 'upload')) {
		unset($params['upload']);
		$params['enctype'] = 'multipart/form-data';
	}

	Clips\context_pop('current_form');
	Clips\context_pop('current_form_data');
	Clips\context_pop('indent_level');
	return Clips\create_tag_with_content('form', $content, $params, array(
		'action' => '#',
		'method' => 'post',
		'class' => ['clips-form', 'default-form']
	));
}
