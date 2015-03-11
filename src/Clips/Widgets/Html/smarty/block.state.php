<?php in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

function smarty_block_state($params, $content = '', $template, &$repeat) {
	$name = Clips\get_default($params, 'name');
	$state = Clips\context_peek('state');

	if($repeat) {
		return;
	}

	if($name && ($name == 'default' && !Clips\context('matched_state'))
		|| ($state && $name == $state)) {
		Clips\context('matched_state', $name);
		return trim($content);
	}
	return '';
}
