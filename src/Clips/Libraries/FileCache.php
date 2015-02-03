<?php namespace Clips\Libraries; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

use Psr\Log\LoggerAwareInterface;
use Clips\Interfaces\ClipsAware;
use Clips\Interfaces\ToolAware;

class FileCache implements LoggerAwareInterface, ClipsAware, ToolAware {
	public function setLogger(\Psr\Log\LoggerInterface $logger) {
		$this->logger = $logger;
	}

	public function setClips($clips) {
		$this->clips = $clips;
	}

	public function setTool($tool) {
		$this->tool = $tool;
	}

	public function genFileName($nt) {
		return clips_out('string://'.$nt, array(
			'timestamp' => strftime('%Y%m%d%H%M%S')
		), false);
	}

	public function cacheDir() {
		$cache = clips_config('cache');
		if($cache)
			return $cache[0];
		return 'application/cache';
	}

	/**
	 * Save the file into the folder using the name template
	 */
	public function save($nt, $folder, $contents, $override = true) {
		if(!file_exists($folder)) { // If no folder exists, make it
			mkdir($folder, 0777, true);
		}

		$filename = $this->genFileName($nt);
		$file = path_join($folder, $filename);
		if(file_exists($file) && !$override) {
			return false;
		}

		file_put_contents($file, $contents);
		return $file;
	}
}
