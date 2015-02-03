<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

function smarty_block_h1($params, $content = '', $template, &$repeat) {
	if($repeat)
		return;
	return "<h1>".$content."</h1>";
}
