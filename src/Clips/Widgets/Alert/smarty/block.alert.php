<?php in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

\Clips\require_widget_smarty_plugin('html', 'div');

function smarty_block_alert($params, $content = '', $template, &$repeat) {
	if($repeat) {
		\Clips\clips_context('indent_level', 1, true);
		return;
	}
	
	$show = \Clips\get_default($params, 'show', 'info');
	unset($params['show']);
	
	$default = array(
		'class' => array('alert', $show)
	);
	
//	$close_button = smarty_block_div(array(
//		'class' => 'btn',
//		'alert-for' => 'close'
//	), 'close', $template, $repeat);
	
	$close_button = \Clips\create_tag('div', array(), array(
		'class' => 'btn',
		'alert-for' => 'close'
	), \Clips\lang('close'));
	
	\Clips\clips_context('indent_level', 1, true);
	
//	$message = smarty_block_div(array(
//		'class' => 'alert-message'
//	), 'sdsds', $template, $repeat);
	
	$message = \Clips\create_tag('div', array(), array(
		'class' => 'alert-message'
	), \Clips\lang($content));
	
//	$controls = smarty_block_div(array(
//		'class' => 'alert-control'
//	), $close_button,  $template, $repeat);
	
	$controls = \Clips\create_tag('div', array(), array(
		'class' => 'alert-control'
	), $close_button);

	\Clips\context_pop('indent_level');
	return \Clips\create_tag('div', $params, $default, $message.$controls);
}