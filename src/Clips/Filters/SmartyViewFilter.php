<?php namespace Clips\Filters; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

use Clips\Interfaces\Initializable;
use Clips\Interfaces\ToolAware;

/**
 * The view filter using smarty
 *
 * @author Jack
 * @date Fri Feb 20 22:01:12 2015
 */
class SmartyViewFilter extends ViewFilter implements Initializable, ToolAware {

	public function init() {
		$this->engine = \Clips\context('smarty');
        $this->engine->template_dir = \Clips\clips_config('template_dir');
		$this->engine->compile_dir = \Clips\clips_config('smarty_compile_dir')[0];
		$this->engine->config_dir = \Clips\clips_config('smarty_config_dir');
        $this->engine->cache_dir = \Clips\clips_config('smarty_cache_dir')[0];
        $this->engine->addPluginsDir(\Clips\clips_config('smarty_plugin_dir', array()));
	}

	public function setTool($tool) {
		$this->tool = $tool;
	}

	protected function doRender($controller, $method, $args, $request, $template, $data, $controller_ret) {
		$this->engine->assign($data);
		echo $this->engine->fetch($template.'.tpl');
	}
}
