<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

class Error {
	public $cause;
	public $message;

	public function __construct($cause, $message = array()) {
		$this->cause = $cause;
		$this->message = $message;
	}
}
