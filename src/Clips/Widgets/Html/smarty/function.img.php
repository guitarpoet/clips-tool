<?php in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

function smarty_function_img($params, $template) {
	$uri = Clips\get_default($params, 'uri');

	if($uri) {
		unset($params['uri']);
		$params['src'] = Clips\static_url($uri);
	}
	return Clips\create_tag('img', $params);
}
