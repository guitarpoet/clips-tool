<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

function smarty_block_h2($params, $content = '', $template, &$repeat) {
	if($repeat)
		return;
	return Clips\create_tag_with_content('h2', $content, $params);
}
