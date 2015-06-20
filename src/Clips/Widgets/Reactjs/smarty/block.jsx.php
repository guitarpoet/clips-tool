<?php in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

function smarty_block_jsx($params, $content = '', $template, &$repeat) {
	if($repeat) {
		Clips\context('indent_level', 1, true);
		return;
	}

	// The element tag name that holds the virtual dom
	$tag = Clips\get_default($params, 'tag', 'div');

	$id = Clips\render_jsx($content, Clips\get_default($params, 'id'));

	Clips\context_pop('indent_level');
	return Clips\create_tag_with_content($tag, '', array('id' => $id, 'class' => array('jsx', 'jsx_holder')));
}
