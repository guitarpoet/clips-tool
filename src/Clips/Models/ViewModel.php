<?php namespace Clips\Models; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

class ViewModel {
	public function __construct($template, $args, $headers = array(), $engine = null) {
		$this->template = $template;
		$this->args = $args;
		$this->engine = $engine;
	}
}
