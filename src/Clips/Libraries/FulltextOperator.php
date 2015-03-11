<?php namespace Clips\Libraries; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

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
