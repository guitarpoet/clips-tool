<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

use Addendum\Annotation;

/**
 * The annotation to set the configuration for the models(mostly for DBModel).
 * Though DBModel can exists without this annotation, but any model annotated with this annotation,
 * can be accessed through clips context(since every model indicated by this annotation, will append
 * to the clips context model)
 *
 * @author Jack
 * @date Mon Feb 23 16:03:31 2015
 */
class Model extends Annotation {
	public $table;
	public $name;
}
