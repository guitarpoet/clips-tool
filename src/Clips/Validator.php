<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

class Validator {

	public function __construct() {
		$this->tool = &get_clips_tool();
		$this->clips = $this->tool->clips;
		// Using a new environment to do the validation
		$this->clips->createEnv('VALIDATION');
	}

	/**
	 * Validate the array according to the rules
	 */
	public function validate($arr, $config) {
		$facts = array();

		// Add the fields
		foreach($arr as $k => $v) {
			$facts []= array('field', $k, $v);
		}

		foreach($config as $c) {
			if(isset($c->field)) {
				$field = $c->field;
				if(isset($c->rules)) {
					// Add the rules
					foreach($c->rules as $k => $v) {
						if(is_string($k)) {
							$facts []= array('rule', $field, $k, $v);
						}
						else {
							if(is_object($v) || is_array($v)) {
								foreach($v as $key => $value) {
									if(is_string($key)) {
										$facts []= array('rule', $field, $key, $value);
									}
									else {
										$facts []= array('rule', $field, $value);
									}
								}
							}
							else
								$facts []= array('rule', $field, $v);
						}
					}
				}
				if(isset($c->messages)) {
					// Add the messages
					foreach($c->messages as $k => $v) {
						$facts []= array('message', $field, $k, $v);
					}
				}
			}
		}

		return call_user_func_array(array($this, 'valid_rules'), $facts);
	}

	public function __call($method, $args) {
		$rule = str_replace("valid_", "", $method);

		return $this->clips->runWithEnv('VALIDATION', function($clips, $context) {
			// Clear the env first
			$clips->clear();

			$clips->load('/rules/validation/'.$context['rule'].'.rules');

			if($context['args']) {
				$clips->assertFacts($context['args']);
			}

			$clips->run();

			return $clips->queryFacts('error');
		}, array('rule' => $rule, 'args' => $args));
	}
}
