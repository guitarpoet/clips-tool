<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

class Console {
	public function __construct($argv) {
		$argv = explode(' ', $argv);
		$tool = get_clips_tool();

		$command = 'shell';

		if(count($argv) >= 2 && trim($argv[1])) { // We have the command
			$command = $argv[1];
		}

		$tool->execute($command, $argv);
	}
}
