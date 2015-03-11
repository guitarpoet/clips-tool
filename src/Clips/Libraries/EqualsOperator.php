<?php namespace Clips\Libraries; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

class EqualsOperator extends WhereOperator {
	public function __construct($left, $right, $arg) {
		parent::__construct(array());
		$this->left = $left;
		$this->right = $right;
		$this->arg = $arg;
	}

	public function getArgs() {
		if($this->arg) {
			return $this->right;
		}
		return null;
	}

	public function toString() {
		if($this->arg) {
			if($this->right === null)
				return $this->left.' is null';
			return $this->left.' = ?';
		}

		if($this->right === null)
			return $this->left.' is null';

		return $this->left.' = '.$this->right;
	}
}

