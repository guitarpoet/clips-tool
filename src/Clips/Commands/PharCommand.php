<?php namespace Clips\Commands; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

use Clips\Command;

/**
 * The command that read the contents of the phar
 *
 * @author Jack
 * @version 1.1
 * @date Sat Aug 29 14:56:53 2015
 */
class PharCommand extends Command {

	public function execute($args) {
		if($args) {
			$file = $args[0];

			if(file_exists($file) && \Clips\is_phar($file)) {
				switch(count($args)) {
				case 1:
					$p = new \Phar($file);
					foreach($p as $k => $v) {
						echo $k.PHP_EOL;
					}
					break;
				default:
					echo \Clips\phar_contents($file, $args[1]);
				}
			}
			else {
				$this->error("The file $file is not exists or is not a phar file!");
			}
		}
		else {
			$this->error('No phar file input!');
		}
	}
}
