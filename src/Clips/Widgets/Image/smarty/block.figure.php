<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

function smarty_block_figure($params, $content = '', $template, &$repeat) {
	if($repeat) {
		Clips\clips_context('indent_level', 1, true);
		return;
	}

	$src = Clips\get_default($params, 'src');
	$path = Clips\get_default($params, 'path', 'responsive/size');
	$resolutions = Clips\get_default($params, 'resolutions');

	$img_path = Clips\find_image($src);

	if(!$img_path) {
		Clips\error('figure', array('Can\'t find image '.$src.'!'));
		return '';
	}
	$size = Clips\image_size($img_path);
	$size = $size['width'];

	if($resolutions) { // If we are using auto resizing, skip the resolutions
		foreach($resolutions as $res) {
			$attr['data-media'.$res] = Clips\site_url('responsive/size/'.(float)$res / 2880 * (float)$size .'/'.$src);
		}
	}

	$attr = array('path' => Clips\site_url($path));
	foreach($params as $key => $value) {
		if($key == 'path') {
			continue;
		}

		if(strpos($key, 'media') !== false) {
			$attr['data-'.$key] = Clips\site_url('responsive/size/'.$value.'/'.$src);
		}
		else
			$attr[$key] = $value;
	}

	$caption = Clips\create_tag_with_content('figcaption', $content);
	$img = Clips\create_tag('img', array('src' => Clips\static_url('application/static/img/'.$src)));
	$noscript = Clips\create_tag_with_content('noscript', $img);

	$level = Clips\context('indent_level');
	if($level === null)
		$level = 0; // Default level is 0
	else
		$level = count($level);
	$indent = '';
	for($i = 0; $i < $level; $i++) {
		$indent .= "\t";
	}
	Clips\context_pop('indent_level');
	return Clips\create_tag_with_content('figure', $noscript."\n$indent".$caption, $attr);
}

