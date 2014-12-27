<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

class Clips_Resource_Handler {
	public function __construct() {
	}

	public function openStream($uri) {
		return null;
	}

	public function closeStream($stream) {
		return false;
	}

	public function contents($uri) {
		return null;
	}
}
