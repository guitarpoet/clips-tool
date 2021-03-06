<?php namespace Clips\Libraries; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

class MmsegException extends \Clips\Exception {
	public function __construct($msg = null) {
		parent::__construct($msg);
	}
}

/**
 * The Chinese text segmenter using php-mmseg, can be used as text analyzer
 *
 * @author Jack
 * @date Sat Feb 21 12:53:06 2015
 */
class Mmseg {

	const DICT_NAME = "uni.lib";

	public function createDict($input_file, $output_dir = "/usr/local/etc/") {
		mmseg_create_dict($input_file, Clips\path_join($output_dir, DICT_NAME));
	}

	public function tokenize() {
		switch(func_num_args()) {
		case 0:
			throw new MmsegException("No input!");
		case 1:
			$str = func_get_arg(0);
			break;
		default:
			$str = func_get_arg(0);
			$callback = func_get_arg(1);
		}
		$arr = array();
		mmseg_tokenize($str, $arr);

		if(isset($callback)) {
			foreach($arr as $t) {
				call_user_func_array($callback, $t);
			}
		}
		else
			return $arr;
	}
}
