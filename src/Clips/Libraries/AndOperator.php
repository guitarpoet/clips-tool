<?php namespace Clips\Libraries; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

class AndOperator extends WhereOperator {
	public function __construct($operators = array(), $arg = true) {
		parent::__construct($operators, $arg);
	}

	public function toString() {
		return '('.implode(' and ', $this->toArray()).')';
	}
}

