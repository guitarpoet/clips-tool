<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

/**
 * The form field class
 *
 * @author Jack
 * @date Mon Feb 23 15:42:01 2015
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
		if(!isset($this->placeholder))
			$this->placeholder = $this->label;
	}

	public function getCascadeOptions($value = null) {
		if(isset($this->cascade_model)) { 
			$tool = get_clips_tool();
			$model = $tool->model($this->cascade_model);
			if($model) { // We do have a cascade model
				$cascade_field = get_default($this, 'cascade_field', null);
				if($cascade_field) {
					if($value)
						$cascade_data = $value;
					else
						$cascade_data = get_default(context('current_form_data'), $cascade_field);
					$cascade_method = get_default($this, 'cascasde_method', 'list'.ucfirst($this->field));
					return $model->$cascade_method($cascade_data);
				}
			}
		}
		return null;
	}

	public function getId() {
		return "field_".$this->name;
	}

	public function getDefault($default = array()) {
		$default['id'] = $this->getId();
		$default['name'] = $this->name;
		$default['placeholder'] = $this->placeholder;
		if(isset($this->type)) {
			$default['type'] = $this->type;
		}
		if(isset($this->defaultValue))
			$default['value'] = $this->defaultValue;
		if(isset($this->value))
			$default['value'] = $this->value;
		$default = copy_arr($this->validationRules(), $default);
		return $default;
	}

	public function validationRules() {
		if(isset($this->rules)) {
			$rules = $this->rules;
			if(isset($this->messages)) {
				foreach($this->messages as $k => $v) {
					$rules['data-validation-'.$k.'-message'] = $v;
				}
			}
			return $rules;
		}
		return array();
	}
}
