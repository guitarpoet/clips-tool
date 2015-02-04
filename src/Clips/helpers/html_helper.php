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

function create_tag($tag = 'div', $attr = array(), $default = array(), $content = null, $close = false) {
	$arr = copy_arr($attr, $default);
	$ret = '<'.$tag.' '.format($arr, 'TagAttribute');
	if($content !== null) {
		$ret .= '>'.$content.'</'.$tag.'>'; 
	}
	else {
		if($close)
			$ret .= ' /';
		$ret .= '>';
	}
	return $ret;
}
