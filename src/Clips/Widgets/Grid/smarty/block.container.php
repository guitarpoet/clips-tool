<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

function smarty_block_container($params, $content = '', $template, &$repeat) {
	if($repeat) {
		Clips\clips_context('indent_level', 1, true);
		return;
	}

	$fluid = Clips\get_default($params, 'fluid', false);
	$class = 'container';

	if($fluid) {
		$class = 'container-fluid';
		unset($params['fluid']);
	}

	Clips\context_pop('indent_level');
	return Clips\create_tag_with_content('div', $content, $params, array('class' => $class));
}
