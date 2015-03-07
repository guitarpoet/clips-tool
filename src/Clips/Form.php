<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

use Clips\Interfaces\Initializable;
use Clips\Interfaces\ToolAware;

/**
 * The form helper class, this is the basic form configuration support class.
 * It can be configured as the docstring in the PHP code, and look for the configuration file
 * of json format. This will be the bridge from the request to the validation rules, and will
 * provide the configuration support for client side validation(like using jQBootStrapValidation).
 * Also, this supports for mutiple form configuration
 *
 * @author Jack
 * @date Mon Feb 23 15:41:27 2015
 */
class Form extends \Addendum\Annotation implements Initializable, ToolAware {

	const FORM_FIELD = '_clips_form';

	public $get = false;
	public $state;

	public function setTool($tool) {
		$this->tool = $tool;
	}

	public function init() {
		$this->tool->widget('Form');
		if(isset($this->value)) {
			if(!is_array($this->value)) {
				$this->value = array($this->value);
			}
		}
		else {
			$this->value = array(default_form_name());
		}
		$this->fieldMap = array();
	}

	public function state($name, $value = null) {
		if($value) {
			if(!isset($this->state)) {
				$this->state = array();
			}
			$this->state[$name] = $value;
			return $value;
		}
		if(isset($this->state)) {
			if(is_array($this->state) && isset($this->state[$name])) {
				return $this->state[$name];
			}
			else
				return $this->state;
		}
		return null;
	}

	public function field($name) {
		$current = clips_context('current_form');
		if($current) {
			// If we can find the current form
			$config = $this->config($current);
			if($config) {
				// If we can find the configuration for this form
				foreach($config as $f) {
					if($f->field == $name) {
						// If has the configuration for the field, let's try to get the field using the field map
						if(!isset($this->fieldMap[$current]))
							$this->fieldMap[$current] = array();

						$map = $this->fieldMap[$current];
						if(isset($map[$name])) // If we can find the field, return it
							return $map[$name];

						// Create the field
						$field = copy_new($f, "Clips\\FormField");
						$field->init();
						$field->form = $current;
						$map[$name] = $field;
						$this->fieldMap[$current] = $map;
						return $field;
					}
				}
			}
		}
		return null;
	}

	protected function getConfig() {
		if(!isset($this->_config)) {
			$this->_config = array();
			// Load the form config
			foreach($this->value as $config) {
				$form_config_dir = clips_config('form_config_dir');
				if($form_config_dir) {
					$form_config_dir = $form_config_dir[0];
					$p = path_join($form_config_dir, $config.'.json');
					if(file_exists($p)) {
						$this->_config[$config] = parse_json(file_get_contents($p));
					}
				}
			}
		}
		return $this->_config;
	}

	/**
	 * Use the first form configuration as the default form
	 */
	public function defaultFormName() {
		if(isset($this->value) && $this->value) {
			if(is_array($this->value))
				return $this->value[0];
			return $this->value;
		}
		return null;
	}

	public function config($name = null) {
		$c = $this->getConfig();
		if($name == null) {
			return $c;
		}
		return get_default($c, $name, null);
	}
}
