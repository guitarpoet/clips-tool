<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

/**
 * The clips command
 */
class Clips_Usage_Command extends Clips_Command {
	public function execute($args) {
		print_r($args);
		echo "This is usage.\n";
	}
}
