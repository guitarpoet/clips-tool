<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed"); 

function smarty_block_lang($params, $content = '', $template, &$repeat) {
	if($repeat)
		return;
	// Get the format from parameters, if no format parameter, use content instead
	$format = Clips\get_default($params, 'format', $content);
	$bundle = Clips\bundle(Clips\get_default($params, 'bundle', ''));
	return $bundle->message($format, $content);
}
