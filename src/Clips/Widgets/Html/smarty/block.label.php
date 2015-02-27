<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

function smarty_block_label($params, $content = '', $template, &$repeat) {
	if($repeat) {
		Clips\clips_context('indent_level', 1, true);
		return;
	}

	Clips\context_pop('indent_level');
	Clips\require_widget_smarty_plugin('Lang', 'lang');	
	return Clips\create_tag_with_content('label', 
		smarty_block_lang($params, $content, $template, $repeat), $params);
}
