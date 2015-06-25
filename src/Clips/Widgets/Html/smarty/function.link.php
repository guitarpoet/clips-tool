<?php in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

function smarty_function_link($params, $template) {
	$uri = Clips\get_default($params, 'uri');
	if($uri) {
		$params['href'] = Clips\static_url($uri);
	}
	return Clips\create_tag('link', $params);
}
