<?php namespace Clips\Libraries; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

define('DEFAULT_COUNT', 15);

use Clips\Libraries\Sql;
use Clips\Interfaces\ToolAware;
use Clips\Interfaces\Initializable;

/**
 * The model that supports sql query.
 *
 * This model needs 3 configurations:
 *
 * 1. Table: The table name that this model host (can be guessed)
 * 2. DataSource: The datasource to run the querys (can be guessed)
 * 3. TablePrefix: The prefix of the table (default is empty string)
 *
 * This model will read the configuration to find the datasource it should use.
 * If there is no configuration for this model, will try the base datasource(first datasource) as the datasource.
 * Then will using the datasource's type initialize sql support(only support mysql for this version).
 *
 * The default table for this model can be set in the configuration, and if there is no table config, will guess the table name like this:
 * UserModel => users
 *
 * @author Jack
 * @date Sat Feb 21 12:14:53 2015
 */
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
			$this->db = $tool->datasource->first();
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

	/**
	 * Get the first one result in get
	 */
	public function one() {
		$ret = call_user_func_array(array($this, 'get'), func_get_args());
		if($ret)
			return $ret[0];
		return null;
	}

	protected function isWhereOper($arg) {
		return is_array($table) || (is_object($table) && is_subclass_of($table, 'Where_Operator'));
	}

	/**
	 * Insert value into table(can be other tables), this function can be override by subclass
	 */
	protected function doInsert($table, $obj) {
		$orig = $this->db->context;
		$this->db->context = $table;
		$ret = false;
		try{
			$ret = $this->db->insert($obj);
		}
		catch(Exception $e) {
		}
		$this->db->context = $orig;
		return $ret;
	}

	protected function doUpdate($table, $obj) {
		if(isset($obj->id)) {
			$orig = $this->db->context;
			$this->db->context = $table;
			$ret = false;
			try{
				$ret = $this->db->update($obj->id, $obj);
			}
			catch(Exception $e) {
			}
			$this->db->context = $orig;
			return $ret;
		}
	}

	protected function doDelete($table, $ids) {
		$orig = $this->db->context;
		$this->db->context = $table;
		$ret = false;
		try{
			if(!is_array($ids))
				$ids = array($ids);

			$ret = $this->db->delete($ids);
		}
		catch(Exception $e) {
		}
		$this->db->context = $orig;
		return $ret;
	}

	protected function doLoad($table, $id) {
		$orig = $this->db->context;
		$this->db->context = $table;
		$ret = false;
		try{
			$ret = $this->db->load($id);
		}
		catch(Exception $e) {
		}
		$this->db->context = $orig;
		return $ret;
	}

	/**
	 * Load the result using id
	 *
	 * @param table or id
	 * 		If this is id, just load the result using the table of this model or using this parameter as table
	 * @param id or null
	 * 		IF the first argument is table, this must be the id
	 * @return result or null
	 */
	public function load($table, $id = 0) {
		if(is_numeric($table)) { // Table is id
			if(isset($this->table)) {
				return $this->doLoad($this->table, $table);
			}
		}
		else {
			return $this->doLoad($table, $id);
		}
		return false;
	}

	/**
	 * Insert the data into table
	 *
	 * @param table or data
	 * 		If this is data, will insert the data into this model's table
	 * @param data or empty
	 * 		If the first argument is table, will insert this data into that table
	 */
	public function insert($table, $obj = array()) {
		if(is_array($table) || is_object($table)) {
			if(isset($this->table)) {
				return $this->doInsert($this->table, $table);
			}
		}
		else {
			return $this->doInsert($table, $obj);
		}
		return false;
	}

	/**
	 * Same as insert
	 */
	public function update($table, $obj = array()) {
		if(is_array($table) || is_object($table)) {
			if(isset($this->table)) {
				return $this->doUpdate($this->table, $table);
			}
		}
		else {
			return $this->doUpdate($table, $obj);
		}
		return false;
	}

	/**
	 * Same as insert
	 */
	public function delete($table, $ids = array()) {
		if(is_array($table) || is_numeric($table)) {
			if(isset($this->table)) {
				return $this->doDelete($this->table, $table);
			}
		}
		else {
			return $this->doDelete($table, $ids);
		}
		return false;
	}

	/**
	 * Support syntax like this:
	 *
	 * 1. get() => Get all the data in model's table (pagination by 0)
	 * 2. get($table) => Get all the data in table (pagination by 0)
	 * 3. get($offset) => Get all the data in table(pagination by offset)
	 * 4. get(WhereOperator $where) => Get all the data in table match where operation(pagination by 0)
	 * 5. get($field, $value); => Get all the data where $field = $value(pagination by 0)
	 * 6. get(WhereOperator $where, $offset); => Get all the data match where operation (pagination by $offset)
	 * 7. get($table, $offset); => Get all the data in $table (pagination by $offset)
	 * 8. get($offset, $count); => Get all the data in table (pagination by $offset, count by $count)
	 * 9. get($table, $field, $value); => Get all the data where $field = $value in $table (pagination by 0)
	 * 10. get($table, WhereOperator $where, $offset); => Get all the data match where operation in $table (pagination by $offset)
	 * 11. get(WhereOperator $where, $offset, $count); => Get all the data match where operation in table (pagination by $offset, count by $count)
	 * 12. get($table, $where, $offset, $count); => Get all the data match where operation in $table (pagination by $offset, count by $count)
	 */
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
