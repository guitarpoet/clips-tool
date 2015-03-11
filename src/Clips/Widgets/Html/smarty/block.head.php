<?php in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

function smarty_block_head($params, $content = '', $template, &$repeat) {
	if($repeat) {
		Clips\clips_context('indent_level', 1, true); // Enstack indent level
		return;
	}

	$encoding = Clips\get_default($params, 'encoding', 'UTF-8');
	$version = Clips\context('html_version');
	$title = Clips\get_default($params, 'title', null);

	if(!$title) // If didn't found title on the parameter, try find it in context
		$title = Clips\context('html_title');

	// Adding metas
	if(!$version)
		$version = 5;

	$meta = Clips\context('html_meta');

	if(!$meta)
		$meta = array();

	switch($version) {
	case '4':
	case '4t':
	case '4s':
	case '4f':
	case 'xhtml':
	case 'xhtmlt':
	case 'xhtmlf':
	case 'xhtml1.1':
		array_unshift($meta, array('http-equiv' => 'Content-Type', 'content' => 'text/html;charset='.$encoding));
		break;
	case '5':
		array_unshift($meta, array('charset' => $encoding));
		break;
	}

	$pre = array();

	foreach($meta as $m) {
		$pre []= Clips\create_tag('meta', $m);
	}

	// Adding the title
	if($title) {
		$pre []= Clips\create_tag_with_content('title', $title);
	}

	$pre = "\t".implode("\n\t\t", $pre);

	if(isset($params['title']))
		unset($params['title']);

	Clips\context_pop('indent_level'); // Pop the stack before output
	return Clips\create_tag_with_content('head', $pre.$content, $params);
}
