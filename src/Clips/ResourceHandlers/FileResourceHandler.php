<?php namespace Clips\ResourceHandlers; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

class FileResourceHandler extends \Clips\BaseResourceHandler {
	public function __construct() {
		parent::__construct();
	}

	public function openStream($uri) {
		return fopen($uri, 'r');
	}

	public function contents($uri) {
		return file_get_contents($uri);
	}
}
