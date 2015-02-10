<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

function smarty_block_field($params, $content = '', $template, &$repeat) {
	if($repeat) {
		Clips\clips_context('indent_level', 1, true);
		Clips\clips_context('current_field', null); // Remove the last current field
		$form = Clips\clips_context('form');
		if($form) {
			$field = Clips\get_default($params, 'field', null);

			if(!isset($field)) {
				Clips\show_error('No field configured for the field plugin!');
			}

			$f = $form->field($field);
			if(!isset($f)) {
				Clips\show_error('No field configuration found for field %s!', $field);
			}
			// Put the current field to the context
			Clips\clips_context('current_field', $f);
		}
		else {
			Clips\show_error('No form configuration found for this field!');
		}
	}
	else {
		if(Clips\clips_context('current_field')) {
			$f = Clips\clips_context('current_field');

			$layout = Clips\get_default($params, 'layout', array());
			$label_layout_class = 'col-1280-2';
			$element_layout_class = 'col-1280-10';

			if(is_array($layout)) {
				if(isset($layout['label']))
					$label_layout_class = 'col-1280-'.$layout['label'];
				if(isset($layout['element']))
					$element_layout_class = 'col-1280-'.$layout['element'];
				if(isset($layout['mobile-label']))
					$label_layout_class .= ' col-320-'.$layout['mobile-label'];
				if(isset($layout['mobile-element']))
					$element_layout_class .= ' col-320-'.$layout['mobile-element'];
			}
			else {
				// Let the programmer mind the layout himself
				return Clips\create_tag_with_content('div', $content, $params, array('class' => array('form-group', 'control-group')));
			}

			$labelClass = Clips\get_default($params, 'labelClass', 'pinet-form-label');
			$inputClass = Clips\get_default($params, 'inputClass', 'pinet-form-input');
			$glyphicon = Clips\get_default($params, 'glyphicon', null);

			if($glyphicon) {
				// Add the glyphicon
				$content .= Clips\create_tag_with_content('span', '', array('class' => array('glyphicon', 'glyphicon-'.$glyphicon, 'form-control-feedback')));
			}

			if(trim($content) == '') {
				Clips\require_widget_smarty_plugin('Form', 'input');	
				$content = smarty_function_input(array(), $template);
			}

			$label = "\t".Clips\create_tag_with_content('label', $f->label, array(
				'for' => $f->getId(),
				'class' => array($label_layout_class, $labelClass, 'control-label', isset($f->required)?'form_field_required':'')
			));

			$level = Clips\clips_context('indent_level');
			if($level === null)
				$level = 0; // Default level is 0
			else
				$level = count($level);
			$indent = '';
			for($i = 0; $i < $level; $i++) {
				$indent .= "\t";
			}
			// Add the element div
			$content = $label."\n".$indent.Clips\create_tag_with_content('div', $content, array('class' => array($element_layout_class, $inputClass)));

			// Added the help block
			$content .= "\n".$indent.Clips\create_tag_with_content('p', '', array('class' => 'help-block'));
			
			// Altogether
			Clips\context_pop('indent_level');
			return Clips\create_tag_with_content('div', $content, $params, array('class' => array('form-group', 'control-group')));
		}
	}

}
