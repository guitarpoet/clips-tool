<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

define('CLIPS_TOOL_PATH', dirname(__FILE__));

class LoadConfig {
	/** @Multi */
	public $dirs = array();
	public $suffix = "";
	public $prefix = "";

	public function __construct($dirs = array(), $suffix = "", $prefix = "") {
		$this->dirs = $dirs;
		$this->suffix = $suffix;
		$this->prefix = $prefix;
	}
}

class Config {

	/** @Multi */
	public $files = array();

	private $config;

	public function load() {
		$arr = array();
		$loaded = array();
		foreach($this->files as $config) {
			$p = realpath($config);
			if(in_array($p, $loaded))
				continue;
			$c = json_decode(file_get_contents($config));
			if(isset($c)) {
				$loaded []= $p;
				$arr []= (array) $c;
			}
		}
		$this->config = $arr;
	}

	public function getLoadConfig() {
		return new LoadConfig(array_merge($this->core_dir, 
			$this->helper_dir, 
			$this->command_dir, 
			$this->model_dir, 
			$this->template_dir,
			$this->library_dir));
	}

	public function __get($property) {
		if(isset($this->config)) {
			$ret = array();
			foreach($this->config as $c) {
				if(isset($c[$property])) {
					if(is_array($c[$property])) {
						$ret = array_merge($ret, $c[$property]);
					}
					else
						$ret []= $c[$property];
				}
			}
			return $ret;
		}
		return false;
	}
}

class Tool {
	private $_loaded_files = array();
	private $_loaded_classes = array();

	private function __construct() {
		$this->clips = new Engine();
	}

	public static function &get_instance() {
		static $instance;
		if(!isset($instance)) {
			$instance = new Tool();
			$instance->init();
		}
		return $instance;
	}

	public function isFileLoaded($file) {
		return in_array($file, $this->_loaded_files);
	}

	public function fileLoaded($file) {
		$this->_loaded_files []= $file;
	}

	public function loadRule($file) {
		$this->clips->load($file);
	}

	public function ruleConsole() {
		$this->clips->console();
	}

	private function init() {
		$this->config = $this->clips->runWithEnv(CLIPS_CORE_ENV, function($clips){
			$clips->reset();
			$clips->assertFacts(new Config());
			$clips->template('Clips\\LoadConfig');
			$clips->load(array(
				CLIPS_TOOL_PATH.'/config/rules/config.rules',
				CLIPS_TOOL_PATH.'/rules/load.rules'
			));

			$clips->run();
			return $clips->queryfacts('Clips\\Config');
		});
		$this->config = $this->config[0];
		$this->config->load(); // Load the configurations
		$this->load_class(array('template'), true, new LoadConfig($this->config->core_dir)); // Load the template
	    $this->library(array('ProgressManager'));
	}

	public function template() {
		return call_user_func_array(array($this->template, "render"), func_get_args());
	}

	public function helper() {
		return $this->load_php(func_get_args(), new LoadConfig($this->config->helper_dir, "_helper"));
	}

	/**
	 * Loading the php script using load config
	 */
	public function load_php($file, $loadConfig = null) {
		if(!is_array($file)) {
			$file = array($file);
		}
		if(!isset($loadConfig)) {
			$loadConfig = $this->config->getLoadConfig();
		}
		$facts = array($loadConfig); 
		foreach($file as $f) {
			$facts []= array('try-load-php', $f);
		}
		$this->clips->runWithEnv(CLIPS_CORE_ENV, function($clips, $facts){
			$clips->reset(); // Reset the environment
			$clips->assertFacts($facts);
			$clips->run();
		}, $facts);
	}

	private function _init_class($class, $init, $name) {
		if(class_exists($class)) { // Yes we have found it
			// We got the class
			if($init) {
				$this->$name = new $class();
				$this->_loaded_classes[$name] = $class;
				return $this->$name;	
			}
			return $class;
		}
		return false;
	}
	/** 
	 * We support 3 kinds of class name here 
	 * 1. Plain PHP File: Something like this test_service.php, will serve as $tool->test_service 
	 * 2. Plain PHP File with folder prefix: Something like libraries/test_service.php, will serve as $tool->test_service 
	 * 3. Namespace based PHP: Something like \Clips\Resource, will serve as $tool->Resource
	*/
	private function getHandleName($class) {
		$arr = array();
		// Test for namespace first
		if(strpos($class, '\\') !== false) {
			$arr = explode('\\', $class);
		}
		else if(strpos($class, '/') !== false) {
			$arr = explode('/', $class);
		} 
		else {
			$arr []= $class;
		}
		return array_pop($arr);
	}

	public function load_class($class, $init = false, $loadConfig = null) {
		if(!isset($loadConfig)) {
			$loadConfig = $this->config->getLoadConfig();
		}

		// Array support
		if(is_array($class)) {
			foreach($class as $c) {
				$this->load_class($c, $init, $loadConfig);
			}
			return true;
		}

		// We must find out the handle name first, in case to get it from the tool
		$handle_name = $this->getHandleName($class);

		if(isset($this->_loaded_classes[$class])) { // If this class is loaded
			if($init && is_object($this->$handle_name))
				return $this->$handle_name;
			else
				return $this->_loaded_classes[$class];
		}

		if(strpos($class, '\\') !== false) { // We have namespace loading here
			// Try without prefix, with load configuration's prefix and with default prefix
			foreach(array('', $loadConfig->prefix, 'Clips\\') as $pre) {
				// Try without prefix
				$class_name = ucfirst($pre.$class.$loadConfig->suffix);

				if(class_exists($class_name)) { // Let composer do this for me
					$result = $this->_init_class($class_name, $init, $handle_name);
					if(isset($result))
						return $result;
				}
			}
		}


		// Let's try loading the class without prefix for plain php file
		$orig_prefix = $loadConfig->prefix;

		// First with no prefix
		$loadConfig->prefix = '';
		$class_name = $loadConfig->prefix.$handle_name.$loadConfig->suffix;
		if(!class_exists($class_name)) {
			$this->load_php($class, $loadConfig); 
		}

		$result = $this->_init_class($class_name, $init, $handle_name);
		if($result)
			return $result;

		// Second, try load the subclass using the subclass prefix
		foreach($this->config->subclass_prefix as $prefix) {
			$loadConfig->prefix = $prefix;
			$class_name = $loadConfig->prefix.$handle_name.$loadConfig->suffix;
			if(!class_exists($class_name)) {
				$this->load_php($class, $loadConfig);
			}
			$result = $this->_init_class($class_name, $init, $handle_name);
			if($result)
				return $result;
		}

		// We don't needs to bother with clips's classes, since all of it will load using composer's class loader, so just init with it, so let's try with the prefix
		foreach(array($loadConfig->prefix, 'Clips\\') as $pre) {
			// Try without prefix
			$class_name = ucfirst($pre.$class.$loadConfig->suffix);

			if(class_exists($class_name)) { // Let composer do this for me
				$result = $this->_init_class($class_name, $init, $handle_name);
				if(isset($result))
					return $result;
			}
		}
		$result = $this->_init_class($orig_prefix.ucfirst($class).$loadConfig->suffix, $init, $handle_name);
		if($result)
			return $result;
		return false;
	}

	public function descCommand($command) {
		$c = $this->command($command);
		if($c) {
			$deps = $c->getDepends();
			if($deps) {
				if(!is_array($deps)) {
					$deps = array($deps);
				}
				return 'depends on '.implode(', ', $deps);
			}
		}
		return null;
	}

	public function listLoadDirs() {
		return $this->clips->runWithEnv(CLIPS_CORE_ENV, function($clips, $dir){
			$clips->reset(); // Reset the environment
			$clips->assertFacts(array('try-load-php', '$$'), new LoadConfig($dir));
			$clips->run();
			return array_unique(array_map(function($item){
				return realpath(dirname($item[0]));
			}, $clips->queryFacts('try-load-php-file')));
		}, $this->config->command_dir);
	}

	public function execute($command, $args) {
		$c = $this->command($command);
		if($c) {
			$deps = $c->getDepends();
			if(!is_array($deps)) {
				$deps = array($deps);
			}

			foreach($deps as $dep) {
				$this->execute($dep, $args);
			}
			return $c->execute($args);
		}
		trigger_error('No command named '.$command.' found!');
	}

	public function command($command) {
		$class = $this->load_class($command, false, new LoadConfig($this->config->command_dir, "Command", "Clips\\Commands\\"));
		if($class)
			return new $class();

		return null;
	}

	public function model($model) {
		$this->load_class(array('model'), false, new LoadConfig(array('core')));
		return $this->load_class($model, true, new LoadConfig($this->config->model_dir, '_model'));
	}

	public function library($library, $init = true, $suffix = "", $prefix = "Clips\\Libraries\\") {
		return $this->load_class($library, $init, new LoadConfig($this->config->library_dir, $suffix, $prefix));
	}
}
