<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

use Addendum\Annotation;

/**
 * This annotation will config the rules for router to load in controller's method
 *
 * @author Jack
 * @date Mon Feb 23 16:17:04 2015
 */
class Rules extends Annotation {
	public $clear = true;
    public $rules = array();
    public $templates = array();
}

