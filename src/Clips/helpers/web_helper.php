<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

function process_list_items($params, $content, $template) {
	$level = clips_context('indent_level');
	if($level === null)
		$level = 0; // Default level is 0
	else
		$level = count($level);

	$indent = '';
	for($i = 0; $i < $level; $i++) {
		$indent .= "\t";
	}

	$items = get_default($params, 'items', null);

	if($items) {
		// We do have items
		if(trim($content)) {
			// Use the content as the template
			$tmp = $content;
		}
		else {
			$tmp = '{li}{$item}{/li}'; // The default template
		}

		$tmp = 'string:'.$tmp;
		$content = array();
		foreach($items as $item) {
			$params['item'] = $item;
			$content []= $template->fetch($tmp, $params);
		}
		$content = implode("\n$indent", $content);

		unset($params['item']);
		unset($params['items']);
	}

	return $content;
}

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

function add_init_js($js) {
	clips_add_init_js($js);
}

function clips_add_init_js($js) {
	clips_add_js(array('init'=>true, 'script'=>$js));
}

function add_js($js, $index = true) {
	clips_add_js($js, $index);
}

function clips_add_js($js, $index = true) {
	$jses = clips_context('js');
	if(!isset($jses) || array_search($js, $jses) === false) {
		clips_context('js', $js, $index);
	}
}

function add_css($css, $index = true) {
	clips_add_css($css, $index);
}

function clips_add_css($css, $index = true) {
	$csses = clips_context('css');
	if(!isset($csses) || array_search($css, $csses) === false) {
		clips_context('css', $css, $index);
	}
}

function add_scss($scss, $index = true) {
	clips_add_scss($scss, $index);
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

function get_widget_path($widget) {
	$tool = &get_clips_tool();
	$class = $tool->widgetClass($widget);
	if($class) {
		return dirname(class_script_path($class));
	}
	return false;
}

function default_form_name() {
	$controller = clips_context('controller_class');
	$method = clips_context('conroller_method');
	$name = explode('Controllers', $controller);

	// Get the name after controllers namespace, and get the last basename as the controller name
	$name = basename($name[count($name) - 1]);
	return $controller.'/'.$method;
}

function require_widget_smarty_plugin($widget, $name) {
	$path = get_widget_path($widget);
	foreach(array('block', 'function') as $prefix) {
		$p = path_join($path, 'smarty', $prefix.'.'.$name.'.php');
		if(file_exists(path_join($path, 'smarty', $prefix.'.'.$name.'.php'))) {
			require_once($p);
			return true;
		}
	}
	return false;
}
