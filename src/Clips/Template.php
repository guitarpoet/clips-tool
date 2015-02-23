<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

/**
 * The default template render engine(based on mustache)
 *
 * @Clips\Library("mustache")
 */
class Template {
	public function render() {
		return call_user_func_array(array($this->mustache, "render"), func_get_args());
	}
}
