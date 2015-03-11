<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

class WidgetException extends Exception {
	public function __construct($msg = null) {
		parent::__construct($msg);
	}
}
