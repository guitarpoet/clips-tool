<?php namespace Clips\Commands; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

use Clips\Command;

class GenerateCommand extends Command {

	public function dump($ret) {
		$ret['date'] = strftime("%a %b %e %H:%M:%S %Y");
		$this->output(\Clips\clips_out('widget', $ret, false));
	}

	public function command() {
		$config = \Clips\interactive('interactive/command', $this);
		$config->date = strftime("%a %b %e %H:%M:%S %Y");

		// Setup the Command folder
		$folder = $config->folder;
		if(!file_exists($folder)) {
			mkdir($folder, 0755, true);
		}

		$name = ucfirst($config->command)."Command";
		$path = \Clips\path_join($folder, $name.'.php');
		$config->name = $name;

		file_put_contents($path, \Clips\clips_out('command', $config, false));
		echo 'Done!';
	}

	public function widget() {
		$config = \Clips\interactive('interactive/widget', $this);
		$config->date = strftime("%a %b %e %H:%M:%S %Y");
		$config->widget = ucfirst($config->widget);
		$widget_dir = \Clips\clips_config('widget_dir');
		foreach($widget_dir as $p) {
			$path = \Clips\try_path($p);
			if($path)
				break;
		}
		if(!$path) {
			$this->output('Can\'t find any widget directory, generation failed!');
			return -1;
		}

		$dir = \Clips\path_join($path, $config->widget);
		if(\file_exists($dir)) {
			$this->output('Widget %s exists!', $config->widget);
			return -1;
		}

		// Make the widget dir first
		mkdir($dir, 0755, true);

		// Write the widget configuration
		\file_put_contents(\Clips\path_join($dir, 'widget.json'), \Clips\clips_out('widget', $config, false));
		
		// Write the widget class
		\file_put_contents(\Clips\path_join($dir, 'Widget.php'), \Clips\clips_out('widget_class', $config, false));
		$this->output('Done!');
	}

	public function execute($args) {
		if($args) {
			$this->tool->helper('console');
			switch($args[0]) {
			case 'widget':
				return $this->widget();
			case 'command':
				return $this->command();
			}
		}
	}
}
