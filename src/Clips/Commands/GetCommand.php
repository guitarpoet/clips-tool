<?php namespace Clips\Commands; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

use Clips\Command;

class GetCommand extends Command {
	public function execute($args) {
		echo 'Hello';
	}
}
