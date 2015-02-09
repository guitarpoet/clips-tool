<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

function smarty_block_head($params, $content = '', $template, &$repeat) {
	if($repeat)
		return;

	$encoding = Clips\get_default($params, 'encoding', 'UTF-8');
	$version = Clips\clips_context('html_version');
	$title = Clips\get_default($params, 'title', null);

	if(!$title) // If didn't found title on the parameter, try find it in context
		$title = Clips\clips_context('html_title');

	// Adding metas
	if(!$version)
		$version = 5;

	$meta = Clips\clips_context('html_meta');

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

	$pre = '';

	foreach($meta as $m) {
		$pre .= "\t\t".Clips\create_tag('meta', $m)."\n";
	}

	// Adding the title
	if($title) {
		$pre .= "\t\t".Clips\create_tag_with_content('title', $title);
	}

	if(isset($params['title']))
		unset($params['title']);
	return Clips\create_tag_with_content('head', "\n".$pre.$content, $params);
}
