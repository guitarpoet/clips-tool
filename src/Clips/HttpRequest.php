<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

class HttpRequest extends Request {
	public function __construct() {
		// Getting the method as the lower case
		$this->method = strtolower(get_default($_SERVER, 'REQUEST_METHOD', 'GET'));
		$this->tool = &get_clips_tool();
		$this->tool->load_class("validator", true);
		$this->validator = $this->tool->validator;
	}

	/**
	 * Get the parameters in the http get
	 *
	 * @param param
	 * 		The http get parameter that needs to be processed, if
	 * 		is array, will return the values as an array
	 * 	@param default
	 * 		The default values if no parameters is there, can be 
	 * 		array too
	 */
	public function get($param = null, $default = null) {
		return $this->_param($_GET, $param, $default);
	}

	public function session($key = null, $value = null) {
		if(!isset($this->_session)) {
			$this->_session = $this->tool->load_class('HttpSession', true);
		}
		if(!$key)
			return $this->_session;

		if(!$value)
			return $this->_session->$key;

		$this->_session->$key = $value;
		return true;
	}

	/**
	 * Get the parameters in the http request(no matter get or post)
	 *
	 * @param param
	 * 		The http get parameter that needs to be processed, if
	 * 		is array, will return the values as an array
	 * 	@param default
	 * 		The default values if no parameters is there, can be 
	 * 		array too
	 */
	public function param($param = null, $default = null) {
		return $this->_param(copy_arr($_GET, $_POST), $param, $default);
	}

	/**
	 * Get the parameters in the http post
	 *
	 * @param param
	 * 		The http get parameter that needs to be processed, if
	 * 		is array, will return the values as an array
	 * 	@param default
	 * 		The default values if no parameters is there, can be 
	 * 		array too
	 */
	public function post($param = null, $default = null) {
		return $this->_param($_POST, $param, $default);
	}

	private function _param($arr, $param = null, $default = null) {
		if(is_array($param)) {
			if(is_array($default) && count($default) == count($param)) {
				$ret = array();
				for($i = 0; $i < count($param); $i++) {
					$ret []= get_default($arr, $param[$i], $default[$i]);

				}
				return $ret;
			}
			else {
				return array_map(function($item){
					return get_default($arr, $param);
				}, $param);
			}
		}

		if($param == null)
			return copy_arr($arr);

		return get_default($arr, $param, $default);
	}

	public function header($name, $default = null) {
		if(!isset($this->headers)) {
			$this->_init_headers();
		}
		
		return get_default($this->headers, $key, $default);
	}

	public function server($key, $default = null) {
		return get_default($_SERVER, $key, $default);
	}

	public function cookie($cookie, $default = null) {
		return get_default($_COOKIE, $cookie, $default);
	}

	private function getIP() {
		if ($this->ip_address !== FALSE) {
			return $this->ip_address;
		}

		$this->ip_address = $_SERVER['REMOTE_ADDR'];

		if (count($this->validator->valid_ip(array('ip', $this->ip_address)))) {
			$this->ip_address = '0.0.0.0';
		}

		return $this->ip_address;
	}

	public function getType() {
		if($this->server('HTTP_X_REQUESTED_WITH') === 'XMLHttpRequest') {
			return 'ajax';
		}
		if (php_sapi_name() === 'cli' OR defined('STDIN')) {
			return 'cli';
		}
		return 'http';
	}

	private function _init_headers() {
		if (function_exists('apache_request_headers')) {
			$headers = apache_request_headers();
		}
		else {
			$headers['Content-Type'] = (isset($_SERVER['CONTENT_TYPE'])) ? $_SERVER['CONTENT_TYPE'] : @getenv('CONTENT_TYPE');

			foreach ($_SERVER as $key => $val) {
				if (strncmp($key, 'HTTP_', 5) === 0) {
					$headers[substr($key, 5)] = $this->server($key);
				}
			}
		}

		// take SOME_HEADER and turn it into Some-Header
		foreach ($headers as $key => $val) {
			$key = str_replace('_', ' ', strtolower($key));
			$key = str_replace(' ', '-', ucwords($key));

			$this->headers[$key] = $val;
		}

		return $this->headers;
	}
}
