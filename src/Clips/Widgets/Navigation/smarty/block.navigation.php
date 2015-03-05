<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

function smarty_block_navigation($params, $content = '', $template, &$repeat) {

	Clips\require_widget_smarty_plugin('Html', 'div');
	Clips\require_widget_smarty_plugin('Html', 'ul');
	Clips\require_widget_smarty_plugin('Html', 'li');
	Clips\require_widget_smarty_plugin('Html', 'a');
	Clips\require_widget_smarty_plugin('Grid', 'container');

	if($repeat) {
		Clips\clips_context('indent_level', 1, true);
		return;
	}

	$default = array(
		'class' => array('navbar', 'navbar-default')
	);

	$f = true;

	// Start the div
	smarty_block_container(array('fluid' => true), $content, $template, $f);

	$actions = Clips\get_default($params, 'actions');
	if($actions) {
		unset($params['actions']);
		// Start the collapse div
		smarty_block_div(array('class' => array('collapse', 'navbar-collapse')), $content, $template, $f);
		// Start the navigation ul
		smarty_block_ul(array(), $content, $template, $f);

		$content = '';

		$level = Clips\context('indent_level');
		if($level === null)
			$level = 0; // Default level is 0
		else
			$level = count($level);

		$indent = '';
		for($i = 0; $i < $level; $i++) {
			$indent .= "\t";
		}

		foreach($actions as $action) {
			// Only if the object is the valid action
			if(Clips\valid_obj($action, 'Clips\\Interfaces\\Action')) {

				// Start the li
				smarty_block_li(array(), '', $template, $f);

				// Start the action
				smarty_block_action(array(), '', $template, $f);

				// Close the action
				$a = smarty_block_action(array('action' => $action), '', $template, $repeat);

				// Close the li
				$content .= "\n$indent".smarty_block_li(array('class' => 'active'), $a, $template, $repeat);
			}
		}
		// End the navigation ul
		$content = smarty_block_ul(array('class' => array('nav', 'nav-bar')), $content, $template, $repeat);

		// Close the collacpse div
		$content = smarty_block_div(array('class' => array('collapse', 'navbar-collapse')), $content, $template, $repeat);
	}

	// Close the div
	$div = smarty_block_container(array('fluid' => true), $content, $template, $repeat);
	Clips\context_pop('indent_level');
	return Clips\create_tag_with_content('nav', $div, $params, $default);
}
