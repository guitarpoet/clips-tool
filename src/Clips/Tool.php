<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

use Addendum\Annotation;

define('CLIPS_TOOL_PATH', __DIR__);

if(!defined('FCPATH'))
	define('FCPATH', getcwd());

class Tool implements Interfaces\Initializable {
	private $_loaded_files = array();
	private $_loaded_classes = array();
	private $_context = array();
	private $_bundles = array();

	private function __construct() {
		$this->clips = new Engine();
	}

	public function resource($uri) {
		return new Resource($uri);
	}

	public function sequence($name = "") {
		$sn = "_seq_".$name;
		if(!isset($this->$sn)) {
			$this->$sn = 0;
		}
		return ++$this->$sn;
	}
	public function log($message, $context = array()) {
		$this->info($message, $context);
	}

	public function emergency($message, $context = array()) {
		if(!is_array($context)) {
			$context = array($context);
		}
		$this->getLogger()->emergency($message, $context);
	}

	public function critical($message, $context = array()) {
		if(!is_array($context)) {
			$context = array($context);
		}
		$this->getLogger()->critical($message, $context);
	}

	public function notice($message, $context = array()) {
		if(!is_array($context)) {
			$context = array($context);
		}
		$this->getLogger()->notice($message, $context);
	}

	public function info($message, $context = array()) {
		if(!is_array($context)) {
			$context = array($context);
		}
		$this->getLogger()->notice($message, $context);
	}

	public function debug($message, $context = array()) {
		if(!is_array($context)) {
			$context = array($context);
		}
		$this->getLogger()->notice($message, $context);
	}

	public function error($message, $context = array()) {
		if(!is_array($context)) {
			$context = array($context);
		}
		$this->getLogger()->error($message, $context);
	}

	public function warning($message, $context = array()) {
		if(!is_array($context)) {
			$context = array($context);
		}
		$this->getLogger()->error($message, $context);
	}

	public function alert($message, $context = array()) {
		if(!is_array($context)) {
			$context = array($context);
		}
		$this->getLogger()->alert($message, $context);
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
		if(isset($class) && is_string($class) && class_exists($class)) {
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

	public function init() {
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
		if(!$this->config)
			die('Error in reading configuration!');
		$this->config = $this->config[0];
		$this->config->load(); // Load the configurations
		$this->load_class(array('template', 'validator'), true, new LoadConfig($this->config->core_dir)); // Load the template

		// Load the helpers
		call_user_func_array(array($this, 'helper'), $this->config->helpers);

		// Load the progress manager
	    $this->library(array('ProgressManager'));

		// Setting the bundle dir
		$bundle_dir = config('bundle_dir');
		if($bundle_dir) {
			context('bundle_dir', $bundle_dir);
		}
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

	public function create($class, $args = null) {
		return $this->_init_class($class, true, "__created__", $args);
	}

	public function annotationEnhance($a, $obj) {
		$class = get_class($a);

		// Context is special
		if($class == 'Clips\\Context') {
			if(isset($a->value) && is_array($a->value)) {
				// This must be the set by array
				context($a->value, null, $a->append);
			}
			else {
				if(isset($a->key) && $a->key)
					context($a->key, $a->value, $a->append);
			}
		}
		else if($class == 'Clips\\Meta') {
			if(isset($a->value) && is_array($a->value)) {
				foreach($a->value as $k => $v) {
					html_meta($k, $v);
				}
			}
			else {
				if(isset($a->key))
					html_meta($a->key, $a->value);
			}
		}
		else if($class == 'Clips\\HttpSession') {
			$controller = context('controller');
			if($controller) {
				if(isset($a->value) && is_array($a->value)) {
					foreach($a->value as $k => $v) {
						$controller->request->session($k, $v);
					}
				}
				else {
					if(isset($a->key))
						$controller->request->session($a->key, $a->value);
				}
			}
		}
		else {
			// Fix the value of annotation first
			if(isset($a->value)) {
			   if(!is_array($a->value))
					$a->value = array($a->value);
			}
			else
				$a->value = array();

			switch($class) {
				case "Clips\\Description":
					html_meta('description', $a->value);
					break;
				case "Clips\\MessageBundle": // The message bundle support
					$this->enhance($a);
					$obj->bundle = $a;
					if(isset($a->name))
						context('current_bundle', $a->name);
					break;
				case "Clips\\Object": // The clips object support
					foreach($a->value as $c) {
						$h = strtolower($this->getHandleName($c));
						$obj->$h = $this->load_class($c, true, null, $a->args);
					}
					break;
				case "Clips\\Library": // The clips library support
					foreach($a->value as $c) {
						$h = strtolower($this->getHandleName($c));
						$obj->$h = $this->library($c, true);
					}
					break;
				case "Clips\\Model": // The clips library support
					if(valid_obj($obj, "Clips\\Libraries\\DBModel")) {
						// If this is model itself, set the table field and append it to model context
						if(isset($a->table)) {
							$obj->table = $a->table;
						}
						if(isset($a->name)) {
							$obj->name = $a->name;
						}
						clips_context('models', $obj, true);
					}
					else {
						// Adding the model dependency
						foreach($a->value as $c) {
							$h = strtolower($this->getHandleName($c));
							$obj->$h = $this->model($c);
						}
					}
					break;
				case "Clips\\Js":
					foreach($a->value as $j) {
						add_js($j);
					}
					break;
				case "Clips\\Css":
					foreach($a->value as $j) {
						add_css($j);
					}
					break;
				case "Clips\\Scss":
					foreach($a->value as $j) {
						add_scss($j);
					}
					break;
				case "Clips\\Form":
					// If this is the form annotation, initialize it and set it to the context
					$this->enhance($a);
					context('form', $a);
					break;
				case "Clips\\Widget":
					$this->widget($a->value);
					break;
				case "Clips\\Widgets\\DataTable":
					$this->enhance($a);
					context('datatable', $a);
					break;
				case "Clips\\Widgets\\ListView":
					$this->enhance($a);
					context('listview', $a);
					break;
			}
		}
		return $obj;
	}

	public function enhance($obj) {
		// Interface enhances
		if(is_subclass_of($obj, 'Psr\\Log\\LoggerAwareInterface')) {
			$obj->setLogger($this->getLogger(get_class($obj))); // Setting the logger according to the class name
		}

		if(is_subclass_of($obj, 'Clips\\Interfaces\\ClipsAware')) {
			$obj->setClips($this->clips);
		}

		if(is_subclass_of($obj, 'Clips\\Interfaces\\ToolAware')) {
			$obj->setTool($this);
		}

		// Enhance the object using the annotation
		$re = new \Addendum\ReflectionAnnotatedClass($obj);
		foreach($re->getAllAnnotations() as $a) {
			$this->annotationEnhance($a, $obj);
		}

		// Process initialize
		if(is_subclass_of($obj, 'CLips\\Interfaces\\Initializable')) {
			// Call the init function
			$obj->init();

		}
		return $obj;
	}

	private function _init_class($class, $init, $name, $args = null) {
		if(isset($class) && is_string($class) && class_exists($class)) { // Yes we have found it
			// We got the class
			if($init) {
				// Construct the class
				if(isset($this->{ strtolower($name) })) {
					$obj = $this->{ strtolower($name) };
					if(valid_obj($obj, $class))
						return $obj;
				}

				$obj = $this->createInstance($class, $args);
				$this->{ strtolower($name) } = $obj;
				$this->enhance($obj);
				$this->_loaded_classes[lcfirst($name)] = $class;
				return $obj;	
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
	public function getHandleName($class) {
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

	/**
	 * TODO There is a very big bug in the load php using require once for this loading
	 */
	public function load_class($class, $init = false, $loadConfig = null, $args = null) {
		if(!isset($loadConfig)) {
			$loadConfig = $this->config->getLoadConfig();
		}

		// Array support
		if(is_array($class)) {
			foreach($class as $c) {
				$this->load_class($c, $init, $loadConfig, $args);
			}
			return true;
		}

		// We must find out the handle name first, in case to get it from the tool
		$handle_name = $this->getHandleName($class);

		/*
		if(isset($this->_loaded_classes[ lcfirst($class)] )) { // If this class is loaded
			if($init && is_object($this->{ strtolower($handle_name) }))
				return $this->{ strtolower($handle_name) };
			else
				return $this->_loaded_classes[lcfirst($class)];
		}
		 */

		// Let's try load using namespace
		
		// Try load the class using the application's namespace
		foreach(array('', $loadConfig->prefix) as $pre) {
			foreach(array_merge(array(''), clips_config('namespace', array())) as $namespace) {
				foreach(array($loadConfig->suffix, 
					ucfirst(str_replace('_', '', $loadConfig->suffix))) as $suffix) {
					$class_name = ucfirst($namespace.$pre.ucfirst($class).$suffix);

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
		$c = $this->command(ucfirst($command));
		if($c) {
			$deps = $c->getDepends();
			if(!is_array($deps)) {
				$deps = array($deps);
			}

			foreach($deps as $dep) {
				$this->execute($dep, $args);
			}
			$reflection = new \Addendum\ReflectionAnnotatedClass($c);
			if(!$reflection->hasAnnotation('Clips\\FullArgs')) {
				array_shift($args); // For the clips script
				array_shift($args); // For the command
			}
			return $c->execute($args);
		}
		trigger_error('No command named '.$command.' found!');
	}

	public function context_peek($key) {
		if(isset($this->_context[$key])) {
			$arr = $this->_context[$key];
			if(is_array($arr) && $arr) {
				return $arr[count($arr) - 1];
			}
			else
				return $arr;
		}
		return null;
	}

	public function context_pop($key) {
		if(isset($this->_context[$key])) {
			$arr = $this->_context[$key];
			if(is_array($arr)) {
				$ret = array_pop($arr);
				$this->context($key, $arr);
			}
			else {
				$ret = $arr;
				unset($this->_context[$key]);
			}
			return $ret;
		}
		return null;
	}

	public function context($key = null, $value = null, $append = false) {
		if($key == null)
			return $this->_context;

		if($value !== null) {
			if($append) {
				if(!isset($this->_context[$key])) {
					$this->_context[$key] = array();
				}
				if(!is_array($this->_context[$key]))
					$this->_context[$key] = array($this->_context[$key]);
				$this->_context[$key] []= $value;
			}
			else {
				$this->_context[$key] = $value;
			}
			return $value;
		}
		if(is_array($key) || is_object($key)) {
			// Setting using array or object
			foreach($key as $k => $v) {
				$this->context($k, $v, $append);
			}
			return true;
		}
		return get_default($this->_context, $key, null);
	}

	public function controller($controller) {
		$class = $this->load_class($controller, false, new LoadConfig($this->config->controller_dir, "Controller", "Controllers\\"));
		if($class)
			return $class;

		return null;
	}

	public function command($command) {
		$class = $this->load_class($command, false, new LoadConfig($this->config->command_dir, "Command", "Commands\\"));
		if($class)
			return $this->create($class);

		return null;
	}

	public function filter($filter) {
		return $this->load_class($filter, true, new LoadConfig($this->config->filter_dir, 'Filter', "Filters\\"));
	}

	public function widgetClass($widget) {
		$name = ucfirst($widget).'Widget';
		if(isset($this->_loaded_classes[$name])) {
			$cls = $this->_loaded_classes[$name];
		}
		else {
			$cls = $this->load_class('Widget', false, new LoadConfig($this->config->widget_dir, '', "Widgets\\".ucfirst($widget).'\\'));
			if($cls)
				$this->_loaded_classes[$name] = $cls;
		}
		return $cls;
	}

	public function widget($widget) {
		if(is_array($widget)) {
			foreach($widget as $w) {
				$this->widget($w);
			}
			return true;
		}

		$class = $this->widgetClass($widget);
		if(isset($this->$class))
			return $this->$class;
		if(class_exists($class)) {
			$w = new $class();
			$this->enhance($w);
			$this->$class = $w;
		}
		return null;
	}

	/**
	 * Try to get bundle manualy.
	 *
	 * In most of the times, the bundle should be get using annotation. But, in some
	 * circumstances, in some functions, for example. You'll need to get the bundle manually,
	 * this function will come to help.
	 *
	 * @param name
	 * 		The name of the bundle.
	 *
	 * @date Mon Feb 23 13:56:47 2015
	 */
	public function bundle($name = '') {
		if(isset($this->_bundles[$name]))
			return $this->_bundles[$name];

		$bundle = new MessageBundle();
		$bundle->name = $name;
		return $this->enhance($bundle);
	}

	public function model($model) {
		return $this->load_class($model, true, new LoadConfig($this->config->model_dir, '_model', 'Models\\'));
	}

	public function object($obj) {
		if(is_array($obj)) {
			$ret = array();
			foreach($obj as $o) {
				$tmp = $this->object($o);
				if($tmp)
					$ret []= $tmp;
			}
			return $ret;
		}
		return $this->load_class($obj, true);
	}

	public function library($library, $init = true, $suffix = "", $args = null, $prefix = "Libraries\\") {
		return $this->load_class($library, $init, new LoadConfig($this->config->library_dir, $suffix, $prefix), $args);
	}
}
