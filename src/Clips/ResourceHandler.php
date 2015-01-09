<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

interface ResourceHandler {
	public function openStream($uri);

	public function closeStream($stream);

	public function contents($uri);
}
