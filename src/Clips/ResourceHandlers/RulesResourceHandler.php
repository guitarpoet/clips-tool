<?php namespace Clips\ResourceHandlers; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

use Clips\BaseResourceHandler;

class RulesResourceHandler extends BaseResourceHandler {
	public function __construct() {
		parent::__construct();
	}

	public function getRules($uri) {
		$tool = &\Clips\get_clips_tool();
		$name = str_replace("rules://", "", $uri);
		foreach(array(getcwd(), CLIPS_TOOL_PATH) as $path) { // Test for clips tool's path and the cwd
			foreach($tool->config->rules_dir as $dir) {
				$file_name = $path.'/'.$dir.'/'.$name.'.rules';
				if(file_exists($path.'/'.$dir.'/'.$name.'.rules')) {
					return $file_name;
				}
			}
		}
		return null;
	}

	public function openStream($uri) {
		$path = $this->getRules($uri);
		if($path)
			return fopen($path, 'r');
		return null;
	}

	public function contents($uri) {
		$path = $this->getRules($uri);
		if($path)
			return file_get_contents($path);
		return null;
	}
}
