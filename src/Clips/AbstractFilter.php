<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

use Clips\Interfaces\Filter;

/**
 * The abstract filter to extend
 *
 * @author Jack
 * @date Mon Feb  2 20:50:49 2015
 */
class AbstractFilter implements Filter {
	public function accept($chain, $controller, $method, $args, $request, $controller_ret = null) {
		return true;
	}

	protected function analyzeRet($ret) {
		if(is_string($ret)) {
			$this->template = $ret;
			$this->args = array();
		}
		else if(is_object($ret)) {
			if(get_class($ret) == "Clips\\Models\\ViewModel") {
				$this->template = $ret->template;
				$this->args = $ret->args;
				$this->headers = $ret->headers;
			}
			if(get_class($ret) == "Clips\\Error") {
				$this->template = 'error/'.$ret->cause;
				$this->args = array('error' => $ret->message);
			}
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


	public function filter_before($chain, $controller, $method, $args, $request) {
	}

	public function filter_after($chain, $controller, $method, $args, $request, $controller_ret) {
	}

	public function filter_render($chain, $controller, $method, $args, $request, $view, $view_context, $controller_ret) {
	}
}
