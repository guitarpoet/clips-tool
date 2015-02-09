<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

use Clips\Interfaces\ToolAware;

class FilterChain extends AbstractFilter implements ToolAware {
	private $run = false;
	private $filters = array();

	public function setTool($tool) {
		$this->tool = $tool;
	}

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

	public function filter_before($chain, $controller, $method, $args, $request) {
		$this->run = true;
		foreach($this->filters as $f) {
			if(!$this->run) {
				break;
			}

			if($f->accept($chain, $controller, $method, $args, $request)) {
				$f->filter_before($chain, $controller, $method, $args, $request);
			}
		}
		if(isset($this->prevent))
			return $this->prevent;
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
