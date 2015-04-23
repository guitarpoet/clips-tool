<?php namespace Clips\ResourceHandlers; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

/**
 * The resource handler for app://
 *
 * @author Jack
 * @version 1.0
 * @date Tue Apr 21 17:55:25 2015
 */
class AppResourceHandler extends \Clips\BaseResourceHandler {
	public function __construct() {
		parent::__construct();
	}

	public function openStream($uri) {
		$file = str_replace('app://', '', $uri);
		$path = \Clips\try_path(\Clips\path_join('application', $file));
		if($path)
			return fopen($path, 'r');
		return null;
	}

	public function contents($uri) {
		$file = str_replace('app://', '', $uri);
		$path = \Clips\try_path(\Clips\path_join('application', $file));
		if($path)
			return file_get_contents($path);
		return null;
	}
}
