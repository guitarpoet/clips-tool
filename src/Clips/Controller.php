<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

use Psr\Log\LoggerAwareInterface;
use Clips\Interfaces\ClipsAware;
use Psr\Log\LoggerInterface;
use Clips\Interfaces\ToolAware;
use Clips\Models\ViewModel;

class Controller implements ClipsAware, LoggerAwareInterface, ToolAware {

	public function __construct() {
	}

	public function context($key, $value = null) {
		return $this->tool->context($key, $value);
	}

	public function setTool($tool) {
		$this->tool = $tool;
	}

	public function setClips($clips) {
		$this->clips = $clips;
	}

	public function setLogger(LoggerInterface $logger) {
		$this->logger = $logger;
	}

	public function render($template, $args = array(), $engine = null) {
		if(!$engine) {
			$default = \clips_config('default_view');
			if($default) {
				$engine = $default[0];
			}
		}
		return new ViewModel($template, $args, $engine);
	}
}
