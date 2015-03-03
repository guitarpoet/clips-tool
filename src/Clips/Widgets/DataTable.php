<?php namespace Clips\Widgets; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

use Addendum\Annotation;
use Clips\Interfaces\Initializable;
use Clips\Interfaces\ToolAware;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

class DataTable extends Annotation implements Initializable, ToolAware, LoggerAwareInterface {
	public $bundle;

	public function setLogger(LoggerInterface $logger) {
		$this->logger = $logger;
	}

	public function setTool($tool) {
		$this->tool = $tool;
	}

	public function init() {
		$this->tool->widget('DataTable');
		if(isset($this->value)) {
			if(!is_array($this->value)) {
				$this->value = array($this->value);
			}
		}
		else {
			$this->value = array(\Clips\default_form_name());
		}

		// Initialize the datatable in javacript
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

				// The bundle configuration in anntation has the highest priority
				$bundle = \Clips\get_default($this, 'bundle', null);

				if($bundle === null) {
					// Try configuration's bundle settings
					$bundle = \Clips\get_default($config, 'bundle', null);

					if($bundle === null) {
						// We can't find the bundle in annotation, try to find it using default name
						$bundle_name = 'pagination/'.$name;
						$bundle = $this->tool->bundle($bundle_name);
						if($bundle->isEmpty()) {
							// All default bundle can't be found, try current controller's bundle
							$bundle = \Clips\context('current_bundle');
							if($bundle === null)
								$bundle = '';
						}
					}
				}
				$bundle = \Clips\bundle($bundle);

				foreach($config->columns as $col) {
					if(isset($col->title)) {
						$col->title = $bundle->message($col->title);
					}

					if(isset($col->data)) {
						// Must smooth the data
						if(strpos($col->data, " as ")) {
							$d = explode(' as ', $col->data);
							if($d)
								$col->data = trim($d[1]);
						}
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

				// Clean the where data stored in the session
				$controller = \Clips\context('controller');
				if($controller) {
					$controller->request->session($name, null);
				}

				// Adding the initialize script to jquery init
				\Clips\context('jquery_init', '$("table[name='.\Clips\to_flat($name).']").DataTable('.str_replace('"datatable_action_column"', 'datatable_action_column', json_encode($config)).')', true);
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
