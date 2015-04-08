<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed"); 

function web_error_handler($errno, $errstr, $errfile, $errline) {
    if (!(error_reporting() & $errno)) {
        return;
    }

    switch ($errno) {
    case E_USER_ERROR:
		error('PHP_ERROR', array('Level: Error', $errstr));
        break;
    case E_USER_WARNING:
		error('PHP_ERROR', array('Level: Warning', $errstr));
        break;
    case E_USER_NOTICE:
		error('PHP_ERROR', array('Level: Notice', $errstr));
        break;
    default:
		error('PHP_ERROR', array('Level: Error', $errstr));
        break;
    }

    return true;
}


/**
 * The web application facade
 *
 * @author Jack <guitarpoet@gmail.com>
 * @date Tue Feb 17 10:34:35 2015
 */
class WebApp {
	public function __construct($name = '') {
		$this->tool = &get_clips_tool();
		context('app', $this);
		context('app_name', $name);
		context('smarty', $this->tool->create('Smarty'));
		set_error_handler("Clips\\web_error_handler");
		$this->name = $name;
		$this->router = $this->tool->load_class('Router', true);
		$this->router->route();
	}
}
