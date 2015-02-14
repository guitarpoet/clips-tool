<?php namespace Clips\Commands; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

use Clips\Command;
use Phinx\Console\PhinxApplication;
use Symfony\Component\Console\Input\ArgvInput;

/**
 * Proxy to the phinx command, to run the phinx through clips comamnd interface
 *
 * @FullArgs
 */
class PhinxCommand extends Command {
	public function execute($args) {
		array_shift($args); // Remove the phinx command
		$a = new PhinxApplication();
		$a->run(new ArgvInput($args));
	}
}
