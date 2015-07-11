<?php in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

function smarty_function_js($params, $template) {
	$tool = \Clips\get_clips_tool();
	//Processing jquery init
	$init = \Clips\clips_context('jquery_init');
	if($init) {
		if(!is_array($init)) {
			$init = array($init);
		}

		$init []= "if(typeof initialize === 'function') initialize();";

		\Clips\add_init_js("//====================================\n// The jQuery initialize function \n//====================================\n\n".'jQuery(function($){'."\n\t".implode("\n\t", $init)."\n".'});');
	}

	// Processing js
	$scripts = \Clips\context('js');
	if($scripts) {
		if(!is_array($scripts)) {
			$scripts = array($scripts);
		}

		$output = array();
		$init = array();
		foreach($scripts as $item) {
			if(is_array($item)) {
				$js = explode("\n", $item['script']);
				$ret = array();
				foreach($js as $line) {
					$ret []= "\t".$line;
				}

				$init []= implode("\n\t\t", $ret);
			}
			else if(is_object($item)) {
				$js = explode("\n", $item->script);
				$ret = array();
				foreach($js as $line) {
					$ret []= "\t".trim($line);
				}
				$init []= implode("\n\t\t", $ret);
			}
			else {
				$path = \Clips\safe_add_extension($item, 'js');
				if(strpos($path, 'http:') === false) 
					$path = \Clips\static_url($path);
				$output []= '<script type="text/javascript" src="'.$path.'"></script>';
			}
		}

		$output []= '<script type="text/javascript">'."\n\t\t".implode("\n\t\t", $init)."\n\t\t".'</script>';
		// Added the jsx support for ReactJs
		$jsx = \Clips\context('jsx');
		if($jsx) {
			if(\Clips\config('babel')) { // TODO: This is a little bit ugly, needs refactor....
				$babel = $tool->library('babel');
				foreach($jsx as $item) {
					$path = Clips\try_path(Clips\safe_add_extension($item, 'jsx'));
					if($path) {
						$output []= '<script type="text/javascript" src="'.Clips\static_url($babel->compile($path)).'"></script>';
					}
				}
			}
			else {
				foreach($jsx as $item) {
					$output []= '<script type="text/babel" src="'.Clips\static_url(Clips\safe_add_extension($item, 'jsx')).'"></script>';
				}
			}
		}
		$jsx = \Clips\context('jsx_script');
		if($jsx) {
			if(\Clips\config('babel')) {
				$babel = $tool->library('babel');
				$cache = $tool->library('fileCache');
				$c = \Clips\context('controller');
				$cm = \Clips\context('controller_method');
				$name = $c.'_'.$cm.'.jsx';
				$cache->save($name, $jsx, \Clips\path_join($cache->cacheDir(), 'js'));
				
				$output []= '<script type="text/javascript" src="'.Clips\static_url($babel->compile($cache->cacheDir().'/js/'.$name)).'"></script>';
			}
			else {
				$output []= '<script type="text/babel">'.implode("\n\t\t", $jsx).'</script>';
			}
		}
		return implode("\n\t\t", $output);
	}
	else {
		return '';
	}
}
