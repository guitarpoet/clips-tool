<?php namespace Clips\Commands; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

class ShellCommand extends \Clips\Command {
	public function execute($args) {
		$tool = get_clips_tool();
		$tool->ruleConsole();
	}
}