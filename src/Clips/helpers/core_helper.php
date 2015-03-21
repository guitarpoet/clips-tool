<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

define('RANDOM_STRING', '3141592653589793238462643383279502884197169399375105820974944592307816406286208998628034825342117067982148086FromfairestcreatureswedesireincreaseThattherebybeautysrosemightneverdieButastheripershouldbytimedeceaseHistenderheirmightbearhismemoryButthoucontractedtothineownbrighteyesFeedstthylightsflamewithself-substantialfuelMakingafaminewhereabundanceliesThyselfthyfoetothysweetselftoocruelThouthatartnowtheworldsfreshornamentAndonlyheraldtothegaudyspringWithinthineownbudburiestthycontentAndtenderchurlmakstwasteinniggardingPitytheworldorelsethisgluttonbeToeattheworldsduebythegraveandthee');

/**
 * Test if the method's modifier is public
 *
 * @author Jack
 * @date Mon Feb 23 15:02:59 2015
 */
function method_is_public($class, $method) {
	if(is_string($class) && is_string($method) && class_exists($class) 
		&& method_exists($class, $method)) {
		$refl = new \ReflectionMethod($class, $method); 
		return $refl->isPublic();
	}
	return false;
}

/**
 * Try to guess the locale.
 *
 * For command line, try to get the locale from intl extension or locale command.
 * For http requests, try to get the locale from lang cookie, then locale cookie, 
 * if not found in any cookie, then try to guess it from the HTTP_ACCEPT_LANGUAGE header
 *
 * @author Jack
 * @date Mon Feb 23 11:38:29 2015
 */
function get_locale() {
	if(is_cli()) {
		// For command line, using the machine's locale
		if(\extension_loaded('intl')) { // Try intl extension first
			$locale = \locale_get_default();
			$locale = explode('_', $locale);
			if(count($locale) > 2)
				array_pop($locale); // Pop the encoding
			return implode('-', $locale);
		}

		switch(PHP_OS) {
		case 'Linux':
		case 'Darwin':
		case 'Unix':
			$locale = str_replace('"', '', exec("locale | grep LANG | awk -F= '{print $2}'"));

			$locale = explode('.', $locale);
			$locale = $locale[0];
			return str_replace('_', '-', $locale);
		}
		return 'en-US'; // Return en-US as default
	}
	else {
		// For web requests
		$controller = context('controller');
		if($controller) {
			// If we do have controller here
			
			// Try lang cookie first
			$locale = $controller->request->cookie('lang');

			if(!$locale) { // There is no lang cookie, then let's try locale cookie
				$locale = $controller->request->cookie('locale');
			}

			if(!$locale) { // There is no locale cookie either, let's try accept language
				$locale = $controller->request->server('HTTP_ACCEPT_LANGUAGE');
				if($locale) {
					$langs = array();
					// break up string into pieces (languages and q factors)
					preg_match_all('/([a-z]{1,8}(-[a-z]{1,8})?)\s*(;\s*q\s*=\s*(1|0\.[0-9]+))?/i', $locale, $lang_parse);
					if (count($lang_parse[1])) {
						// create a list like "en" => 0.8
						$langs = array_combine($lang_parse[1], $lang_parse[4]);

						// set default to 1 for any without q factor
						foreach ($langs as $lang => $val) {
							if ($val === '') {
								$langs[$lang] = 1;
								if(strpos($lang, '-')) {
									$data = explode('-', $lang);
									$data[1] = strtoupper($data[1]);
									$locale = implode('-', $data);
								}
								else
									$locale = $lang;
							}
						}

						// sort list based on value
						arsort($langs, SORT_NUMERIC);
						context('langs', $langs);
					}
				}
			}

			return $locale;
		}
		return 'en-US'; // Return en-US as default
	}
}

/**
 * Call function n times
 *
 * @author Jack
 * @date Sat Feb 21 10:20:25 2015
 * @param n
 * 		How many times
 * @param func
 * 		The function	
 * @param args (default empty)
 * 		The args to send into the function
 * @param start (default 1)
 * 		The start point
 * @return
 * 		The result array of all the return values
 */
function n_times($n, $func, $args = array(), $start = 1) {
	$ret = array();
	for($i = $start; $i < $n + $start; $i++) {
		$args []= $i;
		$ret []= call_user_func_array($func, $args);
		array_pop($args);
	}
	return $ret;
}

/**
 * Choose an item from array randomly.
 *
 * @author Jack
 * @date Sat Feb 21 10:21:21 2015
 */
function choice(array $array) {
	return $array[array_rand($array)];
}

/**
 * Get the resource relative to object's script file, this is quite useful to
 * get the resource content that locates in the composer's vendor folder.
 *
 * @author Jack
 * @date Sat Feb 21 10:22:35 2015
 * @param name
 * 		The name of the resource
 * @param obj
 * 		The object
 * @return
 * 		The content of the resource
 */
function content_relative($name, $obj) {
	$path = class_script_path($obj);
	if($path) {
		$p = path_join(dirname($path), $name);
		if(\file_exists($p)) {
			return \file_get_contents($p);
		}
	}
	return null;
}

/**
 * Get current posix user(will need php posix plugin)
 *
 * @author Jack
 * @date Sat Feb 21 10:24:10 2015
 */
function current_user() {
	if(\extension_loaded('posix')) { // Try intl extension first
		$user = \posix_getpwuid(\posix_geteuid());
	}
	else {
		$user = array();
		$user['name'] = exec('whoami');
	}
	return ucfirst($user['name']);
}

/**
 * Test if the object is valid
 *
 * @author Jack
 * @date Sat Feb 21 10:24:46 2015
 * @param obj
 * 		The object to test
 * @param class
 * 		The object's class or super class
 */
function valid_obj($obj, $class) {
	return is_object($obj) && (class_exists($class) || interface_exists($class))
		&& (get_class($obj) == $class || is_subclass_of($obj, $class) ||
	   	in_array($class, class_implements(get_class($obj))));
}

/**
 * Get, set or append to clips context
 *
 * @author Jack
 * @date Sat Feb 21 10:28:44 2015
 * @param key
 * 		The key of the context
 * @param value
 * 		The value of the key
 * @param append (default false)
 * 		Whether to append or replace this value	to context
 */
function context($key = null, $value = null, $append = false) {
	return clips_context($key, $value, $append);
}

function context_peek($key) {
	$tool = &get_clips_tool();
	return $tool->context_peek($key);
}

/**
 * Test the path before adding extension to it
 *
 * @param path
 * 		The path to add extension
 * @param ext
 * 		The extension
 * @return The path that has the extension
 */
function safe_add_extension($path, $ext) {
	if(str_end_with($path, $ext)) {
		return $path;
	}
	else {
		return $path.'.'.$ext;
	}
}

/**
 * Pop from the context value, if there is only 1 value in the context for the key, just remove it
 *
 * @param key
 * 		The key
 * @return 
 * 		The value of the key in the context
 */
function context_pop($key) {
	$tool = &get_clips_tool();
	return $tool->context_pop($key);
}

/**
 * Appending the error to clips context
 *
 * @author Jack
 * @date Sat Feb 21 10:30:08 2015
 * @param cause
 * 		The cause of the error
 * @param message
 * 		The message of the error
 */
function error($cause, $message = array()) {
	clips_error($cause, $message);
}

function clips_error($cause, $message = array()) {
	context('error', new Error($cause, $message), true);
}

/**
 * Show the error using sprintf pattern
 *
 * @author Jack
 * @date Sat Feb 21 10:31:05 2015
 */
function show_error() {
	$msg = call_user_func_array('sprintf', func_get_args());
	trigger_error($msg);
}


/**
 * Get the annotation from class or method
 *
 * @author Jack
 * @date Sat Feb 21 10:31:44 2015
 * @param class
 * 		The class to get the annotation
 * @param annotation
 * 		The class of the annotation
 * @param method
 * 		The method to get the annotation
 */
function get_annotation($class, $annotation, $method = null) {
	$re = new \Addendum\ReflectionAnnotatedClass($class);
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

/**
 * Format the object using the formatter
 *
 * @param obj
 * 		The object to be formatted
 * @param formatter
 * 		The formatter	
 * @return
 * 		The formatted string
 */
function format($obj, $formatter) {
	$f = Formatter::get($formatter);
	if($f)
		return $f->format($obj);
	return '';
}

/**
 * Get the script path of the given class
 *
 * @author Jack
 * @date Sat Feb 21 10:33:55 2015
 * @param class
 * 		The class
 * @return The path of the class script
 */
function class_script_path($class) {
    $rc = new \ReflectionClass($class);
    return $rc->getFileName();
}

/**
 * The fundamental path guess function, will try current working directory and clips path
 * to get the resource
 *
 * @param path
 * 		The relative path of the resource
 * @param others
 * 		The alternative pathes
 */
function try_path($path, array $others = array()) {
	foreach(array_merge($others, array(getcwd(), clips_path('/'))) as $pre) {
		$p = path_join($pre, $path);
		if(file_exists($p))
			return $p;
	}
	return false;
}

function clips_context($key = null, $value = null, $append = false) {
	if(is_object($key) || is_array($key)) {
		foreach($key as $k => $v) {
			clips_context($k, $v, $append);
		}
		return true;
	}

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

/**
 * Try to find the file in folder recursively
 *
 * @author Jack
 * @date Sat Feb 21 10:37:11 2015
 * @param folder
 * 		The folder to find the file
 * @param file
 * 		The filename	
 * @param suffix (default null)
 * 		The file extension (only match the same extension)
 * @param blur (default false)
 * 		If using blur, will just locate the filename in the filename, not matching it
 * @return All the matching files
 */
function find_file($folder, $file, $suffix = null, $blur = false) {
	$ret = array();

	if(is_array($folder)) {
		foreach($folder as $f) {
			$ret = array_merge($ret, find_file($f, $file, $suffix, $blur));
		}
		return $ret;
	}

	$iterator = new \DirectoryIterator($folder);
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

/**
 * Safely parse the JSON, using a JSON lint library
 * 
 * @author Jack
 * @date Sat Feb 21 10:40:14 2015
 */
function parse_json($json) {
	$parser = new \Seld\JsonLint\JsonParser();
	$ret = $parser->lint($json);
	if($ret) {
		trigger_error($ret->getMessage());
		return null;
	}
	else
		return json_decode($json);
}

/**
 * Make file exists function safe(because the resource uri scheme will trigger php streamhandlers)
 *
 * @author Jack
 * @date Sat Feb 21 10:41:29 2015
 */
function safe_file_exists($file) {
	if(strpos($file, "://") !== false) {
		// Skip this for resource
		return false;
	}
	return file_exists($file);
}

/**
 * Smooth the string like a.b.c to a_b_c
 * 
 * @author Jack
 * @date Sat Feb 21 10:41:51 2015
 */
function smooth($str) {
	$result = explode('.', $str);
	return implode('_', $result);
}

/**
 * Flattern the string like Demo\TestService to demo_test_service
 *
 * @author Jack
 * @date Sat Feb 21 10:42:29 2015
 */
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

/**
 * Make the string like demo/test_service to Demo/TestService
 *
 * @author Jack
 * @date Sat Feb 21 10:43:19 2015
 */
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

/**
 * The missing function in php, to test if the string is end with string.
 *
 * @author Jack
 * @date Sat Feb 21 10:44:01 2015
 */
function str_end_with($haystack, $needle, $trim = true) {
	if($trim) {
		$str = trim($haystack);
	}
	else {
		$str = $haystack;
	}
	return strrpos($str, $needle) === strlen($str) - strlen($needle);
}

/**
 * Get the content using the resource
 *
 * @author Jack
 * @date Sat Feb 21 10:44:37 2015
 */
function resource_contents($uri) {
	$r = new Resource($uri);
	return $r->contents();
}

/**
 * Remove all the files and the folder
 *
 * @author Jack
 * @date Sat Feb 21 10:45:40 2015
 */
function rmr($path) {
	if (PHP_OS === 'Windows') {
		exec("rd /s /q {$path}");
	}
	else {
		exec("rm -rf {$path}");
	}
}

/**
 * Get the tmp file using php's tempnam and system temp dir
 *
 * @author Jack
 * @date Sat Feb 21 10:45:51 2015
 */
function get_tmp_file($prefix = 'clips_tmp') {
	return tempnam(sys_get_temp_dir(), $prefix);
}

/**
 * Log the stacktrace to the log
 *
 * @author Jack
 * @date Sat Feb 21 10:47:12 2015
 * @param message
 * 		The message to log
 * @param level
 * 		The stacktrace level
 */
function stacktrace($message, $level = 3) {
	clips_stacktrace($message, $level);
}

function clips_stacktrace($message, $level = 3) {
	$trace = debug_backtrace();
	$ret = array();
	for($i = 1; $i <= $level; $i++) {
		$ret []= $trace[$i];
	}
	clips_log($message, $ret);
}

/**
 * Log using clips tool's LoggerInterface, only used for test or helper function.
 * For objects, the LoggerAware interface is more appriciated
 *
 * @author Jack
 * @date Sat Feb 21 10:49:09 2015
 * @param message
 * 		The message to log
 * @param context
 * 		The log context
 */
function log($message, $context = array()) {
	clips_log($message, $context);
}

function clips_log($message, $context = array()) {
	$tool = &get_clips_tool();
	$tool->info($message, $context);
}

/**
 * The meta function for console tools, only support posix console, and only get the width of the 
 * console
 *
 * @author Jack
 * @date Sat Feb 21 10:50:15 2015
 */
function console_meta() {
	return array('width' => `tput cols`);
}

class Depends extends \Addendum\Annotation {
}

/**
 * Join the pathes carefully
 *
 * @author Jack
 * @date Sat Feb 21 10:50:57 2015
 */
function path_join() {
	return str_replace('//', '/', str_replace('///', '/', implode('/', func_get_args())));
}

/**
 * Test if the php is called in cli
 *
 * @author Jack
 * @date Sat Feb 21 10:51:14 2015
 */
function is_cli() {
	return (php_sapi_name() === 'cli' OR defined('STDIN'));
}

/**
 * Get the clips tool globally
 *
 * @author Jack
 * @date Sat Feb 21 10:51:37 2015
 */
function &get_clips_tool() {
	return Tool::get_instance();
}

/**
 * Get the configuration
 *
 * @author Jack
 * @date Sat Feb 21 10:52:02 2015
 * @param name
 * 		The name of the config
 * @param default
 * 		if no configuration is found, the default value to be returned
 */
function config($name, $default = null) {
	return clips_config($name, $default);
}

function clips_config($name, $default = null) {
	$tool = &get_clips_tool();
	$ret = $tool->config->$name;
	if($ret)
		return $ret;
	return $default;
}

/**
 * The relative path of the clips tool
 *
 * @author Jack
 * @date Sat Feb 21 10:52:59 2015
 */
function clips_tool_path($path) {
	return CLIPS_TOOL_PATH.$path;
}

/**
 * Record this php file is loaded, this function is used for loading php file
 * using rule engine
 *
 * @author Jack
 * @date Sat Feb 21 10:53:34 2015
 */
function record_file_load($file) {
	if($file) {
		$tool = get_clips_tool();
		$tool->fileLoaded($file);
		return true;
	}
	return false;
}

/**
 * Test if the file is loaded
 *
 * @author Jack
 * @date Sat Feb 21 10:54:21 2015
 */
function file_load($file) {
	if($file) {
		$tool = &get_clips_tool();
		return $tool->isFileLoaded($file);
	}
	return false;
}

/**
 * Adding file prefix and suffix to filename only, this function will act like this:
 *
 * a/b/c/d => a/b/c/prefix_d_suffix.php
 *
 * This is the tool function for loading php file using rule engine
 *
 * @author Jack
 * @date Sat Feb 21 10:56:07 2015
 */
function process_file_name($prefix, $file, $suffix) {
	$arr = explode('/', $file);
	$tail = array_pop($arr);
	$arr []= $prefix.$tail.$suffix;
	return implode('/', $arr);
}

/**
 * Load the php file using guard of record_file_load
 *
 * @author Jack
 * @date Sat Feb 21 10:56:38 2015
 */
function clips_php_require_once($file) {
	if(require_once($file)) {
		record_file_load($file);
		return true;
	}
	return false;
}

/**
 * Load the library
 *
 * @author Jack
 * @date Sat Feb 21 10:56:58 2015
 * @param library
 * 		The library to load
 * @param init (default true)
 * 		Should init the library class?
 * @param suffix (default '')
 * 		The suffix of the class
 */
function clips_library($library, $init = true, $suffix = "") {
	$tool = get_clips_tool();
	return $tool->library($library, $init, $suffix);
}

function str_template($template, $args) {
	return clips_out('string://'.$template, $args, false);
}

/**
 * Output the template using mustache, the template is load using tpl:// resource by default
 *
 * @author Jack
 * @date Sat Feb 21 10:58:36 2015
 * @param template
 * 		The template resource, by default is tpl://{resource}
 * @param args
 * 		The template args
 * @param output (default true)
 * 		Should output to stdout?
 */
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

/**
 * Get the path relative to clips tool
 *
 * @author Jack
 * @date Sat Feb 21 11:00:20 2015
 */
function path($path) {
	return clips_path($path);
}

function clips_path($path) {
	$rc = new \ReflectionClass("Clips\\Tool");
	return path_join(dirname($rc->getFileName()), $path);
}

/**
 * Load the rules using clips engine
 *
 * @author Jack
 * @date Sat Feb 21 11:00:47 2015
 */
function load_rules($rules) {
	clips_load_rules($rules);
}

function clips_load_rules($rules) {
	if($rules) {
		$tool = &get_clips_tool();
		return $tool->clips->load($rules);
	}
	return false;
}

/**
 * Get the value of the key from object or array, if no value is there, return the default value
 *
 * @author Jack
 * @date Sat Feb 21 11:02:05 2015
 */
function get_default($arr, $key, $default = '') {
	if(is_object($arr))
		return isset($arr->$key)? $arr->$key: $default;
	if(is_array($arr))
		return isset($arr[$key])? $arr[$key]: $default;
	return $default;
}

/**
 * This is the helper function for clips engine, to match the string using preg_match
 *
 * @author Jack
 * @date Sat Feb 21 11:02:29 2015
 */
function clips_str_match($str, $pattern) {
	return !!preg_match('/'.$pattern.'/', $str);
}

/**
 * This is the helper function for clips engine, to get the value from object or array
 *
 * @author Jack
 * @date Sat Feb 21 11:03:24 2015
 */
function clips_get_property($obj, $property) {
	if(is_array($obj) && isset($obj[$property])) {
		return $obj[$property];
	}

	if(is_object($obj) && isset($obj->$property)) {
		return $obj->$property;
	}
	return null;
}

/**
 * Get the controller's class
 *
 * @author Jack
 * @date Sat Feb 21 11:03:55 2015
 */
function controller_class($c) {
	$tool = &get_clips_tool();
	return $tool->controller(ucfirst($c));
}

/**
 * Test if the controller is exists, this is used for router's rule
 *
 * @author Jack
 * @date Sat Feb 21 11:04:23 2015
 */
function controller_exists($c) {
	return !! controller_class($c);
}

/**
 * Extend the dest array using the src array.
 * And if the key is in the additional fields, will just append the value instead of replacing it
 *
 * @author Jack
 * @date Sat Feb 21 11:05:25 2015
 * @param $dest
 * 		The dest array
 * @param $src
 * 		The src array
 * @param fields
 * 		The append fields, if the key is in this array, will append instead of replace
 * @return The dest array
 */
function extend_arr($dest, $src, $fields = null) {
	if($src == null || !(is_array($src) || is_object($src)))
		return $dest;

	foreach($src as $key => $value) {
		if(isset($dest[$key]) && ($fields == null || array_search($key, $fields) !== false)) {
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

/**
 * Create an new object using class, and copy all the data from src to it
 *
 * @author Jack
 * @date Sat Feb 21 11:07:08 2015
 * @param src
 * 		The src object or array
 * @param class
 * 		The class of the object, if null will use stdclass
 * @return The object
 */
function copy_new($src, $class = null) {
	return copy_object($src, null, $class);
}

/**
 * Copy the src object or array to dest array
 * 
 * @author Jack
 * @date Sat Feb 21 11:08:43 2015
 */
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

/**
 * Copy src object or array to dest object
 *
 * @author Jack
 * @date Sat Feb 21 11:09:04 2015
 */
function copy_object($src, $dest = null, $class = null) {
	if($src == null)
		return null;

	if($dest == null) {
		if($class == null)
			$dest = new \stdclass();
		else
			$dest = new $class();
	}

	foreach($src as $key => $value) {
		$dest->$key = $value;
	}
	return $dest;
}

/**
 * Get the message bundle by its name
 *
 * @author Jack
 * @date Mon Feb 23 14:01:51 2015
 */
function bundle($name = '') {
	$tool = &get_clips_tool();
	return $tool->bundle($name);
}

/**
 * Get the language message using current bundle
 *
 * @author Jack
 * @date Sun Mar  8 20:00:39 2015
 */
function lang() {
	$bundle_name = Clips\context('current_bundle');
	if(!$bundle_name)
		$bundle_name = '';
	$bundle = bundle($bundle);
	return call_user_func_array(array($bundle, 'message'), func_get_args());
}

/**
 * Generate a random string
 *
 * @author Jack
 * @date Tue Feb 24 11:34:12 2015
 */
function random_string($length = 10) {
	return substr(str_shuffle(RANDOM_STRING), 0, $length);
}

/**
 * Try hash the password using password_hash method, if that is not there, will hash the password
 * using salted md5.
 *
 * If using 2 parameters, will try to verify the password using the hash
 *
 * @param password
 * 		The password
 * @param hash
 * 		The hash to verify (default null)
 * @param force_hash
 * 		Force to use hash (mainly for testing)
 * @return
 * 		If hashing, then the hashed password or if the password matches the hash
 *
 * @author Jack
 * @date Tue Feb 24 11:28:16 2015
 *
 */
function password($password, $hash = null, $force_hash = false) {
	if(function_exists('password_hash') && !$force_hash) { // Thank godness, we are above 5.5, let's use this method in stead
		if(isset($hash)) {
			return password_verify($password, $hash);
		}

		return password_hash($password, PASSWORD_DEFAULT);
	}
	else {
		if(isset($hash)) {
			$data = explode('#', $hash);
			if(count($data) != 2)
				return false;
			$salt = $data[0];
			return $data[1] == md5($salt.$password);
		}
		else {
			$salt = random_string(27);
			$p = $salt.$password;
			return $salt.'#'.md5($p);
		}
	}
}

/**
 * Query the object using the property query
 *
 * The query should be something like this:
 *
 * a.b[1].*.c[2].d.e[3]
 *
 * The '.' notation will refer to hash operation, and the [] notation
 * will refer to collection operation
 *
 * @author Jack
 * @date Tue Mar  3 13:54:24 2015
 */
function property($query, $obj) {
	if($query && $obj) {

	}
	return null;
}

function profile_start($name = 'main') {
	$profile = config('profile');
	if($profile) {
		$name = 'profile_'.$name;
		context($name, array(
			'cpu' => sys_getloadavg(),
			'memory' => memory_get_usage(),
			'time' => microtime()
		));
	}
}

function searcher() {
	$tool = &get_clips_tool();
	return $tool->load_class('Searcher', true);
}

function sequence($name) {
	$tool = &get_clips_tool();
	return $tool->sequence($name);
}

function profile_end($name = 'main') {
	$profile = config('profile');
	if($profile) {
		$n = 'profile_'.$name;
		$info = context($n);
		if($info) {
			$info['time'] = microtime() - $info['time'];
			$info['name'] = $name;
			$info['cpu'] = $info['cpu'][0];
			log('Name: {name}, Time: {time}, CPU: {cpu}, Memory: {memory}', $info);
		}
	}
}

/**
 * Get the current timestamp in string format
 *
 * @author Jack
 * @date Wed Mar 11 08:55:14 2015
 */
function timestamp() {
	return strftime("%a %b %e %H:%M:%S %Y");
}

function now($time = null) {
	if($time) {
		return strftime("%Y-%m-%d %H:%M:%S", strtotime($time));
	}
	return strftime("%Y-%m-%d %H:%M:%S");
}

/**
 * Read and parse the yaml file, if not exists, return false
 *
 * @author Jack
 * @date Wed Mar 11 08:55:39 2015
 */
function yaml($path) {
	$path = try_path($path);
	if($path) {
		return \Symfony\Component\Yaml\Yaml::parse(file_get_contents($path));
	}
	return false;
}

function current_env() {
	$tool = &get_clips_tool();
	return $tool->clips->current_env;
}
