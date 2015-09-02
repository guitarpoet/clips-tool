<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");
use Addendum\Annotation;
use Clips\Interfaces\Initializable;
use Clips\Interfaces\ToolAware;

class DataGenerator extends Annotation implements Initializable, ToolAware {
	public function setTool($tool) {
		$this->tool = $tool;
	}

	public function __get($property) {
		if(isset($this->_data[$property])) {
			return $this->_data[$property];
		}
		return null;
	}

	public function init() {
		if(!isset($this->value)) {
			$this->value = context('test_data');
		}
		// Auto add fake helper
		$this->tool->helper('fake');
		$this->_storage_ids = array();
		$this->load();
	}

	public function load() {
		if(!isset($this->_data)) {
			$this->_data = array();
			if(isset($this->value)) {
				$test_data_dir = config('test_data_dir');
				if(!$test_data_dir) {
					$test_data_dir = config('test_dir');
					$test_data_dir = path_join($test_data_dir[0], 'data');
				}
				else {
					$test_data_dir = $test_data_dir[0];
				}
				if(!is_array($this->value))
					$this->value = array($this->value);

				foreach($this->value as $config) {
					$p = path_join($test_data_dir, $config.'.yml');
					$p = \Clips\try_path($p);
					if($p) {
						$content = file_get_contents($p);
						$args = context('test_args');
						if(!$args) {
							$args = array();
						}
						$this->_data[$config] = parse_yaml(str_template($content, $args));
					}
				}
			}
		}
		return $this->_data;
	}
}
