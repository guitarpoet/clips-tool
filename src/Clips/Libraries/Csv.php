<?php namespace Clips\Libraries; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

use Clips\Resource;
use Clips\BaseService;

/**
 * The CSV data wrapper
 *
 * @author Jack
 * @version 1.0
 * @date Sat Apr 11 14:38:34 2015
 */
class Csv extends BaseService {
	/**
	 * Read the resource as CSV data
	 *
	 * @param uri
	 * 		The resource uri
	 * @param args default null
	 * 		If not null, will process the csv using mustache template
	 * @param fields default null
	 * 		If not null, will take this as the fields, if not, will use the first line
	 * @param flat default true
	 * 		If true, will flattern all the fields	
	 * @param enclosure default "
	 * 		The enclosure of the CSV
	 * @param escape default \
	 * 		The escape character of the CSV
	 */
	public function read($uri, $args = null, $fields = null, $flat = true, $delimiter = ",", $enclosure = '"', $escape = "\\") {
		$r = new Resource($uri);
		$c = $r->contents();
		if($c) {
			if($args) {
				$c = \Clips\out("string://".$c, $args, false);
			}

			$ret = array();
			foreach(explode("\n", $c) as $line) {
				$s = trim($line);
				if($s) {
					$data = str_getcsv($s);
					if($fields) {
						$d = array();
						for($i = 0; $i < count($fields); $i++) {
							$d[$fields[$i]] = $data[$i];
						}
						$ret []= $d;
					}
					else {
						if($flat) {
							$fields = array_map(function($item){ return \Clips\to_flat($item); }, $data);
						}
						else {
							$fields = $data;
						}
					}
				}
			}
			return $ret;
		}
		return array();
	}
}
