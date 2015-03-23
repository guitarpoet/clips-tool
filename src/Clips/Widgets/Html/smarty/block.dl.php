<?php in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

function smarty_block_dl($params, $content = '', $template, &$repeat) {
	if($repeat) {
		Clips\clips_context('indent_level', 1, true);
		return;
	}

	$items = Clips\get_default($params, 'items', array());

	if($items) {
		unset($params['items']);
		$level = Clips\context('indent_level');
		if($level === null)
			$level = 0; // Default level is 0
		else
			$level = count($level);

		$indent = '';
		for($i = 0; $i < $level; $i++) {
			$indent .= "\t";
		}

		if(!trim($content)) {
			$content = '{dt}{$key}{/dt}'."\n$indent".'{dd}{$value}{/dd}';
		}

		$output = array();
		$t = 'string:'.$content;
		foreach($items as $key => $value) {
			$output []= trim($template->fetch($t, array('key' => $key, 'value' => $value)));
		}
		$content = implode("\n$indent", $output);
	}

	Clips\context_pop('indent_level');
	return Clips\create_tag_with_content('dl', $content, $params);
}
