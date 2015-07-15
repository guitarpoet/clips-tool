<?php namespace Clips\Widgets\Babeljs; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

class Widget extends \Clips\Widget {
	protected function loadConfig() {
		parent::loadConfig();
		if(\Clips\config('babel')) {
			$this->config['js']['files'] = array('browser-polyfill.js');
		}
	}
}
