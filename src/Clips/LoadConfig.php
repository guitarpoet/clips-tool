<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

use Addendum\Annotation;

class LoadConfig {
	/** @Clips\Multi */
	public $dirs = array();
	public $suffix = "";
	public $prefix = "";
	/** @Clips\Multi */
	public $args;

	public function __construct($dirs = array(), $suffix = "", $prefix = "", $args = null) {
		$this->dirs = $dirs;
		$this->suffix = $suffix;
		$this->prefix = $prefix;
		$this->args = $args;
	}
}

