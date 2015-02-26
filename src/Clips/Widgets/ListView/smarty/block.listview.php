<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

function smarty_block_listview($params, $content = '', $template, &$repeat) {
	if($repeat) {
		Clips\clips_context('indent_level', 1, true);
		return;
	}

	$default = array(
		'class' => array('listview', 'clips-listview'),
	);

	Clips\context_pop('indent_level');
	return Clips\create_tag_with_content('ul', $content, $params, $default);
}
