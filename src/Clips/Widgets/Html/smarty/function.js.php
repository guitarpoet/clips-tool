<?php in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

function smarty_function_js($params, $template) {
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
		return implode("\n\t\t", $output);
	}
	else {
		return '';
	}
}
