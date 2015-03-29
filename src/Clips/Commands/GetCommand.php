<?php namespace Clips\Commands; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

use Clips\Command;

/**
 * The manual download command
 *
 * @author Jack
 * @version 1.0
 * @date Sat Mar 28 12:57:59 2015
 *
 * @Clips\Library("downloadManager")
 */
class GetCommand extends Command {

	/**
	 * The command to download browser caps file
	 */
	public function bcap() {
		$this->output("Downloading browser caps file from ".BCAP_URL.PHP_EOL);
		$path = \Clips\cache_filename(BCAP_FILENAME);
		if($path) {
			$file = $this->downloadmanager->download(BCAP_URL);
			// Write the file
			file_put_contents($path, $file);

			// We already have the browscap cache
			$b = new \phpbrowscap\Browscap(dirname($path));
			$b->localFile = $path;
			$b->lowercase = true;
			$this->output("Generating the browser cap cache file.\n");
			$b->getBrowser();
			$this->output("Done!");
		}
		else
			$this->error('Can\'t download browser cap file, since there is no cache directory to download!');
	}

	public function execute($args) {
		if($args) {
			$this->tool->helper('console');
			$method = $args[0];
			if(method_exists($this, $method)) {
				return call_user_func(array($this, $method));
			}
			else {
				$this->output("Can't find command for $method".PHP_EOL);
				return -1;
			}
		}
	}
}
