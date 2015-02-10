<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

function smarty_block_form($params, $content = '', $template, &$repeat) {
	if($repeat) {
		// Set the current form to the context
		Clips\clips_context('indent_level', 1, true);
		Clips\clips_context('current_form', Clips\get_default($params, 'name', Clips\default_form_name()));
		return;
	}

	$content .= "\t".Clips\create_tag('input', array('type' => 'hidden', 'name' => Clips\Form::FORM_FIELD, 
		'value' => Clips\get_default($params, 'name', 'form')));

	Clips\context_pop('indent_level');
	return Clips\create_tag_with_content('form', $content, $params, array(
		'action' => '#',
		'method' => 'post',
		'class' => ['clips-form', 'default-form']
	));
}
