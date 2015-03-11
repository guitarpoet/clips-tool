<?php in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

function smarty_block_row($params, $content = '', $template, &$repeat) {
	if($repeat) {
		Clips\context('indent_level', 1, true);
		$level = Clips\context('indent_level');
		$arr = array(
			'level' => count($level),
			'index' => 0);

		$keys = array();
		foreach($params as $k => $v) {
			if(strpos($k, 'layout') === 0) { // The key begin with layout
				$keys []= $k;
				$arr[$k] = $v;
			}
		}

		foreach($keys as $k) {
			unset($params[$k]);
		}
		// Push row's information into context stack
		Clips\context('row', (object)$arr, true);
		return;
	}

	$keys = array();
	foreach($params as $k => $v) {
		if(strpos($k, 'layout') === 0) { // The key begin with layout
			$keys []= $k;
		}
	}

	foreach($keys as $k) {
		unset($params[$k]);
	}

	Clips\context_pop('row');
	Clips\context_pop('indent_level');
	return Clips\create_tag_with_content('div', $content, $params, array('class' => 'row'));
}
