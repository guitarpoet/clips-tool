<?php in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

function smarty_block_a($params, $content = '', $template, &$repeat) {
	if($repeat) {
		Clips\clips_context('indent_level', 1, true);
		return;
	}

	$uri = Clips\get_default($params, 'uri');

	if($uri) {
		unset($params['uri']);
		$params['href'] = Clips\site_url($uri);
	}

	Clips\context_pop('indent_level');
	return Clips\create_tag_with_content('a', $content, $params);
}
