<?php namespace Clips\Libraries; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

class Cache {
	private $_cache = array();

	public function __get($property) {
		return \Clips\get_default($this->_cache, $property);
	}

	public function __set($property, $value) {
		$this->_cache[$property] = $value;
	}

	public function put($key, $value) {
		$this->$key = $value;
	}

	public function get($key) {
		return $this->$key;
	}

	public function has($key) {
		return isset($this->_cache[$key]);
	}
}
