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

	/**
	 * The overall paginate query support.
	 */
	public function pagination($config) {
		$config_dir = clips_config('pagination_config_dir');
		if($config_dir) {
			$config_dir = $config_dir[0];
			$p = path_join($config_dir, $config.'.json');
			if(file_exists($p)) {
				$pagination = Pagination::fromJson(file_get_contents($p));
				$pagination->update($this->request->param());
				$sql = $this->tool->library('sql');
				clips_log('Sql is {0}', $sql->pagination($pagination));

				// Get the first datasource
				$datasource = $this->tool->library('datasource')->first();

				$query = $sql->count($pagination);
				$result = $datasource->query($query[0], $query[1]);

				if($result) {
					$count = $result[0]->count;
					$query = $sql->pagination($pagination);
					$result = $datasource->query($query[0], $query[1]);
					if($result) {
						return $this->render("", array('data' => array(), 'recordsTotal' => 0, 'recordsFiltered' => 0), 'json');
					}
				}
			}
		}
		// Output empty by default
		return $this->render("", array('data' => array(), 'recordsTotal' => 0, 'recordsFiltered' => 0), 'json');
	}

	public function redirect($url) {
		return $this->render("", array(), 'direct', array('Location' => $url));
	}
}
