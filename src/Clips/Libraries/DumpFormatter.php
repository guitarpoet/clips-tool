<?php namespace Clips\Libraries; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

class DumpFormatter extends \Clips\Formatter {
	public function format($obj) {
		ob_start();
		var_dump($obj);
		$contents = ob_get_contents();
		ob_end_clean();
		return $contents;
	}
}
