<?php namespace Clips\Filters; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

use Clips\AbstractFilter;

class ViewFilter extends AbstractFilter {

	protected function analyzeRet($ret) {
		if(is_string($ret)) {
			$this->template = $ret;
			$this->args = array();
		}
		else if(is_object($ret) && get_class($ret) == "Clips\\Models\\ViewModel") {
			$this->template = $ret->template;
			$this->args = $ret->args;
		}
		else if(is_array($ret)) {
			switch(count($ret)) {
			case 0:
				$this->template = 'default';
				$this->args = array();
				break;
			case 1:
				$this->template = $ret[0];
				$this->args = array();
				break;
			case 2:
				$this->template = $ret[0];
				if(is_array($ret[1]))
					$this->args = $ret[1];
				else
					$this->args = array($ret);
				break;
			default:
				$this->template = array_shift($ret);
				$this->args = $ret;
				break;
			}
		}
	}

	public function accept($chain, $controller, $method, $args, $request, $controller_ret = null) {
		// If the controller has returned, we got it
		if(is_object($controller_ret) && get_class($controller_ret)
			== "Clips\\Models\\ViewModel") {

			// If the return value is ViewModel, then check if the engine is the same
			$class = explode('\\', get_class($this));
			$class = $class[count($class) - 1];
			$class = strtolower(str_replace('ViewFilter', '', $class));

			if($controller_ret->engine)
				$accept = strtolower($controller_ret->engine) == $class;
			else
				$accept = true;
		}
		else
			$accept = !!$controller_ret;

		if($accept) {
			$this->analyzeRet($controller_ret);
		}
		return $accept;
	}
}
