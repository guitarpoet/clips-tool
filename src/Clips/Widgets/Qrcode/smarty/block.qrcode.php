<?php in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

function smarty_block_qrcode($params, $content = '', $template, &$repeat) {
	if($repeat) {
		return;
	}

	$config = Clips\get_default($params, 'config');
	if($config) {
		$arr = array();
		foreach($config as $k => $v) {
			$arr []= $k.'='.urlencode($v);
		}
		$params['data-pattern'] = ('qr/generate/_(img)?size=_(size)&'.implode('&', $arr));
	}
	else {
		$params['data-pattern'] = ('qr/generate/_(img)?size=_(size)');
	}
	$params['data-image'] = str_replace('%2f', '%252F', str_replace('%2F', '%252F', urlencode($content)));
	$width = Clips\get_default($params, 'width');

	$div = array('class' => 'responsive');
	if($width) {
		unset($params['width']);
		$div['style'] = 'width:'.$width.'px;';
	}

	return Clips\create_tag_with_content('div', smarty_function_img($params, $template), $div);
}
