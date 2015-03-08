<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

function smarty_block_markup($params, $content = '', $template, &$repeat) {
	if($repeat) {
		Clips\context('indent_level', 1, true);
		return;
	}

	$tool = &Clips\get_clips_tool();
	$flavor = Clips\get_default($params, 'flavor', 'github');
	$markup = $tool->library('markup');
	$content = $markup->render($content ,$flavor);

	$content = explode("\n", $content);

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

	Clips\context_pop('indent_level');
	return Clips\create_tag_with_content('div', $content, $params, array('class' => 'markup'));
}
