<?php namespace Clips\Libraries; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

use Clips\BaseService;

function site_url($arr) {
	if($arr)
		return \Clips\site_url($arr[0]);
	return false;
}

function randtimes($s, $e, $options) {
	$ret = '';
	$n = rand($s, $e);
	for($i = 1; $i <= $n; $i++) {
		$ret .= $options['fn']($i);
	}
	return $ret;
}

function times($n, $options) {
	$ret = '';
	for($i = 0; $i < $n; $i++) {
		$ret .= $options['fn']($i);
	}
	return $ret;
}

function php_call($arr) {
	if($arr) {
		$function = array_shift($arr);
		if(function_exists($function)) {
			return call_user_func_array($function, $arr);
		}
	}
	return false;
}

function static_url($arr) {
	if($arr)
		return \Clips\static_url($arr[0]);
	return false;
}

/**
 * @Clips\Library("fileCache")
 */
class Mustache extends BaseService {

	/**
	 * Render the template using the mustache engine
	 *
	 * @args
	 * 		template: The template uri for render
	 * 		args: The args that used for render
	 */
	public function render($template, $args = array()) {
		if(func_num_args() > 2) { // They must using the variable args method
			$args = func_get_args();
			$template = array_shift($args);
			return $this->render($template, $args);
		}

		// Get the cache file name by md5 it
		$hash = md5($template);
		$cacheDir = $this->filecache->cacheDir();

		if(!file_exists($cacheDir)) { // Create the directory if not exists
			mkdir($cacheDir, 0755, true);
		}
		$phpname = \Clips\path_join($cacheDir, 'tpl_'.$hash.'.php');

		$debug = \Clips\config('debug_template');
		// Check if we can found the compiled php
		if(!file_exists($phpname) || $debug) {
			// Can't found this template in cache, read it from resource
			$resource = new \Clips\Resource($template);
			$str = $resource->contents();

			$flags = \Clips\context('template_flags');
			if(!$flags) {
				$flags = \Clips\config('template_flags');
				if($flags) {
					$flags = $flags[0];
				}
				else {
					$flags = \LightnCandy::FLAG_ERROR_EXCEPTION | \LightnCandy::FLAG_HANDLEBARS | \LightnCandy::FLAG_HANDLEBARSJS;
				}
			}

			$opts = array('flags' => $flags);

			$partials = \Clips\context('template_partials');
			if($partials) {
				$opts['partials'] = $partials;
			}

			$default_helpers = array(
				'site_url' => '\\Clips\\Libraries\\site_url',
				'static_url' => '\\Clips\\Libraries\\static_url',
				'php' => '\\Clips\\Libraries\\php_call'
			);

			$default_block_helpers = array(
				'times' => '\\Clips\\Libraries\\times',
				'randtimes' => '\\Clips\\Libraries\\randtimes'
			);

			$helpers = \Clips\context('template_helpers');

			if($helpers) {
				$opts['helpers'] = array_merge($default_helpers, $helpers);
			}
			else {
				$opts['helpers'] = $default_helpers;
			}

			$block_helpers = \Clips\context('template_block_helpers');

			if($block_helpers) {
				$opts['hbhelpers'] = array_merge($default_block_helpers, $block_helpers);
			}
			else {
				$opts['hbhelpers'] = $default_block_helpers;
			}

			if($str) {
				$php = \LightnCandy::compile($str, $opts);
				// Save it to php file
				file_put_contents($phpname, $php);
			}
		}

		if(file_exists($phpname)) {
			$renderer = include($phpname);
			return $renderer((array) $args);
		}

		return '';
	}
}
