<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

use Addendum\Annotation;

class Config {

	/** @Clips\Multi */
	public $files = array();

	private $config;

	public function load() {
		$cache = try_path('config.cache');
		if($cache && file_newer($cache, $this->files)) {
			$cache = unserialize(file_get_contents($cache));
			$this->config = $cache['config'];
			$this->loaded = $cache['loaded'];
		}
		else {
			$arr = array();
			$loaded = array();
			foreach($this->files as $config) {
				$p = realpath($config);
				if(in_array($p, $loaded))
					continue;

				$info = pathinfo($config);
				if($info['extension'] == 'json')
					$c = parse_json(file_get_contents($config));
				else if($info['extension'] == 'yml')
					$c = yaml($config);
				if(isset($c)) {
					$loaded []= $p;
					$arr []= (array) $c;
				}
			}

			$fn = getcwd().'/config.cache';
			if(is_writeable($fn)) { // Test if the cache is writable
				$cache = array('config' => $arr, 'loaded' => $loaded);
				file_put_contents(getcwd().'/config.cache', serialize($cache));
			}
			$this->loaded = $loaded;
			$this->config = $arr;
		}
	}

	public function getLoadConfig() {
		return new LoadConfig(array_merge($this->core_dir, 
			$this->helper_dir, 
			$this->command_dir, 
			$this->model_dir, 
			$this->template_dir,
			$this->library_dir));
	}

	public function addConfig($config) {
		$this->config []= $config;
	}

	public function __get($property) {
		if(isset($this->config)) {
			$ret = array();
			foreach($this->config as $c) {
				if(isset($c[$property])) {
					if(is_array($c[$property])) {
						$ret = array_merge($ret, $c[$property]);
					}
					else
						$ret []= $c[$property];
				}
			}
			return $ret;
		}
		return false;
	}
}
