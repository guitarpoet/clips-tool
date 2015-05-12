<?php in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

function smarty_block_select($params, $content = '', $template, &$repeat) {
	if($repeat) {
		Clips\context('indent_level', 1, true);
		return;
	}

	$options = Clips\get_default($params, 'options', array());
	$label = Clips\get_default($params, 'label-field', 'label');
	$value = Clips\get_default($params, 'value-field', 'value');
	$blank = Clips\get_default($params, 'blank');
	$prepend = Clips\get_default($params, 'prepend');
	$append = Clips\get_default($params, 'append');
	$default = array();

	$field = Clips\context('current_field');

	if($field) {
		if(isset($field->cascade_model)) { 
			$tool = Clips\get_clips_tool();
			$model = $tool->model($field->cascade_model);
			if($model) { // We do have a cascade model
				$cascade_field = Clips\get_default($field, 'cascade_field', null);
				if($cascade_field) {
					$cascade_data = Clips\get_default(Clips\context('current_form_data'), $cascade_field);

					$cascade_method = Clips\get_default($field, 'cascasde_method', 'list'.ucfirst($field->field));
					$options = $model->$cascade_method($cascade_data);
				}
			}
		}
	}

	if($options) {
		if($prepend) {
			$prepend = array_reverse($prepend);
			foreach($prepend as $k => $v) {
				if(is_string($k)) { // This is key => value
					$d = array();
					$d[$label] = $k;
					$d[$value] = $v;
					array_unshift($options, (object) $d);
				}
				else {
					if(is_array($v) || is_object($v)) { // This array
						array_unshift($options, (object) $v);
					}
					else { // This is string
						array_unshift($options, $v);
					}
				}
			}
		}
		if($append) {
			foreach($append as $k => $v) {
				if(is_string($k)) { // This is key => value
					$d = array();
					$d[$label] = $k;
					$d[$value] = $v;
					$options []= (object)$d;
				}
				else {
					if(is_array($v) || is_object($v)) { // This array
						$options []= (object)$v;
					}
					else { // This is string
						$options []= $v;
					}
				}
			}
		}

		if($blank) {
			if(is_bool($blank)) {
				$blank = array();
				$blank[$label] = '-- Please Select --';
				$blank[$value] = '-- Please Select --';
			}
			array_unshift($options, (object) $blank);
		}

		$data = Clips\context_pop('current_form_field_data');

		if(trim($content)) {
			$tpl = 'string:'.trim($content);
		}
		$content = array();
		foreach($options as $key => $option) {
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
		$content = implode("\n$indent", $content);
		unset($params['options']);
	}

	$f = Clips\clips_context('current_field');
	if($f) {
		$default = $f->getDefault($default);
	}

	Clips\context_pop('indent_level');
	return Clips\create_tag_with_content('select', $content, $params, $default);
}
