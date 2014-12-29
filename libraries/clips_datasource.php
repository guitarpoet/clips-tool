<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

class Clips_Datasource {
	public function __construct($config = null) {
		if(isset($config)) {
			$this->config = $config;
			$this->init($config);
		}
	}

	public function get($name) {
		if(isset($this->$name))
			return $this->$name;

		$tool = get_clips_tool();

		$tool = get_clips_tool();
		$ds = clips_config('datasources');
		foreach($ds as $d) {
			if(isset($d->$name)) {
				$config = $d->$name;
				break;
			}
		}

		if(isset($config) && isset($config->type)) {
			$tool = get_clips_tool();
			$type = $tool->library('datasources/'.$config->type, false, '_datasource');
			$this->$name = new $type($config);
			return $this->$name;
		}
		else {
			trigger_error('No datasource config for datasource '.$name);
		}
		return null;
	}

	protected function init($config) {
	}

	protected function doQuery($query, $args = array()) {
	}

	protected function doLoad($id) {
	}

	protected function doUpdate($id, $args) {
	}

	public function doDelete($id) {
	}

	public function beginBatch() {
	}

	public function endBatch() {
	}

	public function load($id) {
		return $this->doLoad($id);
	}

	public function update($id, $args) {
		if(is_array($id)) {
			return $this->doBatch(function($ids){
				foreach($ids as $id) {
					$this->doUpdate($d);
				}
				return true;
			}, $id);
		}

		return $this->doUpdate($id);
	}

	public function doBatch($callback, $context) {
		if(isset($callback) && is_callable($callback)) {
			try {
				$this->beginBatch();
				return $callback($context);	
			}
			finally {
				$this->endBatch();
			}
		}
	}

	public function delete($id) {
		if(is_array($id)) {
			return $this->doBatch(function($ids){
				foreach($ids as $id) {
					$this->doDelete($d);
				}
				return true;
			}, $id);
		}

		return $this->doDelete($id);
	}

	public function query() {
		$c = func_num_args();
		switch($c) {
		case 0:
			trigger_error('No query found!');
			return array();
		case 1:
			$args = func_get_args();
			$query = array_shift($args);
		case 2:
			if(is_array($args[0])) { // If we got only 2 args, maybe just is query and args call
				$args = $args[0];
			}
		default:
			return $this->doQuery($query, $args);
		}
	}
}
