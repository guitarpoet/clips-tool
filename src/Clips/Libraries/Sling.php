<?php namespace Clips\Libraries; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

use Clips\BaseService;

/**
 * The sling service wrapper that using curl
 *
 * @author Jack
 * @date Sun Aug  9 14:43:59 2015
 * @version 1.1
 *
 * @Clips\Library("curl")
 */
class Sling extends BaseService {

	public function init() {
		$sling_config = \CLips\config('sling');
		if($sling_config) {
			$sling_config = $sling_config[0]; // Use the first configuration
			$this->host = \Clips\get_default($sling_config, 'host');
			$this->port = \Clips\get_default($sling_config, 'port');
			$this->username = \Clips\get_default($sling_config, 'username');
			$this->password = \Clips\get_default($sling_config, 'password');

			$this->curl->setBasicAuthentication($this->username, $this->password);
		}
		else {
			throw new \Clips\Exception('There is no sling configuration!');
		}
	}

	protected function buildUrl($path) {
		return "http://$this->host:$this->port$path";
	}

	public function remove($path) {
		$this->curl->delete($path);
	}

	public function update($path, $data) {
		if(!isset($data['_charset_'])) {
			$data['_charset_'] = 'UTF-8';
		}

		if(isset($data['attachments'])) {
			unset($data['attachments']);
		}

		$this->curl->setHeader('Content-Type', "application/x-www-form-urlencoded; 
charset=".$data['_charset_']);
		
		$this->curl->post($this->buildUrl($path), $data);
	}

	public function data($path, $params = array()) {

		$url = $this->buildUrl($path).'.json';

		if($params) {
			$q = array();
			foreach($params as $k => $v) {
				$q []= urlencode($k) . '=' . urlencode($v);
			}

			$url .= '?'.implode('&', $q);
		}
		$this->curl->get($url);
		if($this->curl->http_status_code == 200) {
			return json_decode($this->curl->response);
		}
		return false;
	}

	public function contents($path) {
		$this->curl->get($this->buildUrl($path));
		if($this->curl->http_status_code == 200) {
			return json_decode($this->curl->response);
		}
		return false;
	}
}
