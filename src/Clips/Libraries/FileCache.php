<?php namespace Clips\Libraries; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Clips\Interfaces\ClipsAware;
use Clips\Interfaces\ToolAware;

/**
 * The file cache support
 *
 * @author Jack
 * @date Sat Feb 21 12:39:32 2015
 */
class FileCache implements LoggerAwareInterface, ClipsAware, ToolAware {
	public function setLogger(LoggerInterface $logger) {
		$this->logger = $logger;
	}

	public function setClips($clips) {
		$this->clips = $clips;
	}

	public function setTool($tool) {
		$this->tool = $tool;
	}

	/**
	 * Generate the temp file name using template.
	 * Only support mustache template now, and only support {{timestamp}} tag.
	 *
	 * usage:
	 * 
	 * <code>
	 * genFileName('demo-log-{{timestamp}}.log');
	 * </code>
	 *
	 * @return The filename
	 */
	public function genFileName($nt) {
		return \Clips\clips_out('string://'.$nt, array(
			'timestamp' => strftime('%Y%m%d%H%M%S')
		), false);
	}

	public function shouldUpdate($nt, $time = -1, $folder = null) {
		if(!$folder)
			$folder = $this->cacheDir();

		if(is_string($time) && file_exists($time)) {
			$time = filectime($time);
		}

		if($time < 0) 
			$time = \time();

		if($this->exists($nt, $folder)) {
			$file = $this->fileName($nt, $folder);
			return \filectime($file) < $time;
		}
		return true;
	}

	/**
	 * Test if the file is exists.
	 */
	public function exists($nt, $folder = null) {
		if(!$folder)
			$folder = $this->cacheDir();
		return \file_exists($this->fileName($nt, $folder));
	}

	/**
	 * Get the filename using the filename pattern
	 */
	public function fileName($nt, $folder = null) {
		if(!$folder)
			$folder = $this->cacheDir();
		$filename = $this->genFileName($nt);
		return \Clips\path_join($folder, $filename);
	}

	public function mkdir($folder) {
		$cache = $this->cacheDir();
		$f = \Clips\path_join($cache, $folder);
		if(!file_exists($f)) { // If no folder exists, make it
			mkdir($f, 0777, true);
		}
		return \Clips\try_path($f);
	}

	/**
	 * Get the configured cache directory
	 */
	public function cacheDir() {
		$cache = \Clips\config('cache');
		if($cache)
			return $cache[0];
		return 'application/cache';
	}

	public function contents($nt, $folder = null) {
		if(!$folder)
			$folder = $this->cacheDir();
		if($this->exists($nt, $folder)) {
			return file_get_contents($this->fileName($nt, $folder));
		}
	}

	/**
	 * Save the file into the folder using the name template
	 */
	public function save($nt, $contents, $folder = null, $override = true) {
		if(!$folder) {
			$folder = $this->cacheDir();
		}
		if(!file_exists($folder)) { // If no folder exists, make it
			mkdir($folder, 0777, true);
		}
		$file = $this->fileName($nt, $folder);
		if(file_exists($file) && !$override) {
			return false;
		}

		file_put_contents($file, $contents);
		return $file;
	}
}
