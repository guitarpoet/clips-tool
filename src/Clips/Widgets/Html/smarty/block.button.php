<?php in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

function smarty_block_button($params, $content = '', $template, &$repeat) {
	if($repeat) {
		Clips\clips_context('indent_level', 1, true);
		return;
	}

	$value = Clips\get_default($params, 'caption');
	if($value) { // We did have value, so the content is the JavaScript
		$id = Clips\get_default($params, 'id', 'clips_button_'.Clips\sequence('button'));
		$js = "$(\"#$id\").click(function(){\n\t\t".trim($content)."\n\t});";
		$content = $value;
		unset($params['caption']);
		$params['id'] = $id;
		Clips\context('jquery_init', $js, true);
	}

	// For i18n
	$bundle_name = Clips\context('current_bundle');
	$bundle = Clips\get_default($params, 'bundle', $bundle_name);

	if($bundle !== null) {
		$bundle = Clips\bundle($bundle);
		$content = $bundle->message($content);
	}

	Clips\context_pop('indent_level');
	return Clips\create_tag_with_content('button', $content, $params);
}
