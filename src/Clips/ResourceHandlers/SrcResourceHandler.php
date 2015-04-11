<?php namespace Clips\ResourceHandlers; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

class SrcResourceHandler extends \Clips\BaseResourceHandler {
	public function __construct() {
		parent::__construct();
	}

	public function openStream($uri) {
		$uri = str_replace('src://', '', $uri);
		$path = \Clips\try_path($uri);
		if($path)
			return fopen($path, 'r');
		return null;
	}

	public function contents($uri) {
		$uri = str_replace('src://', '', $uri);
		$path = \Clips\try_path($uri);
		if($path)
			return file_get_contents($path);
		return null;
	}
}
