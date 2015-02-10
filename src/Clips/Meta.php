<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

use Addendum\Annotation;

class Meta extends Annotation {
	public $key;
	public $value;
}
