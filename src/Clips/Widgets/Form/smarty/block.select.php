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
		if($blank) {
			if(is_bool($blank)) {
				$blank = array();
				$blank[$label] = '-- Please Select --';
				$blank[$value] = '-- Please Select --';
			}
			array_unshift($options, (object) $blank);
		}

		$data = Clips\context('current_form_field_data');


		if(trim($content)) {
			$tpl = 'string:'.trim($content);
		}
		$content = array();
		foreach($options as $key => $option) {
		//	Clips\context('indent_level', 1, true);
			if(isset($tpl)) {
				// We do have template here
				$content []= $template->fetch($tpl, array('option' => $option));
			}
			else {
				if(is_string($key) && !is_string($option)) {
					$l = $key;
					if($data && $data == $option) {
						$default = array('selected');
					}
					else
						$default = array();
					if(is_string($key)) {
						$default['value'] = $option;
					}
				}
				else if(is_string($option)) {
					$l = $option;
					if($data && $data == $option) {
						$default = array('selected');
					}
					else
						$default = array();
					if(is_string($key)) {
						$default['value'] = $key;
					}
				}
				else if(is_array($option)) {
					$l = $option[$label];
					$default = array('value' => $option[$value]);
					if($data && $data == $option[$value]) {
						$default []= 'selected';
					}
				}
				else if(is_object($option)) {
					$l = $option->$label;
					$default = array('value' => $option->$value);
					if($data && $data == $option->$value) {
						$default []= 'selected';
					}
				}
				$content []= Clips\create_tag_with_content('option', $l, $default);
			}
			//Clips\context_pop('indent_level');
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

	Clips\context_pop('indent_level');
	return Clips\create_tag_with_content('select', $content, $params);
}
