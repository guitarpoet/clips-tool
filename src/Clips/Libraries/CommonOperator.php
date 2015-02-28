<?php namespace Clips\Libraries; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

class CommonOperator extends WhereOperator {
	public function __construct($left, $right, $operator) {
		parent::__construct(array());
		$this->left = $left;
		$this->right = $right;
		$this->operator = $operator;
	}

	public function getArgs() {
		return $this->right;
	}

	public function toString() {
		return $this->left.' '.$operator.' ?';
	}
}
