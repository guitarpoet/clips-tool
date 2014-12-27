<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

class Clips_Template {
	public function __construct() {
		$tool = get_clips_tool();
		$this->engine = $tool->library('moustache');
	}

	public function render() {
		return call_user_func_array(array($this->engine, "render"), func_get_args());
	}
}
