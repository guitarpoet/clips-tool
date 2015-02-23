<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

/**
 * The overall error wrapper for all the errors. This object will locate in the clips context
 * to report the error.
 *
 * @author Jack
 * @date Mon Feb 23 15:29:01 2015
 */
class Error {
	public $cause;
	public $message;

	public function __construct($cause, $message = array()) {
		$this->cause = $cause;
		if(!is_array($message))
			$this->message = array($message);
		else
			$this->message = $message;
	}
}
