<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

class Clips_Shell_Command extends Clips_Command {
	public function execute($args) {
		$tool = get_clips_tool();
		$tool->ruleConsole();
	}
}
