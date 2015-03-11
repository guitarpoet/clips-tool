<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

class Symbol extends \Addendum\Annotation {

	public static function symbol($value) {
		$s = new Symbol();
		$s->value = $value;
		return $s;
	}
} 

