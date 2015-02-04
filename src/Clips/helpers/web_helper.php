<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

function static_url($uri) {
	$router = clips_context('router');
	if($router)
		return $router->staticUrl($uri);
	return $uri;
}

function base_url($uri) {
	$router = clips_context('router');
	if($router)
		return $router->baseUrl($uri);
	return $uri;
}

function site_url($uri) {
	return base_url($uri);
}

function clips_add_js($js, $index = true) {
	$jses = clips_context('js');
	if(!isset($jses) || array_search($js, $jses) === false) {
		clips_context('js', $js, $index);
	}
}

function clips_add_css($css, $index = true) {
	$csses = clips_context('css');
	if(!isset($csses) || array_search($css, $csses) === false) {
		clips_context('css', $css, $index);
	}
}

function clips_add_scss($scss, $index = true) {
	$scsses = clips_context('scss');
	if(!isset($scsses) || array_search($scss, $scsses) === false) {
		clips_context('scss', $scss, $index);
	}
}

function to_header($str) {
	$data = explode('_', $str);
	$sa = array();
	foreach($data as $s) {
		$sa []= ucfirst($s);
	}
	return implode('-', $sa);
}

