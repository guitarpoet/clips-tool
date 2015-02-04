<?php namespace Clips\Libraries; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

class TagAttributeFormatter extends \Clips\Formatter {
	public function format($obj) {
		if(is_array($obj)) {
			$result = array();
			foreach($obj as $k => $v) {
				$str = $v;
				if(is_string($k)) {
					$name = \Clips\to_name($k);
					if(is_object($v)) {
						$fa = \get_annotation(get_class($v), 'Clips\\Formatter');
						if($fa) {
							$result []= $name.'="'.\format($v, $fa->value).'"';
						}
					}
					else if(is_array($v)) {
						$result []= $name . '="'.implode(' ', $v).'"';
					}
					else {
						$result []= $name. '="'.$v.'"';
					}
				}
			}
			return implode(' ', $result);
		}
		return '';
	}
}
