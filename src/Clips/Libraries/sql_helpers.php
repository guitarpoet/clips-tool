<?php namespace Clips\Libraries; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

class SqlCommand {
}

/**
 * The abstraction class for sql tables
 */
class SqlTable {
	/**
	 * The name of the table
	 */
	public $name;

	/**
	 * The alias of the table
	 */
	public $alias;

	/**
	 * The type for the table(only support from and join yet)
	 */
	public $type;

	/**
	 * The join type of this table for join
	 */
	public $join_type;

	/**
	 * The join condition for this table
	 */
	public $join_condition;
}

function _and() {
	return new AndOperator(func_get_args());
}

function _jand() {
	return new AndOperator(func_get_args(), false);
}

function _or() {
	return new OrOperator(func_get_args());
}

function _not($operator) {
	return new NotOperator($operator);
}

function _like($left, $right) {
	return new LikeOperator($left, $right);
}

function _equals($left, $right, $arg = true) {
	return new EqualsOperator($left, $right, $arg);
}

function _text($fields, $text, $boolean = false) {
	return new FulltextOperator($fields, $text, $boolean);
}

class Select extends SqlCommand {
	public $fields;
	public function __construct($fields = array('*')) {
		if(func_num_args()) {
			$fields = func_get_args();
		}
		if(is_array($fields))
			$this->fields = $fields;
		else
			$this->fields = array($fields);
	}
}

class Limit extends SqlCommand {
	public $offset;
	public $count;

	public function __construct($offset = 0, $count = 15) {
		$this->offset = $offset;
		$this->count = $count;
	}
}

class From extends SqlCommand {
	public $tables;

	public function __construct($tables = array()) {
		if(func_num_args()) {
			$tables = func_get_args();
		}
		if(is_array($tables))
			$this->tables = $tables;
		else
			$this->tables = array($tables);
	}
}

class Join extends SqlCommand {
	public $table;
	public $type;
	public $text;
	public function __construct($table, $text, $type) {
		$this->table = $table;
		$this->text = $text;
		$this->type = $type;
	}
}

class Where extends SqlCommand {
	public $text;

	public function __construct($text) {
		$this->text = $text;
	}
}

class GroupBy extends SqlCommand {
	public $fields;
	public function __construct($fields = array()) {
		if(func_num_args()) {
			$fields = func_get_args();
		}
		if(is_array($fields)) {
			$this->fields = $fields;
		}
		else
			$this->fields = array($fields);
	}
}

class OrderBy extends SqlCommand {
	public $fields;
	public function __construct($fields = array()) {
		if(is_array($fields)) {
			$this->fields = $fields;
		}
		else
			$this->fields = array($fields);
	}
}

class SqlResult {
	public $sql;
}
