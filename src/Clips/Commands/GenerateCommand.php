<?php namespace Clips\Commands; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

use Clips\Command;

/**
 * @Clips\Library("scaffold")
 */
class GenerateCommand extends Command {

	public function dump($ret, $type) {
		$ret['date'] = \Clips\timestamp();
		$this->output(\Clips\clips_out($type, $ret, false));
	}

	public function bcap() {
		$this->output('Generating bcap file...'.PHP_EOL);
		$path = \Clips\cache_filename(BCAP_FILENAME);
		$b = new \phpbrowscap\Browscap(dirname($path));
		$b->localFile = $path;
		$b->lowercase = true;
		$b->getBrowser();
		$this->output("Done!");
	}

	public function dump_widget($ret) {
		$this->dump($ret, 'widget');
	}

	public function dump_command($ret) {
		$ret['command'] = ucfirst($ret['command']);
		$this->dump($ret, 'command');
	}

	protected function tryFolder($folder, $file) {
		if(!file_exists($folder)) {
			mkdir($folder, 0755, true);
		}

		$path = \Clips\path_join($folder, $file);

		if(\Clips\try_path($path)) { // If we can't find this file.
			$this->output('File %s exists!'.PHP_EOL, $path);
			return false;
		}
		
		return $path;
	}

	public function view() {
		$config = \Clips\interactive('interactive/view', $this);
		$data = \Clips\yaml('migrations/schemas/'.$config->schema.'.yml');
		if($data) {
			$this->scaffold->view($data, $config);
		}
		else {
			$this->output('Can\'t find the schema configuration file for %s!'.PHP_EOL, $config->schema);
		}
		$this->output("Done!".PHP_EOL);
	}

	public function scaffold() {
		$config = \Clips\interactive('interactive/scaffold', $this);
		$data = \Clips\yaml('migrations/schemas/'.$config->schema.'.yml');
		if($data) {
			$this->scaffold->gen($data, $config);
		}
		else {
			$this->output('Can\'t find the schema configuration file for %s!'.PHP_EOL, $config->schema);
		}
		$this->output("Done!".PHP_EOL);
	}

	public function form() {
		$config = \Clips\interactive('interactive/form', $this);
		$data = \Clips\yaml('migrations/schemas/'.$config->schema.'.yml');
		if($data) {
			$this->scaffold->form($data, $config);
		}
		else {
			$this->output('Can\'t find the schema configuration file for %s!'.PHP_EOL, $config->schema);
		}
		$this->output("Done!".PHP_EOL);
	}

	public function model() {
		$config = \Clips\interactive('interactive/model', $this);
		$data = \Clips\yaml('migrations/schemas/'.$config->schema.'.yml');
		if($data) {
			$this->scaffold->model($data, $config);
		}
		else {
			$this->output('Can\'t find the schema configuration file for %s!'.PHP_EOL, $config->schema);
		}
		$this->output("Done!".PHP_EOL);
	}

	public function command() {
		$config = \Clips\interactive('interactive/command', $this);
		$config->date = strftime("%a %b %e %H:%M:%S %Y");

		$path = $this->tryFolder($config->folder, $config->command.'Command.php');

		if(!$path) {
			$this->output("Generate Failed!".PHP_EOL);
			return -1;
		}

		file_put_contents($path, \Clips\clips_out('command', $config, false));
		$this->output('Done!'.PHP_EOL);
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

	public function pagination() {
		$config = \Clips\interactive('interactive/pagination', $this);
		$data = \Clips\yaml('migrations/schemas/'.$config->schema.'.yml');
		if($data) {
			$this->scaffold->pagination($data, $config);
		}
		else {
			$this->output('Can\'t find the schema configuration file for %s!'.PHP_EOL, $config->schema);
		}
		$this->output("Done!".PHP_EOL);
	}

	public function controller() {
		$config = \Clips\interactive('interactive/controller', $this);
		$data = \Clips\yaml('migrations/schemas/'.$config->schema.'.yml');
		if($data) {
			$this->scaffold->controller($data, $config);
		}
		else {
			$this->output('Can\'t find the schema configuration file for %s!'.PHP_EOL, $config->schema);
		}
		$this->output("Done!".PHP_EOL);
	}

	public function execute($args) {
		if($args) {
			$this->tool->helper('console');
			$method = $args[0];
			if(method_exists($this, $method)) {
				return call_user_func(array($this, $method));
			}
			else {
				$this->output("Can't find command for $method".PHP_EOL);
				return -1;
			}
		}
	}
}
