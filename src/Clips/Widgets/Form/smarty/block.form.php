<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

function smarty_block_form($params, $content = '', $template, &$repeat) {
	if($repeat)
		return;
	return Clips\create_tag_with_content('form', $content, $params, array(
		'action' => '#',
		'method' => 'post',
		'class' => ['clips-form', 'default-form']
	));
}
