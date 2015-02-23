<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed"); 

/**
 * The web application facade
 *
 * @author Jack <guitarpoet@gmail.com>
 * @date Tue Feb 17 10:34:35 2015
 */
class WebApp {
	public function __construct($name = '') {
		$this->tool = get_clips_tool();
		context('app', $this);
		$this->name = $name;
		$this->router = $this->tool->load_class('Router', true);
		$this->router->route();
	}
}
