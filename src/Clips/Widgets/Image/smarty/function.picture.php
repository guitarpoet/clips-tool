<?php in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

function smarty_function_picture($params, $template) {
	$src = Clips\get_default($params, 'src');
	$path = Clips\get_default($params, 'path', 'responsive/size');
	$resolutions = Clips\get_default($params, 'resolutions');
	$medias = Clips\get_default($params, 'medias');

	$img_path = Clips\find_image($src);

	if(!$img_path) {
		Clips\error('figure', array('Can\'t find image '.$src.'!'));
		return '';
	}
	$size = Clips\image_size($img_path);
	$size = $size['width'];

	$content = array();
	$level = Clips\context('indent_level');
	if($level === null)
		$level = 0; // Default level is 0
	else
		$level = count($level);

	$indent = '';
	for($i = 0; $i < $level; $i++) {
		$indent .= "\t";
	}

	if($resolutions || $medias) {

		if($resolutions) { // If we are using auto resizing, skip the resolutions
			unset($params['resolutions']);
			foreach($resolutions as $res) {
				$content []= "\t".Clips\create_tag('source', array(
					'src' => Clips\site_url('responsive/size/'.(float)$res / 2880 * (float)$size .'/'.$src),
					'media' => '(min-width:'.$res.'px)'
				));
			}
		}

		if($medias) {
			unset($params['medias']);
			foreach($medias as $media => $res) {
				$content []= "\t".Clips\create_tag('source', array(
					'src' => Clips\site_url('responsive/size/'.$res.'/'.$src),
					'media' => '(min-width:'.$media.'px)'
				));
			}
		}
	}
	else
		$params['path'] = Clips\site_url($path);

	$image_dir = Clips\config('image_dir');
	if($image_dir) {
		$image_dir = $image_dir[0];
	}
	else {
		$image_dir = 'application/static/img/';
	}
	Clips\clips_context('indent_level', 1, true);
	$img = Clips\create_tag('img', array('src' => Clips\static_url(Clips\path_join($image_dir, $src))));
	$content []= "\t".Clips\create_tag_with_content('noscript', $img);
	Clips\context_pop('indent_level');


	return Clips\create_tag_with_content('picture', implode("\n$indent", $content), $params);
}
