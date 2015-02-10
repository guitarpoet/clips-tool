<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

function smarty_block_ul($params, $content = '', $template, &$repeat) {
	if($repeat) {
		Clips\clips_context('indent_level', 1, true);
		return;
	}

	$content = "\t".Clips\process_list_items($params, $content, $template);

	if(isset($params['items']))
		unset($params['items']);

	Clips\context_pop('indent_level');
	return Clips\create_tag_with_content('ul', $content, $params);
}
