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

class WhereOperator {
	public function __construct($operators = array(), $arg = true) {
		if(!is_array($operators))
			$operators = array($operators);

		$this->operators = array();

		foreach($operators as $key => $o) {
			if(is_subclass_of($o, "WhereOperator")) {
				// If the value is an operator, then add it
				$this->operators []= $o;
			}
			else {
				// If it is key and value, add it as equals by default
				if(is_array($o)) {
					foreach($o as $k => $v) {
						if(is_subclass_of($v, "WhereOperator"))
							$this->operators []= $v;
						else
							$this->operators []= _equals($k, $v, $arg);
					}
				}
				else {
					if(is_subclass_of($o, "WhereOperator"))
						$this->operators []= $o;
					else
						$this->operators []= _equals($key, $o, $arg);
				}
			}
		}
	}
	public function getArgs() {
		if(count($this->operators)) {
			$ret = array();
			foreach($this->operators as $o) {
				$args = $o->getArgs();
				if(!$args)
					continue;
				if(is_array($args))
					$ret = array_merge($ret, $args);
				else
					$ret []= $args;
			}
			return $ret;
		}
		return null;
	}

	public function toArray() {
		return array_map(function($data){ return $data->toString();}, $this->operators);
	}
}

class AndOperator extends WhereOperator {
	public function __construct($operators = array(), $arg = true) {
		parent::__construct($operators, $arg);
	}

	public function toString() {
		return '('.implode(' and ', $this->toArray()).')';
	}
}

class OrOperator extends WhereOperator {
	public function __construct($operators = array(), $arg = true) {
		parent::__construct($operators, $arg);
	}

	public function toString() {
		return '('.implode(' or ', $this->toArray()).')';
	}
}

class NotOperator extends WhereOperator {
	public function __construct($operators = array()) {
		parent::__construct($operators);
	}

	public function toString() {
		return implode('not', $this->toArray());
	}
}

class LikeOperator extends WhereOperator {
	public function __construct($left, $right) {
		parent::__construct(array());
		$this->left = $left;
		$this->right = $right;
	}

	public function getArgs() {
		return $this->right;
	}

	public function toString() {
		return $left.' like ?';
	}
}

class EqualsOperator extends WhereOperator {
	public function __construct($left, $right, $arg) {
		parent::__construct(array());
		$this->left = $left;
		$this->right = $right;
		$this->arg = $arg;
	}

	public function getArgs() {
		if($this->arg)
			return $this->right;
		return null;
	}

	public function toString() {
		if($this->arg)
			return $this->left.' = ?';
		return $this->left.' = '.$this->right;
	}
}

class FulltextOperator extends WhereOperator {
	public function __construct($field, $keyword, $boolean = false) {
		parent::__construct(array());
		$this->keyword = $keyword;
		$this->field = $field;
		$this->boolean = $boolean;
	}

	public function getArgs() {
		return $this->keyword;
	}

	public function toString() {
		if($this->boolean)
			return 'match('.$this->field.') against (? in boolean mode)';

		return 'match('.$this->field.') against (?)';
	}
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
	public $orders;
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

class Sql {
	public function __construct($type = 'mysqli') {
		$tool = &get_clips_tool();

		$this->clips = $tool->clips;
		// Register all the template into the clips context
		$this->clips->clear();
		$this->clips->template(array('Clips\\Libraries\\Select', 'Clips\\Libraries\\From', 'Clips\\Libraries\\Join', 'Clips\\Libraries\\Where', 'Clips\\Libraries\\GroupBy', 'Clips\\Libraries\\OrderBy', 'Clips\\Libraries\\Limit', 'Clips\\Libraries\\SqlTable', 'Clips\\Libraries\\SqlResult'));

		if(isset($type)) {
			$this->type = $type;
			$this->clips->load('/config/rules/sql/'.$type.'.rules');
		}
	}

	public function limit($offset = 0, $count = 15) {
		$this->clips->assertFacts('fact_limit', array(new Limit($offset, $count)));
		return $this;
	}

	public function select() {
		$this->clips->assertFacts('fact_select', array(new Select(func_get_args())));
		return $this;
	}

	public function from() {
		$this->clips->assertFacts('fact_from', array(new From(func_get_args())));
		return $this;
	}
	public function where($where = array()) {
		if($where instanceof WhereOperator) {
			$this->clips->assertFacts('fact_where', array(new Where($where->toString())));
			$this->args = $where->getArgs();
			return $this;
		}
		if(is_array($where)) {
			// Using and as default
			return $this->where(_and($where));
		}
		return $this;
	}
	public function join($table, $where = array(), $type = null) {
		if($where instanceof Where_Operator) {
			$this->clips->assertFacts('fact_join', array(new Join($table, $where->toString(), $type)));
		}
		if(is_array($where)) {
			// Using and as default
			$this->join($table, _jand($where), $type);
			return $this;
		}
		return $this;
	}

	public function groupBy($fields) {
		if(func_num_args() > 1) {
			$fields = func_get_args();
		}
		else {
			if(!is_array($fields)) {
				$fields = array($fields);
			}
		}
		$this->clips->assertFacts('fact_group_by', array(new GroupBy($fields)));
		return $this;
	}

	public function orderBy($fields) {
		if(func_num_args() > 1) {
			$fields = func_get_args();
		}
		else {
			if(!is_array($fields)) {
				$fields = array($fields);
			}
		}
		$this->clips->assertFacts('fact_order_by', array(new GroupBy($fields)));
		return $this;
	}

	public function sql() {
		if(isset($this->table_prefix)) // Honor the prefix in sql
			$prefix = array($this->table_prefix);
		else
			$prefix = clips_config('table_prefix', null);

		if(isset($prefix)) {
			$this->clips->assertFacts(array('table-prefix', $prefix[0]));
		}

		$this->clips->run();
		$result = $this->clips->queryFacts('Clips\Libraries\SqlResult');

		$this->clips->reset(); // Reset the assertions
		if($result) {
			if(isset($this->args)) {
				$args = $this->args;
				$this->args = null;
				return array($result[0]->sql, $args);
			}
			return
				array($result[0]->sql);
		}
		return null;
	}
}