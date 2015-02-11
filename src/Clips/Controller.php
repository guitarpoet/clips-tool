<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

use Psr\Log\LoggerAwareInterface;
use Clips\Interfaces\ClipsAware;
use Psr\Log\LoggerInterface;
use Clips\Interfaces\ToolAware;
use Clips\Models\ViewModel;

class Controller implements ClipsAware, LoggerAwareInterface, ToolAware {

	/**
	 * The short hand method for request->get
	 */
	public function get($param = null, $default = null) {
		return $this->request->get($param, $default = null);
	}

	public function post($param = null, $default = null) {
		$this->request->post($param, $default);
	}

	public function context($key, $value = null) {
		return $this->tool->context($key, $value);
	}

	public function setTool($tool) {
		$this->tool = $tool;
	}

	public function setClips($clips) {
		$this->clips = $clips;
	}

	public function setLogger(LoggerInterface $logger) {
		$this->logger = $logger;
	}

	public function render($template, $args = array(), $engine = null, $headers = array()) {
		if(!$engine) {
			$default = clips_config('default_view');
			if($default) {
				$engine = $default[0];
			}
		}
		return new ViewModel($template, $args, $engine, $headers);
	}

	public function meta($key, $value) {
		$meta = clips_context('html_meta');
		if(!$meta)
			$meta = array();
		
		$res = array();
		$found = false;
		foreach($meta as $m) {
			if(isset($m[$key])) {
				$m[$key] = $value;
				$found = true;
			}
			$res []= $m;
		}
		if(!$found)
			$res []= array($key => $value);
		clips_context('html_meta', $res);
	}

	public function redirect($url) {
		return $this->render("", array(), 'direct', array('Location' => $url));
	}
}
