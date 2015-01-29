<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

class HttpRequest extends Request {
	public function __construct() {
		// Getting the method as the lower case
		$this->method = strtolower(get_default($_SERVER, 'REQUEST_METHOD', 'GET')));
	}

	public function server($key, $default = null) {
		return get_default($_SERVER, $key, $default);
	}

	private function getIp() {
		if ($this->ip_address !== FALSE) {
			return $this->ip_address;
		}

		$proxy_ips = config_item('proxy_ips');
		if ( ! empty($proxy_ips)) {
			$proxy_ips = explode(',', str_replace(' ', '', $proxy_ips));
			foreach (array('HTTP_X_FORWARDED_FOR', 'HTTP_CLIENT_IP', 'HTTP_X_CLIENT_IP', 'HTTP_X_CLUSTER_CLIENT_IP') as $header)
			{
				if (($spoof = $this->server($header)) !== FALSE)
				{
					// Some proxies typically list the whole chain of IP
					// addresses through which the client has reached us.
					// e.g. client_ip, proxy_ip1, proxy_ip2, etc.
					if (strpos($spoof, ',') !== FALSE)
					{
						$spoof = explode(',', $spoof, 2);
						$spoof = $spoof[0];
					}

					if ( ! $this->valid_ip($spoof))
					{
						$spoof = FALSE;
					}
					else
					{
						break;
					}
				}
			}

			$this->ip_address = ($spoof !== FALSE && in_array($_SERVER['REMOTE_ADDR'], $proxy_ips, TRUE))
				? $spoof : $_SERVER['REMOTE_ADDR'];
		}
		else
		{
			$this->ip_address = $_SERVER['REMOTE_ADDR'];
		}

		if ( ! $this->valid_ip($this->ip_address)) {
			$this->ip_address = '0.0.0.0';
		}

		return $this->ip_address;
	}

	private function getType() {
		if($this->server('HTTP_X_REQUESTED_WITH') === 'XMLHttpRequest') {
			return 'ajax';
		}
		if (php_sapi_name() === 'cli' OR defined('STDIN')) {
			return 'cli';
		}
		return 'http';
	}
}
