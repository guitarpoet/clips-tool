<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

function base_url($uri) {
	$router = clips_context('router');
	if($router)
		return $router->baseUrl($uri);
	return $uri;
}

function site_url($uri) {
	return base_url($uri);
}

function clips_add_js($js) {
	clips_context('js', $js, true);
}

function clips_add_css($css) {
	clips_context('css', $css, true);
}

function clips_add_scss($scss) {
	clips_context('scss', $scss, true);
}

function to_header($str) {
	$data = explode('_', $str);
	$sa = array();
	foreach($data as $s) {
		$sa []= ucfirst($s);
	}
	return implode('-', $sa);
}

