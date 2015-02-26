<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

/**
 * Set the html title
 *
 * @author Jack
 * @date Mon Feb 23 21:22:20 2015
 */
function html_title($title) {
	context('html_title', $title);
}

/**
 * This is the helper function for ul and ol smarty plugin.
 * Will take the template loated in the literal tag for the list.
 * If this is no template, will use li tag to render
 *
 * @author Jack
 * @date Sat Feb 21 11:48:11 2015
 */
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

/**
 * Get the static resource relative url
 *
 * @author Jack
 * @date Sat Feb 21 11:48:43 2015
 */
function static_url($uri) {
	$router = context('router');
	if($router)
		return $router->staticUrl($uri);
	return $uri;
}

/**
 * Get the base url
 *
 * @author Jack
 * @date Sat Feb 21 11:49:30 2015
 */
function base_url($uri) {
	$router = context('router');
	if($router)
		return $router->baseUrl($uri);
	return $uri;
}

function site_url($uri) {
	return base_url($uri);
}

/**
 * Add the JavaScript script
 *
 * @author Jack
 * @date Sat Feb 21 11:49:56 2015
 */
function add_init_js($js) {
	clips_add_init_js($js);
}

function clips_add_init_js($js) {
	add_js(array('init'=>true, 'script'=>$js));
}

/**
 * Add the JavaScript file at index.
 *
 * @author Jack
 * @date Sat Feb 21 11:51:08 2015
 * @param js
 * 		The JavaScript file
 * @param index
 * 		If is numeric, will insert the file at index, or will append it
 */
function add_js($js, $index = true) {
	clips_add_js($js, $index);
}

function clips_add_js($js, $index = true) {
	$jses = context('js');
	if(!isset($jses) || array_search($js, $jses) === false) {
		context('js', $js, $index);
	}
}

/**
 * Add the css file to context
 *
 * @author Jack
 * @date Sat Feb 21 11:51:26 2015
 */
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

/**
 * Get widget's script path
 *
 * @author Jack
 * @date Sat Feb 21 11:52:08 2015
 */
function get_widget_path($widget) {
	$tool = &get_clips_tool();
	$class = $tool->widgetClass($widget);
	if($class) {
		return dirname(class_script_path($class));
	}
	return false;
}

/**
 * Get default form's configuration name(pagination use this too).
 *
 * The default name is controller's name and the method's name.
 *
 * For example:
 *
 * Controller => Demo\Controllers\AdminController
 * Method => index
 *
 * will get the default name as
 *
 * AdminController/index
 *
 * @author Jack
 * @date Sat Feb 21 11:55:36 2015
 */
function default_form_name() {
	$controller = clips_context('controller_class');
	$method = clips_context('conroller_method');
	$name = explode('Controllers', $controller);

	// Get the name after controllers namespace, and get the last basename as the controller name
	$name = basename($name[count($name) - 1]);
	return $controller.'/'.$method;
}

/**
 * Just require the smarty plugin of some widget, specially useful for writing smarty plugins in widget
 *
 * @author Jack
 * @date Sat Feb 21 11:55:42 2015
 * @param widget
 * 		The widget that has this smarty function
 * @param name
 * 		The script file name to be required
 */
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

/**
 * Find the image at image locations
 *
 * @author Jack
 * @date Thu Feb 26 18:03:10 2015
 */
function find_image($img) {
	$dir = config('image_dir');
	if($dir) {
		foreach($dir as $d) {
			$p = try_path(path_join($d, $img));
			if($p) {
				return $p;
			}
		}
	}
	return false;
}

function image_size($p) {
	if(file_exists($p)) {
		$tool = &get_clips_tool();
		$util = $tool->library('ImageUtils');
		return $util->size($p);
	}
	return false;
}
