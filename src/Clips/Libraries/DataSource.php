<?php namespace Clips\Libraries; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

use Clips\Interfaces\ToolAware;

/**
 * The DataSource facade and base class for all the DataSources
 *
 * @author Jack
 * @date Sat Feb 21 12:04:41 2015
 */
class DataSource implements ToolAware {
	public $context;

	public function setTool($tool) {
		$this->tool = $tool;
	}

	/**
	 * If have configuration, will init using configuration
	 */
	public function __construct($config = null) {
		if(isset($config)) {
			$this->config = $config;
			$this->init($config);
		}
	}

	/**
	 * Release the resources when destruct
	 */
	public function __destruct() {
		$this->destroy();
	}

	/**
	 * Iterate all the datasource names, and return names as an array
	 */
	public function datasources() {
		if(isset($this->_datasources))
			return $this->_datasources;

		$ret = array();
		$ds = \Clips\config('datasources');
		if(isset($ds[0])) {
			foreach($ds as $d) {
				foreach($d as $k => $v) {
					$ret []= $k;
				}
			}
		}
		else {
			foreach($ds as $k => $v) {
				$ret []= $k;
			}
		}

		$this->_datasources = $ret;
		return $ret;
	}

	/**
	 * Get the first datasource, normally the base datasource is using database
	 */
	public function first() {
		if($this->datasources() != array('fake')) {
			$ds = \Clips\config('datasources');
			foreach($ds as $d) {
				$default = \Clips\get_default($d, 'default', null);
				if($default) {
					return $this->get($default);
				}
			}
		}
		foreach($this->datasources() as $ds) {
			return $this->get($ds);
		}

		return null;
	}

	/**
	 * Get the datasource by name(name in the configuration)
	 */
	public function get($name) {
		if(isset($this->$name))
			return $this->$name;

		$tool = $this->tool;
		$ds = \Clips\config('datasources');
		$config = \Clips\get_default($ds, $name);
		if(!$config) { // For supporting json configuration
			foreach($ds as $d) {
				$config = \Clips\get_default($d, $name);
				if($config)
					break;
			}
		}
		$type = \Clips\get_default($config, 'type');
		if($type) {
			$tool = $this->tool;
			$type = $tool->load_class($type, false, new \Clips\LoadConfig($tool->config->datasources_dir, "DataSource", "DataSources\\"));
			$this->$name = new $type($config);
			$this->tool->enhance($this->$name);
			return $this->$name;
		}
		else {
			trigger_error('No datasource config for datasource '.$name);
		}
		return null;
	}

	/**
	 * Get id field
	 */
	protected function idField() {
		return \Clips\get_default($this->config, 'id_field', 'id');
	}

	/**
	 * Interface method, let datasource connect to the datasource using this configuration
	 */
	protected function init($config) {
	}

	/**
	 * Interface method, let datasource release the resources
	 */
	protected function destroy() {
	}

	/**
	 * Interface method, let the datasouce do the query
	 */
	protected function doQuery($query, $args = array()) {
	}

	/**
	 * Interface method, let the datasource do the update operation
	 */
	protected function doUpdate($id, $args) {
	}

	/**
	 * Interface method, let the datasource do the delete operation
	 */
	protected function doDelete($id) {
	}

	/**
	 * Interface method, let the datasource do the fetch operation
	 */
	protected function doFetch($args) {
	}

	/**
	 * Interface method, clear the datasource
	 */
	protected function doClear() {
	}

	/**
	 * Interface method, let the datasource do the iterate operation.
	 * The iterate operation is used for big result sets(for example database).
	 * By using the cursor of the large resultset, will save lots of memory and
	 * time to operate on the resultset.
	 */
	protected function doIterate($query, $args, $callback, $context = array()) {
	}

	/**
	 * Interface method, let the datasource do the insert operation
	 */
	protected function doInsert($args) {
	}

	/**
	 * Begin the batch operation, so that all the operation can be commit or rollback at once
	 *
	 * No all datasource impmenent the transaction, so no commit or rollback on the facade interface.
	 *
	 * You can call commit or transaction method on the Database DataSource directly
	 */
	public function beginBatch() {
	}

	/**
	 * End the batch operation
	 */
	public function endBatch() {
	}

	public function load($id) {
		$result = $this->fetch($this->idField(), $id);
		if($result) {
			if(is_array($result))
				return $result[0];
			return $result;
		}
		return null;
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
			throw new \Clips\DataSourceException('The args must be set for the fetch.');
		}
		throw new \Clips\DataSourceException('No context set for this datasource');
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
		throw new \Clips\DataSourceException('No context set for this datasource');
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

	/**
	 * Supports 2 kinds of syntax:
	 *
	 * 1. query($query, $arg1, $arg2, $arg3) // Variable args
	 * 2. query($query, $args) // Array args
	 */
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

	/**
	 * Clear all the data in this datasource
	 */
	public function clear() {
		$this->doClear();
	}
}
