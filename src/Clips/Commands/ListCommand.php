<?php namespace Clips\Commands; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

class ListCommand extends \Clips\Command {
	private function descCommand($command) {
		return $command->getPathName();
	}

	private function fetchCommands($dir) {
		$iterator = new \DirectoryIterator($dir);
		$commands = array();

		foreach ($iterator as $info) {
			if ($info->isFile() && $info->getExtension() == 'php') {
				$commands []= $this->descCommand($info);
			} 
			else if($info->isDir() && !$info->isDot()) {
				array_merge($commands, $this->fetchCommands($info->getPathname()));
			}
		}
		return $commands;
	}

	public function execute($args) {
		$tool = &get_clips_tool();
		$arr = array();
		foreach($tool->listLoadDirs() as $dir) {
			if($dir) {
				foreach($this->fetchCommands($dir) as $d) {
					$command = str_replace('clips_', '', substr($d, strlen($dir) + 1, strlen($d) - strlen($dir) - 12));
					$arr []= array('command' => $command, 'desc' => $tool->descCommand($command));
				}
			}
		}
		$script = basename(array_shift($args));
		clips_out('list', array('app' => $script, 'commands' => $arr));
	}
}
