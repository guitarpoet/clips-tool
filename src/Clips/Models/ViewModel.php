<?php namespace Clips\Models; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

class ViewModel {
	public function __construct($template, $args, $engine = null, $headers = array()) {
		$this->template = $template;
		$this->headers = $headers;
		$this->args = $args;
		$this->engine = $engine;
	}
}
