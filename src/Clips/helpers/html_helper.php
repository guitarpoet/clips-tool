<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

function html_meta($key, $value) {
	$meta = context('html_meta');
	if(!$meta)
		$meta = array();
	
	$res = array();
	$found = false;
	foreach($meta as $m) {
		if(isset($m[$key])) {
			$m[$key] = $value;
			$found = true;
		}
		$res []= $m;
	}
	if(!$found)
		$res []= array($key => $value);
	context('html_meta', $res);
}

/**
 * Convert the string like this
 *
 * 1. a\b\c => a-b-c
 * 2. a_b_c => a-b-c
 * 3. a/b/c => a-b-c
 * 4. DemoTestController demo-test-controller
 *
 * This function is most useful for html attributes
 *
 * @author Jack
 * @date Sat Feb 21 11:23:30 2015
 */
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

/**
 * Shortcut function for create tag, using mostly for tag with contents
 *
 * @author Jack
 * @date Sat Feb 21 11:24:03 2015
 */
function create_tag_with_content($tag, $content, $attr = array(), $default = array()) {
	return create_tag($tag, $attr, $default, $content);
}


/**
 * Create the html tag(honor the indent level using clips context)
 *
 * Tag has 3 status
 *
 * 1. Open: The tag like <input>
 * 2. Closed: The tag like <br/> 
 * 3. With content: The tag <p>text</p>
 *
 * @author Jack
 * @date Sat Feb 21 11:24:47 2015
 * @param tag (default div)
 * 		The tagname
 * @param attr
 * 		The attributes of the tag
 * @param default
 * 		The default attributes of the tag, the class attribute will append to the attributes, others
 * 		will be replaced by attribute array
 * @param content
 * 		The inner content of the tag
 * @param close
 * 		If this tag is closed
 *
 */
function create_tag($tag = 'div', $attr = array(), $default = array(), $content = null, $close = false) {
	$level = context('indent_level');
	if($level === null)
		$level = 0; // Default level is 0
	else
		$level = count($level);

	// Check for auto layout for grid system
	$row = context_peek('row');
	if($row && $level == $row->level) {
		// This is row's direct child, let's apply the layout
		$class = array('column');
		$index = $row->index;
		$row->index++;
		foreach($row as $k => $v) {
			if(strpos($k, 'layout') === 0) { // The key begin with layout
				// Let's apply the layout
				if($k == 'layout') {
					if(isset($v[$index]))
						$class []= 'col-xs-'.$v[$index];
				}
				else {
					$data = explode('-', $k);
					if(isset($v[$index]))
						$class []= 'col-'.$data[1].'-'.$v[$index];
				}
			}
		}
		if(isset($default['class'])) {
			if(!is_array($default['class']))
				$default['class'] = array($default['class']);
			$default['class'] = array_merge($default['class'], $class);
		}
		else {
			$default['class'] = $class;
		}
	}

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
