<?php in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

function smarty_block_column($params, $content = '', $template, &$repeat) {
	if($repeat) {
		Clips\context('indent_level', 1, true);
		return;
	}

	$keys = array();
	$class = array('column');
	foreach($params as $k => $v) {
		if(strpos($k, 'span') === 0) { // The key begin with layout
			if($k == 'span') {
				$class []= 'col-xs-'.$v;
			}
			else {
				$data = explode('-', $k);
				$class []= 'col-'.$data[1].'-'.$v;
			}
			$keys []= $k;
		}
	}

	foreach($keys as $k) {
		unset($params[$k]);
	}

	Clips\context_pop('indent_level');
	return Clips\create_tag_with_content('div', $content, $params, array('class' => $class));
}
