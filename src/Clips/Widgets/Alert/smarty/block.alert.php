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
	

	$true = true;
	// Add message div
	
	// Open message div
	smarty_block_div(array('class' => 'alert-message'), $content, $template, $true);
	// Close message div
	$message = smarty_block_div(array('class' => 'alert-message'), $content, $template, $repeat);


	// Open controls
	smarty_block_div(array('class' => 'alert-control'), '',  $template, $true);

	// Open close button
	smarty_block_div(array('class' => 'btn', 'alert-for' => 'close'), \Clips\lang('close'), $template, $true);
	// Close close button
	$close_button = smarty_block_div(array('class' => 'btn', 'alert-for' => 'close'), \Clips\lang('close'), $template, $repeat);
	
	// Close controls
	$controls = smarty_block_div(array('class' => 'alert-control'), $close_button,  $template, $repeat);

	$level = Clips\context('indent_level');
	if($level === null)
		$level = 0; // Default level is 0
	else
		$level = count($level);

	$indent = '';
	for($i = 0; $i < $level; $i++) {
		$indent .= "\t";
	}
	
	\Clips\context_pop('indent_level');
	return \Clips\create_tag('div', $params, $default, $message."\n".$indent.$controls);
}
