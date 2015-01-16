<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

define('CLIPS_TOOL_PATH', dirname(__FILE__));

class Requires extends \Addendum\Annotation { }

class LoadConfig {
	/** @Multi */
	public $dirs = array();
	public $suffix = "";
	public $prefix = "";
	/** @Multi */
	public $args;

	public function __construct($dirs = array(), $suffix = "", $prefix = "", $args = null) {
		$this->dirs = $dirs;
		$this->suffix = $suffix;
		$this->prefix = $prefix;
		$this->args = $args;
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

	public function addConfig($config) {
		$this->config []= $config;
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

	public function resource($uri) {
		return new Resource($uri);
	}

	public function getLogger($name = null) {
		if($name) {
			$logger = new \Monolog\Logger($name);
			return $this->initLogger($logger);
		}
		else {
			if(!isset($this->logger)) {
				$this->logger = $this->getLogger(get_class($this));
			}
			return $this->logger;
		}
	}

	protected function createInstance($class, $args = array()) {
		if(class_exists($class)) {
			if($args) {
				$c = new \ReflectionClass($class);
				return $c->newInstanceArgs($args);
			}
			return new $class();
		}
		return null;
	}

	protected function initLogger($logger) {
		foreach($this->config->logger as $config) {
			foreach(get_default($config, "processors", array()) as $class) {
				$processor = $this->createInstance($this->load_class(ucfirst($class), false, new LoadConfig(array(), 'Processor', 'Monolog\\Processor\\' )));
				if($processor)
					$logger->pushProcessor($processor);
			}

			foreach(get_default($config, "handlers", array()) as $class => $args) {
				if(is_cli() && in_array(strtolower($class), array('firephp', 'chromephp'))) {
					// In commandline will skip the firephp or chromephp handler since they'll need to output to header
					continue;
				}

				$filtered_args = array_map(function($item) {
					if(is_string($item) && defined("Monolog\\Logger\\".strtoupper($item))) {
						return constant("Monolog\\Logger\\".strtoupper($item));	
					}
					return $item;
				}, $args);

				$handler = $this->createInstance($this->load_class(ucfirst($class), false, new LoadConfig(array(), 'Handler', 'Monolog\\Handler\\')), $filtered_args);
				if($handler)
					$logger->pushHandler($handler);
			}
		}
		return $logger;
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

	private function _init_class($class, $init, $name, $args = null) {
		if(class_exists($class)) { // Yes we have found it
			// We got the class
			if($init) {
				// Construct the class
				$this->$name = $this->createInstance($class, $args);

				// Setting the log
				if(is_subclass_of($this->$name, 'Psr\\Log\\LoggerAwareInterface')) {
					$this->$name->setLogger($this->getLogger($class)); // Setting the logger according to the class name
				}

				// Process requires annotation
				$reflection = new \Addendum\ReflectionAnnotatedClass($class);
				if($reflection->hasAnnotation('Requires')) {
					$a = $reflection->getAnnotation('Requires');
					foreach($a->value as $r) {
						$this->$name->$r = $this->library($r);
					}
				}

				// Process initialize
				if(is_subclass_of($this->$name, 'CLips\\Interfaces\\Initializable')) {
					// Call the init function
					$this->$name->init();

				}

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

	public function load_class($class, $init = false, $loadConfig = null, $args = null) {
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

		// Let's try load using namespace
		
		// Try load the class using the application's namespace
		foreach(array('', $loadConfig->prefix) as $pre) {
			foreach(array_merge(clips_config('namespace', array()), array('')) as $namespace) {

				foreach(array($loadConfig->suffix, 
					ucfirst(str_replace('_', '', $loadConfig->suffix))) as $suffix) {
					$class_name = ucfirst($namespace.$pre.$class.$suffix);

					if(class_exists($class_name)) { // Let composer do this for me
						$result = $this->_init_class($class_name, $init, $handle_name, $args);
						if(isset($result))
							return $result;
					}
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

		$result = $this->_init_class($class_name, $init, $handle_name, $args);
		if($result)
			return $result;

		// Second, try load the subclass using the subclass prefix
		foreach($this->config->subclass_prefix as $prefix) {
			$loadConfig->prefix = $prefix;
			$class_name = $loadConfig->prefix.$handle_name.$loadConfig->suffix;
			if(!class_exists($class_name)) {
				$this->load_php($class, $loadConfig);
			}
			$result = $this->_init_class($class_name, $init, $handle_name, $args);
			if($result)
				return $result;
		}

		// Try the default clips classes
		$result = $this->_init_class("Clips\\".$orig_prefix.ucfirst($class).$loadConfig->suffix, $init, $handle_name, $args);
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
		return $this->load_class($model, true, new LoadConfig($this->config->model_dir, '_model', 'Models\\'));
	}

	public function library($library, $init = true, $suffix = "", $prefix = "Libraries\\") {
		return $this->load_class($library, $init, new LoadConfig($this->config->library_dir, $suffix, $prefix));
	}
}
