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

		$arr = array();
		mmseg_tokenize($str, $arr);
		foreach($arr as $t) {
			switch($t[0]) {
			case "TOKEN":
				echo "$t[1]/x";
				break;
			case "STOP_WORD":
				echo "$t[1]/s";
				break;
			case "LINE_BREAK":
				echo "\n";
				break;
			}
		}
	}

	public function execute($args) {
		if(count($args) > 2) {
			array_shift($args); // The clips script
			array_shift($args); // The command
			foreach($args as $p) {
				$this->segment($p);
			}
		}
	}
}
