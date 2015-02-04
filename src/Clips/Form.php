<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

use Clips\Interfaces\Initializable;

class Form extends \Addendum\Annotation implements Initializable {
	public function init() {
		if(isset($this->value)) {
			if(!is_array($this->value)) {
				$this->value = array($this->value);
			}
		}
		else {
			$controller = clips_context('controller_class');
			$method = clips_context('conroller_method');
			$name = explode('Controllers', $controller);

			// Get the name after controllers namespace, and get the last basename as the controller name
			$name = basename($name[count($name) - 1]);
			$this->value = $controller_class.'/'.$method;
		}
	}

	protected function getConfig() {
		if(!isset($this->_config)) {
			$this->_config = array();
			// Load the form config
			foreach($this->value as $config) {
				$form_config_dir = clips_config('form_config_dir');
				if($form_config_dir) {
					$form_config_dir = $form_config_dir[0];
					$p = path_join($form_config_dir, $config.'.json');
					if(file_exists($p)) {
						$this->_config[$config] = parse_json(file_get_contents($p));
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
		return get_default($c, $name, null);
	}
}
