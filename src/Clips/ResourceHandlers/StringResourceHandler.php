<?php namespace Clips\ResourceHandlers; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

class StringResourceHandler extends \Clips\BaseResourceHandler {
	public function __construct() {
		parent::__construct();
	}

	public function openStream($uri) {
		return fopen(str_replace("string://", "str:", $uri), 'r');
	}

	public function contents($uri) {
		return str_replace("string://", "", $uri);
	}
}
