<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

function str_end_with($haystack, $needle, $trim = true) {
	if($trim) {
		$str = trim($haystack);
	}
	else {
		$str = $haystack;
	}
	return strrpos($str, $needle) === strlen($str);
}

function rmr($path) {
	if (PHP_OS === 'Windows') {
		exec("rd /s /q {$path}");
	}
	else {
		exec("rm -rf {$path}");
	}
}

function get_tmp_file($prefix = 'clips_tmp') {
	return tempnam(sys_get_temp_dir(), $prefix);
}

function clips_stacktrace($message, $level = 3) {
	$trace = debug_backtrace();
	$ret = array();
	for($i = 1; $i <= $level; $i++) {
		$ret []= $trace[$i];
	}
	clips_log($message, $ret);
}

function clips_log($message, $context = array()) {
	$tool = &get_clips_tool();
	$tool->info($message, $context);
}

function console_meta() {
	return array('width' => `tput cols`);
}

class Depends extends \Addendum\Annotation {
}

function path_join() {
	return str_replace('//', '/', str_replace('///', '/', implode('/', func_get_args())));
}

function is_cli() {
	return (php_sapi_name() === 'cli' OR defined('STDIN'));
}

function &get_clips_tool() {
	return Clips\Tool::get_instance();
}

function clips_config($name, $default = null) {
	$tool = &get_clips_tool();
	$ret = $tool->config->$name;
	if($ret)
		return $ret;
	return $default;
}

function clips_tool_path($path) {
	return CLIPS_TOOL_PATH.$path;
}

function record_file_load($file) {
	if($file) {
		$tool = get_clips_tool();
		$tool->fileLoaded($file);
		return true;
	}
	return false;
}

function file_load($file) {
	if($file) {
		$tool = get_clips_tool();
		return $tool->isFileLoaded($file);
	}
	return false;
}

function process_file_name($prefix, $file, $suffix) {
	$arr = explode('/', $file);
	$tail = array_pop($arr);
	$arr []= $prefix.$tail.$suffix;
	return implode('/', $arr);
}

function clips_php_require_once($file) {
	if(require_once($file)) {
		record_file_load($file);
		return true;
	}
	return false;
}

function clips_library($library, $init = true, $suffix = "") {
	$tool = get_clips_tool();
	return $tool->library($library, $init, $suffix);
}

function clips_out($template, $args, $output = true) {
	$tool = get_clips_tool();
	if(strpos($template, "://")) {
		$ret = $tool->template($template, $args);
	}
	else {
		$ret = $tool->template("tpl://".$template, $args);
	}
	if($output)
		echo $ret;
	return $ret;
}

function clips_path($path) {
	$rc = new ReflectionClass("Clips\\Tool");
	return dirname($rc->getFileName()).$path;
}

function clips_load_rules($rules) {
	if($rules) {
		$tool = &get_clips_tool();
		return $tool->clips->load($rules);
	}
	return false;
}

if(!function_exists('get_default')) {
	function get_default($arr, $key, $default = '') {
		if(is_object($arr))
			return isset($arr->$key)? $arr->$key: $default;
		if(is_array($arr))
			return isset($arr[$key])? $arr[$key]: $default;
		return $default;
	}
}

function clips_str_match($str, $pattern) {
	return !!preg_match('/'.$pattern.'/', $str);
}

function clips_get_property($obj, $property) {
	if(is_array($obj) && isset($obj[$property])) {
		return $obj[$property];
	}

	if(is_object($obj) && isset($obj->$property)) {
		return $obj->$property;
	}
	return null;
}
