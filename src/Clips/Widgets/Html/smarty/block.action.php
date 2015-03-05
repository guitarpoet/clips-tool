<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

function smarty_block_action($params, $content = '', $template, &$repeat) {
	if($repeat) {
		Clips\clips_context('indent_level', 1, true);
		return;
	}

	$value = Clips\get_default($params, 'caption');
	if($value) { // We did have value, so the content is the JavaScript
		$id = Clips\get_default($params, 'id', 'clips_action_'.Clips\sequence('action'));
		$js = "$(\"#$id\").click(function(){\n\t\t".trim($content)."\n\t});";
		$content = $value;
		unset($params['caption']);
		$params['id'] = $id;
		if(!isset($params['title'])) // Add tooltip
			$params['title'] = $value;
		$params['href'] = 'javascript:void(0)';
		Clips\context('jquery_init', $js, true);
	}
	else {
		// Check for action uri
		$uri = Clips\get_default($params, 'uri');
		if($uri) {
			$params['href'] = Clips\site_url($uri);
			unset($params['uri']);
		}
	}
	Clips\context_pop('indent_level');
	return Clips\create_tag_with_content('a', $content, $params);
}
