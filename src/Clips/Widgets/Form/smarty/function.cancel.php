<?php in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

function smarty_function_cancel($params, $template) {
	Clips\require_widget_smarty_plugin('html', 'a');
	$bundle_name = Clips\context('current_bundle');
	$bundle = Clips\get_default($params, 'bundle', $bundle_name);

	$cancel = Clips\get_default($params, 'label', 'Cancel');
	if($bundle !== null) {
		$bundle = Clips\bundle($bundle);
		$cancel = $bundle->message($cancel);
	}

	$request = Clips\context('request');

	$bs = $request->breadscrumb();
	if(count($bs) > 1) {
		array_pop($bs); // Pop current
		$params['href'] = Clips\site_url(array_pop($bs));
		$params['title'] = $cancel;
		$f = true;
		// Open the link
		smarty_block_a($params, '', $template, $f);
		$f = false;
		return smarty_block_a($params, $cancel, $template, $f);
	}
	return '';
}
