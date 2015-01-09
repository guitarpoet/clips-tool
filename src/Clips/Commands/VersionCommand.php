<?php namespace Clips\Commands; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

class VersionCommand extends \Clips\Command {
	public function execute($args) {
		$tool = &get_clips_tool();
		$tool->template
		clips_out get_clips_tool()->config->version[0];
	}
}
