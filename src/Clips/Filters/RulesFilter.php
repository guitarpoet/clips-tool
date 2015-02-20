<?php namespace Clips\Filters; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

use Clips\AbstractFilter;
use Clips\Interfaces\ClipsAware;
use Addendum\ReflectionAnnotatedClass;

/**
 * This filter will be trigger by the Clips\Rules annotation, and will act like this:
 *
 * 1. Load all the rules in Clips\Rules's annotation configuration
 * 2. Let the controller's method to add facts to the clips engine
 * 3. Run the clips engine
 *
 * This is quite handy for some rule based operations(like login). Will save lots of
 * preparation for the rule engine routine.
 *
 * @author Jack
 * @date Fri Feb 20 21:31:54 2015
 */ 
class RulesFilter extends AbstractFilter implements ClipsAware {

	public function setClips($clips) {
		$this->clips = $clips;
	}

	public function accept($chain, $controller, $method, $args, $request, $controller_ret = null) {
		if(isset($this->method))
			return true;
		$re = new ReflectionAnnotatedClass(get_class($controller));
		$this->method = $re->getMethod($method);
		if($this->method->hasAnnotation('Clips\\Rules')) {
			return true;
		}
		return false;
	}

	public function filter_before($chain, $controller, $method, $args, $request) {
		$rules = $this->method->getAnnotations('Clips\\Rules');
		$rules = $rules[0];

		if($rules->clear)
			$this->clips->clear();

		if($rules->templates) {
			if(is_string($rules->templates))
				$rules->templates = array($rules->templates);

			foreach($rules->templates as $t) {
				$this->clips->template($t);
			}
		}

		// Load the rules
		if($rules->rules) {
			if(is_string($rules->rules))
				$run->value = array($rules->rules);

			if(is_array($rules->rules)) {
				$rules->value = $rules->rules;
			}
		}

		if(isset($rules->value)) {
			if(is_string($rules->value))
				$rules->value = array($rules->value);

			foreach($rules->value as $v) {
				$this->clips->load('rules://'.$v);
			}
		}
	}

	public function filter_after($chain, $controller, $method, $args, $request, $controller_ret) {
		$this->clips->run();
	}
}
