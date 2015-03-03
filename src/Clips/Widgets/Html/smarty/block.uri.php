<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

function smarty_block_uri($params, $content = '', $template, &$repeat) {
	if($repeat)
		return;
	if(Clips\get_default($params, 'static'))
		return Clips\static_url($content);
	return Clips\base_url($content);
}
