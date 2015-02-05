<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

/**
 * The form field class
 */
class FormField {
	public $name;
	public $label;
	public $defaultValue;
	public $rules;
	public $placeholder;
	public $translateRules;
	public $field = '';
	public $state;

	public function init() {
		$this->name = $this->field;
		if(!isset($this->state))
			$this->state = 'default';
		if(!isset($this->placeholder))
			$this->placeholder = $this->label;
	}

	public function getId() {
		return "field_".$this->name;
	}
}
