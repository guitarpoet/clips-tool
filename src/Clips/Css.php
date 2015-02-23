<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

use Addendum\Annotation;

/**
 * The annotation for indicate the css files that the controller of method needs.
 *
 * Just put this annotation to the method or the class of the controller, and let framework
 * to care about the location of the css files.
 *
 * @author Jack
 * @date Mon Feb 23 15:27:09 2015
 */
class Css extends Annotation {
}
