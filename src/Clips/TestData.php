<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

use Addendum\Annotation;
use Clips\Interfaces\Initializable;

class TestData extends Annotation implements Initializable {
	const TPL_NAME = '$template';

	private $_config;
	private $_data;

	public function __get($property) {
		if(isset($this->_data[$property])) {
			return $this->_data[$property];
		}
		return null;
	}

	public function init() {
		$this->_data = array();
		// Read the configurations
		foreach($this->getConfig() as $name => $conf) {
			$tpls = array();
			$objs = array();
			foreach($conf as $k => $v) {
				if(strpos($k, "%") === 0) {
					$tpls[$k] = $v;
				}
				else {
					$objs[$k] = $v;
				}
			}
		}
		$this->processObjs($tpls, $objs);
	}

	protected function processObjs($tpls, $objs) {
		// Process templates
		foreach($objs as $name => $obj) {
			if(isset($obj->{TestData::TPL_NAME})) {
				// This object has a template
				$tpl = $obj->{TestData::TPL_NAME};
				unset($obj->{TestData::TPL_NAME});
				$tpl = $tpls[$tpl];
				$obj = copy_object($tpl, $obj);
				$this->_data[$name] = $obj;
			}
		}

		// Process references, because the template may have the references, so, reiterate the objects after all the template processing is done
		foreach($this->_data as $name => $obj) {
			$this->_data[$name] = $this->processReference($obj);
		}
	}

	protected function processReference($obj) {
		if(is_string($obj) && strpos($obj, '$') === 0) {
			$name = substr($obj, 1);
			if(isset($this->_data[$name]))
				return $this->processReference($this->_data[$name]);
		}
		if(is_array($obj)) {
			foreach($obj as $k => $v) {
				$obj[$k] = $this->processReference($v);
			}
		}
		else if(is_object($obj)) {
			if(isset($obj->{'$type'})) {
				$obj = copy_object($obj,  null, $obj->{'$type'});
				unset($obj->{'$type'});
			}

			foreach($obj as $k => $v) {
				$obj->$k = $this->processReference($v);
			}
		}
		return $obj;
	}

	public function getConfig() {
		if(!isset($this->_config)) {
			$this->_config = array();
			if(isset($this->value)) {
				$test_config_dir = clips_config('test_data_dir');
				if(!$test_config_dir) {
					$test_config_dir = clips_config('test_dir');
					$test_config_dir = path_join($test_config_dir[0], 'data');
				}
				else {
					$test_config_dir = $test_config_dir[0];
				}
				if(!is_array($this->value))
					$this->value = array($this->value);

				foreach($this->value as $config) {
					$p = path_join($test_config_dir, $config.'.json');
					if(\file_exists($p)) {
						$this->_config[$config] = parse_json(file_get_contents($p));
					}
				}
			}
		}
		return $this->_config;
	}

	public function config($name = null) {
		$c = $this->getConfig();
		if($name == null) {
			return $c;
		}
		return get_default($c, $name, null);
	}
}