<?php namespace Clips\Libraries; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

use Clips\Interfaces\ToolAware;

class DataSource implements ToolAware {
	public $context;

	public function setTool($tool) {
		$this->tool = $tool;
	}

	public function __construct($config = null) {
		if(isset($config)) {
			$this->config = $config;
			$this->init($config);
		}
	}

	public function __destruct() {
		$this->destroy();
	}

	public function datasources() {
		if(isset($this->_datasources))
			return $this->_datasources;

		$ret = array();
		foreach(\Clips\clips_config('datasources') as $d) {
			foreach($d as $k => $v) {
				$ret []= $k;
			}
		}

		$this->_datasources = $ret;
		return $ret;
	}

	public function get($name) {
		if(isset($this->$name))
			return $this->$name;

		$tool = $this->tool;
		$ds = \Clips\clips_config('datasources');
		foreach($ds as $d) {
			if(isset($d->$name)) {
				$config = $d->$name;
				break;
			}
		}

		if(isset($config) && isset($config->type)) {
			$tool = $this->tool;
			$type = $tool->library($config->type, false, 'DataSource');
			$type = $tool->load_class($config->type, false, new \Clips\LoadConfig($tool->config->datasources_dir, "DataSource", "DataSources\\"));
			$this->$name = new $type($config);
			return $this->$name;
		}
		else {
			trigger_error('No datasource config for datasource '.$name);
		}
		return null;
	}

	protected function idField() {
		return get_default($this->config, 'id_field', 'id');
	}

	protected function init($config) {
	}

	protected function destroy() {
	}

	protected function doQuery($query, $args = array()) {
	}

	protected function doUpdate($id, $args) {
	}

	protected function doDelete($id) {
	}

	protected function doFetch($args) {
	}

	protected function doIterate($query, $args, $callback, $context = array()) {
	}

	protected function doInsert($args) {
	}

	public function beginBatch() {
	}

	public function endBatch() {
	}

	public function load($id) {
		$result = $this->fetch($this->idField(), $id);
		if($result && is_array($result))
			return $result[0];
		return $result;
	}

	/**
	 * Fetch the result using args, supporting query like this:
	 *
	 * 1. field, value: Querying the entity using one field
	 * 2. Array: Querying the entity using the fields (All by and)
	 * 3. Where_Operator: Only support the sql this version, you can using the syntax of sql generator
	 */
	public function fetch() {
		if(isset($this->context)) {
			if(func_num_args() == 1) {
				$args = func_get_args();
				return $this->doFetch($args[0]);
			}
			if(func_num_args() == 2) {
				$args = func_get_args();
				return $this->doFetch(array($args[0] => $args[1]));
			}
			throw new Exception('The args must be set for the fetch.');
		}
		throw new Exception('No context set for this datasource');
	}

	public function iterate($query, $callback, $args = array(), $context = array()) {
		return $this->doIterate($query, $args, $callback, $context);
	}

	public function update($id, $args) {
		return $this->doUpdate($id, $args);
	}

	public function doBatch($callback, $context) {
		if(isset($callback) && is_callable($callback)) {
			$this->beginBatch();
			try {
				$callback($context);	
			}
			catch(Exception $ex) {
			}
			$this->endBatch();
		}
	}

	public function insert($args) {
		if(isset($this->context)) {
			if(is_array($args) && isset($args[0])) {
				return $this->doBatch(function($args){
					foreach($args as $a) {
						$this->doInsert($a);
					}
					return true;
				}, $args);
			}
			return $this->doInsert($args);
		}
		throw new Exception('No context set for this datasource');
	}

	public function delete($id) {
		if(is_array($id)) {
			return $this->doBatch(function($ids){
				foreach($ids as $i) {
					$this->doDelete($i);
				}
				return true;
			}, $id);
		}

		return $this->doDelete($id);
	}

	public function query() {
		$c = func_num_args();
		if($c) {
			$args = func_get_args();
			$query = array_shift($args);
			if($args && is_array($args[0])) { // If we got only 2 args, maybe just is query and args call
				$args = $args[0];
			}
			return $this->doQuery($query, $args);
		}
		else {
			trigger_error('No query found!');
			return array();
		}
	}
}
