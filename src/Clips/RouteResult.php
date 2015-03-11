<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

class RouteResult {
	public $controller;
	public $method;
	/** @Clips\Multi */
	public $args;
}

