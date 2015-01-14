<?php namespace Clips\Commands; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

class SassCommand extends \Clips\Command {
	public function execute($args) {
		$tool = &get_clips_tool();
		$sass = $tool->library('sass');
		array_shift($args);
		array_shift($args);
		$sass->addIncludePath(getcwd());
		echo $sass->compile($args);
	}
}
