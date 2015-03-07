<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

function smarty_block_field($params, $content = '', $template, &$repeat) {
	Clips\require_widget_smarty_plugin('Html', 'div');	
	Clips\require_widget_smarty_plugin('Html', 'label');

	// Global variables
	$labelClass = Clips\get_default($params, 'label-class', 'form-label');
	$inputClass = Clips\get_default($params, 'input-class', 'form-input');
	$glyphicon = Clips\get_default($params, 'glyphicon', null);

	if($repeat) { // The header part
		// Init the context
		Clips\context('indent_level', 1, true);
		Clips\context('indent_level', 1, true); // Indent for the inner div
		Clips\context_pop('current_field'); // Remove the last current field

		// Getting the form
		$form = Clips\context('form');
		if($form) {
			// Getting the field
			$field = Clips\get_default($params, 'field', null);

			if(!isset($field)) {
				Clips\show_error('No field configured for the field plugin!');
				return;
			}

			$f = $form->field($field);
			if(!isset($f)) {
				Clips\show_error('No field configuration found for field %s!', $field);
				return;
			}

			// Put the field to context
			Clips\context('current_field', $f);


			// Processing the form data
			$data = Clips\get_default(Clips\context('current_form_data'), $field);
			if($data) {
				Clips\context('current_form_field_data', $data);
				// Update the field's value to data 
				$f->value = $data;
			}

			// Processing the states
			$state = Clips\get_default($params, 'state');

			if(!$state) {
				$state = Clips\field_state($f);
			}

			if($state) {
				Clips\context('current_form_field_state', $state);
			}

		}
		else {
			Clips\show_error('No form configuration found for this field!');
		}
	}
	else {
		if(Clips\clips_context('current_field')) {
			Clips\context_pop('indent_level');
			$f = Clips\clips_context('current_field');

			if(\Clips\get_default($f, 'state') == 'none' || $f->state == 'none') {
				Clips\context_pop('indent_level');
				return '';
			}

			if(\Clips\get_default($f, 'hidden') || $f->state == 'hidden') {
				Clips\context_pop('indent_level');
				Clips\require_widget_smarty_plugin('Form', 'input');	
				return smarty_function_input(array('type' => 'hidden'), $template);
			}
			// Now for rendering
			$ret = array();

			// Render the icon
			if($glyphicon) {
				// Add the glyphicon
				$ret []= Clips\create_tag_with_content('span', '', array('class' => array('glyphicon', 'glyphicon-'.$glyphicon, 'form-control-feedback')), array(), true);
			}

			// Render the label
			$r = true;
			smarty_block_label(array(), '', $template, $r); // Skip the label head
			$r = false;
			$ret []= smarty_block_label(array(
				'for' => $f->getId(),
				'class' => array($labelClass, 'control-label', isset($f->required)?'form_field_required':'')
			), $f->label, $template, $r);

			// Render the input row head
			$r = true;
			smarty_block_div(array(), '', $template, $r); // Skip the div head

			// If no input set, using default input
			if(trim($content) == '') {
				Clips\require_widget_smarty_plugin('Form', 'input');	
				$content = smarty_function_input(array(), $template);
			}

			// Close the input div
			$ret []= smarty_block_div(array('class' => $inputClass), $content, $template, $repeat);

			// Added the help block
			$ret []= Clips\create_tag_with_content('p', '', array('class' => 'help-block'), array(), true);

			$level = Clips\context('indent_level');
			if($level === null)
				$level = 0; // Default level is 0
			else
				$level = count($level);

			$indent = '';
			for($i = 0; $i < $level; $i++) {
				$indent .= "\t";
			}
			
			// Altogether
			Clips\context_pop('indent_level');
			CLips\context_pop('current_form_field_data');
			CLips\context_pop('current_form_field_state');
			return Clips\create_tag_with_content('div', implode("\n$indent", $ret), $params, array('class' => array('form-group', 'control-group')));
		}
	}
}
