<?php namespace Clips\Libraries; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

class NotOperator extends WhereOperator {
	public function __construct($operators = array()) {
		parent::__construct($operators);
	}

	public function toString() {
		return 'not '.implode('', $this->toArray());
	}
}

