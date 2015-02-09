<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

function smarty_block_form($params, $content = '', $template, &$repeat) {
	if($repeat) {
		// Set the current form to the context
		clips_context('current_form', get_default($params, 'name', default_form_name()));
		return;
	}

	$content .= Clips\create_tag('input', array('type' => 'hidden', 'name' => Clips\Form::FORM_FIELD, 
		'value' => get_default($params, 'name', 'form')));
	return Clips\create_tag_with_content('form', $content, $params, array(
		'action' => '#',
		'method' => 'post',
		'class' => ['clips-form', 'default-form']
	));
}
