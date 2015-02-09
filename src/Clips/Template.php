<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

class Template {
	public function __construct() {
		$tool = &\Clips\get_clips_tool();
		$this->engine = $tool->library('Mustache');
	}

	public function render() {
		return call_user_func_array(array($this->engine, "render"), func_get_args());
	}
}
