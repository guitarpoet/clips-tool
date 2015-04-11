<?php in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

function smarty_block_template($params, $content = '', $template, &$repeat) {
	if($repeat) {
		Clips\clips_context('indent_level', 1, true);
		return;
	}

	Clips\context_pop('indent_level');
	return Clips\create_tag_with_content('script', $content, $params, 
		array('type'=>"text/x-handlebars-template"));
}
