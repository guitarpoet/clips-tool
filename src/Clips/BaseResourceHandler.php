<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

class BaseResourceHandler implements ResourceHandler {
	public function __construct() {
	}

	public function openStream($uri) {
		return null;
	}

	public function closeStream($stream) {
		return fclose($stream);
	}

	public function contents($uri) {
		return null;
	}
}
