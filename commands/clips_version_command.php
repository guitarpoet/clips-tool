<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

class Clips_Version_Command extends Clips_Command {
	public function execute($args) {
		$this->start(5);

		for($i = 1; $i <= 5; $i++) {
			$this->progress($i);
			sleep(1);
		}

		echo get_clips_tool()->config->version[0];
	}
}
