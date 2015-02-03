<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

use Clips\Interfaces\Initializable;
use Clips\Interfaces\ToolAware;
use Psr\Log\LoggerAwareInterface;
use Addendum\Annotation;

/**
 * The widget base class
 *
 * @author Jack
 * @date Tue Feb  3 17:37:49 2015
 */
class Widget extends Annotation implements Initializable, ToolAware, LoggerAwareInterface {

	public function init() {
		$this->base_dir = dirname(class_script_path($this));
		$this->rel_dir = substr($this->base_dir, strlen(FCPATH));
		$this->loadConfig();
		$this->initTemplateEngine();
	}

	public function setTool($tool) {
		$this->tool = $tool;
	}

	public function setLogger(\Psr\Log\LoggerInterface $logger) {
		$this->logger = $logger;
	}

	protected function initDepends() {
	}

	protected function initTemplateEngine() {
		$smarty = clips_context('smarty');
		if($smarty) {
			$smarty->addPluginsDir(path_join($this->base_dir, 'smarty'));
		}
	}

	protected function initScss() {
	}

	protected function initJs() {
	}

	protected function initCss() {
	}

    protected function loadConfig() {
		$config_file = path_join($this->base_dir, 'widget.json');
		if(file_exists($config_file)) {
			$this->config = \parse_json(file_get_contents($config_file));
		}
		else {
			throw new WidgetException('Cant\'t find configuration file for widget ' . get_class($this));
		}
	}
}
