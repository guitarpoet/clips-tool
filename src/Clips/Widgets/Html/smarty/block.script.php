<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

function smarty_block_script($params, $content = '', $template, &$repeat) {
	if($repeat) {
		return;
	}

	if($content) {
		return Clips\create_tag_with_content('script', trim($content), array('type' => 'text/javascript'));
	}
	return '';
}
