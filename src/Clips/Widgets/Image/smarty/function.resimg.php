<?php in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

Clips\require_widget_smarty_plugin('Html', 'img');	

function smarty_function_resimg($params, $template) {
	return Clips\create_tag_with_content('div', smarty_function_img($params, $template), array('class' => 'responsive'));
}
