<?php namespace Clips\Libraries; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

require_once(__DIR__.'/sql_helpers.php');

class Sql {
	public function __construct($type = 'mysqli') {
		$tool = &\Clips\get_clips_tool();

		$this->clips = $tool->clips;
		// Register all the template into the clips context
		$this->clips->clear();
		$this->clips->template(array('Clips\\Libraries\\Select', 'Clips\\Libraries\\From', 'Clips\\Libraries\\Join', 'Clips\\Libraries\\Where', 'Clips\\Libraries\\GroupBy', 'Clips\\Libraries\\OrderBy', 'Clips\\Libraries\\Limit', 'Clips\\Libraries\\SqlTable', 'Clips\\Libraries\\SqlResult'));

		if(isset($type)) {
			$type = strtolower($type);
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
		if($where instanceof WhereOperator) {
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
		$this->clips->assertFacts('fact_order_by', array(new OrderBy($fields)));
		return $this;
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
				$this->where((array) $p->where);
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

	public function count($p) {
		if(isset($p->groupBy) && $p->groupBy) {
			$q = $this->_pagi($p);
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

	public function pagination($p) {
		return $this->_pagi($p);
	}

	public function sql() {
		if(isset($this->table_prefix)) // Honor the prefix in sql
			$prefix = array($this->table_prefix);
		else
			$prefix = \Clips\clips_config('table_prefix', null);

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
