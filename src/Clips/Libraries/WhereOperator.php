<?php namespace Clips\Libraries; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

class WhereOperator {

	protected function isWhere($v) {
		return \Clips\valid_obj($v, 'Clips\\Libraries\\WhereOperator');
	}

	public function __construct($operators = array(), $arg = true) {
		if(!is_array($operators))
			$operators = array($operators);

		$this->operators = array();

		foreach($operators as $key => $o) {
			if($this->isWhere($o)) {
				// If the value is an operator, then add it
				$this->operators []= $o;
			}
			else {
				// If it is key and value, add it as equals by default
				if(is_array($o)) {
					foreach($o as $k => $v) {
						if($this->isWhere($v))
							$this->operators []= $v;
						else
							$this->operators []= _equals($k, $v, $arg);
					}
				}
				else {
					if($this->isWhere($o))
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
				if($args === null)
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
