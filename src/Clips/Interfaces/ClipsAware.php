<?php namespace Clips\Interfaces; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

interface ClipsAware {
	public function setClips($clips);
}
