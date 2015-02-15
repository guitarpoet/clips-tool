<?php namespace Clips\Libraries; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

define('DEFAULT_COUNT', 15);

use Clips\Libraries\Sql;
use Clips\Interfaces\ToolAware;
use Clips\Interfaces\Initializable;

class DBModel extends Sql implements ToolAware, Initializable {

	public function setTool($tool) {
		$this->tool = $tool;
	}

	public function init() {
		$tool = $this->tool;
		$tool->library('datasource'); // Load the datasource library

		$config = $tool->config;
		$name = get_class($this);

		// Remove the prefixes
		foreach(array_merge(array('_model', 'Models\\', 'Clips\\'), \Clips\clips_config('namespace', array())) as $pre) {
			$name = str_replace($pre, '', $name);
		}

		if(!isset($this->table)) {
			$this->table = strtolower(str_replace('Model', '', $name)).'s'; // If no table is set for this model, just guess for its table
		}

		// Check for models config first
		if($config->models) {
			foreach($config->models as $mc) {
				if(isset($mc->$name)) {
					if(isset($mc->$name->datasource)) {
						$datasource = $mc->$name->datasource;
						break;	
					}
				}
				if(isset($mc->datasource)) { // Let's try the overall configuration
					$datasource = $mc->datasource;
					break;
				}
			}
		}

		if(!isset($datasource)) {
			// There is still no datasource information, let's try using first one of the datasource
			foreach($tool->datasource->datasources() as $ds) {
				$this->db = $tool->datasource->get($ds);
				break;
			}
		}
		else {
			$this->db = $tool->datasource->get($datasource);
		}
		if(isset($this->db)) {
			parent::__construct($this->db->type);
			if(isset($this->db->table_prefix))
				$this->table_prefix = $this->db->table_prefix;
			return;
		}

		throw new Exception('Cant\'t find any datasource for this model.');
	}

	public function one($index = 0) {
		$ret = call_user_func_array(array($this, 'get'), func_get_args());
		if(count($ret) >= $index)
			return $ret[$index];
		return null;
	}

	protected function isWhereOper($arg) {
		return is_array($table) || (is_object($table) && is_subclass_of($table, 'Where_Operator'));
	}

	public function get() {
		switch(func_num_args()) {
		case 0: // No argument is set, let's check if we have table set
			if(isset($this->table))
				return $this->get($this->table, array(), 0, DEFAULT_COUNT);
			return array(); // No way to get
		case 1:
			$table = func_get_arg(0);
			if(is_string($table)) { // It must be the table name
				return $this->get($table, array(), 0, DEFAULT_COUNT);
			}

			if(is_int($table) && isset($this->table)) { // It must be the offset
				return $this->get($this->table, array(), $table, DEFAULT_COUNT);
			}

			if($this->isWhereOper($table) && isset($this->table)) { // It must be the where args
				return $this->get($this->table, $table, 0, DEFAULT_COUNT);
			}

			return array(); // No way to get
		case 2:
			$table = func_get_arg(0);
			if(is_string($table)) {
				if(isset($this->table)) { // It must be name and arg
					return $this->get($this->table, 
						array($table => func_get_arg(1)), 
						0, DEFAULT_COUNT);
				}
				else {
					if($this->isWhereOper(func_get_arg(1))) { // It must be table name and the where args
						return $this->get($table, func_get_arg(1), 0, DEFAULT_COUNT);
					}
					// It should be table and offset
					return $this->get($table, array(), func_get_arg(1), DEFAULT_COUNT);
				}
			}

			if(is_int($table) && isset($this->table)) { // It must be the offset and limit
				return $this->get($this->table, array(), $table, func_get_arg(1));
			}

			if($this->isWhereOper($table) && isset($this->table)) { // It must be the where args and the offset
				return $this->get($this->table, $table, func_get_arg(1), DEFAULT_COUNT);
			}
			break;
		case 3:
			$table = func_get_arg(0);
			if(is_string($table)) { // It must be the table name
				$offset = func_get_arg(1);
				if(is_string($offset)) { // Must be name and arg
					return $this->get($table, array(
						$offset => func_get_arg(2)
					), 0, DEFAULT_COUNT);
				}

				if($this->isWhereOper($offset)) { // It must be table name and the where args with offset
					return $this->get($table, func_get_arg(1), func_get_arg(2), DEFAULT_COUNT);
				}

				if(is_int($offset)) { // It must be table name with offsets
					return $this->get($table, array(), $offset, func_get_arg(2));
				}
			}
			return array(); // No way to get
		case 4:
			$table = func_get_arg(0);
			$db = $this->select('*')->from($table)->where(func_get_arg(1));
			$offset = func_get_arg(2);
			if($offset > 0)
				$db->limit($offset, func_get_arg(3));
			return $db->result();
		}
		return array();
	}

	public function result() {
		$sql = $this->sql();
		switch(count($sql)) {
		case 0:
			throw new \Clips\DataSourceException('Can\'t do the query since no query generated!');
		case 1:
			return $this->db->query($sql[0]);
		default:
			return $this->db->query($sql[0], $sql[1]);
		}
	}
}
