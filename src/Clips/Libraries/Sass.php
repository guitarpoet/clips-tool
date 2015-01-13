<?php namespace Clips\Libraries; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

if(!extension_loaded('sass')) {
	show_error('Cant\'t find any sass plugin installed!!');
	die;
}

define("SASS_FORMAT_NESTED", 0);
define("SASS_FORMAT_EXPANDED", 1);
define("SASS_FORMAT_COMPACT", 2);
define("SASS_FORMAT_COMPRESSED", 3);
define("SASS_FORMAT_FORMATTED", 4);

/**
 * The Sass compiler for compiling sass to css
 *
 * @author Jack
 * @date Tue Jan 13 10:53:34 2015
 */
class Sass {

	protected $options = array();

	public $sasses = array();

	public $plugins = array();

	public $error;

	protected $includePathes;

	protected function initPlugins() {
		$tool = &get_clips_tool();
		foreach(clips_config('sass_plugins', array()) as $plugin) {
			$this->plugins []= $tool->library($plugin);
		}
	}

	public function __construct() {
		$this->includePathes = array();
		$this->initPlugins();
	}

	public function __get($props) {
		return $this->options[$props];
	}

	public function __set($props, $value) {
		$this->options[$props] = $value;
	}

	public function version() {
		return sass_version();
	}

	public function addIncludePath() {
		if(func_num_args() == 2 && is_int(func_get_arg(1))) {
			// This must be the add using index
			$p = func_get_arg(0);
			$i = func_get_arg(1);

			if($i > 0 && i <= count($this->includePathes)) {
				$this->includePathes = array_splice($i, 0, $p);
			}
		}
		else {
			foreach(func_get_args() as $path) {
				if(in_array($path, $this->includePathes)) // We already have this scss
					continue;
				$this->includePathes []= $path;
			}
		}
	}

	public function removeIncludePath() {
		$arr = array();
		$exclude = func_get_args();
		foreach($this->includePathes as $path) {
			if(in_array($path, $exclude)) {
				continue;
			}
			$arr []= $path;
		}
		$this->includePathes = $arr;
	}

	public function addSass($file, $index = -1) {
		if(strpos($file, "string://") === false) { // skip string resource
			$pi = pathinfo($file);

			if(!isset($pi['extension']) || $pi['extension'] != 'scss')
				$file .= '.scss';
		}

		if(array_search($file, $this->sasses) === FALSE) {
			if($index == -1)
				$this->sasses []= $file;
			else
				array_splice($this->sasses, $index, 0, $file);
		}
	}

	public function readFile($file) {
		if(strpos($file, "://") !== false) {
			$r = new \Clips\Resource($file);
			return $r->contents();
		}

		foreach($this->includePathes as $path) {
			$filepath = $path.'/'.$file;
			if(file_exists($filepath) && is_file($filepath) &&
				is_readable($filepath)) {
				return file_get_contents($filepath);
			}
		}
		return '';
	}

	protected function precompile() {
		$this->prefix = '';
		$this->suffix = '';
		$this->addSass('variables', 0); // Auto added the variables scss before compile

		foreach($this->plugins as $plugin) {
			if(method_exists($plugin, 'prefix')) {
				$plugin->prefix($this);
			}
		}

		$this->content = $this->prefix."\n";
		foreach($this->sasses as $sass) {
			$this->content .= $this->readFile($sass)."\n";
		}


		foreach($this->plugins as $plugin) {
			if(method_exists($plugin, 'suffix')) {
				$plugin->suffix($this);
			}
		}
		$this->content .= $this->suffix;
		return $this->content;
	}

	public function compile() {
		$args = func_get_args();
		if($args) {
			foreach($args as $sass) {
				$this->addSass($sass);
			}
		}

		$content = $this->precompile();
		$this->include_path = (implode(PATH_SEPARATOR, $this->includePathes));
		return sass_compile("data", $content, $this->options, $this->error);
	}
}
