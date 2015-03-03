<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

function smarty_function_submit($params, $template) {
	$bundle_name = Clips\context('current_bundle');
	$bundle = Clips\get_default($params, 'bundle', $bundle_name);

	$submit = Clips\get_default($params, 'value', 'Submit');
	if($bundle !== null) {
		$bundle = Clips\bundle($bundle);
		$submit = $bundle->message($submit);
	}

	if(isset($params['value']))
		$params['value'] = $submit;

	return Clips\create_tag('input', $params, array('type' => 'submit', 'value' => $submit, 'class' => array('btn', 'btn-primary')));
}
