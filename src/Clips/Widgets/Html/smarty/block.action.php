<?php in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

use Clips\Interfaces\Action;

function smarty_block_action($params, $content = '', $template, &$repeat) {
	if($repeat) {
		Clips\clips_context('indent_level', 1, true);
		return;
	}

	$default = array('class' => 'action');
	$action = Clips\get_default($params, 'action');

	if($action && Clips\valid_obj($action, 'Clips\\Interfaces\\Action')) {
		$ps = $action->params();

		$icon = $action->icon();

		$caption = array();
		if($icon) {
			$caption []= Clips\create_tag_with_content('i', '', array('class' => $icon));
			$caption []= Clips\create_tag_with_content('span', $action->label());
			if($action->children())
				$caption []= Clips\create_tag_with_content('i', '', array('class' => array('fa', 'fa-angle-left', 'pull-right')));
			$caption = implode('', $caption);
		}
		else {
			$caption = $action->label();
		}

		if(!isset($params['title'])) // Add tooltip
			$params['title'] = $action->label();

		// This is valid action object
		switch($action->type()) {
		case Action::CLIENT:
			$params['caption'] = $caption;
			if($ps) {
				foreach($ps as $k => $v) {
					$params['data-'.$k] = $v;
				}
			}
			$content = $action->content();
			break;
		case Action::HEADER:
			return $caption;
		case Action::SERVER:
			$content = $caption;
			if($ps) {
				$suffix = implode('/', array_map(function($item){ return urlencode($item); }, $ps));
				$params['uri'] = \Clips\path_join($action->content(), $suffix);
			}
			else
				$params['uri'] = $action->content();
			break;
		case Action::EXTERNAL:
			$content = $caption;
			$suffix = array();
			foreach($ps as $k => $v) {
				$suffix []= urlencode($k).'='.urlencode($v);
			}
			if($suffix) {
				$suffix = implode('&', $suffix);
				$params['href'] = $action->content().'?'.$suffix;
			}
			else
				$params['href'] = $action->content();
			break;
		}


		unset($params['action']);
	}

	$value = Clips\get_default($params, 'caption');
	if($value) { // We did have value, so the content is the JavaScript
		$id = Clips\get_default($params, 'id', 'clips_action_'.Clips\sequence('action'));
		$js = "$(\"#$id\").click(function(){\n\t\t".trim($content)."\n\t});";
		$content = $template->fetch('string:'.$value);
		unset($params['caption']);
		$params['id'] = $id;
		$params['href'] = 'javascript:void(0)';
		Clips\context('jquery_init', $js, true);
	}
	else {
		// Check for action uri
		$uri = Clips\get_default($params, 'uri');
		if($uri) {
			$params['href'] = Clips\site_url($uri);
			unset($params['uri']);
	}
	if(!isset($params['title'])) // Add tooltip
		$params['title'] = trim($content);
	}
	Clips\context_pop('indent_level');

	$bundle_name = Clips\context('current_bundle');
	$bundle = Clips\get_default($params, 'bundle', $bundle_name);
	if($bundle !== null) {
		$bundle = Clips\bundle($bundle);
		$content = $bundle->message($content);
	}

	return Clips\create_tag_with_content('a', $content, $params, $default);
}
