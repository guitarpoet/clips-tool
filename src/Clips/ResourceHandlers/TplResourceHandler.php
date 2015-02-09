<?php namespace Clips\ResourceHandlers; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

class TplResourceHandler extends \Clips\BaseResourceHandler {
	public function __construct() {
		parent::__construct();
	}

	public function getTemplate($uri) {
		$tool = &\Clips\get_clips_tool();
		$name = str_replace("tpl://", "", $uri);
		foreach(array(getcwd(), CLIPS_TOOL_PATH) as $path) { // Test for clips tool's path and the cwd
			foreach($tool->config->template_dir as $dir) {
				$file_name = $path.'/'.$dir.'/'.$name.'.tpl';
				if(file_exists($path.'/'.$dir.'/'.$name.'.tpl')) {
					return $file_name;
				}
			}
		}
		return null;
	}

	public function openStream($uri) {
		$path = $this->getTemplate($uri);
		if($path)
			return fopen($path, 'r');
		return null;
	}

	public function contents($uri) {
		$path = $this->getTemplate($uri);
		if($path)
			return file_get_contents($path);
		return null;
	}
}
