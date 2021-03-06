<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");


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
* Convert a hexa decimal color code to its RGB equivalent
*
* @param string $hexStr (hexadecimal color value)
* @param boolean $returnAsString (if set true, returns the value separated by the separator character. Otherwise returns associative array)
* @param string $seperator (to separate RGB values. Applicable only if second parameter is true.)
* @return array or string (depending on second parameter. Returns False if invalid hex color value)
*/                                                                                                 
function hex2rgb($hexStr, $returnAsString = false, $seperator = ',') {
    $hexStr = preg_replace("/[^0-9A-Fa-f]/", '', $hexStr); // Gets a proper hex string
    $rgbArray = array();
    if (strlen($hexStr) == 6) { //If a proper hex code, convert using bitwise operation. No overhead... faster
        $colorVal = hexdec($hexStr);
        $rgbArray['r'] = 0xFF & ($colorVal >> 0x10);
        $rgbArray['g'] = 0xFF & ($colorVal >> 0x8);
        $rgbArray['b'] = 0xFF & $colorVal;
		$rgbArray['a'] = 0;
    } elseif (strlen($hexStr) == 3) { //if shorthand notation, need some string manipulations
        $rgbArray['r'] = hexdec(str_repeat(substr($hexStr, 0, 1), 2));
        $rgbArray['g'] = hexdec(str_repeat(substr($hexStr, 1, 1), 2));
        $rgbArray['b'] = hexdec(str_repeat(substr($hexStr, 2, 1), 2));
		$rgbArray['a'] = 0;
    } else {
        return false; //Invalid hex color code
    }
    return $returnAsString ? implode($seperator, $rgbArray) : $rgbArray; // returns the rgb string or the associative array
}

function post_redirect($url, $params = array(), $title = 'redirecting') {
	if(strpos($url, '://') === false) { // If it is not external link
		$url = site_url($url);
	}

	$data = array();
	$fields = array();

	foreach($params as $k => $v) {
		$fields []= array(
			'name' => $k,
			'value' => $v
		);
	}

	$data['fields'] = $fields;
	$data['url'] = $url;

	return clips_out('autopost_form', $data, false);
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
			$tmp = trim($content);
		}
		else {
			$tmp = '{li}{$item}{/li}'; // The default template
		}

		$index = get_default($params, 'index');
		$tmp = 'string:'.$tmp;
		$content = array();
		$i = 0;
		foreach($items as $key => $item) {
			$params['item'] = $item;
			if($index)
				$params[$index] = $i++;
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
function base_url($uri, $full = false) {
	$router = context('router');
	if($router)
		return $router->baseUrl($uri, $full);
	return $uri;
}

function site_url($uri, $full = false) {
	return base_url($uri, $full);
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
 * Add the React's JSX script
 *
 * @author Jack
 * @version 1.1
 * @date Sat Jun 20 18:02:49 2015
 */
function add_jsx($jsx, $index = true) {
	$jsxes = context('jsx');
	if(!is_array($jsxes)) {
		$jsxes = array($jsxes);
	}
	// Only add this when jsxes is empty or the jsx is not in jsxes (in order to stop the double import)
	if(!$jsxes || array_search($jsx, $jsxes) === false) {
		context('jsx', $jsx, $index);
	}
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
	if(!is_array($jses))
		$jses = array($jses);
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
 * Get the uri relative to widget's path
 *
 * @author Jack
 * @date Sat Mar  7 10:03:19 2015
 */
function widget_uri($widget, $uri = '/') {
	$path = get_widget_path($widget);
	if($path) {
		if(strpos($path, FCPATH) === false) {
			// We might be in the soft link(in development mode)
			$base_dir = dirname(dirname(dirname(class_script_path('Clips\\Widget'))));
			$base = path_join('vendor/guitarpoet/clips-tool/', substr($path, strlen($base_dir)));
		}
		else
			$base = substr($path, strlen(FCPATH));

		return path_join($base, $uri);
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

function field_state($field) {
	if(isset($field->state)) { // Honor the state of the field
		return $field->state;
	}

	// Test for security engine next
	$tool = &get_clips_tool();
	$security = $tool->load_class('securityEngine', true);
	$result = $security->test($field);
	if($result) {
		$result = $result[0];
		$state = get_default($result, 'state');

		log('Filtering field {0} of form {1} with state {2}', array($field->name, $field->form, $state));

		// Will update field's state to this state
		$field->state = $state;
		return $state;
	}

	$form = context('form'); // Using form's state
	if(isset($form->state))
		return $form->state;

	return null;
}

function browser_meta() {
	$request = context('request');
	if($request) {
		// If we really in a browser environment
		return $request->browserMeta();
	}
	return null;
}

function browser_match($query, $meta = null) {
	if(!$meta) {
		$meta = browser_meta();
		if(!$meta) {
			return false;
		}
	}
	$matcher = new Libraries\UserAgentMatcher($query);
	$result = $matcher->match_Expr();
	if($result) {
		// Test for browser first
		if($meta->browser != $result['browser'])
			return false;

		// Then test for platform
		if(isset($result['platform']) && $meta->platform != $result['platform'])
			return false;

		// Then test for device type
		if(isset($result['device']) && $meta->device_type != $result['device'])
			return false;

		// Test for version at last
		if(isset($result['version'])) {
			foreach($result['version']['op'] as $op) {
				switch($op['type']) {
				case 'version':
					if($op['version'] == $meta->version)
						return  true;
					break;
				case 'matcher':
					$expr = 'return '.$meta->version.$op['operator'].$op['version'].';';
					if(eval($expr))
						return true;
					break;
				case 'between':
					if((double) $meta->version >= (double) $op['versions'][0]
						&& (double) $meta->version <= (double) $op['versions'][1])
						return true;
					break;
				}
			}
		}
		else {
			return true;
		}
	}
	return false;
}

function ip2mac($ip) {

	if(strpos(PHP_OS, 'WIN')) {
		$cmd = 'arp -a '.$ip;
	}
	else
		$cmd = '/usr/sbin/arp -a '.$ip;

	if(PHP_OS == 'Darwin' || strpos(PHP_OS, 'BSD') !== false) {
		$cmd = '/usr/sbin/arp '.$ip;
	}

	exec($cmd, $out, $code);

	if($code)
		return null;

	if($out) {
		if(strpos(PHP_OS, 'WIN')) {
			if(count($out) > 3) {
				$out = trim($out[3]);
				$out = preg_split('/\s+/', $out);
				return str_replace('-', ':', $out[1]);
			}
			return null;
		}
		else {
			$out = $out[0];
			$out = explode(' ', $out);
			return str_pad($out[3], 17, '0', STR_PAD_LEFT);
		}
	}
	return null;
}

function render_jsx($jsx, $id = null) {
	if(!$id) {
		$id = 'jsx-'.sequence('jsx');
	}

	$content = "\n\t\t\t".implode("\n\t\t\t", array_map(function($item){ return trim($item); }, explode("\n", trim($jsx))));
	context('jsx_script', "React.render($content\n\t\t\t, document.getElementById('$id'));", true);
	return $id;
}

function mime_types($mime) {
	if(!context('mimes')) {
	$mimes = array(	'hqx'	=>	'application/mac-binhex40',
				'cpt'	=>	'application/mac-compactpro',
				'csv'	=>	array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel'),
				'bin'	=>	'application/macbinary',
				'dms'	=>	'application/octet-stream',
				'lha'	=>	'application/octet-stream',
				'lzh'	=>	'application/octet-stream',
				'exe'	=>	array('application/octet-stream', 'application/x-msdownload'),
				'class'	=>	'application/octet-stream',
				'psd'	=>	'application/x-photoshop',
				'so'	=>	'application/octet-stream',
				'sea'	=>	'application/octet-stream',
				'dll'	=>	'application/octet-stream',
				'oda'	=>	'application/oda',
				'pdf'	=>	array('application/pdf', 'application/x-download'),
				'ai'	=>	'application/postscript',
				'eps'	=>	'application/postscript',
				'ps'	=>	'application/postscript',
				'smi'	=>	'application/smil',
				'smil'	=>	'application/smil',
				'mif'	=>	'application/vnd.mif',
				'xls'	=>	array('application/excel', 'application/vnd.ms-excel', 'application/msexcel'),
				'ppt'	=>	array('application/powerpoint', 'application/vnd.ms-powerpoint'),
				'wbxml'	=>	'application/wbxml',
				'wmlc'	=>	'application/wmlc',
				'dcr'	=>	'application/x-director',
				'dir'	=>	'application/x-director',
				'dxr'	=>	'application/x-director',
				'dvi'	=>	'application/x-dvi',
				'gtar'	=>	'application/x-gtar',
				'gz'	=>	'application/x-gzip',
				'php'	=>	'application/x-httpd-php',
				'php4'	=>	'application/x-httpd-php',
				'php3'	=>	'application/x-httpd-php',
				'phtml'	=>	'application/x-httpd-php',
				'phps'	=>	'application/x-httpd-php-source',
				'js'	=>	'application/x-javascript',
				'swf'	=>	'application/x-shockwave-flash',
				'sit'	=>	'application/x-stuffit',
				'tar'	=>	'application/x-tar',
				'tgz'	=>	array('application/x-tar', 'application/x-gzip-compressed'),
				'xhtml'	=>	'application/xhtml+xml',
				'xht'	=>	'application/xhtml+xml',
				'zip'	=>  array('application/x-zip', 'application/zip', 'application/x-zip-compressed'),
				'mid'	=>	'audio/midi',
				'midi'	=>	'audio/midi',
				'mpga'	=>	'audio/mpeg',
				'mp2'	=>	'audio/mpeg',
				'mp3'	=>	array('audio/mpeg', 'audio/mpg', 'audio/mpeg3', 'audio/mp3'),
				'aif'	=>	'audio/x-aiff',
				'aiff'	=>	'audio/x-aiff',
				'aifc'	=>	'audio/x-aiff',
				'ram'	=>	'audio/x-pn-realaudio',
				'rm'	=>	'audio/x-pn-realaudio',
				'rpm'	=>	'audio/x-pn-realaudio-plugin',
				'ra'	=>	'audio/x-realaudio',
				'rv'	=>	'video/vnd.rn-realvideo',
				'wav'	=>	array('audio/x-wav', 'audio/wave', 'audio/wav'),
				'bmp'	=>	array('image/bmp', 'image/x-windows-bmp'),
				'gif'	=>	'image/gif',
				'jpeg'	=>	array('image/jpeg', 'image/pjpeg'),
				'jpg'	=>	array('image/jpeg', 'image/pjpeg'),
				'jpe'	=>	array('image/jpeg', 'image/pjpeg'),
				'png'	=>	array('image/png',  'image/x-png'),
				'tiff'	=>	'image/tiff',
				'tif'	=>	'image/tiff',
				'css'	=>	'text/css',
				'html'	=>	'text/html',
				'htm'	=>	'text/html',
				'shtml'	=>	'text/html',
				'txt'	=>	'text/plain',
				'text'	=>	'text/plain',
				'log'	=>	array('text/plain', 'text/x-log'),
				'rtx'	=>	'text/richtext',
				'rtf'	=>	'text/rtf',
				'rmvb'	=>	'application/vnd.rn-realmedia-vbr',
				'rm'	=>	'application/vnd.rn-realmedia',
				'xml'	=>	'text/xml',
				'xsl'	=>	'text/xml',
				'mpeg'	=>	'video/mpeg',
				'mpg'	=>	'video/mpeg',
				'mpe'	=>	'video/mpeg',
				'qt'	=>	'video/quicktime',
				'mov'	=>	'video/quicktime',
				'avi'	=>	'video/x-msvideo',
				'movie'	=>	'video/x-sgi-movie',
				'doc'	=>	'application/msword',
				'docx'	=>	array('application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip'),
				'xlsx'	=>	array('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/zip'),
				'word'	=>	array('application/msword', 'application/octet-stream'),
				'xl'	=>	'application/excel',
				'eml'	=>	'message/rfc822',
				'json' => array('application/json', 'text/json')
			);
		context('mimes', $mimes);
	}
	else {
		$mimes = context('mimes');
	}
	return get_default($mimes, $mime, false);
}

function image_size($p) {
	if(file_exists($p)) {
		$tool = &get_clips_tool();
		$util = $tool->library('ImageUtils');
		return $util->size($p);
	}
	return false;
}

function clean_file_name($filename) {
	$bad = array(
					"<!--",
					"-->",
					"'",
					"<",
					">",
					'"',
					'&',
					'$',
					'=',
					';',
					'?',
					'/',
					"%20",
					"%22",
					"%3c",		// <
					"%253c",	// <
					"%3e",		// >
					"%0e",		// >
					"%28",		// (
					"%29",		// )
					"%2528",	// (
					"%26",		// &
					"%24",		// $
					"%3f",		// ?
					"%3b",		// ;
					"%3d"		// =
				);

	$filename = str_replace($bad, '', $filename);

	return stripslashes($filename);
}
