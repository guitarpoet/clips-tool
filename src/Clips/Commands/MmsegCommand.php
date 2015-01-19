<?php namespace Clips\Commands; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

class MmsegCommand extends \Clips\Command {
	const DICT_NAME = "uni.lib";
	const SYNO_DICT_NAME = "synonyms.dat";
	const THESAURUS_DICT_NAME = "thesaurus.lib";

	public function execute($args) {
		if(count($args) == 0) {
			$this->error("Argument is not enough for command mmseg!");
			return;
		}

		if(count($args) == 1) {
			if(file_exists($args[0])) {
				$cmd = "dict";
				$input = $args[0];
				$output = null;
			}
			else {
				$this->error("Argument is not enough for command mmseg!");
				return;
			}
		}
		else if(count($args) == 2) {
			if(file_exists($args[0])) {
				$cmd = "dict";
				$input = $args[0];
				$output = $args[1];
			}
			else {
				$cmd = $args[0];
				$input = $args[1];
				$output = null;
			}
		}
		else {
			$cmd = $args[0];
			$input = $args[1];
			$output = $args[2];
		}

		switch($cmd) {
		case "dict":
			if(!isset($output))
				$output = MmsegCommand::DICT_NAME;
			mmseg_create_dict($input, $output);
			break;
		case "syno":
			if(!isset($output))
				$output = MmsegCommand::SYNO_DICT_NAME;
			mmseg_create_dict($input, $output);
			break;
		case "thes":
			if(!isset($output))
				$output = MmsegCommand::THESAURUS_DICT_NAME;
			mmseg_create_dict($input, $output);
			break;
		}
		echo "Saved to file $output!\n";
	}
}
