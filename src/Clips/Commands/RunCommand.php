<?php namespace Clips\Commands; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

class RunCommand extends \Clips\Command {
	public function execute($args) {
		if(count($args) > 2) {
			$tool = &\Clips\get_clips_tool();
			foreach(array_splice($args, 2) as $file) {
				$tool->loadRule($file);
			}
		}
	}
}
