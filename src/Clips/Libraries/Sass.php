<?php namespace Clips\Libraries; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

if(!extension_loaded('sass')) {
	\Clips\show_error('Cant\'t find any sass plugin installed!!');
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
class Sass extends \Clips\Libraries\ConsoleBase implements \Psr\Log\LoggerAwareInterface {

	protected $options = array();

	public $sasses = array();

	public $plugins = array();

	public $error;

	protected $includePathes;

	public function getIncludePaths() {
		return $this->includePathes;
	}

    public function setLogger(\Psr\Log\LoggerInterface $logger) {
        $this->logger = $logger;
    }

	protected function prompt($continue = false) {
		if($continue)
			return '... ';
		return 'sass$ ';
	}

	protected function isComplete($line) {
		return sass_is_complete('* {'.$line.'}');
	}

	protected function doRun($line) {
		$c = '* {'.$line.'}';
		$ret = sass_compile("data", $c, $this->options, $this->error);
		if($ret)
			echo $ret;	
		else
			echo $this->error;
	}

	protected function initPlugins() {
		$tool = &\Clips\get_clips_tool();
		foreach(\Clips\clips_config('sass_plugins', array()) as $plugin) {
			$this->plugins []= $tool->library($plugin);
		}
	}

	public function __construct() {
		$this->includePathes = array();
		$this->initPlugins();
	}

	public function __get($props) {
		if(isset($this->options[$props]))
			return $this->options[$props];
		return null;
	}

	public function __set($props, $value) {
		$this->options[$props] = $value;
	}

	public function version() {
		return sass_version();
	}

	public function addIncludePath() {
		if(func_num_args() == 1 && is_array(func_get_arg(0))) {
			foreach(func_get_arg(0) as $p) {
				$this->addIncludePath($p);
			}
			return;
		}

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

		// Try the absolute path first
		if(file_exists($file))
			return file_get_contents($file);

		foreach(\Clips\clips_config('sass_folders', array()) as $folder) {
			$r = \Clips\try_path(\Clips\path_join($folder, $file));
			if($r) {
				return file_get_contents($r);
			}
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

		foreach(\Clips\config('sass_preload', array()) as $load) {
			$this->addSass($load, 0); // Auto added the scsses before compile
		}

		foreach($this->plugins as $plugin) {
			if(method_exists($plugin, 'prefix')) {
				$plugin->prefix($this);
			}
		}

		$this->content = $this->prefix."\n";
		foreach($this->sasses as $sass) {
			if(\Clips\config('debug_sass')) {
				$this->content .= "\n".str_pad("/* = Start $sass ", 77, "=")." */\n";
				$name = explode('Widgets/', $sass);
				$name = $name[1];
				$c = $this->readFile($sass);
				$arr = array();
				$line = 1;
				foreach(explode("\n", $c) as $l) {
					if($line % 5 == 1)
						$arr []= $l." // $name:".$line;
					else
						$arr [] = $l;
					$line++;
				}
				$this->content .= implode("\n", $arr);
				$this->content .= "\n".str_pad("/* = End $sass ", 77, "=")." */\n\n";
			}
			else
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
				if(is_array($sass)) {
					foreach($sass as $s) {
						$this->addSass($s);
					}
					continue;
				}
				$this->addSass($sass);
			}
		}

		if(\Clips\config('debug_sass')) {
			$this->logger->debug('Sasses files to compile is ', array($this->sasses));
		}

		$content = $this->precompile();

		$this->include_path = (implode(PATH_SEPARATOR, $this->includePathes));
		$ret = sass_compile("data", $content, $this->options, $this->error);

		if($ret || !$this->error) {
			return $ret;
		}

		$data = explode(":", $this->error);
		if(count($data) > 1) {
			$line = $data[1];
			$data = explode("\n", $content);
			throw new \Exception($this->error." at line -> [".$data[$line - 1]." ]\n");
		}
		else
			throw new \Exception($this->error);
	}
}
