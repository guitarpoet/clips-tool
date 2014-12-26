<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

class Clips_Version_Command extends Clips_Command {
	public function execute() {
		echo get_clips_tool()->config->version[0];
	}
}
