<?php namespace Clips\Commands; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

class SassCommand extends \Clips\Command {
	public function execute($args) {
		$tool = &\Clips\get_clips_tool();
		$sass = $tool->library('sass');
		$sass->addIncludePath(getcwd());
		if(count($args) == 1 && $args['0'] == 'console')
			$sass->console();
		else
			echo $sass->compile($args);
	}
}
