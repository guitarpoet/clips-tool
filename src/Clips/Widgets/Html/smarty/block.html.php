<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

function smarty_block_html($params, $content = '', $template, &$repeat) {
	$version = Clips\get_default($params, 'version', '5');
	Clips\clips_context('html_version', $version);

	if($repeat)
		return;

	$default = array();
	switch($version) {
	case '4':
	case '4t':
		$default['lang'] = 'en';
		$pre = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
		break;
	case '4s':
		$default['lang'] = 'en';
		$pre = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">';
		break;
	case '4f':
		$default['lang'] = 'en';
		$pre = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">';
		break;
	case 'xhtml':
		$default['xmlns'] = 'http://www.w3.org/1999/xhtml';
		$default['xml:lang'] = 'en';
		$pre = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
		break;
	case 'xhtmlt':
		$default['xmlns'] = 'http://www.w3.org/1999/xhtml';
		$default['xml:lang'] = 'en';
		$pre = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
		break;
	case 'xhtmlf':
		$default['xmlns'] = 'http://www.w3.org/1999/xhtml';
		$default['xml:lang'] = 'en';
		$pre = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">';
		break;
	case 'xhtml1.1':
		$default['xmlns'] = 'http://www.w3.org/1999/xhtml';
		$default['xml:lang'] = 'en';
		$pre = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">';
		break;
	case '5':
		$default['lang'] = 'en';
		$pre = '<!DOCTYPE html>';
		break;
	}
	if(isset($params['version']))
		unset($params['version']);
	return $pre."\n".Clips\create_tag_with_content('html', $content, $params, $default);
}
