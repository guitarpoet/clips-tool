<?php namespace Clips\Commands; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

if(!extension_loaded('mmseg')) {
	show_error('Cant\'t find any mmseg plugin installed!!');
	die;
}

class SegCommand extends \Clips\Command {

	public function segment($path) {
		if(file_exists($path)) {
			$str = file_get_contents($path);
		}
		else {
			$str = $path;
		}

		$tool = &\Clips\get_clips_tool();
		$mmseg = $tool->library('mmseg');
		$mmseg->tokenize($str, function($type, $token) {
			switch($type) {
			case "TOKEN":
				echo "$token/x";
				break;
			case "STOP_WORD":
				echo "$token/s";
				break;
			case "LINE_BREAK":
				echo $token;
				break;
			}
		});
	}

	public function execute($args) {
		foreach($args as $p) {
			$this->segment($p);
		}
	}
}
