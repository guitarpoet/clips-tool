<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

use Clips\Interfaces\Initializable;
use Clips\Interfaces\ToolAware;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Addendum\Annotation;

/**
 * The widget base class
 *
 * @author Jack
 * @date Tue Feb  3 17:37:49 2015
 */
class Widget extends Annotation implements Initializable, ToolAware, LoggerAwareInterface {

	public function init() {
		context('widgets', $this, true);
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
		$this->initJsx();
		$this->initContext();
		$this->doInit();
	}

	protected function doInit() {
	}

	public function setTool($tool) {
		$this->tool = $tool;
	}

	public function setLogger(LoggerInterface $logger) {
		$this->logger = $logger;
	}

	protected function initContext() {
		$context = get_default($this->config, 'context');
		if($context) {
			// Add this context to context
			context('widget_context', $context, true);
			context($context, null, true);
        }
	}

	protected function initDepends() {
		$depends = get_default($this->config, 'depends');
		if($depends) {
			$this->tool->widget($depends);
        }
	}

	protected function initTemplateEngine() {
		$smarty = context('smarty');
		if($smarty) {
			$smarty->addPluginsDir(path_join($this->base_dir, 'smarty'));
		}
	}

	protected function initScss() {
		// We should have sass in the context
		$sass = $this->sass;
		$scsses = get_default($this->config, 'scss');
		if($sass && $scsses) {
			// Add the include path, so that others can just import the scss this widget provided
			$sass->addIncludePath(path_join($this->base_dir, 'scss'));

			$scss_config = $scsses;
			$depends = get_default($scss_config, 'depends');
            if($depends) {
				if(!is_array($depends)) {
					$depends = array($depends);
				}

				foreach($depends as $d) {
					clips_add_scss($d);
				}
			}			

			// Add the scss files
			$files = get_default($scss_config, 'files');
			if($files) {
				if(!is_array($files))
					$files = array($files);
                foreach($files as $o) {
					if(is_array($o)) {
						foreach($o as $k => $v) {
							// This is file => query part
							if(!browser_match($v)) {
								// If the browser won't match this
								// JavaScript
								continue;
							}
							$file = $k;
							clips_add_scss(path_join($this->rel_dir, 'scss', $file));
						}
						continue;
					}
					$file = $o;
					clips_add_scss(path_join($this->base_dir, 'scss', $file));
                }
            }
		}
	}

	protected function initJsx() {
		$jsx = get_default($this->config, 'jsx');
		if($jsx) {
			$depends = get_default($jsx, 'depends');
            if($depends) {
				if(!is_array($depends))
					$depends = array($depends);
                foreach($depends as $d) {
					add_jsx($d);
				}				
			}			

			// Add the js files
			$files = get_default($jsx, 'files');
			if($files) {
				if(!is_array($files))
					$files = array($files);
					foreach($files as $o) {
						if(is_array($o)) {
							foreach($o as $k => $v) {
								// This is file => query part
								if(!browser_match($v)) {
									// If the browser won't match this
									// JavaScript
									continue;
								}
								$file = $k;
								add_jsx(path_join($this->rel_dir, 'jsx', $file));
							}
							continue;
						}
						$file = $o;
						add_jsx(path_join($this->rel_dir, 'jsx', $file));
					}
				}
		}
	}


	protected function initJs() {
		$js = get_default($this->config, 'js');
		if($js) {
			$depends = get_default($js, 'depends');
            if($depends) {
				if(!is_array($depends))
					$depends = array($depends);
                foreach($depends as $d) {
					clips_add_js($d);
				}				
			}			

			// Add the js files
			$files = get_default($js, 'files');
			if($files) {
				if(!is_array($files))
					$files = array($files);
                foreach($files as $o) {
					if(is_array($o)) {
						foreach($o as $k => $v) {
							// This is file => query part
							if(!browser_match($v)) {
								// If the browser won't match this
								// JavaScript
								continue;
							}
							$file = $k;
							clips_add_js(path_join($this->rel_dir, 'js', $file));
						}
						continue;
					}
					$file = $o;
					clips_add_js(path_join($this->rel_dir, 'js', $file));
                }
            }
		}
	}

	protected function initCss() {
		$css = get_default($this->config, 'css');
		if($css) {
			$depends = get_default($css, 'depends');
            if($depends) {
				if(!is_array($depends))
					$depends = array($depends);

                foreach($depends as $c) {
					clips_add_css($c);
				}				
			}			

			// Add the css files
			$files = get_default($css, 'files');
			if($files) {
				if(!is_array($files))
					$files = array($files);

                foreach($files as $o) {
					if(is_array($o)) {
						foreach($o as $k => $v) {
							// This is file => query part
							if(!browser_match($v)) {
								// If the browser won't match this
								// JavaScript
								continue;
							}
							$file = $k;
							add_css(path_join($this->rel_dir, 'css', $file));
						}
						continue;
					}
					$file = $o;
					clips_add_css(static_url(path_join($this->rel_dir, 'css', $file)));
                }
            }
		}
	}

	/**
	 * Loading the widget configuration in yaml
	 */
    protected function loadConfig() {
		$config_file = path_join($this->base_dir, 'widget.yml');
		if(file_exists($config_file)) {
			$this->config = yaml($config_file);
		}
		else {
			$config_file = path_join($this->base_dir, 'widget.json');
			if(file_exists($config_file)) {
				$this->config = parse_json(file_get_contents($config_file));
			}
			else {
				throw new WidgetException('Cant\'t find configuration file for widget ' . get_class($this));
			}
		}
	}
}
