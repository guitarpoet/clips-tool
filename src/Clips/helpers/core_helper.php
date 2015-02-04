<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

function show_error() {
	$msg = call_user_func_array('sprintf', func_get_args());
	trigger_error($msg);
}

function get_annotation($class, $annotation, $method = null) {
	$re = new Addendum\ReflectionAnnotatedClass($class);
	if($method) {
		$an = $re->getMethod($method);
	}
	else {
		$an = $re;
	}
	if($an->hasAnnotation($annotation)) {
		foreach($an->getAnnotations() as $a) {
			if(get_class($a) == $annotation)
				return $a;
		}
	}
}

function format($obj, $formatter) {
	$f = Clips\Formatter::get($formatter);
	if($f)
		return $f->format($obj);
	return '';
}

function class_script_path($class) {
    $rc = new ReflectionClass($class);
    return $rc->getFileName();
}

function try_path($path, $others = array()) {
	foreach(array_merge($others, array(getcwd(), clips_path('/'))) as $pre) {
		$p = path_join($pre, $path);
		if(file_exists($p))
			return $p;
	}
	return false;
}

function clips_context($key = null, $value = null, $append = false) {
	$tool = &get_clips_tool();
	if(is_numeric($append)) {
		$arr = $tool->context($key);
		if($arr == null)
			$arr = array();
		$index = $append;
		if($index >= 0 && $index < count($arr)) {
			return $tool->context($key, array_splice($arr, $index, 0, $value));
		}
		$append = true;
	}
	return $tool->context($key, $value, $append);
}

function find_file($folder, $file, $suffix = null, $blur = false) {
	$ret = array();

	if(is_array($folder)) {
		foreach($folder as $f) {
			$ret = array_merge($ret, find_file($f, $file, $suffix, $blur));
		}
		return $ret;
	}

	$iterator = new DirectoryIterator($folder);
    foreach ($iterator as $fileinfo) {
		if($fileinfo->isDot())
			continue;

        if ($fileinfo->isFile()) {
            $name = $fileinfo->getPathname();
			$info = pathinfo($name);
			if($info && ($suffix == null || ($suffix && $info['extension'] == $suffix))) {
				if($info['filename'] == $file){
					$ret []= $name;
				}
				else if($blur && strpos($info['filename'], $file) !== false) {
					$ret []= $name;
				}
			}
        }
		else if ($fileinfo->isDir()) {
			$ret = array_merge($ret, find_file($fileinfo->getPathname(), $file, $suffix, $blur));
		}
    }
	return $ret;
}

function parse_json($json) {
	return json_decode($json);
}

function safe_file_exists($file) {
	if(strpos($file, "://") !== false) {
		// Skip this for resource
		return false;
	}
	return file_exists($file);
}

function to_flat($str) {
	$result = array();
	$str = str_replace('/', '\\', $str);
	foreach(explode('\\', $str) as $s) {
		$tmp = array();
		foreach(str_split($s) as $c) {
			if(ctype_upper($c) && $tmp) {
				$result []= strtolower(implode('', $tmp));
				$tmp = array();
			}
			$tmp []= $c;
		}
		if($tmp)
			$result []= strtolower(implode('', $tmp));
	}
	return implode('_', $result);
}

function to_camel($str) {
	$arr = explode('/', $str);
	$ret = array();
	foreach($arr as $a) {
		$data = explode('_', $a);
		$sa = array();
		foreach($data as $s) {
			$sa []= ucfirst($s);
		}
		$ret []= implode('', $sa);
	}
	return implode('/', $ret);
}

function str_end_with($haystack, $needle, $trim = true) {
	if($trim) {
		$str = trim($haystack);
	}
	else {
		$str = $haystack;
	}
	return strrpos($str, $needle) === strlen($str);
}

function resource_contents($uri) {
	$r = new Clips\Resource($uri);
	return $r->contents();
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
	return path_join(dirname($rc->getFileName()), $path);
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

function controller_class($c) {
	$tool = &get_clips_tool();
	return $tool->controller(ucfirst($c));
}

function controller_exists($c) {
	return !! controller_class($c);
}

function extend_arr($dest, $src) {
	if($src == null || !(is_array($src) || is_object($src)))
		return $dest;

	foreach($src as $key => $value) {
		if(isset($dest[$key])) {
			$v = $dest[$key];
			if(!is_array($v)) {
				$v = array($v);	
			}
			if(!is_array($value)) {
				$value = array($value);
			}
			$value = array_merge($v, $value);
		}
		$dest[$key] = $value;
	}
	return $dest;
}

if(!function_exists('copy_new')) {
	function copy_new($src, $class = null) {
		return copy_object($src, null, $class);
	}
}

if(!function_exists('copy_arr')) {
	function copy_arr($src, $dest = null) {
		if($src == null)
			return $dest;

		if($dest == null) {
			$dest = array();
		}

		foreach($src as $key => $value) {
			$dest[$key] = $value;
		}
		return $dest;
	}
}

if(!function_exists('copy_object')) {
	function copy_object($src, $dest = null, $class = null) {
		if($src == null)
			return null;

		if($dest == null) {
			if($class == null)
				$dest = new stdclass();
			else
				$dest = new $class();
		}

		foreach($src as $key => $value) {
			$k = str_replace('.', '_', $key);
			$dest->$k = $value;
		}
		return $dest;
	}
}
