<?php namespace Clips\Widgets; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

use Addendum\Annotation;
use Clips\Interfaces\Initializable;
use Clips\Interfaces\ToolAware;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

class ListView extends Annotation implements Initializable, ToolAware, LoggerAwareInterface {
	public function setLogger(LoggerInterface $logger) {
		$this->logger = $logger;
	}

	public function setTool($tool) {
		$this->tool = $tool;
	}

	public function init() {
		$this->tool->widget('ListView');
		if(isset($this->value)) {
			if(!is_array($this->value)) {
				$this->value = array($this->value);
			}
		}
		else {
			$this->value = array(\Clips\default_form_name());
		}

		// Initialize the ListView in javacript
		foreach($this->getConfig() as $name => $config) {
			if($config) {

				// Setting the default values for the datatable configuration
				if(!isset($config->ajax)) {
					$controller_class = \Clips\context('controller_seg');
					$method = \Clips\context('controller_method');
					$uri = \Clips\context('uri');
					if(strpos($uri, $method) !== false) {
						$d = explode($method, $uri);
						$config->ajax = \Clips\site_url(\Clips\path_join($d[0], $name));
					}
					else {
						if(strpos($uri, $controller_class) !== false) {
							$config->ajax = \Clips\site_url(\Clips\path_join($uri, $name));
						}
						else {
							$config->ajax = \Clips\site_url(\Clips\path_join($uri, $controller_class, $name));
						}
					}
				}

				$config->processing = true;
				$config->serverSide = true;

				foreach($config->columns as $col) {
					if(isset($col->data)) {
						// Must smooth the data
						$col->data = \Clips\smooth($col->data);
					}

					if(isset($col->refer)) {
						$col->refer = \Clips\smooth($col->refer);
					}

					if(isset($col->action)) {
						// If has action, use action render
						$col->render = 'datatable_action_column';
					}
				}

				// Adding the initialize script to jquery init
				\Clips\context('jquery_init', '$("ul[name='.\Clips\to_flat($name).']").listview('.json_encode($config).')', true);
			}
		}
	}

	protected function getConfig() {
		if(!isset($this->_config)) {
			$this->_config = array();
			// Load the config
			foreach($this->value as $config) {
				$datatable_config_dir = \Clips\clips_config('pagination_config_dir');
				if($datatable_config_dir) {
					$datatable_config_dir = $datatable_config_dir[0];
					$p = \Clips\path_join($datatable_config_dir, $config.'.json');
					if(file_exists($p)) {
						$this->_config[$config] = \Clips\parse_json(file_get_contents($p));
					}
				}
			}
		}
		return $this->_config;
	}

	public function config($name = null) {
		$c = $this->getConfig();
		if($name == null) {
			return $c;
		}
		return \Clips\get_default($c, $name, null);
	}
}
