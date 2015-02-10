<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

function smarty_block_ul($params, $content = '', $template, &$repeat) {
	if($repeat)
		return;

	$content = Clips\process_list_items($params, $content, $template);

	if(isset($params['items']))
		unset($params['items']);

	return Clips\create_tag_with_content('ul', $content, $params);
}
