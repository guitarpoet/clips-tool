<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

function breadcrumb_process_item($item) {
	if(is_object($item) && get_class($item) == 'Action') {
		return Clips\create_tag('a', array(
			'title' => $item->label,
			'href' => $item->uri(),
		), array(), $item->label);
	}
	else if(is_array($item) && isset($item['uri'])) {
		$item = (object) $item;
		return Clips\create_tag('a', array(
			'title' => $item->label,
			'href' => $item->uri,
		), array(), $item->label);
	}
	return '';
}

function smarty_block_breadcrumb($params, $content = '', $template, &$repeat) {
	if($repeat) {
		return;
	}

	$items = get_default($params, 'items', null);
	$tags = array();

	if($items) {
		$list_tags = array();
		foreach($items as $item) {
			$a = breadcrumb_process_item($item);
			if($a != '')
				$list_tags []= Clips\create_tag('li', array(), array(), breadcrumb_process_item($item));
			else
				$list_tags []= Clips\create_tag('li', array('class' => 'divider'), array(), '');
		}
		$tags []= implode("\n", $list_tags);
	}
	else {
		$tags []= $content;
	}

	return Clips\create_tag('div', array('class' => 'breadcrumb'), array(), implode("", $tags));
}