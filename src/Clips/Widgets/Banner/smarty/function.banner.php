<?php in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

function smarty_function_banner($params, $template) {
	$src = get_default($params, 'src', null);
	if($src == null) {
		return '';
	}

	$height = get_default($params, 'height', null);
	if($height != null) {
		$params['style'] .= 'height:'.$height.';';
	}

	return Clips\create_tag('div', $params, array(
		'class'=>'pinet_banner'
	), '');
}
