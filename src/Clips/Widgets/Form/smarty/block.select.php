<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

function smarty_block_select($params, $content = '', $template, &$repeat) {
	if($repeat) {
		Clips\context('indent_level', 1, true);
		return;
	}

	$options = Clips\get_default($params, 'options', array());
	$label = Clips\get_default($params, 'label-field', 'label');
	$value = Clips\get_default($params, 'value-field', 'value');
	$blank = Clips\get_default($params, 'blank');

	if($options) {
		$content = array();
		if($blank) {
			if(is_bool($blank)) {
				$blank = array();
				$blank[$label] = '-- Please Select --';
				$blank[$value] = -1;
			}
			array_unshift($options, $blank);
		}
		foreach($options as $option) {
			Clips\context('indent_level', 1, true);
			if(is_string($option)) {
				$content []= Clips\create_tag_with_content('option', $option);
			}
			else if(is_array($option)) {
				$content []= Clips\create_tag_with_content('option', $option[$label], array('value' => $option[$value]));
			}
			else if(is_object($option)) {
				$content []= Clips\create_tag_with_content('option', $option->$label, array('value' => $option->$value));
			}
			Clips\context_pop('indent_level');
		}

		$level = Clips\context('indent_level');
		if($level === null)
			$level = 0; // Default level is 0
		else
			$level = count($level);
		$indent = '';
		for($i = 0; $i < $level; $i++) {
			$indent .= "\t";
		}
		$content = implode("\n\t$indent", $content);
		unset($params['options']);
	}

	$ret = Clips\create_tag_with_content('select', $content, $params);
	Clips\context_pop('indent_level');
	return $ret;
}
