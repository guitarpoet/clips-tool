<?php namespace Clips\Commands; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

use Clips\Command;

/**
 */
class UnserializeCommand extends Command {
	public function execute($args) {
		if($args) {
			if(!is_array($args)) {
				$args = array($args);
			}
			foreach($args as $file) {
				$file = \Clips\try_path($file);
				if($file) {
					$c = file_get_contents($file);
					$o = unserialize($c);
					print_r($o);
				}
			}
		}

	}
}
