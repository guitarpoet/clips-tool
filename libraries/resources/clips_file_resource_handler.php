<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

class Clips_File_Resource_Handler extends Clips_Resource_Handler {
	public function __construct() {
		parent::__construct();
	}

	public function openStream($uri) {
		return fopen($uri);
	}

	public function closeStream($stream) {
		return fclose($stream);
	}

	public function contents($uri) {
		return file_get_contents($uri);
	}
}
