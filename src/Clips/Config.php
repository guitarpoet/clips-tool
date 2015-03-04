<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

use Addendum\Annotation;

class Config extends Annotation {

	/** @Clips\Multi */
	public $files = array();

	private $config;

	public function load() {
		$arr = array();
		$loaded = array();
		foreach($this->files as $config) {
			$p = realpath($config);
			if(in_array($p, $loaded))
				continue;
			$c = parse_json(file_get_contents($config));
			if(isset($c)) {
				$loaded []= $p;
				$arr []= (array) $c;
			}
		}
		$this->config = $arr;
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
