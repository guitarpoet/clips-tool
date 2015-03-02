<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed"); 

use Addendum\Annotation;

/**
 * This annotation indicates that the object enhanced by clips tool, will have a dependency object name like this.
 *
 * @author Jack
 * @date Mon Feb 23 16:06:46 2015
 */
class Object extends Annotation {
	public $name;
	public $args = null;
}
