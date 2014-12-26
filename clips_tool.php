<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

define('CLIPS_TOOL_PATH', dirname(__FILE__));

function get_clips_tool() {
	if(!isset(Clips_Tool::$instance)) {
		Clips_Tool::$instance = new Clips_Tool();
	}
	return Clips_Tool::$instance;
}

function clips_tool_path($path) {
	return CLIPS_TOOL_PATH.$path;
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
		$this->config = call_user_func_array('array_merge', $arr);
	}

	public function __get($property) {
		if(isset($this->config)) {
			return $this->config->$property;
		}
		return false;
	}
}

class Clips_Tool {
	public static $instance;

	public function __construct() {
		$this->clips = new Clips();
		$this->init();
	}

	private function init() {
		$this->clips->runWithEnv(CLIPS_CORE_ENV, function($clips){
			// Load additional rules into core env to support configuration and loading
			$clips->reset();
			$clips->assertFacts(new Clips_Config());
			$clips->load(
				CLIPS_TOOL_PATH.'/config/rules/config.rules',
				CLIPS_TOOL_PATH.'/core/rules/load.rules'
			);
			$clips->run();
			$this->config = $clips->queryfacts('Clips_Config');
			$this->config = $this->config[0];
		});
		$this->config->load();
	}
}
