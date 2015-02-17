<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed"); 

use Addendum\Annotation;

/**
 * The test value wrapper
 */
class TestValue extends Annotation {
	public $file;
	public $context;
}
