<?php namespace Clips\Filters; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

use Clips\AbstractFilter;

class ViewFilter extends AbstractFilter {

	public function filter_after($chain, $controller, $method, $args, $request, $controller_ret) {
		$chain->filter_render($chain, $controller, $method, $args, $request, $this->template, $this->args, $controller_ret);

		$this->doRender($controller, $method, $args, $request, $this->template, $this->args, $controller_ret);
	}

	protected function doRender($controller, $method, $args, $request, $template, $data, $controller_ret) {
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

		if($accept)
			$this->analyzeRet($controller_ret);

		return $accept;
	}
}
