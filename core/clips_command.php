<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

/**
 *
 * The base class for all the commands
 *
 * @author Jack
 * @version 1.0
 * @date Fri Dec 26 15:07:51 2014
 */
class Clips_Command {
	public function execute($args) {
	}

	public function incre($value = 1) {
		$tool = get_clips_tool();
		if(isset($tool->progress_manager)) {
			$tool->progress_manager->incre($value);
		}
	}

	public function progress($value) {
		$tool = get_clips_tool();
		if(isset($tool->progress_manager)) {
			$tool->progress_manager->update($value);
		}
	}

	public function start($total = 100) {
		$tool = get_clips_tool();
		if(isset($tool->progress_manager)) {
			$tool->progress_manager->start($total);
		}
	}
}
