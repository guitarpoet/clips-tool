<?php namespace Clips\Libraries; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

class TagAttributeFormatter extends \Clips\Formatter {
	private function processObject($obj) {
		$fa = \get_annotation(get_class($obj), 'Clips\\Formatter');
		if($fa) {
			return $name.'="'.\format($obj, $fa->value).'"';
		}
		else {
			return $this->format((array) $obj);
		}
	}

	public function format($obj) {
		if(is_array($obj)) {
			$result = array();
			foreach($obj as $k => $v) {
				$str = $v;
				if(is_string($k)) {
					$name = \Clips\to_name($k);
					if(is_object($v)) {
						$result []= $this->processObject($v);
					}
					else if(is_array($v)) {
						$result []= $name . '="'.implode(' ', $v).'"';
					}
					else {
						$result []= $name. '="'.$v.'"';
					}
				}
				else {
					if(is_array($v) || is_object($v))
						$result []= $this->format((array) $v);
					else
						$result []= $v;
				}
			}
			return implode(' ', $result);
		}
		return '';
	}
}
