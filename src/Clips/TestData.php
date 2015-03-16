<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

use Addendum\Annotation;
use Clips\Interfaces\Initializable;
use Clips\Interfaces\ToolAware;

class TestData extends Annotation implements Initializable, ToolAware {
	const TPL_NAME = '$template';

	private $_config;
	private $_data;
	private $_storage_ids;
	private $tool;

	public function setTool($tool) {
		$this->tool = $tool;
	}

	public function __get($property) {
		if(isset($this->_data[$property])) {
			return $this->_data[$property];
		}
		return null;
	}

	public function all() {
		return $this->_data;
	}

	public function init() {
		if(!isset($this->value)) {
			$this->value = context('test_data');
		}
		// Auto add fake helper
		$this->tool->helper('fake');
		$this->_data = array();
		$this->_storage_ids = array();
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
		$seq = array();
		// Process templates and sequence
		foreach($objs as $name => $obj) {
			if(isset($obj->{TestData::TPL_NAME})) {
				// This object has a template
				$tpl = $obj->{TestData::TPL_NAME};
				unset($obj->{TestData::TPL_NAME});
				$tpl = $tpls[$tpl];
				$obj = copy_object($tpl, $obj);
			}
			if(str_end_with($name, '*')) {
				// This is sequence
				$seq []= $name;
			}
			$this->_data[$name] = $obj;
		}

		foreach($seq as $s) {
			$name = substr($s, 0, strlen($s) - 1);
			$sequence = $this->_data[$s];
			unset($this->_data[$s]);
			// Default sequence count is 5
			$count = get_default($sequence, '$count', 5);
			if(isset($sequence->{'$count'}))
				unset($sequence->{'$count'});

			if(strpos($count, '$') === 0) {
				$tmp_name = substr($count, 1);
				$count = get_default($this->_data, $tmp_name, $count);
			}
			if(!is_numeric($count)) {
				if(strpos($count, '!') === 0) {
					$count = substr($count, 1);
					$count = eval('return '.$count.';');
				}
				else
					$count = 5;
			}

			$numbers = array();
			foreach($sequence as $k => $v) {
				if(is_string($v) && strpos($v, '@') !== false) {
					$numbers []= $k;
				}
			}
			for($i = 0; $i < $count; $i++) {
				$data = copy_object($sequence);
				foreach($numbers as $n) {
					$data->$n = str_replace('@', $i + 1, $sequence->$n);
				}
				$this->_data[$name.($i + 1)] = $data;
			}
		}

		// Process references, because the template may have the references, so, reiterate the objects after all the template processing is done
		foreach($this->_data as $name => $obj) {
			$this->_data[$name] = $this->processReference($obj);
		}
	}

	protected function processReference($obj) {
		if(is_string($obj)) {
			if(strpos($obj, '$') === 0) {
				$name = substr($obj, 1);
				if(isset($this->_data[$name]))
					return $this->processReference($this->_data[$name]);
			}
			else if(strpos($obj, '!') === 0) {
				$name = substr($obj, 1);
				return eval('return '.$name.';');
			}
		}
		else if(is_array($obj)) {
			foreach($obj as $k => $v) {
				$obj[$k] = $this->processReference($v);
			}
		}
		else if(is_object($obj)) {
			foreach($obj as $k => $v) {
				$obj->$k = $this->processReference($v);
			}

			if(isset($obj->{'$type'})) {
				$obj = copy_object($obj,  null, $obj->{'$type'});
				unset($obj->{'$type'});
			}

			if(isset($obj->{'$storage'}) && $obj->{'$storage'}) {
				// This object needs to be stored
				if(isset($obj->{'$model'})) {
					$model = $this->tool->model($obj->{'$model'});
					unset($obj->{'$model'});
					unset($obj->{'$storage'});
					$id = $model->insert($obj);
					if($id) {
						$obj->id = $id;
						array_unshift($this->_storage_ids, array($model, $id));
					}
				}
			}
		}
		return $obj;
	}

	public function clean() {
		foreach($this->_storage_ids as $s) {
			$model = $s[0];
			var_dump($s[1]);
			$model->delete($s[1]);
		}
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

	public function get($class = '') {
		$ret = array();
		foreach($this->_data as $k => $v) {
			if($class) {
				if(!valid_obj($v, $class))
					continue;
			}
			$ret []= $v;
		}
		return $ret;
	}

	public function config($name = null) {
		$c = $this->getConfig();
		if($name == null) {
			return $c;
		}
		return get_default($c, $name, null);
	}
}
