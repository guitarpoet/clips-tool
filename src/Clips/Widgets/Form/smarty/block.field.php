<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

function smarty_block_field($params, $content = '', $template, &$repeat) {
	if($repeat) {
		clips_context('current_field', null); // Remove the last current field
		$form = clips_context('form');
		if($form) {
			$field = get_default($params, 'field', null);

			if(!isset($field)) {
				show_error('No field configured for the field plugin!');
			}

			$f = $form->field($field);
			if(!isset($f)) {
				show_error('No field configuration found for field %s!', $field);
			}
			// Put the current field to the context
			clips_context('current_field', $f);
		}
		else {
			show_error('No form configuration found for this field!');
		}
	}
	else {
		if(clips_context('current_field')) {
			$f = clips_context('current_field');

			$layout = get_default($params, 'layout', array());
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

			$labelClass = get_default($params, 'labelClass', 'pinet-form-label');
			$inputClass = get_default($params, 'inputClass', 'pinet-form-input');
			$glyphicon = get_default($params, 'glyphicon', null);

			if($glyphicon) {
				// Add the glyphicon
				$content .= Clips\create_tag_with_content('span', '', array('class' => array('glyphicon', 'glyphicon-'.$glyphicon, 'form-control-feedback')));
			}

			if(trim($content) == '') {
				require_widget_smarty_plugin('Form', 'input');	
				$content = smarty_function_input(array(), $template);
			}

			$label = Clips\create_tag_with_content('label', $f->label, array(
				'for' => $f->getId(),
				'class' => array($label_layout_class, $labelClass, 'control-label', isset($f->required)?'form_field_required':'')
			));
			// Add the element div
			$content = $label.Clips\create_tag_with_content('div', $content, array('class' => array($element_layout_class, $inputClass)));

			// Added the help block
			$content .= Clips\create_tag_with_content('p', '', array('class' => 'help-block'));
			
			// Altogether
			return Clips\create_tag_with_content('div', $content, $params, array('class' => array('form-group', 'control-group')));
		}
	}

}
