<?php namespace Clips\Filters; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

use Clips\AbstractFilter;
use Clips\Interfaces\ToolAware;

/**
 * This filter will initialize the widget v2 together, if there is a cache file for this, will using the
 * cache file instead.
 *
 * @author Jack
 * @version 1.1
 * @date Sat Jun 13 15:58:58 2015
 *
 * @Clips\Library({"fileCache", "sass"})
 */
class WidgetFilter extends AbstractFilter implements ToolAware {

	public function setTool($tool) {
		$this->tool = $tool;
	}

	public function filter_before($chain, $controller, $method, $args, $request) {
		$widgets = \Clips\clips_context('widgetsv2');

		if($widgets) {
			if(!is_array($widgets)) {
				$widgets = array($widgets);
			}

			$name = implode(' ', array_map(function($item){ return get_class($item); }, $widgets));
			$name = 'widget_'.md5($name).'.json';
			$smarty = \Clips\context('smarty');

			if($this->filecache->exists($name) && \Clips\config('widget_cache')) {
				// Let's use this widget cache
				$cache = unserialize($this->filecache->contents($name));

				if(isset($cache['js']))
					$this->tool->context('js', $cache['js']);
				if(isset($cache['jquery'])) {
					$jquery = $cache['jquery'];
					if(!is_array($jquery)) {
						$jquery = array($jquery);
						foreach($jquery as $j) {
							$this->tool->context('jquery_init', $j, true);
						}
					}
					$this->tool->context('js', $cache['js']);
				}
				if(isset($cache['css']))
					$this->tool->context('css', $cache['css']);
				if(isset($cache['scss']))
					$this->tool->context('scss', $cache['scss']);
				if(isset($cache['sass_include']))
					$this->sass->addIncludePath($cache['sass_include']);
				if(isset($cache['context'])) {
					$c = $cache['context'];
					if(!is_array($c)) {
						$c = array($c);
						foreach($c as $cc) {
							$this->tool->context($cc, null, true);
						}
					}
				}
				if(isset($cache['smarty'])) {
					if($smarty) {
						$smarty->setPluginsDir($cache['smarty']);
					}
				}
			}
			else {
				$js = $this->tool->context('js');
				$css = $this->tool->context('css');
				$scss = $this->tool->context('scss');

				$this->tool->context('js', null);
				$this->tool->context('css', null);
				$this->tool->context('scss', null);

				foreach($widgets as $w) {
					$w->init_v2();
				}

				if($js) {
					if(!is_array($js)) {
						$js = array($js);
					}
					foreach($js as $j) {
						$this->tool->context('js', $j, true);
					}
				}

				if($css) {
					if(!is_array($css)) {
						$js = array($css);
					}
					foreach($css as $j) {
						$this->tool->context('css', $j, true);
					}
				}

				if($scss) {
					if(!is_array($scss)) {
						$js = array($scss);
					}
					foreach($scss as $j) {
						$this->tool->context('scss', $j, true);
					}
				}

				$cache = array();
				// Caching smarty's plugins dir
				if($smarty) {
					$cache['smarty'] = $smarty->getPluginsDir();
				}

				$js = \Clips\context('js');
				if($js) {
					$cache['js'] = $js;
				}

				$css = \Clips\context('css');
				if($css) {
					$cache['css'] = $css;
				}

				$jquery = \Clips\context('jquery_init');
				if($css) {
					$cache['jquery'] = $css;
				}

				$cache['sass_include'] = $this->sass->getIncludePaths();

				$scss = \Clips\context('scss');
				if($scss)
					$cache['scss'] = $scss;

				$context = \Clips\context('widget_context');
				if($context) {
					if(!is_array($context)) {
						$context = array($context);
					}
					$cache['context'] = array();
					foreach($context as $c) {
						$cache['context'] []= $c;
					}
				}

				// Save the cache
				$this->filecache->save($name, serialize($cache));
			}
		}
	}
}
