<?php namespace Clips\Libraries\Sass; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

/**
 * This is the super class of all the sass compile plugins
 *
 * @author Jack
 * @date Sat Feb 21 11:59:42 2015
 */
class SassPlugin {
	/**
	 * Processing befoer the content, will add something before the content(some variable or function definition, for example)
	 *
	 * @param compiler
	 * 		The compiler reference
	 */
	public function prefix($compiler) {

	}

	/**
	 * Processing after the content, will add something after the content(some function calls, for example)
	 * @param compiler
	 * 		The compiler reference
	 */
	public function suffix($compiler) {

	}
}
