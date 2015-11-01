<?php namespace Clips\Commands; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

use Clips\Command;

/**
 * The command that read the contents of the zip
 *
 * @author Jack
 * @version 1.1
 * @date Mon Oct 12 16:53:34 2015
 */
class ZipCommand extends Command {

	public function execute($args) {
		if($args) {
			$file = $args[0];

			if(file_exists($file) && \Clips\is_zip($file)) {
				switch(count($args)) {
				case 1:
					$p = new \ZipArchive();
					$p->open($file);
					for($i = 0; $i < $p->numFiles; $i++) {
						$stat = $p->statIndex($i);
						echo $stat['name'].PHP_EOL;
						print_r($stat);
					}
					break;
				default:
					echo \Clips\zip_contents($file, $args[1]);
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
