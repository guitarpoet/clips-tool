<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

use Clips\Libraries\ConsoleBase;

if(!defined('CLIPS_CORE_ENV'))
	define('CLIPS_CORE_ENV', 'CORE');

if(!defined('CLIPS_MAIN_ENV'))
	define('CLIPS_MAIN_ENV', 'MAIN');

class Multi extends \Addendum\Annotation {}

class Symbol extends \Addendum\Annotation {

	public static function symbol($value) {
		$s = new Symbol();
		$s->value = $value;
		return $s;
	}
} 

/**
 *  The clips extension engine and execution context
 */
class Engine extends ConsoleBase {

	/**
	 * The clips execution context
	 */
	public static $context;

	public $env_stack = array();

	public $current_env;

	public function __construct($name = CLIPS_MAIN_ENV) {
		if(!isset(Engine::$context)) {
			// Initialize the context
			Engine::$context = array();
			clips_init(Engine::$context);

			// Create the core env
			$this->createEnv(CLIPS_CORE_ENV);

			// Loading the core rules into core
			$this->switchCore();
			$path = dirname(__FILE__).'/rules/clips.rules'; // Load the default functions
			$this->load($path);
			$this->load(dirname(__FILE__).'/rules/core.rules');

			// Come back to main again
			$this->switchMain();
		}

		if(!$this->isEnvExists($name))
			$this->createEnv($name);

		$this->switchEnv($name); // Switch the env to the name
		$this->_init_base_support();
	}

	private function _init_base_support() {
		$path = dirname(__FILE__).'/rules/clips.rules'; // Load the default functions
		if(!$this->classExists('PHP-OBJECT'))
			$this->load($path);
		if(function_exists('get_instance')) {
			$this->ci = get_instance(); // Add the ci object to the context, if function get_instance is exists
		}
	}

	private function translate($var) {
		if($var === null)
			return 'nil';

		switch(gettype($var)) {
		case 'string':
			return '"'.addslashes($var).'"'; // Quote the string
		case 'boolean':
			return $var? 'TRUE' : 'FALSE';
		case 'array':
		case 'object':
			if(is_object($var) && get_class($var) == 'Clips\\Symbol') {
				return $var->value;
			}
			// For array and object, let's make them multiple values
			$ret = array();
			foreach($var as $key => $value) {
				$ret []= $this->translate($value);
			}
			return implode(' ', $ret);
		}
		return $var;
	}

	public function runWithEnv($name, $callback, $args = array()) {
		$this->env_stack []= $this->current_env;
		if($this->switchEnv($name)) {
			$ret = false;
			try {
				$ret = call_user_func_array($callback, array($this, $args));
			}
			catch(Exception $ex) {
			}

		 	$env = array_pop($this->env_stack);
			$this->switchEnv($env);
			return $ret;
		}
		else {
		 	 array_pop($this->env_stack);
		}
		return false;
	}

	public function isEnvExists($name) {
		$meta = $this->getMeta();
		return in_array($name, $meta['envs']);
	}

	public function createEnv($name) {
		if(!$this->isEnvExists($name))
			return clips_create_env($name);
		return false;
	}

	/**
	 * Switch the current environment to the environment of that name.
	 *
	 * @return
	 * 		Will return current env's name if switched success, or false if swich failed.
	 */
	public function switchEnv($name) {
		if($this->isEnvExists($name)) {
			if(clips_switch_env($name)) {
				$env = $this->current_env;
				$this->current_env = $name;
				return $env;
			}
			return false;
		}
		trigger_error("The env of name $name is not exists!!!");
		return false;
	}

	public function getMeta() {
		$meta = array();
		clips_meta($meta);
		return $meta;
	}

	public function currentEnv() {
		$meta = $this->getMeta();
		return $meta['current'];
	}

	/**
	 * Define the template in the clips for the php class
	 *
	 * @param $class
	 * 		The class name string for the php class
	 */
	public function template($class) {
		if(is_array($class)) {
			foreach($class as $c) {
				$this->template($c);
			}
			return true;
		}

		if(!$this->templateExists($class)) {
			$this->command($this->defineTemplate($class));
			return true;
		}
		return false;
	}

	//======================================================================
	//
	// Clips string manipulation methods
	//
	//======================================================================

	public function defineFact($data) {
		$ret = array();
		if(is_string($data)) {
			$data = array($data);
		}
		if(is_array($data))  {
			if(!isset($data['__template__'])) { // This is a static fact
				$name = array_shift($data);
				$ret []= '('.$name;
				foreach($data as $d) {
					$ret []= $this->translate($d);
				}
			}
			else {
				return $this->defineFact((object) $data);
			}
		}
		else {
			$obj = $data;
			$name = get_class($obj);

			$reflection = new \Addendum\ReflectionAnnotatedClass($name);
			
			if(isset($obj->__template__)) {
				$name = $obj->__template__;
			}

			$ret []= '('.$name;
			foreach($obj as $key => $value) {
				if(strpos($key, '_') === 0) // Skip _ variables
					continue;
				$ret []= '('.$key;
				if($reflection->hasProperty($key)) {
					if($reflection->getProperty($key)->hasAnnotation('Clips\\Symbol')) {
						$value = Symbol::symbol($value);
					}
				}
				$ret []= $this->translate($value).')';
			}
		}
		return implode(' ', $ret).')';
	}

	public function defineInstance($name, $class = 'PHP-OBJECT', $args = array()) {
		$ret = array();
		$ret []= '(make-instance';
		$ret []= $name;
		$ret []= 'of';
		$ret []= $class;
		foreach($args as $key => $value) {
			$ret []= '('.$key;
			$ret []= $this->translate($value);
			$ret []= ')';
		}
		return implode(' ', $ret).')';
	}

	/**
	 * Define the template according to the class
	 */
	public function defineTemplate($class) {
		if(is_string($class) && class_exists($class)) {
			$reflection = new \Addendum\ReflectionAnnotatedClass($class);
			
			$ret = array();
			$ret []= '(deftemplate '.$class;
			foreach(get_class_vars($class) as $slot => $v) {
				if($reflection->getProperty($slot)->hasAnnotation('Clips\\Multi')) {
					$ret []= '(multislot '.$slot.')';
				}
				else
					$ret []= '(slot '.$slot.')';
			} 
			return implode(' ', $ret).')';
		}
		return false;
	}

	/**
	 * Define the template slot
	 */
	public function defineSlot($name, $type = 'slot', $default = null, $constraints = array()) {
		$slot = array();
		$slot []= '('.$type; // Add the slot define
		$slot []= $name;
		if($default !== null) {
			$slot []= '(default '.$default.')';
		}

		foreach($constraints as $c) {
			if(isset($c['type'])) {
				switch($c['type']) {
				case 'range':
				case 'cardinality': // For range and cardinality, we use 2 parameters
					$slot []= '('.$c['type'].' '.$c['begin'].' '.$c['end'].')'; // Default is (type value)
					break;
				default:
					$slot []= '('.$c['type'].' '.$c['value'].')'; // Default is (type value)
				}
			}
		}
		return implode(' ', $slot).')';
	}

	/**
	 * Define a clips class
	 */
	public function defineClass($class, $parents, $abstract = false, $slots = null, $methods = null) {
		$ret = array();
		$ret []= '(defclass '.$class;
		$p = $parents;
		if(is_array($parents)) {
			$p = implode(' ', $parents);
		}

		$ret []= '(is-a '.$p.')';

		if($abstract) {
			$ret []= '(role abstract)';
		}
		else {
			$ret []= '(role concrete)';
		}

		if($slots != null) {
			foreach($slots as $slot) {
				if(is_string($slot))
					$ret []= $this->defineSlot($slot);
				else
					$ret []= $this->defineSlot(
						$slot['name'], 
						get_default($slot, 'type', 'slot'),
						get_default($slot, 'default'),
						get_default($slot, 'constraints', array())
					);
			}
		}

		return implode(' ', $ret).')';
	}

	//======================================================================
	//
	// Clips Runtime commands
	//
	//======================================================================

	/**
	 * Reset the clips runtime
	 */
	public function reset() {
		$this->command('(reset)');
	}

	/**
	 * Clear all the defines, this will clear the clip's context also
	 */
	public function clear() {
		foreach(Engine::$context as $key => $value) {
			unset(Engine::$context[$key]);
		}
		$this->command('(clear)');
		$this->_init_base_support();
	}

	/**
	 * Print the template's details
	 */
	public function printTemplate($name) {
		if(is_array($name)) {
			foreach($name as $n) {
				$this->printTemplate($name);
			}
		}
		else
			$this->command("(ppdeftemplate $name)");
	}

	/**
	 * List the templates in the enviroment
	 */
	public function listTemplates() {
		$this->command('(list-deftemplates)');
	}

	/**
	 * List all the rule names in the envrionment
	 */
	public function listRules() {
		$this->command('(list-defrules)');
	}

	/**
	 * Print the rule
	 */
	public function printRule($name) {
		if(is_array($name)) {
			foreach($name as $n) {
				$this->printRule($n);
			}
		}
		else
			$this->command('(ppdefrule '.$name.')');
	}

	/**
	 * List the agenda of the clips
	 */
	public function agenda() {
		$this->command('(agenda)');
	}

	/**
	 * List all the templates in the clips context
	 */
	public function templates() {
		$this->command('(list-deftemplates)');
	}


	/**
	 * Show facts in the clips can use the args as filter
	 */
	public function facts() {
		$str = array('(facts');
		if(func_num_args()) {
			foreach(func_get_args() as $arg) {
				$str []= $arg;
			}
		}
		return $this->command(implode(' ', $str).')');
	}

	/**
	 * Run the clips context
	 */
	public function run() {
		$this->command('(run)');
	}

	//======================================================================
	//
	// Misc methods
	//
	//======================================================================

	public function __get($key) {
		if(isset(Engine::$context[$key])) {
			return Engine::$context[$key];
		}
		return $this->$key;
	}

	public function __set($key, $value) {
		Engine::$context[$key] = $value;
		if(!$this->instanceExists($key)) {
			$this->command($this->defineInstance($key));
		}
	}

	protected function prompt($continue = false) {
		if($continue)
			return '... ';
		return 'clips$ ';
	}

	protected function isComplete($line) {
		return true;
	}

	protected function doRun($line) {
		$this->command($line, true);
	}

	public function instanceExists($name) {
		if($name)
			return clips_instance_exists($name);
		return false;
	}

	public function classExists($class) {
		if($class)
			return clips_class_exists($class);
		return false;
	}

	public function templateExists($template) {
		if($template)
			return clips_template_exists($template);
		return false;
	}

	public function assertFacts($data) {
		if(func_num_args() > 1) { // We got multiple args call
			return $this->assertFacts(func_get_args());
		}

		if(!$data || !(is_object($data) || is_string($data) || is_array($data))) { // The data must be array or object or string
			return false;
		}

		if(is_object($data) || // If the data is object
			!isset($data[0]) || // If the data is an hash
			is_string($data[0])) { // Or the first element of the data is string, let the data be an array
			$data = array($data);
		}

		foreach($data as $fact) {
			if(is_object($fact)) { // Add the class as template for the object
				$this->template(get_class($fact));
			}
			$this->command('(assert '.$this->defineFact($fact).')');
		}
		return true;
	}

	public function defineFacts($name, $data) {
		$ret = array();
		$ret []= '(deffacts '.$name;
		foreach($data as $fact) {
			$ret []= $this->defineFact($fact);
		}
		$this->command(implode(' ', $ret).')');
	}

	/**
	 * Execute the clips command
	 */
	public function command($command, $debug = false) {
		if(is_array($command)) {
			foreach($command as $c) {
				$this->command($c);
			}
			return;
		}
		if($command)
			clips_exec($command."\n", $debug); // Add \n automaticly
	}

	public function queryFacts($name = null) {
		$arr = array();
		if(!$name)
			return clips_query_facts($arr);
		return clips_query_facts($arr, $name);
	}

	public function queryOneFact($name = null) {
		$ret = $this->queryFacts($name);
		if(count($ret))
			return $ret[0];
		return null;
	}

	public function switchMain() {
		return $this->switchEnv(CLIPS_MAIN_ENV);
	}

	public function switchCore() {
		return $this->switchEnv(CLIPS_CORE_ENV);
	}

	/**
	 * Load and execute the clips rule file
	 */
	public function load($file) {
		if($this->current_env == CLIPS_CORE_ENV) { // If is the core, let's load the file directly
			if(is_array($file)) {
				foreach($file as $f) {
					$this->load($f);
				}
			}
			else {
				if(file_exists($file)) {
					clips_load($file);
				}
			}
			return;
		}

		if(!is_array($file)) {
			$file = array($file);
		}

		// Getting the args for loading
		$facts = array();
		foreach($file as $f) {
			$facts []= array('load_arg', $f);
		}

		// Calculating the loading rules using CORE env
		$commands = $this->runWithEnv(CLIPS_CORE_ENV, function($clips, $facts) {
			$clips->reset(); // Reset the envrionment for calculate
			$clips->assertFacts($facts);
			$clips->run();
			return $clips->queryFacts('command');
		}, $facts);

		foreach($commands as $command) { // Let's run the commands
			$str = '';
			foreach(explode("\n", $command[0]) as $c) {
				$str .= $c."\n";
				if(clips_is_command_complete($str)) {
					$this->command($str);
					$str = '';
				}
			}
		}
	}
}
