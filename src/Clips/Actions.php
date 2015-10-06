<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

use Addendum\Annotation;

/**
 * This is the annotation to let controller generate the default actions to render arguments before render.
 * This annotation is quite useful for auto generate the form actions for the CRUD form.
 *
 * @author Jack
 * @version 1.0
 */
class Actions extends Annotation {
}
