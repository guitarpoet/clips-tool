<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

function to_name($str) {
	$result = array();
	$str = str_replace('/', '\\', $str);
	$str = str_replace('_', '\\', $str);
	foreach(explode('\\', $str) as $s) {
		$tmp = array();
		foreach(str_split($s) as $c) {
			if(ctype_upper($c) && $tmp) {
				$result []= strtolower(implode('', $tmp));
				$tmp = array();
			}
			$tmp []= $c;
		}
		if($tmp)
			$result []= strtolower(implode('', $tmp));
	}
	return implode('-', $result);
}

function create_tag_with_content($tag, $content, $attr = array(), $default = array()) {
	return create_tag($tag, $attr, $default, $content);
}

function create_tag($tag = 'div', $attr = array(), $default = array(), $content = null, $close = false) {
	$level = clips_context('indent_level');
	if($level === null)
		$level = 0; // Default level is 0
	else
		$level = count($level);

	$indent = '';
	for($i = 0; $i < $level; $i++) {
		$indent .= "\t";
	}

	$arr = extend_arr($default, $attr, array('class'));
	$attr = format($arr, 'TagAttribute');
	$ret = '<'.$tag;
	if($attr)
		$ret .= ' '.$attr;
	if($content !== null) {
		$ret .= ">\n\t$indent".trim($content)."\n$indent".'</'.$tag.'>'; 
	}
	else {
		if($close)
			$ret .= ' /';
		$ret .= '>';
	}
	return $ret;
}
