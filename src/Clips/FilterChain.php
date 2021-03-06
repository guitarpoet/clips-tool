<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

use Clips\Interfaces\ToolAware;

/**
 * The filter chain
 *
 * @author Jack
 * @date Mon Feb 23 15:40:10 2015
 */
class FilterChain extends AbstractFilter implements ToolAware {
	private $run = false;
	private $filters = array();

	public function setTool($tool) {
		$this->tool = $tool;
	}

	/**
	 * Stop the filter chain running process, this should be used for filters that
	 * wants to stop all the filting
	 */
	public function stop() {
		$this->run = false;
	}

	public function addFilter($filter, $index = -1) {
		if(is_array($filter)) {
			foreach($filter as $f) {
				$this->addFilter($f);
			}
			return true;
		}

		$f = $this->tool->filter($filter);
		if($f) {
			if($index >= 0 && count($this->filters) < $index) {
				$this->filters = array_splice($this->filters, $index, 0, $f);
			}
			else {
				$this->filters []= $f;
			}
			return true;
		}
		return false;
	}

	/**
	 * Call every filter's filter before function in the filter chain, if any of the filter failed,
	 * will stop the filter before processing(this is very useful for form processing and security
	 * checking....)
	 */
	public function filter_before($chain, $controller, $method, $args, $request) {
		$this->run = true;
		foreach($this->filters as $f) {
			if(!$this->run) {
				break;
			}

			if($f->accept($chain, $controller, $method, $args, $request)) {
				$this->succeed = $f->filter_before($chain, $controller, $method, $args, $request);
			}

			if(isset($this->succeed))
				return $this->succeed;
		}
		return true;
	}

	public function filter_after($chain, $controller, $method, $args, $request, $controller_ret) {
		$this->analyzeRet($controller_ret);

		if(isset($this->headers)) {
			foreach($this->headers as $h => $v) {
				header($h.': '. $v);
			}
		}
		$this->run = true;
		foreach($this->filters as $f) {
			if(!$this->run) {
				break;
			}

			if($f->accept($chain, $controller, $method, $args, $request, $controller_ret)) {
				$f->filter_after($chain, $controller, $method, $args, $request, $controller_ret);
			}
		}
	}

	public function filter_render($chain, $controller, $method, $args, $request, $view, $view_context, $controller_ret) {
		$this->run = true;
		foreach($this->filters as $f) {
			if(!$this->run) {
				break;
			}
			$f->filter_render($chain, $controller, $method, $args, $request, $view, $view_context, $controller_ret);
		}
	}
}
