<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

/**
 * The base class for all the formatter
 * 
 * @author Jack
 * @date Mon Feb 23 15:41:50 2015
 */
class Formatter extends \Addendum\Annotation {
	public function format($obj) {
		return '';
	}

	public static function get($name, $args = null) {
		$tool = &get_clips_tool();
		return $tool->library($name, true, 'Formatter', $args);
	}
}
