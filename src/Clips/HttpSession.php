<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

use Clips\Interfaces\Initializable;
use Addendum\Annotation;

class HttpSession extends Annotation implements Initializable {
	public function init() {
		if(HttpSession::status() != 'active')
			session_start();
	}

	public function __get($property) {
		if(HttpSession::status() == 'active') {
			return get_default($_SESSION, $property, null);
		}
		return null;
	}

	public function __set($property, $value) {
		if(HttpSession::status() == 'active') {
			$_SESSION[$property] = $value;
			return true;
		}
		return false;
	}

	public function abort() {
		session_abort();
	}

	public function reset() {
		session_reset();
	}

	public static function status() {
		if(function_exists('session_status')) {
			switch(session_status()) {
			case PHP_SESSION_DISABLED:
				return 'disabled';
			case PHP_SESSION_NONE:
				return 'none';
			case PHP_SESSION_ACTIVE:
				return 'active';
			}
		}
		else {
			return isset($_SESSION) ? 'active': 'none';
		}
	}

	public function destroy() {
		session_destroy();
	}
}
