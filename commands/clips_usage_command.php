<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

/**
 * The clips command
 */
class Clips_Usage_Command extends Clips_Command {
	public function execute($args) {
		if($args) {
			$script = array_shift($args);
			$tool = get_clips_tool();
			echo $tool->template("tpl://usage", array('script' => $script));
		}
	}
}
