<?php namespace Clips\ResourceHandlers; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

class FileResourceHandler extends \Clips\BaseResourceHandler {
	public function __construct() {
		parent::__construct();
	}

	public function openStream($uri) {
		$file = str_replace('file://', '', $uri);
		if(file_exists($file))
			return fopen($file, 'r');
		return null;
	}

	public function contents($uri) {
		$file = str_replace('file://', '', $uri);
		if(file_exists($file))
			return file_get_contents($file);
		return null;
	}
}
