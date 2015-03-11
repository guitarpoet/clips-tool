<?php in_array(__FILE__, get_included_files()) or exit("No direct script access allowed"); 

function smarty_block_lang($params, $content = '', $template, &$repeat) {
	if($repeat)
		return;
	// Get the format from parameters, if no format parameter, use content instead
	$format = Clips\get_default($params, 'format', $content);
	$bundle_name = Clips\context('current_bundle');
	if(!$bundle_name)
		$bundle_name = '';
	$bundle = Clips\bundle(Clips\get_default($params, 'bundle', $bundle_name));
	return $bundle->message($format, $content);
}
