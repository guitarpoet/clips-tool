<?php in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

function smarty_block_states($params, $content = '', $template, &$repeat) {
	$state = Clips\get_default($params, 'state');

	if($repeat) {
		if($state)
			Clips\context('state', $state, true); // Append the state into the state stack
		return;
	}

	if($state)
		Clips\context_pop('state'); 

	Clips\context_pop('matched_state');

	return trim($content);
}
