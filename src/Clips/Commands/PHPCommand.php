<?php namespace Clips\Commands; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

use Clips\Command;

class PHPCommand extends Command {
	public function execute($args) {
		if($args) {
			foreach($args as $script) {
				require($script);
			}
		}
		else {
			$this->error("No PHP Script found!");
		}
	}
}
