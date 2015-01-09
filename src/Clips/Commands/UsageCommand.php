<?php namespace Clips\Commands; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

class UsageCommand extends \Clips\Command {
	public function execute($args) {
		clips_out('usage', array('script' => basename($args[0])));
	}
}
