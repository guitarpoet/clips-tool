<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

function smarty_block_label($params, $content = '', $template, &$repeat) {
	if($repeat) {
		Clips\clips_context('indent_level', 1, true);
		return;
	}

	Clips\context_pop('indent_level');

	$bundle_name = Clips\context('current_bundle');
	$bundle = Clips\get_default($params, 'bundle', $bundle_name);

	if($bundle !== null) {
		$bundle = Clips\bundle($bundle);
		$content = $bundle->message($content);
	}

	return Clips\create_tag_with_content('label', $content, $params);
}
