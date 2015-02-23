<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

use Addendum\Annotation;

/**
 * The annotation to manipulate the html meta tags for controller class or method
 *
 * @author Jack
 * @date Mon Feb 23 16:01:36 2015
 */
class Meta extends Annotation {
	public $key;
	public $value;
}
