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
		if(strpos($this->base_dir, FCPATH) === false) {
			// We might be in the soft link(in development mode)
			$base_dir = dirname(dirname(dirname(class_script_path('Clips\\Widget'))));
			$this->rel_dir = path_join('vendor/guitarpoet/clips-tool/', substr($this->base_dir, strlen($base_dir)));
		}
		else
			$this->rel_dir = substr($this->base_dir, strlen(FCPATH));
		$this->sass = $this->tool->library('sass');
		$this->loadConfig();
		$this->initDepends();
		$this->initTemplateEngine();
		$this->initScss();
		$this->initCss();
		$this->initJs();
		$this->initContext();
	}

	public function setTool($tool) {
		$this->tool = $tool;
	}

	public function setLogger(\Psr\Log\LoggerInterface $logger) {
		$this->logger = $logger;
	}

	protected function initContext() {
		if(isset($this->config->context)) {
			clips_context($this->config->context, null, true);
        }
	}

	protected function initDepends() {
		if(isset($this->config->depends)) {
			$this->tool->widget($this->config->depends);
        }
	}

	protected function initTemplateEngine() {
		$smarty = clips_context('smarty');
		if($smarty) {
			$smarty->addPluginsDir(path_join($this->base_dir, 'smarty'));
		}
	}

	protected function initScss() {
		// We should have sass in the context
		$sass = $this->sass;
		if($sass && isset($this->config->scss)) {
			// Add the include path, so that others can just import the scss this widget provided
			$sass->addIncludePath(path_join($this->base_dir, 'scss'));

			$scss_config = $this->config->scss;
            if(isset($scss_config->depends)) {
                foreach($scss_config->depends as $d) {
					clips_add_scss($d);
				}				
			}			

			// Add the scss files
			if(isset($scss_config->files)) {
                foreach($scss_config->files as $file) {
					clips_add_scss(path_join($this->base_dir, 'scss', $file));
                }
            }
		}
	}

	protected function initJs() {
		if(isset($this->config->js)) {
			$js_config = $this->config->js;
            if(isset($js_config->depends)) {
                foreach($js_config->depends as $j) {
					clips_add_js($d);
				}				
			}			

			// Add the js files
			if(isset($js_config->files)) {
                foreach($js_config->files as $file) {
					clips_add_js(static_url(path_join($this->rel_dir, 'js', $file)));
                }
            }
		}
	}

	protected function initCss() {
		if(isset($this->config->css)) {
			$css_config = $this->config->css;
            if(isset($css_config->depends)) {
                foreach($css_config->depends as $c) {
					clips_add_css($c);
				}				
			}			

			// Add the css files
			if(isset($css_config->files)) {
                foreach($css_config->files as $file) {
					clips_add_css(static_url(path_join($this->rel_dir, 'css', $file)));
                }
            }
		}
	}

    protected function loadConfig() {
		$config_file = path_join($this->base_dir, 'widget.json');
		if(file_exists($config_file)) {
			$this->config = parse_json(file_get_contents($config_file));
		}
		else {
			throw new WidgetException('Cant\'t find configuration file for widget ' . get_class($this));
		}
	}
}
