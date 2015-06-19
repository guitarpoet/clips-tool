<?php namespace Clips\Libraries; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

use Clips\BaseService;

/**
 * This is the newer version of DBModel.
 *
 * This Model trying its best to support the old DBModel's method, and extending the older DBModel with
 * these functions:
 *
 * 1. You can now use complex query string to act as select list or where
 * 2. You can now just use a SQL query (MySQL dialect) to do the query
 *
 * The table prefix and dialect translation is done by ClipsDataSource other than DBModel itself
 *
 * @author Jack
 * @version 1.1
 * @date Tue Jun 16 13:06:15 2015
 *
 */
class DBModelV2 extends BaseService {

	public function __construct() {
		$this->where = array();
		$this->args = array();
	}

	/**
	 * Clean the object(or array) using the fields table has(removing all the needless fields)
	 *
	 * @param table
	 * 		The table meta to use
	 * @param obj
	 * 		The array or object to clean
	 */
	public function cleanFields($table, $obj) {
		$fields = $this->listFields($table);
		$ret = array();
		foreach($obj as $k => $v) {
			if(array_search($k, $fields) !== false)
				$ret[$k] = $v;
		}
		return is_array($obj)? $ret: (object) $ret;
	}

	/**
	 * List all the field names in table
	 */
	public function listFields($table) {
		return array_map(function($i){return $i->COLUMN_NAME;}, $this->get('INFORMATION_SCHEMA.COLUMNS as COLUMNS', 'TABLE_NAME', $table));
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

	protected function doClear($table) {
		$orig = $this->db->context;
		$this->db->context = $table;
		$ret = false;
		try{
			$this->db->clear();
			$ret = true;
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
	 * Clear the model
	 */
	public function clear($table = null) {
		if($table == null) {
			if(isset($this->table)) {
				return $this->doClear($this->table);
			}
		}
		else {
			return $this->doClear($table);
		}
	}

	public function sql() {
	}

	public function select() {
		$args = func_get_args();
		if($args) {
			if(is_array($args[0])) { // The select is using array of fields
				$this->select = implode(',', $args[0]);
			}
			else {
				$this->select = implode(',', $args);
			}
		}
		return $this;
	}

	public function from() {
		$args = func_get_args();
		if($args) {
			if(is_array($args[0])) { // The select is using array of tables
				$this->from = implode(',', $args[0]);
			}
			else {
				$this->from = implode(',', $args);
			}
		}
		return $this;
	}

	public function where($args = null) {
		return new WhereBuilder($this, $args);
	}

	public function join($table, $where = array(), $type = "") {
	}

	public function limit($offset = 0, $count = 15) {
	}

	public function groupBy($fields) {
	}

	public function orderBy($fields) {
	}

	public function result() {
	}

	protected function _pagi($p, $count = false) {
		if(\Clips\valid_obj($p, 'Clips\\Pagination')) {
			// For fields
			if($count) {
				$this->select(array_merge(array('count(*) as count'), $p->fields()));
			}
			else {
				$this->select($p->fields());
			}

			// Check for the table first
			if(isset($p->from) && $p->from) {
				$this->from($p->from);
			}
			else {
				\Clips\clips_error("No table to select from!");
				return false;
			}

			// For where
			if(isset($p->where) && $p->where) {
				$this->where($p->where);
			}

			// For joins
			if(isset($p->join) && $p->join) {
				if(is_array($p->join)) {
					if(!is_array($p->join[0])) {
						$p->join = array($p->join);
					}
				}
				foreach($p->join as $j) {
					switch(count($j)) {
					case 0:
					case 1:
						\Clips\clips_error("Too few arguments for join!", array($j));
						break;
					case 2:
						$this->join($j[0], (array)$j[1]);
						break;
					default:
						$this->join($j[0], (array)$j[1], $j[2]);
						break;
					}
				}
			}

			// For group bys
			if(isset($p->groupBy) && $p->groupBy) {
				$this->groupBy($p->groupBy);
			}

			// For order bys
			if(isset($p->orderBy) && $p->orderBy) {
				$this->orderBy($p->orderBy);
			}

			// For limits
			if(!$count)
				$this->limit($p->offset, $p->length);

			return $this->sql();
		}
		return false;
	}

	public function pagination($p) {
		return $this->_pagi($p);
	}

	public function count($p) {
		if(isset($p->groupBy) && $p->groupBy) {
			$q = $this->_pagi($p, true);
            if(is_string($q))
			    $query = 'select count(*) as count from ('.$q.') as inner_query';
			else {
				$query = 'select count(*) as count from ('.$q[0].') as inner_query';
            }

			if(count($q) == 1) {
				return array($query);
			}
			else
				return array($query, $q[1]);
		}	
		return $this->_pagi($p, true);
	}
}
