<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

class DataSourceException extends Exception {
	public function __construct($msg = null) {
		parent::__construct($msg);
	}
}
