<?php namespace Clips\Libraries; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

require_once(__DIR__.'/sql_helpers.php');

class Sql {

	public function __construct($type = 'mysqli') {
		$tool = &\Clips\get_clips_tool();

		$this->type = $type;
		$this->clips = $tool->clips;
		if(!$this->clips->isEnvExists('SQL')) {
			$this->clips->createEnv('SQL');
		}
		// Register all the template into the clips context
		$this->clips->runWithEnv('SQL', function($clips, $type) {
			$clips->clear();
			$clips->template(array('Clips\\Libraries\\Select', 'Clips\\Libraries\\From', 'Clips\\Libraries\\Join', 'Clips\\Libraries\\Where', 'Clips\\Libraries\\GroupBy', 'Clips\\Libraries\\OrderBy', 'Clips\\Libraries\\Limit', 'Clips\\Libraries\\SqlTable', 'Clips\\Libraries\\SqlResult'));

			if(isset($type)) {
				$type = strtolower($type);
				$clips->load('/config/rules/sql/'.$type.'.rules');
			}
		}, $type);
	}

	public function limit($offset = 0, $count = 15) {
		$this->clips->runWithEnv('SQL', function($clips, $data) {
			$offset = $data[0];
			$count = $data[1];
			$clips->assertFacts('fact_limit', array(new Limit($offset, $count)));
		}, array($offset, $count));
		return $this;
	}

	public function select() {
		$this->clips->runWithEnv('SQL', function($clips, $args) {
			$clips->assertFacts('fact_select', array(new Select($args)));
		}, func_get_args());
		return $this;
	}

	public function from() {
		$this->clips->runWithEnv('SQL', function($clips, $args) {
			$clips->assertFacts('fact_from', array(new From($args)));
		}, func_get_args());
		return $this;
	}
	public function where($where = array()) {
		if(\Clips\valid_obj($where, 'Clips\\Libraries\\WhereOperator')) {
			$this->clips->runWithEnv('SQL', function($clips, $where) {
				$clips->assertFacts('fact_where', array(new Where($where->toString())));
			}, $where);
			$this->args = $where->getArgs();
			return $this;
		}
		if(is_array($where) || is_object($where)) {
			// Using and as default
			return $this->where(_and((array) $where));
		}
		return $this;
	}
	public function join($table, $where = array(), $type = "") {
		if($where instanceof WhereOperator) {
			$this->clips->runWithEnv('SQL', function($clips, $data) {
				$table = $data[0];
				$where = $data[1];
				$type = $data[2];
				$clips->assertFacts('fact_join', array(new Join($table, $where->toString(), " ".$type)));
			}, array($table, $where, $type));
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
		$this->clips->runWithEnv('SQL', function($clips, $fields) {
			$clips->assertFacts('fact_group_by', array(new GroupBy($fields)));
		}, $fields);
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
		$this->clips->runWithEnv('SQL', function($clips, $fields) {
			$clips->assertFacts('fact_order_by', array(new OrderBy($fields)));
		}, $fields);
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

	public function pagination($p) {
		return $this->_pagi($p);
	}

	public function sql() {
		if(isset($this->table_prefix)) // Honor the prefix in sql
			$prefix = array($this->table_prefix);
		else
			$prefix = \Clips\config('table_prefix', null);

		if(isset($prefix)) {
			$this->clips->runWithEnv('SQL', function($clips, $prefix) {
				$clips->assertFacts(array('table-prefix', $prefix[0]));
			}, $prefix);
		}

		$result = $this->clips->runWithEnv('SQL', function($clips, $prefix) {
			$clips->run();
			$clips->assertFacts(array('table-prefix', $prefix[0]));
			$result = $clips->queryFacts('Clips\Libraries\SqlResult');
			$clips->reset();
			return $result;
		}, $prefix);

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
