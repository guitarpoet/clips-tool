<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

class RouteResult {
	public $controller;
	public $method;
	/** @Clips\Multi */
	public $args;
}

