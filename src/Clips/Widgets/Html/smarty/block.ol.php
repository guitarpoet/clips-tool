<?php in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

function smarty_block_ol($params, $content = '', $template, &$repeat) {
	if($repeat)
		return;

	$content = Clips\process_list_items($params, $content, $template);

	if(isset($params['items']))
		unset($params['items']);

	return Clips\create_tag_with_content('ol', $content, $params);
}
