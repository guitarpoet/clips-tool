<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

Clips\require_widget_smarty_plugin('Html', 'li');
Clips\require_widget_smarty_plugin('Html', 'ul');
Clips\require_widget_smarty_plugin('Html', 'action');

function _smarty_block_navigation_tree_node($action, $indent, $template, $repeat, $li_class = array()) {
	$tool = &Clips\get_clips_tool();
	$security = $tool->load_class('securityEngine', true);
	$result = $security->test($action);
	if($result) { // If rejected this action
		Clips\log('Rejected action [{0}] for reasion [{1}]', array($action->label(), $result[0]->reason, $action));
		return '';
	}
	
	$f = true;
	// Start the li
	smarty_block_li(array(), '', $template, $f);

	// Start the action
	smarty_block_action(array(), '', $template, $f);

	// Close the action
	$a = smarty_block_action(array('action' => $action), '', $template, $repeat);

	$children = $action->children();
	if($children) {
		$sub = array();
		smarty_block_ul(array('class' => 'sub-navi'), '', $template, $f);
		foreach($children as $c) {
			$sub []= _smarty_block_navigation_tree_node($c, $indent."\t\t", $template, $repeat, $li_class);
		}
		$a .= "\n$indent\t".smarty_block_ul(array('class' => 'sub-navi'), implode("", $sub), $template, $repeat);
	}

	$class = array();
	if($li_class) {
		if(!is_array($li_class))
			 $li_class = array($li_class);
		$class = $li_class;
	}
	if($action->active()) {
		$class []= 'active';
	}
	// Close the li
 	return "\n$indent".smarty_block_li(array('class' => $class), $a, $template, $repeat);
}

function smarty_block_navigation($params, $content = '', $template, &$repeat) {

	if($repeat) {
		// Start the navigation ul
		Clips\clips_context('indent_level', 1, true);
		return;
	}

	$default = array(
		'class' => array('nav', 'navbar')
	);

	$f = true;

	$actions = Clips\get_default($params, 'actions');

	if($actions) {
		unset($params['actions']);

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
				$content .= _smarty_block_navigation_tree_node($action, $indent, $template, $repeat, Clips\get_default($params, 'item-class', array()));
			}
		}
	}

	Clips\context_pop('indent_level');
	return Clips\create_tag_with_content('ul', $content, $params, $default);
}
