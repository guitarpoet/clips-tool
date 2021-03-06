<?php namespace Clips\Libraries; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

class OrOperator extends WhereOperator {
	public function __construct($operators = array(), $arg = true) {
		parent::__construct($operators, $arg);
	}

	public function toString() {
		return '('.implode(' or ', $this->toArray()).')';
	}
}

