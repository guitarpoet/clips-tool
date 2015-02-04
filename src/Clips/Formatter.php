<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

/**
 * The base class for all the formatter
 */
class Formatter {
	public function format($obj) {
		return '';
	}

	public static function get($name, $args = null) {
		$tool = &get_clips_tool();
		return $tool->library($name, true, 'Formatter', $args);
	}
}
