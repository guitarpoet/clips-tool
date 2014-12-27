<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

define('CLIPS_TOOL_PATH', dirname(__FILE__));

require_once(CLIPS_TOOL_PATH.'/core/clips.php'); // Require the clips

function get_clips_tool() {
	return Clips_Tool::get_instance();
}

function clips_tool_path($path) {
	return CLIPS_TOOL_PATH.$path;
}

function record_file_load($file) {
	if($file) {
		$tool = get_clips_tool();
		$tool->fileLoaded($file);
		return true;
	}
	return false;
}

function file_load($file) {
	if($file) {
		$tool = get_clips_tool();
		return $tool->isFileLoaded($file);
	}
	return false;
}

function process_file_name($prefix, $file, $suffix) {
	$arr = explode('/', $file);
	$tail = array_pop($arr);
	$arr []= $prefix.$tail.$suffix;
	return implode('/', $arr);
}

function clips_php_require_once($file) {
	if(require_once($file)) {
		record_file_load($file);
		return true;
	}
	return false;
}

class Load_Config {
	/** @ClipsMulti */
	public $dirs = array();
	public $suffix = "";
	public $prefix = "";

	public function __construct($dirs = array(), $suffix = "", $prefix = "") {
		$this->dirs = $dirs;
		$this->suffix = $suffix;
		$this->prefix = $prefix;
	}
}

class Clips_Config {

	/** @ClipsMulti */
	public $files = array();

	private $config;

	public function load() {
		$arr = array();
		foreach($this->files as $config) {
			$c = json_decode(file_get_contents($config));
			if(isset($c)) {
				$arr []= (array) $c;
			}
		}
		$this->config = $arr;
	}

	public function getLoadConfig() {
		return new Load_Config(array_merge($this->core_dir, 
			$this->helper_dir, 
			$this->command_dir, 
			$this->model_dir, 
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

class Clips_Tool {
	private $_loaded_files = array();
	private $_loaded_classes = array();

	private function __construct() {
		$this->clips = new Clips();
	}

	public static function get_instance() {
		static $instance;
		if(!isset($instance)) {
			$instance = new Clips_Tool();
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

	private function init() {
		$this->clips->runWithEnv(CLIPS_CORE_ENV, function($clips){
			$clips->reset();
			$clips->assertFacts(new Clips_Config());
			$clips->template('Load_Config');
			$clips->load(array(
				CLIPS_TOOL_PATH.'/config/rules/config.rules',
				CLIPS_TOOL_PATH.'/core/rules/load.rules'
			));

			$clips->run();
			$this->config = $clips->queryfacts('Clips_Config');
			$this->config = $this->config[0];
		});
		$this->config->load(); // Load the configurations
		$this->helper('core'); // Load the core helpers
		$this->load_class(array('resource', 'command'), false, new Load_Config(array('core'))); // Load the base classes
		$this->load_class(array('template'), true, new Load_Config(array('core'))); // Load the template
	}

	public function template() {
		return call_user_func_array(array($this->template, "render"), func_get_args());
	}

	public function helper() {
		return $this->load_php(func_get_args(), new Load_Config($this->config->helper_dir, "_helper"));
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

	public function load_class($class, $init = false, $loadConfig = null) {
		// Let's load this class
		if(!isset($loadConfig)) {
			$loadConfig = $this->config->getLoadConfig();
		}

		if(is_array($class)) {
			foreach($class as $c) {
				$this->load_class($c, $init, $loadConfig);
			}
			return true;
		}

		$the_class = explode("/", $class);
		$the_class = array_pop($the_class); // The last one is the class name
		if(isset($this->_loaded_classes[$the_class])) { // If this class is loaded
			if($init)
				return $this->$the_class;
			return $this->_loaded_classes[$the_class];
		}

		$loadConfig->prefix = 'clips_';
		$clips_class_name = $loadConfig->prefix.$the_class.$loadConfig->suffix;
		if(!class_exists($clips_class_name)) {
			// Try to load clips class first if class is not defined
			$this->load_php($class, $loadConfig); 
		}

		// Let's try loading the class without prefix
		$loadConfig->prefix = '';
		$class_name = $loadConfig->prefix.$the_class.$loadConfig->suffix;
		if(!class_exists($class_name)) {
			$this->load_php($class, $loadConfig); 
		}

		$result = $this->_init_class($class_name, $init, $the_class);
		if($result)
			return $result;

		// Finally, try load the subclass
		foreach($this->config->subclass_prefix as $prefix) {
			$loadConfig->prefix = $prefix;
			$class_name = $loadConfig->prefix.$the_class.$loadConfig->suffix;
			if(!class_exists($class_name)) {
				$this->load_php($class, $loadConfig);
			}
			$result = $this->_init_class($class_name, $init, $the_class);
			if($result)
				return $result;
		}

		// We didn't get any customized class, try the clips ones
		$result = $this->_init_class($clips_class_name, $init, $the_class);
		if($result)
			return $result;
		return false;
	}

	public function execute($command, $args) {
		$command = $this->command($command);
		if($command) {
			return $command->execute($args);
		}
		trigger_error('No command named '.$command.' found!');
	}

	public function command($command) {
		$class = $this->load_class($command, false, new Load_Config($this->config->command_dir, "_command"));
		if($class)
			return new $class();
		return null;
	}

	public function library($library, $init = true, $suffix = "") {
		return $this->load_class($library, $init, new Load_Config($this->config->library_dir, $suffix));
	}
}
