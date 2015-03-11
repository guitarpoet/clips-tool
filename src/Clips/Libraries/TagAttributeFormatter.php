<?php namespace Clips\Libraries; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

class TagAttributeFormatter extends \Clips\Formatter {
	private function processObject($obj) {
		$fa = \CLips\get_annotation(get_class($obj), 'Clips\\Formatter');
		if($fa) {
			return $name.'="'.\format($obj, $fa->value).'"';
		}
		else {
			return $this->format((array) $obj);
		}
	}

	public function format($obj) {
		if(is_array($obj) && $obj) {
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
						// For i18n
						if($k == 'placeholder'
							|| $k == 'title'
							|| $k == 'alt') {
							$bundle_name = \Clips\context('current_bundle');
							if($bundle_name !== null) {
								$bundle = \Clips\bundle($bundle_name);
								$result []= $name. '="'.$bundle->message($v).'"';
							}
							else
								$result []= $name. '="'.$v.'"';
						}
						else
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
