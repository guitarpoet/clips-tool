<?php namespace Clips\Libraries; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

use Clips\BaseService;

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

		// Check if we can found the compiled php
		if(!file_exists($phpname)) {
			// Can't found this template in cache, read it from resource
			$resource = new \Clips\Resource($template);
			$str = $resource->contents();

			if($str) {
				$php = \LightnCandy::compile($str, array('flags' => \LightnCandy::FLAG_PROPERTY));
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
