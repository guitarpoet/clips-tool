<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

function smarty_function_submit($params, $template) {
	return Clips\create_tag('input', $params, array('type' => 'submit', 'value' => 'Submit', 'class' => array('btn', 'btn-primary')));
}
