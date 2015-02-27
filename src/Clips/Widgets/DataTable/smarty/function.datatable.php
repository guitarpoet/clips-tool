<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

function smarty_function_datatable($params, $template) {
	$default = array(
		'class' => array('datatable', 'clips-datatable'),
		'cellspacing' => '0'
	);

	return \Clips\create_tag_with_content('table', '', $params, $default, true);
}
