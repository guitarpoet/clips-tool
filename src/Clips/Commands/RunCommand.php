<?php namespace Clips\Commands; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

class RunCommand extends \Clips\Command {
	public function execute($args) {
		if(count($args) > 0) {
			$tool = &\Clips\get_clips_tool();
			foreach($args as $file) {
				$tool->loadRule($file);
				$tool->clips->run();
			}
		}
	}
}
