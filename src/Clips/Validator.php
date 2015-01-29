<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

class Validator {

	public function __construct() {
		$this->tool = &get_clips_tool();
		$this->clips = $this->tool->clips;
		// Using a new environment to do the validation
		$this->clips->createEnv('VALIDATION');
	}

	public function __call($method, $args) {
		$rule = str_replace("valid_", "", $method);

		return !$this->clips->runWithEnv('VALIDATION', function($clips, $context) {
			// Clear the env first
			$clips->clear();

			$clips->load('/rules/validation/'.$context['rule'].'.rules');

			if($context['args'])
				$clips->assertFacts($context['args']);

			$clips->run();

			return count($clips->queryFacts('error'));
		}, array('rule' => $rule, 'args' => $args));
	}
}
