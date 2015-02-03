<?php namespace Clips\Filters; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

use Clips\Interfaces\Initializable;
use Clips\Interfaces\ToolAware;

class SmartyViewFilter extends ViewFilter implements Initializable, ToolAware {

	public function init() {
		$this->engine = $this->tool->create('Smarty');
		$this->tool->context('smarty', $this->engine); // Make the smarty public, so that the customize can be easier
        $this->engine->template_dir = clips_config('template_dir');
		$this->engine->compile_dir = clips_config('smarty_compile_dir')[0];
		$this->engine->config_dir = clips_config('smarty_config_dir');
        $this->engine->cache_dir = clips_config('smarty_cache_dir')[0];
        $this->engine->addPluginsDir(clips_config('smarty_plugin_dir', array()));
	}

	public function setTool($tool) {
		$this->tool = $tool;
	}

	protected function doRender($controller, $method, $args, $request, $template, $data, $controller_ret) {
		$this->engine->assign($data);
		echo $this->engine->fetch($template.'.tpl');
	}
}
